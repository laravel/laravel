<?php namespace Illuminate\Cache;

class ApcWrapper {

	/**
	 * Indicates if APCu is supported.
	 *
	 * @var bool
	 */
	protected $apcu = false;

	/**
	 * Create a new APC wrapper instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->apcu = function_exists('apcu_fetch');
	}

	/**
	 * Get an item from the cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->apcu ? apcu_fetch($key) : apc_fetch($key);
	}

	/**
	 * Store an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $seconds
	 * @return array|bool
	 */
	public function put($key, $value, $seconds)
	{
		return $this->apcu ? apcu_store($key, $value, $seconds) : apc_store($key, $value, $seconds);
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array|bool
	 */
	public function increment($key, $value)
	{
		return $this->apcu ? apcu_inc($key, $value) : apc_inc($key, $value);
	}

	/**
	 * Decrement the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array|bool
	 */
	public function decrement($key, $value)
	{
		return $this->apcu ? apcu_dec($key, $value) : apc_dec($key, $value);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * @return array|bool
	 */
	public function delete($key)
	{
		return $this->apcu ? apcu_delete($key) : apc_delete($key);
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->apcu ? apcu_clear_cache() : apc_clear_cache('user');
	}

}
