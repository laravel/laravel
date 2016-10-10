<?php namespace Illuminate\Events;

use Illuminate\Container\Container;

class Dispatcher {

	/**
	 * The IoC container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * The registered event listeners.
	 *
	 * @var array
	 */
	protected $listeners = array();

	/**
	 * The wildcard listeners.
	 *
	 * @var array
	 */
	protected $wildcards = array();

	/**
	 * The sorted event listeners.
	 *
	 * @var array
	 */
	protected $sorted = array();

	/**
	 * The event firing stack.
	 *
	 * @var array
	 */
	protected $firing = array();

	/**
	 * Create a new event dispatcher instance.
	 *
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function __construct(Container $container = null)
	{
		$this->container = $container ?: new Container;
	}

	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  string|array  $event
	 * @param  mixed   $listener
	 * @param  int     $priority
	 * @return void
	 */
	public function listen($events, $listener, $priority = 0)
	{
		foreach ((array) $events as $event)
		{
			if (str_contains($event, '*'))
			{
				return $this->setupWildcardListen($event, $listener);
			}

			$this->listeners[$event][$priority][] = $this->makeListener($listener);

			unset($this->sorted[$event]);
		}
	}

	/**
	 * Setup a wildcard listener callback.
	 *
	 * @param  string  $event
	 * @param  mixed   $listener
	 * @return void
	 */
	protected function setupWildcardListen($event, $listener)
	{
		$this->wildcards[$event][] = $this->makeListener($listener);
	}

	/**
	 * Determine if a given event has listeners.
	 *
	 * @param  string  $eventName
	 * @return bool
	 */
	public function hasListeners($eventName)
	{
		return isset($this->listeners[$eventName]);
	}

	/**
	 * Register a queued event and payload.
	 *
	 * @param  string  $event
	 * @param  array   $payload
	 * @return void
	 */
	public function queue($event, $payload = array())
	{
		$me = $this;

		$this->listen($event.'_queue', function() use ($me, $event, $payload)
		{
			$me->fire($event, $payload);
		});
	}

	/**
	 * Register an event subscriber with the dispatcher.
	 *
	 * @param  string  $subscriber
	 * @return void
	 */
	public function subscribe($subscriber)
	{
		$subscriber = $this->resolveSubscriber($subscriber);

		$subscriber->subscribe($this);
	}

	/**
	 * Resolve the subscriber instance.
	 *
	 * @param  mixed  $subscriber
	 * @return mixed
	 */
	protected function resolveSubscriber($subscriber)
	{
		if (is_string($subscriber))
		{
			return $this->container->make($subscriber);
		}

		return $subscriber;
	}

	/**
	 * Fire an event until the first non-null response is returned.
	 *
	 * @param  string  $event
	 * @param  array   $payload
	 * @return mixed
	 */
	public function until($event, $payload = array())
	{
		return $this->fire($event, $payload, true);
	}

	/**
	 * Flush a set of queued events.
	 *
	 * @param  string  $event
	 * @return void
	 */
	public function flush($event)
	{
		$this->fire($event.'_queue');
	}

	/**
	 * Get the event that is currently firing.
	 *
	 * @return string
	 */
	public function firing()
	{
		return last($this->firing);
	}

	/**
	 * Fire an event and call the listeners.
	 *
	 * @param  string  $event
	 * @param  mixed   $payload
	 * @param  bool    $halt
	 * @return array|null
	 */
	public function fire($event, $payload = array(), $halt = false)
	{
		$responses = array();

		// If an array is not given to us as the payload, we will turn it into one so
		// we can easily use call_user_func_array on the listeners, passing in the
		// payload to each of them so that they receive each of these arguments.
		if ( ! is_array($payload)) $payload = array($payload);

		$this->firing[] = $event;

		foreach ($this->getListeners($event) as $listener)
		{
			$response = call_user_func_array($listener, $payload);

			// If a response is returned from the listener and event halting is enabled
			// we will just return this response, and not call the rest of the event
			// listeners. Otherwise we will add the response on the response list.
			if ( ! is_null($response) && $halt)
			{
				array_pop($this->firing);

				return $response;
			}

			// If a boolean false is returned from a listener, we will stop propagating
			// the event to any further listeners down in the chain, else we keep on
			// looping through the listeners and firing every one in our sequence.
			if ($response === false) break;

			$responses[] = $response;
		}

		array_pop($this->firing);

		return $halt ? null : $responses;
	}

	/**
	 * Get all of the listeners for a given event name.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	public function getListeners($eventName)
	{
		$wildcards = $this->getWildcardListeners($eventName);

		if ( ! isset($this->sorted[$eventName]))
		{
			$this->sortListeners($eventName);
		}

		return array_merge($this->sorted[$eventName], $wildcards);
	}

	/**
	 * Get the wildcard listeners for the event.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected function getWildcardListeners($eventName)
	{
		$wildcards = array();

		foreach ($this->wildcards as $key => $listeners)
		{
			if (str_is($key, $eventName)) $wildcards = array_merge($wildcards, $listeners);
		}

		return $wildcards;
	}

	/**
	 * Sort the listeners for a given event by priority.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected function sortListeners($eventName)
	{
		$this->sorted[$eventName] = array();

		// If listeners exist for the given event, we will sort them by the priority
		// so that we can call them in the correct order. We will cache off these
		// sorted event listeners so we do not have to re-sort on every events.
		if (isset($this->listeners[$eventName]))
		{
			krsort($this->listeners[$eventName]);

			$this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
		}
	}

	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  mixed   $listener
	 * @return mixed
	 */
	public function makeListener($listener)
	{
		if (is_string($listener))
		{
			$listener = $this->createClassListener($listener);
		}

		return $listener;
	}

	/**
	 * Create a class based listener using the IoC container.
	 *
	 * @param  mixed    $listener
	 * @return \Closure
	 */
	public function createClassListener($listener)
	{
		$container = $this->container;

		return function() use ($listener, $container)
		{
			// If the listener has an @ sign, we will assume it is being used to delimit
			// the class name from the handle method name. This allows for handlers
			// to run multiple handler methods in a single class for convenience.
			$segments = explode('@', $listener);

			$method = count($segments) == 2 ? $segments[1] : 'handle';

			$callable = array($container->make($segments[0]), $method);

			// We will make a callable of the listener instance and a method that should
			// be called on that instance, then we will pass in the arguments that we
			// received in this method into this listener class instance's methods.
			$data = func_get_args();

			return call_user_func_array($callable, $data);
		};
	}

	/**
	 * Remove a set of listeners from the dispatcher.
	 *
	 * @param  string  $event
	 * @return void
	 */
	public function forget($event)
	{
		unset($this->listeners[$event]);

		unset($this->sorted[$event]);
	}

}
