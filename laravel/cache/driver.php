<?php namespace Laravel\Cache;

use Closure;

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
	 *		// Retrieve an item from the cache
	 *		$name = Cache::get('name');
	 *
	 *		// Retrieve an item from the cache and return a default value if it doesn't exist
	 *		$name = Cache::get('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $driver
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if ( ! is_null($item = $this->retrieve($key))) return $item;

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Retrieve an item from the cache driver.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	abstract protected function retrieve($key);

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * <code>
	 *		// Store an item in the cache for 5 minutes
	 *		Cache::put('name', 'Fred', 5);
	 * </code>
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
	 *		// Get an item from the cache and store the default value if it doesn't exist
	 *		Cache::remember('name', 'Fred', 5);
	 *
	 *		// Closures may also be used to defer retrieval of the default value
	 *		Cache::remember('users', function() {return DB::table('users')->get();}, 5);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @return mixed
	 */
	public function remember($key, $value, $minutes)
	{
		if ( ! is_null($item = $this->get($key, null))) return $item;

		$default = ($default instanceof Closure) ? call_user_func($default) : $default;

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