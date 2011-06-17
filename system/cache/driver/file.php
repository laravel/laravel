<?php namespace System\Cache\Driver;

class Memcached implements \System\Cache\Driver {

	/**
	 * All of the loaded cache items.
	 *
	 * @var array
	 */
	private $items = array();

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
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if (array_key_exists($key, $this->items))
		{
			return $this->items[$key];
		}

		$cache = \System\Memcached::instance()->get(\System\Config::get('cache.key').$key);

		if ($cache === false)
		{
			return $default;
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
		\System\Memcached::instance()->set(\System\Config::get('cache.key').$key, $value, 0, $minutes * 60);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		\System\Memcached::instance()->delete(\System\Config::get('cache.key').$key);
	}

}