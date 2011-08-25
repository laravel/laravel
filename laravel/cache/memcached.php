<?php namespace Laravel\Cache;

use Laravel\Config;

class Memcached extends Driver {

	/**
	 * The Memcache instance.
	 *
	 * @var Memcache
	 */
	private $memcache;

	/**
	 * Create a new Memcached cache driver instance.
	 *
	 * @param  Memcache  $memcache
	 * @return void
	 */
	public function __construct(\Memcache $memcache)
	{
		$this->memcache = $memcache;
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * <code>
	 *		// Determine if the "name" item exists in the cache
	 *		$exists = Cache::driver()->has('name');
	 * </code>
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	/**
	 * Retrieve an item from the cache driver.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function retrieve($key)
	{
		return (($cache = $this->memcache->get(Config::get('cache.key').$key)) !== false) ? $cache : null;
	}

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * <code>
	 *		// Write the "name" item to the cache for 30 minutes
	 *		Cache::driver()->put('name', 'Fred', 30);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$this->memcache->set(Config::get('cache.key').$key, $value, 0, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->memcache->delete(Config::get('cache.key').$key);
	}

}