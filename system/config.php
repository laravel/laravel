<?php namespace System;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * @var array
	 */
	public static $items = array();

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
	 * Configuration items are retrieved using "dot" notation. So, asking for the
	 * "application.timezone" configuration item would return the "timezone" option
	 * from the "application" configuration file.
	 *
	 * If the name of a configuration file is passed without specifying an item, the
	 * entire configuration array will be returned.
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return array
	 */
	public static function get($key, $default = null)
	{
		if (strpos($key, '.') === false)
		{
			static::load($key);

			return Arr::get(static::$items, $key, $default);
		}

		list($file, $key) = static::parse($key);

		if ( ! static::load($file))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

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

		static::$items[$file][$key] = $value;
	}

	/**
	 * Parse a configuration key.
	 *
	 * The value on the left side of the dot is the configuration file
	 * name, while the right side of the dot is the item within that file.
	 *
	 * @param  string  $key
	 * @return array
	 */
	private static function parse($key)
	{
		$segments = explode('.', $key);

		if (count($segments) < 2)
		{
			throw new \Exception("Invalid configuration key [$key].");
		}

		return array($segments[0], implode('.', array_slice($segments, 1)));
	}

	/**
	 * Load all of the configuration items from a file.
	 *
	 * If it exists, the configuration file in the application/config directory will be loaded first.
	 * Any environment specific configuration files will be merged with the root file.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	public static function load($file)
	{
		if (array_key_exists($file, static::$items)) return true;

		$config = (file_exists($path = CONFIG_PATH.$file.EXT)) ? require $path : array();

		if (isset($_SERVER['LARAVEL_ENV']) and file_exists($path = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/'.$file.EXT))
		{
			$config = array_merge($config, require $path);
		}

		if (count($config) > 0)
		{
			static::$items[$file] = $config;
		}

		return isset(static::$items[$file]);
	}

}