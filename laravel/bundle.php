<?php namespace Laravel; isset($GLOBALS['APP_PATH']) or die('No direct script access.');

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
	 * Register a bundle for the application.
	 *
	 * @param  string  $bundle
	 * @param  string  $location
	 * @param  string  $handles
	 * @return void
	 */
	public static function register($bundle, $config = array())
	{
		$defaults = array('location' => $bundle, 'handles' => null, 'auto' => false);

		// If the given config is actually a string, we will assume it is a location
		// and convert it to an array so that the developer may conveniently add
		// bundles to the configuration without making an array for each one.
		if (is_string($config))
		{
			$config = array('location' => $config);
		}

		if ( ! isset($config['location']))
		{
			throw new \Exception("Location not set for bundle [$bundle]");
		}

		// We will trim the trailing slash from the location and add it back so
		// we don't have to worry about the developer adding or not adding it
		// to the location path for the bundle.
		$config['location'] = $GLOBALS['BUNDLE_PATH'].rtrim($config['location'], DS).DS;

		static::$bundles[$bundle] = array_merge($defaults, $config);
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

		if ($bundle !== DEFAULT_BUNDLE and ! static::exists($bundle))
		{
			throw new \Exception("Bundle [$bundle] has not been installed.");
		}

		// Each bundle may have a "start" script which is responsible for preparing
		// the bundle for use by the application. The start script may register any
		// classes the bundle uses with the auto-loader, or perhaps will start any
		// dependent bundles so that they are available.
		if (file_exists($path = static::path($bundle).'bundle'.EXT))
		{
			require_once $path;
		}

		// Each bundle may also have a "routes" file which is responsible for
		// registering the bundle's routes. This is kept separate from the
		// start script for reverse routing efficiency purposes.
		static::routes($bundle);

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
		if (file_exists($path = static::path($bundle).'routes'.EXT))
		{
			require_once $path;
		}
	}

	/**
	 * Determine which bundle handles the given URI.
	 *
	 * If no bundle is assigned to handle the URI, the default bundle is returned.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function handles($uri)
	{
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
		return in_array(strtolower($bundle), static::names());
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
	 *		// Returns the $GLOBALS['APP_PATH'] constant as the default bundle
	 *		$path = Bundle::path('application');
	 * </code>
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function path($bundle)
	{
		return ($bundle == DEFAULT_BUNDLE) ? $GLOBALS['APP_PATH'] : static::$bundles[$bundle]['location'];
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