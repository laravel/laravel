<?php namespace App\Providers;

use Log;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

	/**
	 * Configure the application's logging facilities.
	 *
	 * @param  Log  $log
	 * @return void
	 */
	public function boot()
	{
		Log::useFiles(storage_path().'/logs/laravel.log');
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