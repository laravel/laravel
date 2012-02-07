<?php namespace Laravel; defined('DS') or die('No direct script access.');

use FilesystemIterator as fIterator;

class Bundle {

	/**
	 * All of the application's bundles.
	 *
	 * @var array
	 */
	public static $bundles = array();

	/**
	 * A cache of the parsed bundle elements.
	 *
	 * @var array
	 */
	public static $elements = array();

	/**
	 * All of the bundles that have been started.
	 *
	 * @var array
	 */
	public static $started = array();

	/**
	 * All of the bundles that have their routes files loaded.
	 *
	 * @var array
	 */
	public static $routed = array();

	/**
	 * Detect all of the installed bundles from disk.
	 *
	 * @param  string  $path
	 * @return array
	 */
	public static function detect($path)
	{
		return static::search($path);
	}

	/**
	 * Detect all of the installed bundles from disk.
	 *
	 * @param  string  $path
	 * @return array
	 */
	protected static function search($path)
	{
		$bundles = array();

		$items = new fIterator($path);

		foreach ($items as $item)
		{
			// If the item is a directory, we'll search for a bundle.info file.
			// If one exists, we will add it to the bundle array. We will set
			// the location automatically since we know it.
			if ($item->isDir())
			{
				$path = $item->getRealPath().DS.'bundle.info';

				// If we found a file, we'll require in the array it contains
				// and add it to the directory. The info array will contain
				// basic info like the bundle name and any URIs it may
				// handle incoming requests for.
				if (file_exists($path))
				{
					$info = require $path;

					$info['location'] = dirname($path).DS;

					$bundles[$info['name']] = $info;

					continue;
				}
				// If a bundle.info file doesn't exist within a directory,
				// we'll recurse into the directory to keep searching in
				// the bundle directory for nested bundles.
				else
				{
					$recurse = static::detect($item->getRealPath());

					$bundles = array_merge($bundles, $recurse);
				}
			}
		}

		return $bundles;
	}

	/**
	 * Register a bundle for the application.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public static function register($config)
	{
		$defaults = array('handles' => null, 'auto' => false);

		// If a handles clause has been specified, we will cap it with a trailing
		// slash so the bundle is not extra greedy with its routes. Otherwise a
		// bundle that handles "s" would handle all routes beginning with "s".
		if (isset($config['handles']))
		{
			$config['handles'] = str_finish($config['handles'], '/');
		}

		static::$bundles[$config['name']] = array_merge($defaults, $config);
	}

	/**
	 * Disable a bundle for the current request.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public static function disable($bundle)
	{
		unset(static::$bundles[$bundle]);
	}

	/**
	 * Load a bundle by running it's start-up script.
	 *
	 * If the bundle has already been started, no action will be taken.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public static function start($bundle)
	{
		if (static::started($bundle)) return;

		if ( ! static::exists($bundle))
		{
			throw new \Exception("Bundle [$bundle] has not been installed.");
		}

		// Each bundle may have a "start" script which is responsible for preparing
		// the bundle for use by the application. The start script may register any
		// classes the bundle uses with the auto-loader, or perhaps will start any
		// dependent bundles so that they are available.
		if (file_exists($path = static::path($bundle).'bundle'.EXT))
		{
			require $path;
		}

		// Each bundle may also have a "routes" file which is responsible for
		// registering the bundle's routes. This is kept separate from the
		// start script for reverse routing efficiency purposes.
		static::routes($bundle);

		Event::fire("started: {$bundle}");

		static::$started[] = strtolower($bundle);
	}

	/**
	 * Load the "routes" file for a given bundle.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public static function routes($bundle)
	{
		$path = static::path($bundle).'routes'.EXT;

		if ( ! static::routed($bundle) and file_exists($path))
		{
			require $path;
		}

		static::$routed[] = $bundle;
	}

	/**
	 * Determine which bundle handles the given URI.
	 *
	 * If no bundle is assigned to handle the URI, the default bundle is returned.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	public static function handles($uri)
	{
		$uri = rtrim($uri, '/').'/';

		foreach (static::$bundles as $key => $value)
		{
			if (starts_with($uri, $value['handles'])) return $key;
		}

		return DEFAULT_BUNDLE;
	}

	/**
	 * Deteremine if a bundle exists within the bundles directory.
	 *
	 * @param  string  $bundle
	 * @return bool
	 */
	public static function exists($bundle)
	{
		return $bundle == DEFAULT_BUNDLE or in_array(strtolower($bundle), static::names());
	}

	/**
	 * Determine if a given bundle has been started for the request.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public static function started($bundle)
	{
		return in_array(strtolower($bundle), static::$started);
	}

	/**
	 * Determine if a given bundle has its routes file loaded.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public static function routed($bundle)
	{
		return in_array(strtolower($bundle), static::$routed);
	}

	/**
	 * Get the identifier prefix for the bundle.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function prefix($bundle)
	{
		return ($bundle !== DEFAULT_BUNDLE) ? "{$bundle}::" : '';
	}

	/**
	 * Get the class prefix for a given bundle.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function class_prefix($bundle)
	{
		return ($bundle !== DEFAULT_BUNDLE) ? Str::classify($bundle).'_' : '';
	}

	/**
	 * Return the root bundle path for a given bundle.
	 *
	 * <code>
	 *		// Returns the bundle path for the "admin" bundle
	 *		$path = Bundle::path('admin');
	 *
	 *		// Returns the path('app') constant as the default bundle
	 *		$path = Bundle::path('application');
	 * </code>
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function path($bundle)
	{
		return ($bundle == DEFAULT_BUNDLE) ? path('app') : static::$bundles[$bundle]['location'];
	}

	/**
	 * Return the root asset path for the given bundle.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function assets($bundle)
	{
		return ($bundle != DEFAULT_BUNDLE) ? URL::base()."/bundles/{$bundle}/" : URL::base().'/';
	}

	/**
	 * Get the bundle name from a given identifier.
	 *
	 * <code>
	 *		// Returns "admin" as the bundle name for the identifier
	 *		$bundle = Bundle::name('admin::home.index');
	 * </code>
	 *
	 * @param  string  $identifier
	 * @return string
	 */
	public static function name($identifier)
	{
		list($bundle, $element) = static::parse($identifier);

		return $bundle;
	}

	/**
	 * Get the element name from a given identifier.
	 *
	 * <code>
	 *		// Returns "home.index" as the element name for the identifier
	 *		$bundle = Bundle::bundle('admin::home.index');
	 * </code>
	 *
	 * @param  string  $identifier
	 * @return string
	 */
	public static function element($identifier)
	{
		list($bundle, $element) = static::parse($identifier);

		return $element;
	}

	/**
	 * Reconstruct an identifier from a given bundle and element.
	 *
	 * <code>
	 *		// Returns "admin::home.index"
	 *		$identifier = Bundle::identifier('admin', 'home.index');
	 *
	 *		// Returns "home.index"
	 *		$identifier = Bundle::identifier('application', 'home.index');
	 * </code>
	 *
	 * @param  string  $bundle
	 * @param  string  $element
	 * @return string
	 */
	public static function identifier($bundle, $element)
	{
		return (is_null($bundle) or $bundle == DEFAULT_BUNDLE) ? $element : $bundle.'::'.$element;
	}

	/**
	 * Return the bundle name if it exists, else return the default bundle.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function resolve($bundle)
	{
		return (static::exists($bundle)) ? $bundle : DEFAULT_BUNDLE;
	}

	/**
	 * Parse a element identifier and return the bundle name and element.
	 *
	 * <code>
	 *		// Returns array(null, 'admin.user')
	 *		$element = Bundle::parse('admin.user');
	 *
	 *		// Parses "admin::user" and returns array('admin', 'user')
	 *		$element = Bundle::parse('admin::user');
	 * </code>
	 *
	 * @param  string  $identifier
	 * @return array
	 */
	public static function parse($identifier)
	{
		// The parsed elements are cached so we don't have to reparse them on each
		// subsequent request for the parsed element. So, if we've already parsed
		// the given element, we'll just return the cached copy.
		if (isset(static::$elements[$identifier]))
		{
			return static::$elements[$identifier];
		}

		if (strpos($identifier, '::') !== false)
		{
			$element = explode('::', strtolower($identifier));
		}
		// If no bundle is in the identifier, we will insert the default bundle
		// since classes like Config and Lang organize their items by bundle.
		// The "application" folder essentially behaves as a bundle.
		else
		{
			$element = array(DEFAULT_BUNDLE, strtolower($identifier));
		}

		return static::$elements[$identifier] = $element;
	}

	/**
	 * Get the information for a given bundle.
	 *
	 * @param  string  $bundle
	 * @return object
	 */
	public static function get($bundle)
	{
		return (object) array_get(static::$bundles, $bundle);
	}

	/**
	 * Get all of the installed bundles for the application.
	 *
	 * @return array
	 */
	public static function all()
	{
		return static::$bundles;
	}

	/**
	 * Get all of the installed bundle names.
	 *
	 * @return array
	 */
	public static function names()
	{
		return array_keys(static::$bundles);
	}

}