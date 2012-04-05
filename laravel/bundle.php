<?php namespace Laravel; defined('DS') or die('No direct script access.');

use Laravel\Routing\Router;
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
	 * Register the bundle for the application.
	 *
	 * @param  string  $bundle
	 * @param  array   $config
	 * @return void
	 */
	public static function register($bundle, $config = array())
	{
		$defaults = array('handles' => null, 'auto' => false);

		// If the given configuration is actually a string, we will assume it is a
		// location and set the bundle name to match it. This is common for most
		// bundles who simply live in the root bundle directory.
		if (is_string($config))
		{
			$bundle = $config;

			$config = array('location' => $bundle);
		}

		// If no location is set, we will set the location to match the name of
		// the bundle. This is for bundles who are installed to the root of
		// the bundle directory so a location was not set.
		if ( ! isset($config['location']))
		{
			$config['location'] = $bundle;
		}

		static::$bundles[$bundle] = array_merge($defaults, $config);

		// It is possible for the develoepr to specify auto-loader mappings
		// directly on the bundle registration. This provides a convenient
		// way to register mappings withuot a bootstrap.
		if (isset($config['autoloads']))
		{
			static::autoloads($bundle, $config);
		}
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

		// Each bundle may have a start script which is responsible for preparing
		// the bundle for use by the application. The start script may register
		// any classes the bundle uses with the auto-loader, etc.
		if (file_exists($path = static::path($bundle).'start'.EXT))
		{
			require $path;
		}

		// Each bundle may also have a "routes" file which is responsible for
		// registering the bundle's routes. This is kept separate from the
		// start script for reverse routing efficiency purposes.
		static::routes($bundle);

		Event::fire("laravel.started: {$bundle}");

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
		if (static::routed($bundle)) return;

		$path = static::path($bundle).'routes'.EXT;

		// By setting the bundle property on the router the router knows what
		// value to replace the (:bundle) place-holder with when the bundle
		// routes are added, keeping the routes flexible.
		Router::$bundle = static::option($bundle, 'handles');

		if ( ! static::routed($bundle) and file_exists($path))
		{
			static::$routed[] = $bundle;

			require $path;
		}
	}

	/**
	 * Register the auto-loading configuration for a bundle.
	 *
	 * @param  string  $bundle
	 * @param  array   $config
	 * @return void
	 */
	protected static function autoloads($bundle, $config)
	{
		$path = rtrim(Bundle::path($bundle), DS);

		foreach ($config['autoloads'] as $type => $mappings)
		{
			// When registering each type of mapping we'll replace the (:bundle)
			// place-holder with the path to the bundle's root directory, so
			// the developer may dryly register the mappings.
			$mappings = array_map(function($mapping) use ($path)
			{
				return str_replace('(:bundle)', $path, $mapping);

			}, $mappings);

			// Once the mappings are formatted, we will call the Autoloader
			// function matching the mapping type and pass in the array of
			// mappings so they can be registered and used.
			Autoloader::$type($mappings);
		}
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
	 * Determine which bundle handles the given URI.
	 *
	 * The default bundle is returned if no other bundle is assigned.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	public static function handles($uri)
	{
		$uri = rtrim($uri, '/').'/';

		foreach (static::$bundles as $key => $value)
		{
			if (isset($value['handles']) and starts_with($uri, $value['handles'].'/'))
			{
				return $key;
			}
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
		if (is_null($bundle) or $bundle === DEFAULT_BUNDLE)
		{
			return path('app');
		}
		else if ($location = array_get(static::$bundles, $bundle.'.location'))
		{
			return str_finish(path('bundle').$location, DS);
		}
	}

	/**
	 * Return the root asset path for the given bundle.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	public static function assets($bundle)
	{
		if (is_null($bundle)) return static::assets(DEFAULT_BUNDLE);

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
		return array_get(static::$bundles, $bundle);
	}

	/**
	 * Get an option for a given bundle.
	 *
	 * @param  string  $bundle
	 * @param  string  $option
	 * @return mixed
	 */
	public static function option($bundle, $option)
	{
		$bundle = static::get($bundle);

		if ( ! is_null($bundle)) return array_get($bundle, $option);
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