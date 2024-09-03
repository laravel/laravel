<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register(): void
    {
        // Example of binding a service to the container
        // $this->app->bind(SomeInterface::class, SomeConcreteClass::class);
        
        // Register additional services here
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot(): void
    {
        // Example of registering routes or event listeners
        // $this->app->router->group([], function ($router) {
        //     require base_path('routes/web.php');
        // });
    }
}
