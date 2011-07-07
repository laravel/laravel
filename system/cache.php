<?php namespace System;

class Cache {

	/**
	 * The active cache drivers.
	 *
	 * @var Cache\Driver
	 */
	private static $drivers = array();

	/**
	 * Get a cache driver instance. Cache drivers are singletons.
	 *
	 * @param  string  $driver
	 * @return Cache\Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver))
		{
			$driver = Config::get('cache.driver');
		}

		if ( ! array_key_exists($driver, static::$drivers))
		{
			static::$drivers[$driver] = Cache\Factory::make($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Get an item from the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $driver
	 * @return mixed
	 */	
	public static function get($key, $default = null, $driver = null)
	{
		$item = static::driver($driver)->get($key);

		if (is_null($item))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

		return $item;
	}

	/**
	 * Get an item from the cache. If it doesn't exist, store the default value
	 * in the cache and return it.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @param  string  $driver
	 * @return mixed
	 */
	public static function maybe($key, $default, $minutes, $driver = null)
	{
		if ( ! is_null($item = static::get($key)))
		{
			return $item;
		}

		$default = is_callable($default) ? call_user_func($default) : $default;

		static::driver($driver)->put($key, $default, $minutes);

		return $default;
	}

	/**
	 * Pass all other methods to the default driver.
	 *
	 * Passing method calls to the driver instance provides a better API for the
	 * developer. For instance, instead of saying Cache::driver()->foo(), we can
	 * now just say Cache::foo().
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}