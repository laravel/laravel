<?php namespace Laravel; use Closure;

class Config {

	/**
	 * The paths to the configuration files.
	 *
	 * @var array
	 */
	public static $paths = array(SYS_CONFIG_PATH, CONFIG_PATH);

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by their owning file name.
	 *
	 * @var array
	 */
	public static $items = array();

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * <code>
	 *		// Determine if the "session" configuration file exists
	 *		$exists = Config::has('session');
	 *
	 *		// Determine if the "timezone" option exists in the "application" configuration array
	 *		$exists = Config::has('application.timezone');
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
	 * If no item is requested, the entire configuration array will be returned.
	 *
	 * <code>
	 *		// Get the "session" configuration array
	 *		$session = Config::get('session');
	 *
	 *		// Get the "timezone" option from the "application" configuration file
	 *		$timezone = Config::get('application.timezone');
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
			return ($default instanceof Closure) ? call_user_func($default) : $default;
		}

		if (is_null($key)) return static::$items[$file];

		return Arr::get(static::$items[$file], $key, $default);
	}

	/**
	 * Set a configuration item's value.
	 *
	 * <code>
	 *		// Set the "session" configuration array
	 *		Config::set('session', $array);
	 *
	 *		// Set the "timezone" option in the "application" configuration file
	 *		Config::set('application.timezone', 'UTC');
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
	 * @param  string  $key
	 * @return array
	 */
	protected static function parse($key)
	{
		$segments = explode('.', $key);

		// If there is only one segment after exploding on dots, we will return NULL
		// as the key value, causing the entire configuration array to be returned.
		$key = (count($segments) > 1) ? implode('.', array_slice($segments, 1)) : null;

		return array($segments[0], $key);
	}

	/**
	 * Load all of the configuration items from a configuration file.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	protected static function load($file)
	{
		if (isset(static::$items[$file])) return true;

		$config = array();

		// Configuration files cascade. Typically, the system configuration array is loaded
		// first, followed by the application array, providing the convenient cascading
		// of configuration options from system to application.
		foreach (static::$paths as $directory)
		{
			if (file_exists($path = $directory.$file.EXT))
			{
				$config = array_merge($config, require $path);
			}
		}

		if (count($config) > 0) static::$items[$file] = $config;

		return isset(static::$items[$file]);
	}

	/**
	 * Add a directory to the configuration manager's search paths.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public static function glance($path)
	{
		static::$paths[] = $path;
	}

}