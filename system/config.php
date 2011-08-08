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
		list($module, $file, $key) = static::parse($key);

		if ( ! static::load($module, $file))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

		if (is_null($key)) return static::$items[$module][$file];

		return Arr::get(static::$items[$module][$file], $key, $default);
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
		list($module, $file, $key) = static::parse($key);

		if (is_null($key) or ! static::load($module, $file))
		{
			throw new \Exception("Error setting configuration option. Option [$key] is not defined.");
		}

		static::$items[$module][$file][$key] = $value;
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
	 * @return bool
	 */
	public static function load($module, $file)
	{
		if (isset(static::$items[$module]) and array_key_exists($file, static::$items[$module])) return true;

		$path = ($module === 'application') ? CONFIG_PATH : MODULE_PATH.$module.'/config/';

		// Load the base configuration items from the application directory.
		$config = (file_exists($base = $path.$file.EXT)) ? require $base : array();

		// Merge any environment specific configuration into the base array.
		if (isset($_SERVER['LARAVEL_ENV']) and file_exists($path = $path.$_SERVER['LARAVEL_ENV'].'/'.$file.EXT))
		{
			$config = array_merge($config, require $path);
		}

		if (count($config) > 0) static::$items[$module][$file] = $config;

		return isset(static::$items[$module][$file]);
	}

}