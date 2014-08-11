<?php namespace Providers;

use Illuminate\Foundation\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

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