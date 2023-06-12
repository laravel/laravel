<?php

use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on any
    | requests to your application. You may add your own services to the
    | arrays below to provide additional features to this application.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        // Package Service Providers...
    ])->merge([
        // Application Service Providers...
        App\Providers\AppServiceProvider::class,
    ])->merge([
        // Added Service Providers (Do not remove this line)...
    ])->toArray(),

];
