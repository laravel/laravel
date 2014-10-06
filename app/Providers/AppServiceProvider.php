<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Stack\Builder as Stack;

class AppServiceProvider extends ServiceProvider {

	/**
	 * All of the application's route middleware keys.
	 *
	 * @var array
	 */
	protected $middleware = [
		'auth' => 'App\Http\Middleware\AuthMiddleware',
		'auth.basic' => 'App\Http\Middleware\BasicAuthMiddleware',
		'csrf' => 'App\Http\Middleware\CsrfMiddleware',
		'guest' => 'App\Http\Middleware\GusetMiddleware',
	];

	/**
	 * The application's middleware stack.
	 *
	 * @var array
	 */
	protected $stack = [
		'App\Http\Middleware\MaintenanceMiddleware',
		'Illuminate\Cookie\Guard',
		'Illuminate\Cookie\Queue',
		'Illuminate\Session\Middleware\Reader',
		'Illuminate\Session\Middleware\Writer',
	];

	/**
	 * Bootstrap any necessary services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// This service provider is a convenient place to register your services
		// in the IoC container. If you wish, you may make additional methods
		// or service providers to keep the code more focused and granular.

		$this->app->stack(function(Stack $stack, Router $router)
		{
			return $stack
				->middleware($this->stack)
				->then(function($request) use ($router)
				{
					return $router->dispatch($request);
				});
			});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
