<?php namespace Laravel\Cache\Drivers; use Laravel\File as F;

class File extends Driver {

	/**
	 * The path to which the cache files should be written.
	 *
	 * @var string
	 */
	private $path;

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
		if ( ! F::exists($this->path.$key)) return null;

		if (time() >= substr($cache = F::get($this->path.$key), 0, 10))
		{
			return $this->forget($key);
		}

		return unserialize(substr($cache, 10));
	}

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		// The expiration time is stored as a UNIX timestamp at the beginning of the file.
		F::put($this->path.$key, (time() + ($minutes * 60)).serialize($value));
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		F::delete($this->path.$key);
	}

}