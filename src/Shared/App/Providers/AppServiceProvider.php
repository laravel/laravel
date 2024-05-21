<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Providers;

use Carbon\CarbonImmutable;
use Dedoc\Scramble\Scramble;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Lightit\Security\Domain\Actions\PreventDebugInProductionAction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());

        PreventDebugInProductionAction::execute(
            isProduction: $this->app->isProduction(),
            isDebug: (bool) config('app.debug')
        );

        Model::shouldBeStrict(! $this->app->isProduction());

        RateLimiter::for('api', function (Request $request) {
            /** @var int $rateLimiter */
            $rateLimiter = config('app.rate.limit');

            return Limit::perMinute($rateLimiter)
                ->by($request->user()?->id ?: $request->ip());
        });

        Date::use(CarbonImmutable::class);

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            });

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }
}
