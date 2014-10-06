<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Stack\Builder as Stack;

class AppServiceProvider extends ServiceProvider {

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
				->middleware('Illuminate\Cookie\Guard')
				->middleware('Illuminate\Cookie\Queue')
				->middleware('Illuminate\Session\Middlewares\Reader')
				->middleware('Illuminate\Session\Middlewares\Writer')
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
