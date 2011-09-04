<?php namespace Laravel;

abstract class Facade {

	/**
	 * Magic Method for passing methods to a class registered in the IoC container.
	 * This provides a convenient method of accessing functions on classes that
	 * could not otherwise be accessed staticly.
	 *
	 * Facades allow Laravel to still have a high level of dependency injection
	 * and testability while still accomodating the common desire to conveniently
	 * use classes via static methods.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(IoC::container()->resolve('laravel.'.static::$resolve), $method), $parameters);
	}

}