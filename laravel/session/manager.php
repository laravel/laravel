<?php namespace Laravel\Session;

use Laravel\IoC;
use Laravel\Config;

class Manager {

	/**
	 * The active session driver.
	 *
	 * @var Session\Driver
	 */
	public static $driver;

	/**
	 * Get the session driver.
	 *
	 * The session driver returned will be the driver specified in the session configuration
	 * file. Only one session driver may be active for a given request, so the driver will
	 * be managed as a singleton.
	 *
	 * @return Session\Driver
	 */
	public static function driver()
	{
		if (is_null(static::$driver))
		{
			$driver = Config::get('session.driver');

			if (in_array($driver, array('cookie', 'file', 'database', 'apc', 'memcached')))
			{
				return static::$driver = IoC::container()->resolve('laravel.session.'.$driver);
			}

			throw new \Exception("Session driver [$driver] is not supported.");
		}

		return static::$driver;
	}

	/**
	 * Pass all other methods to the default session driver.
	 *
	 * By dynamically passing these method calls to the default driver, the developer is
	 * able to use with a convenient API when working with the session.
	 *
	 * <code>
	 *		// Get an item from the default session driver
	 *		$name = Session::get('name');
	 *
	 *		// Equivalent call using the driver method
	 *		$name = Session::driver()->get('name');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}