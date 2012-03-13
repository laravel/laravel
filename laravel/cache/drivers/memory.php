<?php namespace Laravel\Cache\Drivers;

class Memory extends Driver {

	/**
	 * The in-memory array of cached items.
	 *
	 * @var string
	 */
	protected $storage = array();

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
		if (array_key_exists($key, $this->storage))
		{
			return $this->storage[$key];
		}
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
		$this->storage[$key] = $value;
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		unset($this->storage[$key]);
	}

}