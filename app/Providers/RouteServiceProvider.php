<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/'; // Cambia esto según tu lógica de negocio:
    // public const HOME = '/customer/dashboard'; // Para clientes
    // public const HOME = '/subagent/dashboard'; // Para subagentes  
    // public const HOME = '/agency/dashboard'; // Para agencias

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}