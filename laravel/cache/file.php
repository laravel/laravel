<?php namespace Laravel\Cache;

class File extends Driver {

	/**
	 * The file manager instance.
	 *
	 * @var Laravel\File
	 */
	private $file;

	/**
	 * Create a new File cache driver instance.
	 *
	 * @param  Laravel\File  $file
	 * @return void
	 */
	public function __construct(\Laravel\File $file)
	{
		$this->file = $file;
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * <code>
	 *		// Determine if the "name" item exists in the cache
	 *		$exists = Cache::driver()->has('name');
	 * </code>
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	/**
	 * Retrieve an item from the cache driver.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function retrieve($key)
	{
		if ( ! $this->file->exists(CACHE_PATH.$key)) return null;

		if (time() >= substr($cache = $this->file->get(CACHE_PATH.$key), 0, 10))
		{
			return $this->forget($key);
		}

		return unserialize(substr($cache, 10));
	}

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * <code>
	 *		// Write the "name" item to the cache for 30 minutes
	 *		Cache::driver()->put('name', 'Fred', 30);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$this->file->put(CACHE_PATH.$key, (time() + ($minutes * 60)).serialize($value));
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->file->delete(CACHE_PATH.$key);
	}

}