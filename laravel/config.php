<?php namespace Laravel;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by file names.
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
	 *		$sqlite = Config::get('database.connections.sqlite');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return array
	 */
	public static function get($key, $default = null)
	{
		list($file, $key) = static::parse($key);

		if ( ! static::load($file))
		{
			return ($default instanceof \Closure) ? call_user_func($default) : $default;
		}

		if (is_null($key)) return static::$items[$file];

		return Arr::get(static::$items[$file], $key, $default);
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
		list($file, $key) = static::parse($key);

		static::load($file);

		(is_null($key)) ? Arr::set(static::$items, $file, $value) : Arr::set(static::$items[$file], $key, $value);
	}

	/**
	 * Parse a configuration key and return its file and key segments.
	 *
	 * Configuration keys follow a {file}.{key} convention.
	 *
	 * @param  string  $key
	 * @return array
	 */
	private static function parse($key)
	{
		$segments = explode('.', $key);

		return array($segments[0], (count($segments) > 1) ? implode('.', array_slice($segments, 1)) : null);
	}

	/**
	 * Load all of the configuration items from a module configuration file.
	 *
	 * If the configuration file has already been loaded, it will not be loaded again.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	private static function load($file)
	{
		if (isset(static::$items[$file])) return true;

		$config = array();

		foreach (static::paths() as $directory)
		{
			$config = (file_exists($path = $directory.$file.EXT)) ? array_merge($config, require $path) : $config;
		}

		if (count($config) > 0)
		{
			static::$items[$file] = $config;
		}

		return isset(static::$items[$file]);
	}

	/**
	 * Get the path hierarchy for a given configuration file and module.
	 *
	 * The paths returned by this method paths will be searched by the load method when merging
	 * configuration files, meaning the configuration files will cascade in this order.
	 *
	 * The system configuration directory will be searched first, followed by the application
	 * directory, and finally the environment directory.
	 *
	 * @return array
	 */
	private static function paths()
	{
		$paths = array(SYS_CONFIG_PATH, CONFIG_PATH);

		if (isset($_SERVER['LARAVEL_ENV']))
		{
			$paths[] = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/';
		}

		return $paths;
	}

}