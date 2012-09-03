<?php namespace Laravel\Cache\Drivers;

class Memcached extends Sectionable {

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
	 * Create a new Memcached cache driver instance.
	 *
	 * @param  Memcached  $memcache
	 * @param  string     $key
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

}