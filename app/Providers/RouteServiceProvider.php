<?php namespace App\Providers;

use Illuminate\Routing\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * Called before routes are registered.
	 *
	 * Register any model bindings or pattern based filters.
	 *
	 * @return void
	 */
	public function before()
	{
		//
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->app->booted(function()
		{
			// Once the application has booted, we will include the default routes
			// file. This "namespace" helper will load the routes file within a
			// route group which automatically sets the controller namespace.

			$this->namespaced(function()
			{
				require app_path().'/Http/routes.php';
			});
		});
	}

}