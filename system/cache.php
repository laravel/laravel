<?php namespace System;

class Cache {

	/**
	 * The active cache drivers.
	 *
	 * @var Cache\Driver
	 */
	private static $drivers = array();

	/**
	 * Get the cache driver instance.
	 *
	 * @param  string  $driver
	 * @return Cache\Driver
	 */
	public static function driver($driver = null)
	{
		if ( ! array_key_exists($driver, static::$drivers))
		{
			// --------------------------------------------------
			// If no driver was specified, use the default.
			// --------------------------------------------------
			if (is_null($driver))
			{
				$driver = Config::get('cache.driver');
			}

			// --------------------------------------------------
			// Create the cache driver.
			// --------------------------------------------------
			static::$drivers[$driver] = Cache\Factory::make($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Pass all other methods to the default driver.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}