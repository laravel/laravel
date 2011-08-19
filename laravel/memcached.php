<?php namespace Laravel;

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
			static::$instance = static::connect(Config::get('cache.servers'));
		}

		return static::$instance;
	}

	/**
	 * Connect to the configured Memcached servers.
	 *
	 * @param  array     $servers
	 * @return Memcache
	 */
	private static function connect($servers)
	{
		if ( ! class_exists('Memcache'))
		{
			throw new \Exception('Attempting to use Memcached, but the Memcached PHP extension is not installed on this server.');
		}

		$memcache = new \Memcache;

		foreach ($servers as $server)
		{
			$memcache->addServer($server['host'], $server['port'], true, $server['weight']);
		}

		if ($memcache->getVersion() === false)
		{
			throw new \Exception('Memcached is configured. However, no connections could be made. Please verify your memcached configuration.');
		}

		return $memcache;
	}

}