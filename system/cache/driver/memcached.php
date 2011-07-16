<?php namespace System\Cache\Driver;

use System\Config;

class Memcached implements \System\Cache\Driver {

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
		return (($cache = \System\Memcached::instance()->get(Config::get('cache.key').$key)) !== false) ? $cache : null;
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
		\System\Memcached::instance()->set(Config::get('cache.key').$key, $value, 0, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		\System\Memcached::instance()->delete(Config::get('cache.key').$key);
	}

}