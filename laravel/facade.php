<?php namespace Laravel;

abstract class Facade {

	/**
	 * Magic Method for passing methods to a class registered in the IoC container.
	 * This provides a convenient method of accessing functions on classes that
	 * could not otherwise be accessed staticly.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(IoC::container()->resolve('laravel.'.static::$resolve), $method), $parameters);
	}

}