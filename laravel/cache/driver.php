<?php namespace Laravel\Cache;

abstract class Driver {

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	abstract public function has($key);

	/**
	 * Get an item from the cache.
	 *
	 * A default value may also be specified, and will be returned in the requested
	 * item does not exist in the cache.
	 *
	 * <code>
	 *		// Get the "name" item from the cache
	 *		$name = Cache::driver()->get('name');
	 *
	 *		// Get the "name" item from the cache or return "Fred"
	 *		$name = Cache::driver()->get('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $driver
	 * @return mixed
	 */
	abstract public function get($key, $default = null);

	/**
	 * Prepare the cache item for returning to the requestor.
	 *
	 * If the item is NULL, the default will be returned.
	 *
	 * @param  mixed  $item
	 * @param  mixed  $default
	 * @return mixed
	 */
	protected function prepare($item, $default)
	{
		if ( ! is_null($item)) return $item;

		return (is_callable($default)) ? call_user_func($default) : $default;
	}

	/**
	 * Write an item to the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	abstract public function put($key, $value, $minutes);

	/**
	 * Get an item from the cache. If the item doesn't exist in the cache, store
	 * the default value in the cache and return it.
	 *
	 * <code>
	 *		// Get the "name" item from the cache or store "Fred" for 30 minutes
	 *		$name = Cache::driver()->remember('name', 'Fred', 30);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @return mixed
	 */
	public function remember($key, $value, $minutes)
	{
		if ( ! is_null($item = $this->get($key, null, $driver))) return $item;

		$default = is_callable($default) ? call_user_func($default) : $default;

		$this->put($key, $default, $minutes);

		return $default;
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	abstract public function forget($key);

}