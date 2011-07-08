<?php namespace System\Cache\Driver;

class File implements \System\Cache\Driver {

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
	 * @param  mixed   $default
	 * @return mixed
	 */	
	public function get($key)
	{
		if ( ! file_exists(APP_PATH.'storage/cache/'.$key))
		{
			return null;
		}

		$cache = file_get_contents(APP_PATH.'storage/cache/'.$key);

		if (time() >= substr($cache, 0, 10))
		{
			$this->forget($key);

			return null;
		}

		return $this->items[$key] = unserialize(substr($cache, 10));
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
		file_put_contents(APP_PATH.'storage/cache/'.$key, (time() + ($minutes * 60)).serialize($value), LOCK_EX);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		@unlink(APP_PATH.'storage/cache/'.$key);
	}

}