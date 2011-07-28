<?php namespace System\Cache\Driver;

class File implements \System\Cache\Driver {

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
	public function get($key)
	{
		if ( ! file_exists(CACHE_PATH.$key))
		{
			return null;
		}

		$cache = file_get_contents(CACHE_PATH.$key);

		if (time() >= substr($cache, 0, 10))
		{
			$this->forget($key);

			return null;
		}

		return unserialize(substr($cache, 10));
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
		file_put_contents(CACHE_PATH.$key, (time() + ($minutes * 60)).serialize($value), LOCK_EX);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		@unlink(CACHE_PATH.$key);
	}

}