<?php namespace App\Providers;

use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

	/**
	 * Configure the application's logging facilities.
	 *
	 * @param  \Illuminate\Contracts\Logging\Log  $log
	 * @return void
	 */
	public function boot(Log $log)
	{
		$log->useFiles(storage_path().'/logs/laravel.log');
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
