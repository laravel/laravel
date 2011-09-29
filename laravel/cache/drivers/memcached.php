<?php namespace Laravel\Cache\Drivers; use Laravel\Config, Memcache;

class Memcached extends Driver {

	/**
	 * The Memcache instance.
	 *
	 * @var Memcache
	 */
	private $memcache;

	/**
	 * The cache key from the cache configuration file.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * Create a new Memcached cache driver instance.
	 *
	 * @param  Memcache  $memcache
	 * @return void
	 */
	public function __construct(Memcache $memcache, $key)
	{
		$this->key = $key;
		$this->memcache = $memcache;
	}

	/**
	 * Determine if an item exists in the cache.
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
		if (($cache = $this->memcache->get($this->key.$key)) !== false) return $cache;
	}

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$this->memcache->set($this->key.$key, $value, 0, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->memcache->delete($this->key.$key);
	}

}