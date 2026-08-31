<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Laravel;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Events\EventDispatcher;
use Illuminate\Support\Carbon as IlluminateCarbon;
use Illuminate\Support\Facades\Date;
use Throwable;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /** @var callable|null */
    protected $appGetter = null;

    /** @var callable|null */
    protected $localeGetter = null;

    /** @var callable|null */
    protected $fallbackLocaleGetter = null;

    public function setAppGetter(?callable $appGetter): void
    {
        $this->appGetter = $appGetter;
    }

    public function setLocaleGetter(?callable $localeGetter): void
    {
        $this->localeGetter = $localeGetter;
    }

    public function setFallbackLocaleGetter(?callable $fallbackLocaleGetter): void
    {
        $this->fallbackLocaleGetter = $fallbackLocaleGetter;
    }

    public function boot()
    {
        $this->updateLocale();
        $this->updateFallbackLocale();

        if (!$this->app->bound('events')) {
            return;
        }

        $service = $this;
        $events = $this->app['events'];

        if ($this->isEventDispatcher($events)) {
            $events->listen(class_exists('Illuminate\Foundation\Events\LocaleUpdated') ? 'Illuminate\Foundation\Events\LocaleUpdated' : 'locale.changed', function () use ($service) {
                $service->updateLocale();
            });
        }
    }

    public function updateLocale()
    {
        $locale = $this->getLocale();

        if ($locale === null) {
            return;
        }

        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        CarbonPeriod::setLocale($locale);
        CarbonInterval::setLocale($locale);

        if (class_exists(IlluminateCarbon::class)) {
            IlluminateCarbon::setLocale($locale);
        }

        if (class_exists(Date::class)) {
            try {
                $root = Date::getFacadeRoot();
                $root->setLocale($locale);
            } catch (Throwable) {
                // Non Carbon class in use in Date facade
            }
        }
    }

    public function updateFallbackLocale()
    {
        $locale = $this->getFallbackLocale();

        if ($locale === null) {
            return;
        }

        Carbon::setFallbackLocale($locale);
        CarbonImmutable::setFallbackLocale($locale);
        CarbonPeriod::setFallbackLocale($locale);
        CarbonInterval::setFallbackLocale($locale);

        if (class_exists(IlluminateCarbon::class) && method_exists(IlluminateCarbon::class, 'setFallbackLocale')) {
            IlluminateCarbon::setFallbackLocale($locale);
        }

        if (class_exists(Date::class)) {
            try {
                $root = Date::getFacadeRoot();
                $root->setFallbackLocale($locale);
            } catch (Throwable) { // @codeCoverageIgnore
                // Non Carbon class in use in Date facade
            }
        }
    }

    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }

    protected function getLocale()
    {
        if ($this->localeGetter) {
            return ($this->localeGetter)();
        }

        $app = $this->getApp();
        $app = $app && method_exists($app, 'getLocale')
            ? $app
            : $this->getGlobalApp('translator');

        return $app ? $app->getLocale() : null;
    }

    protected function getFallbackLocale()
    {
        if ($this->fallbackLocaleGetter) {
            return ($this->fallbackLocaleGetter)();
        }

        $app = $this->getApp();

        return $app && method_exists($app, 'getFallbackLocale')
            ? $app->getFallbackLocale()
            : $this->getGlobalApp('translator')?->getFallback();
    }

    protected function getApp()
    {
        if ($this->appGetter) {
            return ($this->appGetter)();
        }

        return $this->app ?? $this->getGlobalApp();
    }

    protected function getGlobalApp(...$args)
    {
        return \function_exists('app') ? \app(...$args) : null;
    }

    protected function isEventDispatcher($instance)
    {
        return $instance instanceof EventDispatcher
            || $instance instanceof Dispatcher
            || $instance instanceof DispatcherContract;
    }
}
