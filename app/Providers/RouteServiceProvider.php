<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * All of the application's route middleware keys.
	 *
	 * @var array
	 */
	protected $middleware = [
		'auth' => 'App\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
	];

	/**
	 * Called before routes are registered.
	 *
	 * Register any model bindings or pattern based filters.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @param  \Illuminate\Contracts\Routing\UrlGenerator  $url
	 * @return void
	 */
	public function before(Router $router, UrlGenerator $url)
	{
		$url->setRootControllerNamespace('App\Http\Controllers');
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => 'App\Http\Controllers'], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
