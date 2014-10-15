<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * The root controller namespace for URL generation.
	 *
	 * @var string
	 */
	protected $rootUrlNamespace = 'App\Http\Controllers';

	/**
	 * The controllers to scan for route annotations.
	 *
	 * @var array
	 */
	protected $scan = [
		'App\Http\Controllers\HomeController',
		'App\Http\Controllers\Auth\AuthController',
		'App\Http\Controllers\Auth\PasswordController',
	];

	/**
	 * Called before routes are registered.
	 *
	 * Register any model bindings or pattern based filters.
	 *
	 * @param  Router  $router
	 * @return void
	 */
	public function before(Router $router)
	{
		//
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map(Router $router)
	{
		//
	}

}
