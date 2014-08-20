<?php namespace App\Providers;

use Illuminate\Foundation\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Get the directories to scan for events.
	 *
	 * @return array
	 */
	public function scan()
	{
		return [
			app_path().'/src',
		];
	}

}