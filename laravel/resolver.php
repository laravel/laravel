<?php namespace Laravel;

abstract class Resolver {

	/**
	 * Magic Method for resolving classes out of the IoC container.
	 *
	 * This allows the derived class to provide access to all of the Laravel libraries
	 * registered in the container. Currently, this class is derived by the Application
	 * and Controller classes.
	 */
	public function __get($key)
	{
		if (IoC::container()->registered('laravel.'.$key))
		{
			return IoC::container()->resolve('laravel.'.$key);
		}
		elseif (IoC::container()->registered($key))
		{
			return IoC::container()->resolve($key);
		}

		throw new \Exception("Attempting to access undefined property [$key].");
	}

}