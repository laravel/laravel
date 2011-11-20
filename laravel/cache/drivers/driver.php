<?php namespace Laravel\Cache\Drivers; use Closure;

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
	 * <code>
	 *		// Get an item from the cache driver
	 *		$name = Cache::driver('name');
	 *
	 *		// Return a default value if the requested item isn't cached
	 *		$name = Cache::get('name', 'Taylor');
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
	 *		// Put an item in the cache for 15 minutes
	 *		Cache::put('name', 'Taylor', 15);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	abstract public function put($key, $value, $minutes);

	/**
	 * Get an item from the cache. If the item doesn't exist in the
	 * cache, store the default value in the cache and return it.
	 *
	 * <code>
	 *		// Get an item from the cache, or cache a value for 15 minutes
	 *		$name = Cache::remember('name', 'Taylor', 15);
	 *
	 *		// Use a closure for deferred execution
	 *		$count = Cache::remember('count', function() { return User::count(); }, 15);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @return mixed
	 */
	public function remember($key, $default, $minutes)
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
