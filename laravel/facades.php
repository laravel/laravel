<?php namespace Laravel\Facades;

use Laravel\IoC;

/**
 * The Laravel framework makes thorough use of dependency injection assisted by an application
 * inversion of control container. This allows for great flexibility, easy testing, and better
 * architecture. However, most PHP framework users may be used to accessing classes through
 * a variety of static methods. Laravel provides "facades" to simulate this behavior while
 * still using heavy dependency injection.
 *
 * Each class that is commonly used by the developer has a corresponding facade defined in
 * this file. All of the various facades inherit from the abstract Facade class, which only
 * has a single __callStatic magic method. The facade simply resolves the requested class
 * out of the IoC container and calls the appropriate method.
 */
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
		$class = IoC::container()->resolve(static::$resolve);

		$count = count($parameters);

		if ($count > 5)
		{
			return call_user_func_array(array($class, $method), $parameters);
		}
		elseif ($count == 1)
		{
			return $class->$method($parameters[0]);
		}
		elseif ($count == 2)
		{
			return $class->$method($parameters[0], $parameters[1]);
		}
		elseif ($count == 3)
		{
			return $class->$method($parameters[0], $parameters[1], $parameters[2]);
		}
		elseif ($count == 4)
		{
			return $class->$method($parameters[0], $parameters[1], $parameters[2], $parameters[3]);
		}
		elseif ($count == 5)
		{
			return $class->$method($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]);
		}
	}

}

class Session extends Facade { public static $resolve = 'laravel.session'; }