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
		if (array_key_exists($file, static::$items))
		{
			return;
		}

		if ( ! file_exists($path = APP_PATH.'config/'.$file.EXT))
		{
			throw new \Exception("Configuration file [$file] does not exist.");
		}

		static::$items[$file] = require $path;
	}

}