<?php namespace Laravel\Session\Drivers;

use Laravel\Cache\Manager as Cache;

class Factory {

	/**
	 * Create a new session driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'apc':
				return new APC(Cache::driver('apc'));

			case 'cookie':
				return new Cookie;

			case 'database':
				return new Database(\Laravel\Database\Manager::connection());

			case 'file':
				return new File(SESSION_PATH);

			case 'memcached':
				return new Memcached(Cache::driver('memcached'));

			case 'redis':
				return new Redis(Cache::driver('redis'));

			default:
				throw new \DomainException("Session driver [$driver] is not supported.");
		}
	}

}
