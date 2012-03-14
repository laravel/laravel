<?php namespace LCQRS\Message\Drivers;

use Laravel\Event as MessagingService;
use Closure;

class Event {

	/**
	 * Publish a message to a channel
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function pub($channel, $message)
	{
		MessagingService::fire($channel, $message);
	}

	/**
	 * Add subsciption for channel
	 *
	 * @param  string   $channel
	 * @param  closure  $callback
	 * @return void
	 */
	public function sub($channel, Closure $callback)
	{
		MessagingService::listen($channel, $callback);
	}

}