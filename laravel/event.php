<?php namespace Laravel;

class Event {

	/**
	 * All of the registered events.
	 *
	 * @var array
	 */
	public static $events = array();

	/**
	 * Determine if an event has any registered listeners.
	 *
	 * @param  string  $event
	 * @return bool
	 */
	public static function listeners($event)
	{
		return isset(static::$events[$event]);
	}

	/**
	 * Register a callback for a given event.
	 *
	 * <code>
	 *		// Register a callback for the "start" event
	 *		Event::listen('start', function() {return 'Started!';});
	 *
	 *		// Register an object instance callback for the given event
	 *		Event::listen('event', array($object, 'method'));
	 * </code>
	 *
	 * @param  string  $event
	 * @param  mixed   $callback
	 * @return void
	 */
	public static function listen($event, $callback)
	{
		static::$events[$event][] = $callback;
	}

	/**
	 * Fire an event so that all listeners are called.
	 *
	 * <code>
	 *		// Fire the "start" event
	 *		$responses = Event::fire('start');
	 *
	 *		// Fire the "start" event passing an array of parameters
	 *		$responses = Event::fire('start', array('Laravel', 'Framework'));
	 * </code>
	 *
	 * @param  string  $event
	 * @param  array   $parameters
	 * @return array
	 */
	public static function fire($event, $parameters = array())
	{
		$responses = array();

		if (static::listeners($event))
		{
			foreach (static::$events[$event] as $callback)
			{
				$responses[] = call_user_func_array($callback, $parameters);
			}
		}

		return $responses;
	}

}