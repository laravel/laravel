<?php

namespace App\Providers;

use App\Services\BookInterface;
use App\Services\NewYorkTimesBookApi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {


        $this->app->singleton(BookInterface::class, function(Application $app){
            return new NewYorkTimesBookApi($app->make(PendingRequest::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
