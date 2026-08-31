<?php

namespace Laravel\Sanctum;

use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Console\Commands\PruneExpired;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class SanctumServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'auth.guards.sanctum' => array_merge([
                'driver' => 'sanctum',
                'provider' => null,
            ], config('auth.guards.sanctum', [])),
        ]);

        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/sanctum.php', 'sanctum');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->runningInConsole()) {
            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'sanctum-migrations');

            $this->publishes([
                __DIR__.'/../config/sanctum.php' => config_path('sanctum.php'),
            ], 'sanctum-config');

            $this->commands([
                PruneExpired::class,
            ]);
        }

        $this->defineRoutes();
        $this->configureGuard();
        $this->configureMiddleware();
    }

    /**
     * Define the Sanctum routes.
     *
     * @return void
     */
    protected function defineRoutes()
    {
        if (app()->routesAreCached() || config('sanctum.routes') === false) {
            return;
        }

        Route::group(['prefix' => config('sanctum.prefix', 'sanctum')], function () {
            Route::get(
                '/csrf-cookie',
                CsrfCookieController::class.'@show'
            )->middleware('web')->name('sanctum.csrf-cookie');
        });
    }

    /**
     * Configure the Sanctum authentication guard.
     *
     * @return void
     */
    protected function configureGuard()
    {
        Auth::resolved(function ($auth) {
            $requestGuardCreator = fn ($config) => $this->createGuard($auth, $config);

            $auth->extend('sanctum', function ($app, $name, array $config) use ($requestGuardCreator) {
                return tap($requestGuardCreator($config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Register the guard.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @param  array  $config
     * @return RequestGuard
     */
    protected function createGuard($auth, $config)
    {
        return new RequestGuard(
            new Guard(
                $auth,
                config('sanctum.expiration'),
                $config['provider'],
                config('sanctum.last_used_at', true)
            ),
            request(),
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }

    /**
     * Configure the Sanctum middleware and priority.
     *
     * @return void
     */
    protected function configureMiddleware()
    {
        $kernel = app()->make(Kernel::class);

        $kernel->prependToMiddlewarePriority(EnsureFrontendRequestsAreStateful::class);
    }
}
