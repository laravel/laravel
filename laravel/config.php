<?php namespace Laravel;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by their owning file name.
	 *
	 * @var array
	 */
	protected static $items = array();

	/**
	 * The paths to the configuration files.
	 *
	 * @var array
	 */
	protected static $paths = array();

	/**
	 * Set the paths in which the configuration files are located.
	 *
	 * @param  array  $paths
	 * @return void
	 */
	public static function paths($paths)
	{
		static::$paths = $paths;
	}

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return ! is_null(static::get($key));
	}

	/**
	 * Get a configuration item.
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return array
	 */
	public static function get($key, $default = null)
	{
		list($file, $key) = static::parse($key);

		if ( ! static::load($file))
		{
			return ($default instanceof \Closure) ? call_user_func($default) : $default;
		}

		if (is_null($key)) return static::$items[$file];

		return Arr::get(static::$items[$file], $key, $default);
	}

	/**
	 * Set a configuration item.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set($key, $value)
	{
		list($file, $key) = static::parse($key);

		static::load($file);

		(is_null($key)) ? Arr::set(static::$items, $file, $value) : Arr::set(static::$items[$file], $key, $value);
	}

	/**
	 * Parse a configuration key and return its file and key segments.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected static function parse($key)
	{
		$segments = explode('.', $key);

		$key = (count($segments) > 1) ? implode('.', array_slice($segments, 1)) : null;

		return array($segments[0], $key);
	}

	/**
	 * Load all of the configuration items from a module configuration file.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	protected static function load($file)
	{
		if (isset(static::$items[$file])) return true;

		$config = array();

		foreach (static::$paths as $directory)
		{
			if (file_exists($path = $directory.$file.EXT))
			{
				$config = array_merge($config, require $path);
			}
		}

		if (count($config) > 0)
		{
			static::$items[$file] = $config;
		}

		return isset(static::$items[$file]);
	}

}