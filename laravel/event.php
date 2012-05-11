<?php namespace Laravel;

class Event {

	/**
	 * All of the registered events.
	 *
	 * @var array
	 */
	public static $events = array();

	/**
	 * The queued events waiting for flushing.
	 *
	 * @var array
	 */
	public static $queued = array();

	/**
	 * All of the registered queue flusher callbacks.
	 *
	 * @var array
	 */
	public static $flushers = array();

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
	 * Override all callbacks for a given event with a new callback.
	 *
	 * @param  string  $event
	 * @param  mixed   $callback
	 * @return void
	 */
	public static function override($event, $callback)
	{
		static::clear($event);

		static::listen($event, $callback);
	}

	/**
	 * Add an item to an event queue for processing.
	 *
	 * @param  string  $queue
	 * @param  string  $key
	 * @param  mixed   $data
	 * @return void
	 */
	public static function queue($queue, $key, $data = array())
	{
		static::$queued[$queue][$key] = $data;
	}

	/**
	 * Register a queue flusher callback.
	 *
	 * @param  string  $queue
	 * @param  mixed   $callback
	 * @return void
	 */
	public static function flusher($queue, $callback)
	{
		static::$flushers[$queue][] = $callback;
	}

	/**
	 * Clear all event listeners for a given event.
	 *
	 * @param  string  $event
	 * @return void
	 */
	public static function clear($event)
	{
		unset(static::$events[$event]);
	}

	/**
	 * Fire an event and return the first response.
	 *
	 * <code>
	 *		// Fire the "start" event
	 *		$response = Event::first('start');
	 *
	 *		// Fire the "start" event passing an array of parameters
	 *		$response = Event::first('start', array('Laravel', 'Framework'));
	 * </code>
	 *
	 * @param  string  $event
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function first($event, $parameters = array())
	{
		return head(static::fire($event, $parameters));
	}

	/**
	 * Fire an event and return the the first response.
	 *
	 * Execution will be halted after the first valid response is found.
	 *
	 * @param  string  $event
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function until($event, $parameters = array())
	{
		return static::fire($event, $parameters, true);
	}

	/**
	 * Flush an event queue, firing the flusher for each payload.
	 *
	 * @param  string  $queue
	 * @return void
	 */
	public static function flush($queue)
	{
		foreach (static::$flushers[$queue] as $flusher)
		{
			// We will simply spin through each payload registered for the event and
			// fire the flusher, passing each payloads as we go. This allows all
			// the events on the queue to be processed by the flusher easily.
			foreach (static::$queued[$queue] as $key => $payload)
			{
				array_unshift($payload, $key);

				call_user_func_array($flusher, $payload);
			}
		}
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
	 *
	 *		// Fire multiple events with the same parameters
	 *		$responses = Event::fire(array('start', 'loading'), $parameters);
	 * </code>
	 *
	 * @param  string|array  $event
	 * @param  array         $parameters
	 * @param  bool          $halt
	 * @return array
	 */
	public static function fire($events, $parameters = array(), $halt = false)
	{
		$responses = array();

		$parameters = (array) $parameters;

		// If the event has listeners, we will simply iterate through them and call
		// each listener, passing in the parameters. We will add the responses to
		// an array of event responses and return the array.
		foreach ((array) $events as $event)
		{
			if (static::listeners($event))
			{
				foreach (static::$events[$event] as $callback)
				{
					$response = call_user_func_array($callback, $parameters);

					// If the event is set to halt, we will return the first response
					// that is not null. This allows the developer to easily stack
					// events but still get the first valid response.
					if ($halt and ! is_null($response))
					{
						return $response;
					}

					// After the handler has been called, we'll add the response to
					// an array of responses and return the array to the caller so
					// all of the responses can be easily examined.
					$responses[] = $response;
				}
			}
		}

		return $halt ? null : $responses;
	}

}