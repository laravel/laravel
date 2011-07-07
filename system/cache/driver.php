<?php namespace System\Cache;

interface Driver {

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key);

	/**
	 * Get an item from the cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */	
	public function get($key);

	/**
	 * Write an item to the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes);

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key);

}