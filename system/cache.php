<?php namespace System;

class Cache {

	/**
	 * All of the active cache drivers.
	 *
	 * @var Cache\Driver
	 * @see driver
	 */
	public static $drivers = array();

	/**
	 * Get a cache driver instance.
	 *
	 * If no driver name is specified, the default cache driver will be returned
	 * as defined in the cache configuration file.
	 *
	 * @param  string  $driver
	 * @return Cache\Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver)) $driver = Config::get('cache.driver');

		if ( ! array_key_exists($driver, static::$drivers))
		{
			switch ($driver)
			{
				case 'file':
					return static::$drivers[$driver] = new Cache\File;

				case 'memcached':
					return static::$drivers[$driver] = new Cache\Memcached;

				case 'apc':
					return static::$drivers[$driver] = new Cache\APC;

				default:
					throw new \Exception("Cache driver [$driver] is not supported.");
			}
		}

		return static::$drivers[$driver];
	}

	/**
	 * Get an item from the cache.
	 *
	 * If the cached item doesn't exist, the specified default value will be returned.
	 *
	 * <code>
	 *		// Get the "name" item from the cache
	 *		$name = Cache::get('name');
	 *
	 *		// Get the "name" item, but return "Fred" if it doesn't exist
	 *		$name = Cache::get('name', 'Fred');
	 * </code>
	 *
	 * The driver may also be specified:
	 *
	 * <code>
	 *		$name = Cache::get('name', null, 'memcached');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $driver
	 * @return mixed
	 */	
	public static function get($key, $default = null, $driver = null)
	{
		if (is_null($item = static::driver($driver)->get($key)))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

		return $item;
	}

	/**
	 * Get an item from the cache. If the item doesn't exist in the cache, store
	 * the default value in the cache and return it.
	 *
	 * <code>
	 *		// Get the name item. If it doesn't exist, store "Fred" for 30 minutes
	 *		$name = Cache::remember('name', 'Fred', 30);
	 *
	 *		// Closures may also be used as default values
	 *		$name = Cache::remember('votes', function() {return Vote::count();}, 30);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @param  string  $driver
	 * @return mixed
	 */
	public static function remember($key, $default, $minutes, $driver = null)
	{
		if ( ! is_null($item = static::get($key, null, $driver))) return $item;

		$default = is_callable($default) ? call_user_func($default) : $default;

		static::driver($driver)->put($key, $default, $minutes);

		return $default;
	}

	/**
	 * Pass all other methods to the default driver.
	 *
	 * Passing method calls to the driver instance provides a better API for the
	 * developer. For instance, instead of saying Cache::driver()->foo(), we can
	 * just say Cache::foo().
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}