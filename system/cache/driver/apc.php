<?php namespace System\Cache\Driver;

class APC implements \System\Cache\Driver {

	/**
	 * All of the loaded cache items.
	 *
	 * @var array
	 */
	public $items = array();

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
	 * Get an item from the cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key)
	{
		$cache = apc_fetch(\System\Config::get('cache.key').$key);

		if ($cache === false)
		{
			return null;
		}

		return $this->items[$key] = $cache;
	}

	/**
	 * Write an item to the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		apc_store(\System\Config::get('cache.key').$key, $value, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		apc_delete(\System\Config::get('cache.key').$key);
	}

}