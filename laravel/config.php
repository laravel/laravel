<?php namespace Laravel;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by module and file names.
	 *
	 * @var array
	 */
	public static $items = array();

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * <code>
	 *		// Determine if the "session" configuration file exists
	 *		Config::has('session');
	 *
	 *		// Determine if the application timezone option exists
	 *		Config::has('application.timezone');
	 * </code>
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
	 * <code>
	 *		// Get the timezone option from the application configuration file
	 *		$timezone = Config::get('application.timezone');
	 *
	 * 		// Get the SQLite database connection configuration
	 *		$sqlite = Config::get('db.connections.sqlite');
	 *
	 *		// Get a configuration item from a module configuration file
	 *		$option = Config::get('module::file.option');
	 * </code>
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
	 * Like the get method, "dot" notation is used to set items, and setting items
	 * at any depth in the configuration array is supported.
	 *
	 * If a specific configuration item is not specified, the entire configuration
	 * array will be replaced with the given value.
	 *
	 * <code>
	 *		// Set the timezone option in the application configuration file
	 *		Config::set('application.timezone', 'America/Chicago');
	 *
	 *		// Set the session configuration array
	 *		Config::set('session', array());
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set($key, $value)
	{
		list($module, $file, $key) = static::parse($key);

		if ( ! static::load($module, $file))
		{
			throw new \Exception("Error setting configuration option. Configuration file [$file] is not defined.");
		}

		Arr::set(static::$items[$module][$file], $key, $value);
	}

	/**
	 * Parse a configuration key and return its module, file, and key segments.
	 *
	 * Modular configuration keys follow a {module}::{file}.{key} convention.
	 *
	 * @param  string  $key
	 * @return array
	 */
	private static function parse($key)
	{
		list($module, $key) = Module::parse($key);

		$segments = explode('.', $key);

		return array($module, $segments[0], (count($segments) > 1) ? implode('.', array_slice($segments, 1)) : null);
	}

	/**
	 * Load all of the configuration items from a module configuration file.
	 *
	 * If the configuration file has already been loaded, it will not be loaded again.
	 *
	 * @param  string  $file
	 * @param  string  $module
	 * @return bool
	 */
	private static function load($module, $file)
	{
		if (isset(static::$items[$module]) and array_key_exists($file, static::$items[$module])) return true;

		$config = array();

		foreach (static::paths($module, $file) as $directory)
		{
			$config = (file_exists($path = $directory.$file.EXT)) ? array_merge($config, require $path) : $config;
		}

		if (count($config) > 0) static::$items[$module][$file] = $config;

		return isset(static::$items[$module][$file]);
	}

	/**
	 * Get the path hierarchy for a given configuration file and module.
	 *
	 * The paths returned by this method paths will be searched by the load method when merging
	 * configuration files, meaning the configuration files will cascade in this order.
	 *
	 * By default, the base configuration directory will be searched first, followed by the configuration
	 * directory for the active module. Next, any environment specific configuration directories
	 * will be searched.
	 *
	 * @param  string  $module
	 * @param  string  $file
	 * @return array
	 */
	private static function paths($module, $file)
	{
		$module = str_replace('.', '/', $module);

		$paths = array(CONFIG_PATH, Module::path($module).'config/');

		if (isset($_SERVER['LARAVEL_ENV']))
		{
			$paths[] = Module::path($module).'/config/'.$_SERVER['LARAVEL_ENV'].'/';
		}

		return $paths;
	}

}