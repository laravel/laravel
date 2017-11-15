<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
        | If you receive this SQL Error:
        | "SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long;",
        |
        | Uncomment the next line
        */
        // \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
