<?php namespace Laravel\Cache\Drivers;

use Laravel\Config;

class Factory {

	/**
	 * Create a new cache driver instance.
	 *
	 * @param  string          $driver
	 * @return Driver
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'apc':
				return new APC(Config::get('cache.key'));

			case 'file':
				return new File(CACHE_PATH);

			case 'memcached':
				return new Memcached(\Laravel\Memcached::instance(), Config::get('cache.key'));

			case 'redis':
				return new Redis(\Laravel\Redis::db());

			default:
				throw new \Exception("Cache driver {$driver} is not supported.");
		}
	}

}