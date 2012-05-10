<?php namespace Laravel\Cache\Drivers;

class Memory extends Sectionable {

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
		if ($this->sectionable($key))
		{
			list($section, $key) = $this->parse($key);

			return $this->get_from_section($section, $key);
		}
		else
		{
			return array_get($this->storage, $key);
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
		if ($this->sectionable($key))
		{
			list($section, $key) = $this->parse($key);

			return $this->put_in_section($section, $key, $value, $minutes);
		}
		else
		{
			array_set($this->storage, $key, $value);
		}
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
		if ($this->sectionable($key))
		{
			list($section, $key) = $this->parse($key);

			return $this->forever_in_section($section, $key, $value);
		}
		else
		{
			$this->put($key, $value, 0);
		}
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		if ($this->sectionable($key))
		{
			list($section, $key) = $this->parse($key);

			if ($key == '*')
			{
				$this->forget_section($section);
			}
			else
			{
				$this->forget_in_section($section, $key);
			}
		}
		else
		{
			array_forget($this->storage, $key);
		}
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