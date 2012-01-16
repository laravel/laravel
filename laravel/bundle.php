<?php namespace Laravel; defined('APP_PATH') or die('No direct script access.');

class Bundle {

	/**
	 * All of the application's bundles.
	 *
	 * @var array
	 */
	protected static $bundles;

	/**
	 * A cache of the parsed bundle elements.
	 *
	 * @var array
	 */
	protected static $elements = array();

	/**
	 * All of the bundles that have been started.
	 *
	 * @var array
	 */
	protected static $started = array();

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
			require $path;
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
		if (static::started($bundle)) return;

		if (file_exists($path = static::path($bundle).'routes'.EXT))
		{
			require $path;
		}
	}

	/**
	 * Determine if the given bundle is "routable".
	 *
	 * A bundle is considered routable if it has a controller directory or a routes file.
	 *
	 * @param  string  $bundle
	 * @return bool
	 */
	public static function routable($bundle)
	{
		$path = static::path($bundle);

		return is_dir($path.'controllers/') or file_exists($path.'routes'.EXT);
	}

	/**
	 * Deteremine if a bundle exists within the bundles directory.
	 *
	 * @param  string  $bundle
	 * @return bool
	 */
	public static function exists($bundle)
	{
		return in_array(strtolower($bundle), static::all());
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
	 *		// Returns the APP_PATH constant as the default bundle
	 *		$path = Bundle::path('application');
	 * </code>
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function path($bundle)
	{
		return ($bundle != DEFAULT_BUNDLE) ? BUNDLE_PATH.strtolower($bundle).DS : APP_PATH;
	}

	/**
	 * Return the root asset path for the given bundle.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function assets($bundle)
	{
		return ($bundle != DEFAULT_BUNDLE) ? PUBLIC_PATH."bundles/{$bundle}/" : PUBLIC_PATH;
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
	 * Detect all of the existing bundles in the application.
	 *
	 * The names of the bundles are cached so this operation will be only be
	 * performed once and then the same array will be returned on each later
	 * request for the bundle names.
	 *
	 * @return array
	 */
	public static function all()
	{
		if (is_array(static::$bundles)) return static::$bundles;

		$bundles = array();

		foreach (array_filter(glob(BUNDLE_PATH.'*'), 'is_dir') as $bundle)
		{
			$bundles[] = basename($bundle);
		}

		return static::$bundles = $bundles;
	}

}