<?php namespace Laravel\Cache;

class File extends Driver {

	/**
	 * The file engine instance.
	 *
	 * @var Laravel\File
	 */
	private $file;

	/**
	 * The path to which the cache files should be written.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Create a new File cache driver instance.
	 *
	 * @param  Laravel\File  $file
	 * @param  string        $path
	 * @return void
	 */
	public function __construct(\Laravel\File $file, $path)
	{
		$this->file = $file;
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
		if ( ! $this->file->exists($this->path.$key)) return null;

		if (time() >= substr($cache = $this->file->get($this->path.$key), 0, 10))
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
		$this->file->put($this->path.$key, (time() + ($minutes * 60)).serialize($value));
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->file->delete($this->path.$key);
	}

}