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
		// --------------------------------------------------
		// If the cache driver has already been instantiated,
		// we'll just return that existing instance.
		//
		// Otherwise, we'll instantiate a new one.
		// --------------------------------------------------
		if ( ! array_key_exists($driver, static::$drivers))
		{
			if (is_null($driver))
			{
				$driver = Config::get('cache.driver');
			}

			static::$drivers[$driver] = Cache\Factory::make($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Pass all other methods to the default driver.
	 */
	public static function __callStatic($method, $parameters)
	{
		// --------------------------------------------------
		// Passing method calls to the driver instance allows
		// a better API for the developer.
		//
		// For instance, instead of saying Cache::driver()->foo(),
		// we can now just say Cache::foo().
		// --------------------------------------------------
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}