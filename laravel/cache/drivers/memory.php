<?php namespace Laravel\Cache\Drivers;

class Memory extends Driver implements Sectionable {

	/**
	 * The in-memory array of cached items.
	 *
	 * @var string
	 */
	public $storage = array();

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
		return array_get($this->storage, $key);
	}

	/**
	 * Retrieve a sectioned item from the cache driver.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get_from_section($section, $key, $default = null)
	{
		$key = $this->section_item_key($section, $key);

		return array_get($this->storage, $key, $default);
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
		array_set($this->storage, $key, $value);
	}

	/**
	 * Write a sectioned item to the cache.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put_in_section($section, $key, $value, $minutes)
	{
		$this->put($this->section_item_key($section, $key), $value, $minutes);
	}

	/**
	 * Write an item to the cache that lasts forever.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		$this->put($key, $value, 0);
	}

	/**
	 * Write a sectioned item to the cache that lasts forever.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever_in_section($section, $key, $value)
	{
		$this->put_in_section($section, $key, $value, 0);
	}

	/**
	 * Get a sectioned item from the cache, or cache and return the default value.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @return mixed
	 */
	public function remember_in_section($section, $key, $default, $minutes, $function = 'put')
	{
		$key = $this->section_item_key($section, $key);

		return $this->remember($key, $default, $minutes, $function);
	}

	/**
	 * Get a sectioned item from the cache, or cache the default value forever.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function sear_in_section($section, $key, $default)
	{
		return $this->sear($this->section_item_key($section, $key), $default);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		array_forget($this->storage, $key);
	}

	/**
	 * Delete a sectioned item from the cache.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @return void
	 */
	public function forget_in_section($section, $key)
	{
		$this->forget($this->section_item_key($section, $key));
	}

	/**
	 * Delete an entire section from the cache.
	 *
	 * @param  string    $section
	 * @return int|bool
	 */
	public function forget_section($section)
	{
		array_forget($this->storage, 'section#'.$section);
	}

	/**
	 * Flush the entire cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->storage = array();
	}

	/**
	 * Get a section item key for a given section and key.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @return string
	 */
	protected function section_item_key($section, $key)
	{
		return "section#{$section}.{$key}";
	}

}