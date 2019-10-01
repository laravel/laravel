<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Implementation bindings.
     *
     * @var string[]
     */
    private $implementationBindings = [
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        foreach ($this->implementationBindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }

        if (config('app.debug')) {
            $this->app->register(\Arcanedev\LogViewer\LogViewerServiceProvider::class);
            $this->app->register(\PrettyRoutes\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
