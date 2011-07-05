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
		// -----------------------------------------------------
		// If no dot is in the key, we will just return the
		// entire configuration array.
		// -----------------------------------------------------
		if(strpos($key, '.') === false)
		{
			static::load($key);

			return (array_key_exists($key, static::$items)) ? static::$items[$key] : $default;
		}

		list($file, $key) = static::parse($key);

		static::load($file);

		// -----------------------------------------------------
		// If the file doesn't exist, return the default.
		// -----------------------------------------------------
		if ( ! array_key_exists($file, static::$items))
		{
			return $default;
		}

		// -----------------------------------------------------
		// Return the configuration item. If the item doesn't
		// exist, the default value will be returned.
		// -----------------------------------------------------
		return (array_key_exists($key, static::$items[$file])) ? static::$items[$file][$key] : $default;
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
		// -----------------------------------------------------
		// The left side of the dot is the file name, while
		// the right side of the dot is the item within that
		// file being requested.
		// -----------------------------------------------------
		$segments = explode('.', $key);

		if (count($segments) < 2)
		{
			throw new \Exception("Invalid configuration key [$key].");
		}

		return array($segments[0], implode('.', array_slice($segments, 1)));
	}

	/**
	 * Load all of the configuration items.
	 *
	 * @param  string  $file
	 * @return void
	 */
	public static function load($file)
	{
		// -----------------------------------------------------
		// Bail out if already loaded or doesn't exist.
		// -----------------------------------------------------
		if (array_key_exists($file, static::$items) or ! file_exists($path = APP_PATH.'config/'.$file.EXT))
		{
			return;
		}

		// -----------------------------------------------------
		// Load the configuration array into the array of items.
		// The items array is keyed by filename.
		// -----------------------------------------------------
		static::$items[$file] = require $path;
	}

}