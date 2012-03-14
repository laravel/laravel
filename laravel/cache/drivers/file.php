<?php namespace Laravel\Cache\Drivers;

class File extends Driver {

	/**
	 * The path to which the cache files should be written.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new File cache driver instance.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

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
	 * Retrieve an item from the cache driver.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function retrieve($key)
	{
		if ( ! file_exists($this->path.$key)) return null;

		// File based caches store have the expiration timestamp stored in
		// UNIX format prepended to their contents. This timestamp is then
		// extracted and removed when the cache is read to determine if
		// the file is still valid.
		if (time() >= substr($cache = file_get_contents($this->path.$key), 0, 10))
		{
			return $this->forget($key);
		}

		return unserialize(substr($cache, 10));
	}

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
	public function put($key, $value, $minutes)
	{
		$value = $this->expiration($minutes).serialize($value);

		file_put_contents($this->path.$key, $value, LOCK_EX);
	}

	/**
	 * Write an item to the cache for five years.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		return $this->put($key, $value, 2628000);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		if (file_exists($this->path.$key)) @unlink($this->path.$key);
	}

}