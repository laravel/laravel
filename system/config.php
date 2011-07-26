<?php namespace System;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * @var array
	 */
	private static $items = array();

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

		static::load($file);

		if ( ! array_key_exists($file, static::$items))
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
	 * @param  string  $file
	 * @return void
	 */
	public static function load($file)
	{
		$directory = (isset($_SERVER['LARAVEL_ENV'])) ? $_SERVER['LARAVEL_ENV'].'/' : '';

		if ( ! array_key_exists($file, static::$items) and file_exists($path = APP_PATH.'config/'.$directory.$file.EXT))
		{
			static::$items[$file] = require $path;
		}
	}

}