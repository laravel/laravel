<?php namespace Laravel\Cache\Drivers;

class APC_Engine {

	/**
	 * Retrieve an item from the APC cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function fetch($key)
	{
		return apc_fetch($key);
	}

	/**
	 * Store an item in the APC cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $seconds
	 * @return void
	 */
	public function store($key, $value, $seconds)
	{
		apc_store($key, $value, $seconds);
	}

	/**
	 * Delete an item from the APC cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function delete($key)
	{
		apc_delete($key);
	}

}

class APC extends Driver {

	/**
	 * The APC engine instance.
	 *
	 * @var APC_Engine
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
	 * @param  APC_Engine  $apc
	 * @param  string      $key
	 * @return void
	 */
	public function __construct(APC_Engine $apc, $key)
	{
		$this->apc = $apc;
		$this->key = $key;
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
		if ( ! is_null($cache = $this->apc->fetch($this->key.$key))) return $cache;
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
		$this->apc->store($this->key.$key, $value, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->apc->delete($this->key.$key);
	}

}