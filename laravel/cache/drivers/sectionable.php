<?php namespace Laravel\Cache\Drivers;

abstract class Sectionable extends Driver {

	/**
	 * Retrieve a sectioned item from the cache driver.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	abstract public function get_from_section($section, $key, $default = null);

	/**
	 * Write a sectioned item to the cache.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	abstract public function put_in_section($section, $key, $value, $minutes);

	/**
	 * Write a sectioned item to the cache that lasts forever.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	abstract public function forever_in_section($section, $key, $value);

	/**
	 * Get a sectioned item from the cache, or cache and return the default value.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @return mixed
	 */
	abstract public function remember_in_section($section, $key, $default, $minutes, $function = 'put');

	/**
	 * Get a sectioned item from the cache, or cache the default value forever.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	abstract public function sear_in_section($section, $key, $default);

	/**
	 * Delete a sectioned item from the cache.
	 *
	 * @param  string  $section
	 * @param  string  $key
	 * @return void
	 */
	abstract public function forget_in_section($section, $key);

	/**
	 * Delete an entire section from the cache.
	 *
	 * @param  string    $section
	 * @return int|bool
	 */
	abstract public function forget_section($section);

	/**
	 * Indicates if a key is sectionable.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function sectionable($key)
	{
		return $this->implicit and $this->sectioned($key);
	}

	/**
	 * Determine if a key is sectioned.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function sectioned($key)
	{
		return str_contains($key, '::');
	}

	/**
	 * Get the section and key from a sectioned key.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function parse($key)
	{
		return explode('::', $key, 2);
	}

}