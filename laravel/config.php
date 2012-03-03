<?php namespace Laravel; defined('DS') or die('No direct script access.');

use Closure;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by their owning bundle and file.
	 *
	 * @var array
	 */
	public static $items = array();

	/**
	 * A cache of the parsed configuration items.
	 *
	 * @var array
	 */
	public static $cache = array();

	/**
	 * The configuration loader event name.
	 *
	 * @var string
	 */
	const loader = 'laravel.config.loader';

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * <code>
	 *		// Determine if the "session" configuration file exists
	 *		$exists = Config::has('session');
	 *
	 *		// Determine if the "timezone" option exists in the configuration
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
	 *		// Get a configuration item from a bundle's configuration file
	 *		$name = Config::get('admin::names.first');
	 *
	 *		// Get the "timezone" option from the "application" configuration file
	 *		$timezone = Config::get('application.timezone');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function get($key, $default = null)
	{
		list($bundle, $file, $item) = static::parse($key);

		if ( ! static::load($bundle, $file)) return value($default);

		$items = static::$items[$bundle][$file];

		// If a specific configuration item was not requested, the key will be null,
		// meaning we need to return the entire array of configuration item from the
		// requested configuration file. Otherwise we can return the item.
		if (is_null($item))
		{
			return $items;
		}
		else
		{
			return array_get($items, $item, $default);
		}
	}

	/**
	 * Set a configuration item's value.
	 *
	 * <code>
	 *		// Set the "session" configuration array
	 *		Config::set('session', $array);
	 *
	 *		// Set a configuration option that belongs by a bundle
	 *		Config::set('admin::names.first', 'Taylor');
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
		list($bundle, $file, $item) = static::parse($key);

		static::load($bundle, $file);

		// If the item is null, it means the developer wishes to set the entire
		// configuration array to a given value, so we will pass the entire
		// array for the bundle into the array_set method.
		if (is_null($item))
		{
			array_set(static::$items[$bundle], $file, $value);
		}
		else
		{
			array_set(static::$items[$bundle][$file], $item, $value);
		}
	}

	/**
	 * Parse a key and return its bundle, file, and key segments.
	 *
	 * Configuration items are named using the {bundle}::{file}.{item} convention.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected static function parse($key)
	{
		// First, we'll check the keyed cache of configuration items, as this will
		// be the fastest method of retrieving the configuration option. After an
		// item is parsed, it is always stored in the cache by its key.
		if (array_key_exists($key, static::$cache))
		{
			return static::$cache[$key];
		}

		$bundle = Bundle::name($key);

		$segments = explode('.', Bundle::element($key));

		// If there are not at least two segments in the array, it means that the
		// developer is requesting the entire configuration array to be returned.
		// If that is the case, we'll make the item field "null".
		if (count($segments) >= 2)
		{
			$parsed = array($bundle, $segments[0], implode('.', array_slice($segments, 1)));
		}
		else
		{
			$parsed = array($bundle, $segments[0], null);
		}

		return static::$cache[$key] = $parsed;
	}

	/**
	 * Load all of the configuration items from a configuration file.
	 *
	 * @param  string  $bundle
	 * @param  string  $file
	 * @return bool
	 */
	public static function load($bundle, $file)
	{
		if (isset(static::$items[$bundle][$file])) return true;

		// We allow a "config.loader" event to be registered which is responsible for
		// returning an array representing the configuration for the bundle and file
		// requested. This allows many types of config "drivers".
		$config = Event::first(static::loader, func_get_args());

		// If configuration items were actually found for the bundle and file we
		// will add them to the configuration array and return true, otherwise
		// we will return false indicating the file was not found.
		if (count($config) > 0)
		{
			static::$items[$bundle][$file] = $config;
		}

		return isset(static::$items[$bundle][$file]);
	}

	/**
	 * Load the configuration items from a configuration file.
	 *
	 * @param  string  $bundle
	 * @param  string  $file
	 * @return array
	 */
	public static function file($bundle, $file)
	{
		$config = array();

		// Configuration files cascade. Typically, the bundle configuration array is
		// loaded first, followed by the environment array, providing the convenient
		// cascading of configuration options across environments.
		foreach (static::paths($bundle) as $directory)
		{
			if ($directory !== '' and file_exists($path = $directory.$file.EXT))
			{
				$config = array_merge($config, require $path);
			}
		}

		return $config;
	}

	/**
	 * Get the array of configuration paths that should be searched for a bundle.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	protected static function paths($bundle)
	{
		$paths[] = Bundle::path($bundle).'config/';

		// Configuration files can be made specific for a given environment. If an
		// environment has been set, we will merge the environment configuration
		// in last, so that it overrides all other options.
		if (isset($_SERVER['LARAVEL_ENV']))
		{
			$paths[] = $paths[count($paths) - 1].$_SERVER['LARAVEL_ENV'].'/';
		}

		return $paths;
	}

}