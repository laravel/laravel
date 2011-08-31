<?php namespace Laravel;

abstract class Facade {

	/**
	 * Magic Method that allows the calling of a class staticly. This provides a convenient API
	 * while still maintaining the benefits of dependency injection and testability of the class.
	 *
	 * Each facade has a "resolve" property that informs the base class of what it needs to resolve
	 * our of the IoC container each time an operation is called on the facade.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(IoC::container()->resolve('laravel.'.static::$resolve), $method), $parameters);
	}

}