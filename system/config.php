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
		list($file, $key) = static::parse($key);

		static::load($file);

		if (array_key_exists($key, static::$items[$file]))
		{
			return static::$items[$file][$key];
		}

		throw new \Exception("Configuration item [$key] is not defined.");
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
		//
		// This syntax allows for the easy retrieval and setting
		// of configuration items.
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
		// If we have already loaded the file, bail out.
		// -----------------------------------------------------
		if (array_key_exists($file, static::$items))
		{
			return;
		}

		if ( ! file_exists($path = APP_PATH.'config/'.$file.EXT))
		{
			throw new \Exception("Configuration file [$file] does not exist.");
		}

		// -----------------------------------------------------
		// Load the configuration array into the array of items.
		// The items array is keyed by filename.
		// -----------------------------------------------------
		static::$items[$file] = require $path;
	}

}