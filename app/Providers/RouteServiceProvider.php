<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

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
	 * All of the application's route middleware keys.
	 *
	 * @var array
	 */
	protected $middleware = [
		'auth' => 'App\Http\Middleware\Authenticated',
		'auth.basic' => 'App\Http\Middleware\AuthenticatedWithBasicAuth',
		'csrf' => 'App\Http\Middleware\CsrfTokenIsValid',
		'guest' => 'App\Http\Middleware\IsGuest',
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
		// require app_path('Http/routes.php');
	}

}
