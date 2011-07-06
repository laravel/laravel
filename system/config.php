<?php namespace System;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * @var array
	 */
	private static $items = array();

	/**
	 * Determine if a configuration item exists.
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
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		// If no "dot" is present in the key, return the entire configuration array.
		if(strpos($key, '.') === false)
		{
			static::load($key);

			return Arr::get(static::$items, $key, $default);
		}

		list($file, $key) = static::parse($key);

		static::load($file);

		// Verify that the configuration file actually exists.
		if ( ! array_key_exists($file, static::$items))
		{
			return $default;
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
	 * @param  string  $key
	 * @return array
	 */
	private static function parse($key)
	{
		// The left side of the dot is the file name, while the right side of the dot
		// is the item within that file being requested.

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
		// Bail out if already loaded or doesn't exist.
		if (array_key_exists($file, static::$items) or ! file_exists($path = APP_PATH.'config/'.$file.EXT))
		{
			return;
		}

		static::$items[$file] = require $path;
	}

}