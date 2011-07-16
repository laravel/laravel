<?php namespace System;

class Memcached {

	/**
	 * The Memcache instance.
	 *
	 * @var Memcache
	 */
	private static $instance = null;

	/**
	 * Get the singleton Memcache instance.
	 *
	 * @return Memcache
	 */
	public static function instance()
	{
		if (is_null(static::$instance))
		{
			if ( ! class_exists('Memcache'))
			{
				throw new \Exception('Attempting to use Memcached, but the Memcached PHP extension is not installed on this server.');
			}

			$memcache = new \Memcache;

			foreach (Config::get('cache.servers') as $server)
			{
				$memcache->addServer($server['host'], $server['port'], true, $server['weight']);
			}

			if ($memcache->getVersion() === false)
			{
				throw new \Exception('Memcached is configured. However, no connections could be made. Please verify your memcached configuration.');
			}

			static::$instance = $memcache;
		}

		return static::$instance;
	}

}