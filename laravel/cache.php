<?php namespace Laravel; defined('DS') or die('No direct script access.');

class Cache {

	/**
	 * All of the active cache drivers.
	 *
	 * @var array
	 */
	public static $drivers = array();

	/**
	 * Get a cache driver instance.
	 *
	 * If no driver name is specified, the default will be returned.
	 *
	 * <code>
	 *		// Get the default cache driver instance
	 *		$driver = Cache::driver();
	 *
	 *		// Get a specific cache driver instance by name
	 *		$driver = Cache::driver('memcached');
	 * </code>
	 *
	 * @param  string        $driver
	 * @return Cache\Drivers\Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver)) $driver = Config::get('cache.driver');

		if ( ! isset(static::$drivers[$driver]))
		{
			static::$drivers[$driver] = static::factory($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Create a new cache driver instance.
	 *
	 * @param  string  $driver
	 * @return Cache\Drivers\Driver
	 */
	protected static function factory($driver)
	{
		switch ($driver)
		{
			case 'apc':
				return new Cache\Drivers\APC(Config::get('cache.key'));

			case 'file':
				return new Cache\Drivers\File(path('storage').'cache'.DS);

			case 'memcached':
				return new Cache\Drivers\Memcached(Memcached::connection(), Config::get('cache.key'));

			case 'redis':
				return new Cache\Drivers\Redis(Redis::db());

			case 'database':
				return new Cache\Drivers\Database(Config::get('cache.key'));

			default:
				throw new \Exception("Cache driver {$driver} is not supported.");
		}
	}

	/**
	 * Magic Method for calling the methods on the default cache driver.
	 *
	 * <code>
	 *		// Call the "get" method on the default cache driver
	 *		$name = Cache::get('name');
	 *
	 *		// Call the "put" method on the default cache driver
	 *		Cache::put('name', 'Taylor', 15);
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}
