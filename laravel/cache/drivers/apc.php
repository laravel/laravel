<?php namespace Laravel\Cache\Drivers;

use Laravel\Proxy;

class APC extends Driver {

	/**
	 * The proxy class instance.
	 *
	 * @var Proxy
	 */
	private $apc;

	/**
	 * The cache key from the cache configuration file.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * Create a new APC cache driver instance.
	 *
	 * @param  Proxy   $proxy
	 * @param  string  $key
	 * @return void
	 */
	public function __construct(Proxy $apc, $key)
	{
		$this->key = $key;
		$this->proxy = $proxy;
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
		if ( ! is_null($cache = $this->proxy->apc_fetch($this->key.$key))) return $cache;
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
		$this->proxy->apc_store($this->key.$key, $value, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->proxy->apc_delete($this->key.$key);
	}

}