<?php namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Events\ListenerServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'event.name' => [
			'EventListener',
		],
	];

}
