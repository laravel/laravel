<?php namespace Laravel\Cache\Drivers;

class Memcached extends Driver implements Sectionable {

	/**
	 * The Memcache instance.
	 *
	 * @var Memcached
	 */
	public $memcache;

	/**
	 * The cache key from the cache configuration file.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Indicates that section caching is implicit based on keys.
	 *
	 * @var bool
	 */
	public $implicit = true;

	/**
	 * The implicit section key delimiter.
	 *
	 * @var string
	 */
	public $delimiter = '::';

	/**
	 * Create a new Memcached cache driver instance.
	 *
	 * @param  Memcached  $memcache
	 * @return void
	 */
	public function __construct(\Memcached $memcache, $key)
	{
		$this->key = $key;
		$this->memcache = $memcache;
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
		if ($this->sectionable($key))
		{
			list($section, $key) = $this->parse($key);

			return $this->get_from_section($section, $key);
		}
		elseif (($cache = $this->memcache->get($this->key.$key)) !== false)
		{
			return $cache;
		}
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
		return $this->get($this->section_item_key($section, $key), $default);
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
			$this->memcache->set($this->key.$key, $value, $minutes * 60);
		}
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
		if ($this->sectionable($key))
		{
			list($section, $key) = $this->parse($key);

			return $this->forever_in_section($section, $key, $value);
		}
		else
		{
			return $this->put($key, $value, 0);
		}
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
		return $this->forever($this->section_item_key($section, $key), $value);
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
			$this->memcache->delete($this->key.$key);
		}
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
		return $this->forget($this->section_item_key($section, $key));
	}

	/**
	 * Delete an entire section from the cache.
	 *
	 * @param  string    $section
	 * @return int|bool
	 */
	public function forget_section($section)
	{
		return $this->memcache->increment($this->key.$this->section_key($section));
	}

	/**
	 * Get the current section ID for a given section.
	 *
	 * @param  string  $section
	 * @return int
	 */
	protected function section_id($section)
	{
		return $this->sear($this->section_key($section), function()
		{
			return rand(1, 10000);
		});
	}

	/**
	 * Get a section key name for a given section.
	 *
	 * @param  string  $section
	 * @return string
	 */
	protected function section_key($section)
	{
		return $section.'_section_key';
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
		return $section.'#'.$this->section_id($section).'#'.$key;
	}

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