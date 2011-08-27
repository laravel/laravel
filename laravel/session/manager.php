<?php namespace Laravel\Session;

use Laravel\Config;
use Laravel\Container;

class Manager {

	/**
	 * Get the session driver.
	 *
	 * The session driver returned will be the driver specified in the session configuration
	 * file. Only one session driver may be active for a given request, so the driver will
	 * be managed as a singleton.
	 *
	 * @param  Container       $container
	 * @param  string          $driver
	 * @return Session\Driver
	 */
	public static function driver(Container $container, $driver)
	{
		if (in_array($driver, array('cookie', 'file', 'database', 'apc', 'memcached')))
		{
			return $container->resolve('laravel.session.'.$driver);
		}

		throw new \Exception("Session driver [$driver] is not supported.");
	}

}