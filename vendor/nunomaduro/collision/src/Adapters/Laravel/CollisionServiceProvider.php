<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use NunoMaduro\Collision\Writer;
use Spatie\Ignition\Contracts\SolutionProviderRepository;

/**
 * @internal
 *
 * @final
 */
class CollisionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected bool $defer = true;

    /**
     * Boots application services.
     */
    public function boot(): void
    {
        $this->commands([
            TestCommand::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            $this->app->bind(Provider::class, function () {
                if ($this->app->has(SolutionProviderRepository::class)) { // @phpstan-ignore-line
                    /** @var SolutionProviderRepository $solutionProviderRepository */
                    $solutionProviderRepository = $this->app->get(SolutionProviderRepository::class); // @phpstan-ignore-line

                    $solutionsRepository = new IgnitionSolutionsRepository($solutionProviderRepository);
                } else {
                    $solutionsRepository = new NullSolutionsRepository;
                }

                $writer = new Writer($solutionsRepository);
                $handler = new Handler($writer);

                return new Provider(null, $handler);
            });

            /** @var ExceptionHandlerContract $appExceptionHandler */
            $appExceptionHandler = $this->app->make(ExceptionHandlerContract::class);

            $this->app->singleton(
                ExceptionHandlerContract::class,
                function ($app) use ($appExceptionHandler) {
                    return new ExceptionHandler($app, $appExceptionHandler);
                }
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [Provider::class];
    }
}
