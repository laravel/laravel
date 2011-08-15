<?php namespace System;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration item arrays are keyed by module and file.
	 *
	 * @var array
	 */
	public static $items = array();

	/**
	 * Determine if a configuration file or item exists.
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
		list($module, $file, $key) = static::parse($key);

		static::load($module, $file);

		if (is_null($key)) return static::$items[$module][$file];

		return Arr::get(static::$items[$module][$file], $key, $default);
	}

	/**
	 * Set a configuration item.
	 *
	 * If a configuration item is not specified, the entire configuration array will be set.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set($key, $value)
	{
		list($module, $file, $key) = static::parse($key);

		static::load($module, $file);

		Arr::set(static::$items[$module][$file], $key, $value);
	}

	/**
	 * Parse a configuration key into its module, file, and key segments.
	 *
	 * @param  string  $key
	 * @return array
	 */
	private static function parse($key)
	{
		$module = (strpos($key, '::') !== false) ? substr($key, 0, strpos($key, ':')) : 'application';

		if ($module !== 'application')
		{
			$key = substr($key, strpos($key, ':') + 2);
		}

		$key = (count($segments = explode('.', $key)) > 1) ? implode('.', array_slice($segments, 1)) : null;

		return array($module, $segments[0], $key);
	}

	/**
	 * Load all of the configuration items from a file.
	 *
	 * @param  string  $file
	 * @param  string  $module
	 * @return void
	 */
	private static function load($module, $file)
	{
		if (isset(static::$items[$module][$file])) return true;

		$path = ($module === 'application') ? CONFIG_PATH : MODULE_PATH.$module.'/config/';

		// Load the base configuration file. Once that is loaded, we will merge any environment
		// specific configuration options into the base array. This allows for the convenient
		// cascading of configuration options depending on the application environment.
		$config = (file_exists($base = $path.$file.EXT)) ? require $base : array();

		if (isset($_SERVER['LARAVEL_ENV']) and file_exists($path = $path.$_SERVER['LARAVEL_ENV'].'/'.$file.EXT))
		{
			$config = array_merge($config, require $path);
		}

		static::$items[$module][$file] = $config;
	}

}