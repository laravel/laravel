<?php namespace Laravel\Cache;

/**
 * Wrap the file functions in a class that can be injected into driver.
 * Since the file functions are global, the driver is untestable without
 * injecting a wrapper around them.
 */
class File_Engine {

	/**
	 * Determine if a file exists.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	public function exists($file)
	{
		return file_exists($file);
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param  string  $file
	 * @return string
	 */
	public function get($file)
	{
		return file_get_contents($file);
	}

	/**
	 * Write to a file.
	 *
	 * @param  string  $file
	 * @param  string  $value
	 * @return void
	 */
	public function put($file, $value)
	{
		file_put_contents($file, $value, LOCK_EX);
	}

	/**
	 * Delete a file.
	 *
	 * @param  string  $file
	 * @return void
	 */
	public function forget($file)
	{
		@unlink($file);
	}

}

class File extends Driver {

	/**
	 * The File cache engine.
	 *
	 * @var File_Engine
	 */
	private $file;

	/**
	 * Create a new File cache driver instance.
	 *
	 * @param  File_Engine  $file
	 * @return void
	 */
	public function __construct(File_Engine $file)
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
		$this->file->forget(CACHE_PATH.$key);
	}

}