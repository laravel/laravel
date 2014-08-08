<?php

use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->setupLogging();
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

	/**
	 * Setup the logging facilities for the application.
	 *
	 * @return void
	 */
	protected function setupLogging()
	{
		// Here we will configure the error logger setup for the application which
		// is built on top of the wonderful Monolog library. By default we will
		// build a basic log file setup which creates a single file for logs.

		Log::useFiles(storage_path().'/logs/laravel.log');
	}

}