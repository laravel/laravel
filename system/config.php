<?php namespace System;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * @var array
	 */
	private static $items = array();

	/**
	 * Get a configuration item.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public static function get($key)
	{
		// ---------------------------------------------
		// Parse the configuration key.
		// ---------------------------------------------
		list($file, $key) = static::parse($key);

		// ---------------------------------------------
		// Load the configuration file.
		// ---------------------------------------------
		static::load($file);

		// ---------------------------------------------
		// Return the requested item.
		// ---------------------------------------------
		return (array_key_exists($key, static::$items[$file])) ? static::$items[$file][$key] : null;
	}

	/**
	 * Set a configuration item.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set($file, $value)
	{
		// ---------------------------------------------
		// Parse the configuration key.
		// ---------------------------------------------
		list($file, $key) = static::parse($key);

		// ---------------------------------------------
		// Load the configuration file.
		// ---------------------------------------------
		static::load($file);

		// ---------------------------------------------
		// Set the item's value.
		// ---------------------------------------------
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
		// ---------------------------------------------
		// Get the key segments.
		// ---------------------------------------------
		$segments = explode('.', $key);

		// ---------------------------------------------
		// Validate the key format.
		// ---------------------------------------------
		if (count($segments) < 2)
		{
			throw new \Exception("Invalid configuration key [$key].");
		}

		// ---------------------------------------------
		// Return the file and item name.
		// ---------------------------------------------
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
		// ---------------------------------------------
		// If the file has already been loaded, bail.
		// ---------------------------------------------
		if (array_key_exists($file, static::$items))
		{
			return;
		}

		// ---------------------------------------------
		// Verify that the configuration file exists.
		// ---------------------------------------------
		if ( ! file_exists($path = APP_PATH.'config/'.$file.EXT))
		{
			throw new \Exception("Configuration file [$file] does not exist.");
		}

		// ---------------------------------------------
		// Load the configuration file.
		// ---------------------------------------------
		static::$items[$file] = require $path;
	}

}