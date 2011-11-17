<?php namespace Laravel;

class Memcached {

	/**
	 * The Memcached connection instance.
	 *
	 * @var Memcache
	 */
	protected static $instance;

	/**
	 * Get the Memcached connection instance.
	 *
	 * This connection will be managed as a singleton instance so that only
	 * one connection to the Memcached severs will be established.
	 *
	 * @return Memcache
	 */
	public static function instance()
	{
		if (is_null(static::$instance))
		{
			static::$instance = static::connect(Config::get('cache.memcached'));
		}

		return static::$instance;
	}

	/**
	 * Create a new Memcached connection instance.
	 *
	 * The configuration array passed to this method should be an array of
	 * server hosts / ports, like those defined in the cache configuration
	 * file.
	 *
	 * <code>
	 *		// Create a new localhost Memcached connection instance.
	 *		$memcache = Memcached::connect(array('host' => '127.0.0.1', 'port' => 11211));
	 * </code>
	 *
	 * @param  array     $servers
	 * @return Memcache
	 */
	public static function connect($servers)
	{
		$memcache = new \Memcache;

		foreach ($servers as $server)
		{
			$memcache->addServer($server['host'], $server['port'], true, $server['weight']);
		}

		if ($memcache->getVersion() === false)
		{
			throw new \RuntimeException('Could not establish memcached connection.');
		}

		return $memcache;
	}

	/**
	 * Dynamically pass all other method calls to the Memcache instance.
	 *
	 * <code>
	 *		// Get an item from the Memcache instance
	 *		$name = Memcached::get('name');
	 *
	 *		// Store data on the Memcache server
	 *		Memcached::set('name', 'Taylor');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::instance(), $method), $parameters);
	}

}