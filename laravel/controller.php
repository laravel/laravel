<?php namespace Laravel; use Laravel\Routing\Destination;

abstract class Controller implements Destination {

	/**
	 * The "before" filters defined for the controller.
	 *
	 * @var array
	 */
	public $before = array();

	/**
	 * The "after" filters defined for the controller.
	 *
	 * @var array
	 */
	public $after = array();

	/**
	 * Get an array of filter names defined for the destination.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name)
	{
		return $this->$name;
	}

	/**
	 * Magic Method to handle calls to undefined functions on the controller.
	 *
	 * By default, the 404 response will be returned for an calls to undefined
	 * methods on the controller. However, this method may also be overridden
	 * and used as a pseudo-router by the controller.
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	/**
	 * Dynamically resolve items from the application IoC container.
	 *
	 * First, "laravel." will be prefixed to the requested item to see if there is
	 * a matching Laravel core class in the IoC container. If there is not, we will
	 * check for the item in the container using the name as-is.
	 */
	public function __get($key)
	{
		if (IoC::container()->registered("laravel.{$key}"))
		{
			return IoC::container()->core($key);
		}
		elseif (IoC::container()->registered($key))
		{
			return IoC::container()->resolve($key);
		}

		throw new \Exception("Attempting to access undefined property [$key] on controller.");
	}

}