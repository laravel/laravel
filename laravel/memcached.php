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
	 * <code>
	 *		// Get the Memcache instance and get an item from the cache
	 *		$name = Memcached::instance()->get('name');
	 *
	 *		// Get the Memcache instance and place an item in the cache
	 *		Memcached::instance()->set('name', 'Taylor');
	 * </code>
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
	 * @param  array     $servers
	 * @return Memcache
	 */
	protected static function connect($servers)
	{
		$memcache = new \Memcache;

		foreach ($servers as $server)
		{
			$memcache->addServer($server['host'], $server['port'], true, $server['weight']);
		}

		if ($memcache->getVersion() === false)
		{
			throw new \Exception('Could not establish memcached connection.');
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