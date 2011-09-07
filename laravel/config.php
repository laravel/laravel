<?php namespace Laravel;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by file names.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * The paths containing the configuration files.
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Create a new configuration manager instance.
	 *
	 * @param  array  $paths
	 * @return void
	 */
	public function __construct($paths)
	{
		$this->paths = $paths;
	}

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ! is_null($this->get($key));
	}

	/**
	 * Get a configuration item.
	 *
	 * If the name of a configuration file is passed without specifying an item, the
	 * entire configuration array will be returned.
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return array
	 */
	public function get($key, $default = null)
	{
		list($file, $key) = $this->parse($key);

		if ( ! $this->load($file))
		{
			return ($default instanceof \Closure) ? call_user_func($default) : $default;
		}

		if (is_null($key)) return $this->items[$file];

		return Arr::get($this->items[$file], $key, $default);
	}

	/**
	 * Set a configuration item.
	 *
	 * If a specific configuration item is not specified, the entire configuration
	 * array will be replaced with the given value.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value)
	{
		list($file, $key) = $this->parse($key);

		$this->load($file);

		(is_null($key)) ? Arr::set($this->items, $file, $value) : Arr::set($this->items[$file], $key, $value);
	}

	/**
	 * Parse a configuration key and return its file and key segments.
	 *
	 * Configuration keys follow a {file}.{key} convention.
	 *
	 * @param  string  $key
	 * @return array
	 */
	private function parse($key)
	{
		$segments = explode('.', $key);

		return array($segments[0], (count($segments) > 1) ? implode('.', array_slice($segments, 1)) : null);
	}

	/**
	 * Load all of the configuration items from a module configuration file.
	 *
	 * If the configuration file has already been loaded, it will not be loaded again.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	protected function load($file)
	{
		if (isset($this->items[$file])) return true;

		$config = array();

		foreach ($this->paths as $directory)
		{
			$config = (file_exists($path = $directory.$file.EXT)) ? array_merge($config, require $path) : $config;
		}

		if (count($config) > 0) $this->items[$file] = $config;

		return isset($this->items[$file]);
	}

}