<?php namespace Laravel;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by their owning file name.
	 *
	 * @var array
	 */
	protected static $items = array();

	/**
	 * The paths to the configuration files.
	 *
	 * @var array
	 */
	protected static $paths = array();

	/**
	 * Set the paths in which the configuration files are located.
	 *
	 * @param  array  $paths
	 * @return void
	 */
	public static function paths($paths)
	{
		static::$paths = $paths;
	}

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * <code>
	 *		// Determine if the "options" configuration file exists
	 *		$options = Config::has('options');
	 *
	 *		// Determine if a specific configuration item exists
	 *		$timezone = Config::has('application.timezone');
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
	 * Configuration items are stored in the application/config directory, and provide
	 * general configuration options for a wide range of Laravel facilities.
	 *
	 * The arrays may be accessed using JavaScript style "dot" notation to drill deep
	 * intot he configuration files. For example, asking for "database.connectors.sqlite"
	 * would return the connector closure for SQLite stored in the database configuration
	 * file. If no specific item is specfied, the entire configuration array is returned.
	 *
	 * Like most Laravel "get" functions, a default value may be provided, and it will
	 * be returned if the requested file or item doesn't exist.
	 *
	 * <code>
	 *		// Get the "timezone" option from the application config file
	 *		$timezone = Config::get('application.timezone');
	 *
	 *		// Get an option, but return a default value if it doesn't exist
	 *		$value = Config::get('some.option', 'Default');
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

		if (is_null($key))
		{
			return static::$items[$file];
		}

		return Arr::get(static::$items[$file], $key, $default);
	}

	/**
	 * Set a configuration item.
	 *
	 * Configuration items are stored in the application/config directory, and provide
	 * general configuration options for a wide range of Laravel facilities.
	 *
	 * Like the "get" method, this method uses JavaScript style "dot" notation to access
	 * and manipulate the arrays in the configuration files. Also, like the "get" method,
	 * if no specific item is specified, the entire configuration array will be set.
	 *
	 * <code>
	 *		// Set the "timezone" option in the "application" array
	 *		Config::set('application.timezone', 'America/Chicago');
	 *
	 *		// Set the entire "session" configuration array
	 *		Config::set('session', $array);
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

		if (is_null($key))
		{
			Arr::set(static::$items, $file, $value);
		}
		else
		{
			Arr::set(static::$items[$file], $key, $value);
		}
	}

	/**
	 * Parse a configuration key and return its file and key segments.
	 *
	 * Configuration keys follow a {file}.{key} convention. So, for example, the
	 * "session.driver" option refers to the "driver" option within the "session"
	 * configuration file.
	 *
	 * If no specific item is specified, such as when requested "session", null will
	 * be returned as the value of the key since the entire file is being requested.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected static function parse($key)
	{
		$segments = explode('.', $key);

		$key = (count($segments) > 1) ? implode('.', array_slice($segments, 1)) : null;

		return array($segments[0], $key);
	}

	/**
	 * Load all of the configuration items from a module configuration file.
	 *
	 * If the configuration file has already been loaded into the items array, there
	 * is no need to load it again, so "true" will be returned immediately.
	 *
	 * Configuration files cascade across directories. So, for example, if a configuration
	 * file is in the system directory, its options will be overriden by a matching file
	 * in the application directory.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	protected static function load($file)
	{
		if (isset(static::$items[$file])) return true;

		$config = array();

		foreach (static::$paths as $directory)
		{
			if (file_exists($path = $directory.$file.EXT))
			{
				$config = array_merge($config, require $path);
			}
		}

		if (count($config) > 0)
		{
			static::$items[$file] = $config;
		}

		return isset(static::$items[$file]);
	}

}