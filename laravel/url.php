<?php namespace Laravel; use Laravel\Routing\Route, Laravel\Routing\Router;

class URL {

	/**
	 * The cached base URL.
	 *
	 * @var string
	 */
	public static $base;

	/**
	 * Get the full URL for the current request.
	 *
	 * @return string
	 */
	public static function current()
	{
		return static::to(URI::current());
	}

	/**
	 * Get the base URL of the application.
	 *
	 * @return string
	 */
	public static function base()
	{
		if (isset(static::$base)) return static::$base;

		$base = 'http://localhost';

		// If the application URL configuration is set, we will just use
		// that instead of trying to guess the URL based on the $_SERVER
		// array's host and script name.
		if (($url = Config::get('application.url')) !== '')
		{
			$base = $url;
		}
		elseif (isset($_SERVER['HTTP_HOST']))
		{
			$protocol = (Request::secure()) ? 'https://' : 'http://';

			// Basically, by removing the basename, we are removing everything after the
			// and including the front controller from the request URI. Leaving us with
			// the path in which the framework is installed. From that path, we can
			// construct the base URL to the application.
			$path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

			$base = rtrim($protocol.$_SERVER['HTTP_HOST'].$path, '/');
		}

		return static::$base = $base;
	}

	/**
	 * Generate an application URL.
	 *
	 * <code>
	 *		// Create a URL to a location within the application
	 *		$url = URL::to('user/profile');
	 *
	 *		// Create a HTTPS URL to a location within the application
	 *		$url = URL::to('user/profile', true);
	 * </code>
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to($url = '', $https = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) return $url;

		$root = static::base().'/'.Config::get('application.index');

		// Since SSL is not often used while developing the application, we allow the
		// developer to disable SSL on all framework generated links to make it more
		// convenient to work with the site while developing locally.
		if ($https and Config::get('application.ssl'))
		{
			$root = preg_replace('~http://~', 'https://', $root, 1);
		}

		return rtrim($root, '/').'/'.ltrim($url, '/');
	}

	/**
	 * Generate an application URL with HTTPS.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_secure($url = '')
	{
		return static::to($url, true);
	}

	/**
	 * Generate a URL to a controller action.
	 *
	 * <code>
	 *		// Generate a URL to the "index" method of the "user" controller
	 *		$url = URL::to_action('user@index');
	 *
	 *		// Generate a URL to http://example.com/user/profile/taylor
	 *		$url = URL::to_action('user@profile', array('taylor'));
	 * </code>
	 *
	 * @param  string  $action
	 * @param  array   $parameters
	 * @param  string  $method
	 * @return string
	 */
	public static function to_action($action, $parameters = array(), $method = 'GET')
	{
		// This allows us to use true reverse routing to controllers, since
		// URIs may be setup to handle the action that do not follow the
		// typical Laravel controller URI convention.
		$route = Router::uses($action, $method);

		if ( ! is_null($route))
		{
			$uri = static::explicit($route, $action, $parameters);
		}
		// If no route was found that handled the given action, we'll just
		// generate the URL using the typical controller routing setup
		// for URIs and turn SSL to false.
		else
		{
			$uri = static::convention($action, $parameters);
		}

		return static::to($uri, $https);
	}

	/**
	 * Generate a HTTPS URL to a controller action.
	 *
	 * @param  string  $action
	 * @param  array   $parameters
	 * @return string
	 */
	public static function to_post_action($action, $parameters = array())
	{
		return static::to_action($action, $parameters, 'POST');
	}

	/**
	 * Generate a action URL from a route definition
	 *
	 * @param  array   $route
	 * @param  string  $action
	 * @param  array   $parameters
	 * @return string
	 */
	protected static function explicit($route, $action, $parameters)
	{
		$https = array_get(current($route), 'https', false);

		return Route::transpose(Route::destination(key($route)), $parameters);
	}

	/**
	 * Generate an action URI by convention.
	 *
	 * @param  string  $action
	 * @param  array   $parameters
	 * @return string
	 */
	protected static function convention($action, $parameters)
	{
		list($bundle, $action) = Bundle::parse($action);

		$bundle = Bundle::get($bundle);

		// If a bundle exists for the action, we will attempt to use it's "handles"
		// clause as the root of the generated URL, as the bundle can only handle
		// URIs that begin with that string.
		$root = $bundle['handles'] ?: '';

		$https = false;

		// We'll replace both dots and @ signs in the URI since both are used
		// to specify the controller and action, and by convention should be
		// translated into URI slashes.
		$uri = $root.str_replace(array('.', '@'), '/', $action);

		return str_finish($uri, '/').implode('/', $parameters);
	}

	/**
	 * Generate an application URL to an asset.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_asset($url, $https = null)
	{
		if (is_null($https)) $https = Request::secure();

		$url = static::to($url, $https);

		// Since assets are not served by Laravel, we do not need to come through
		// the front controller. So, we'll remove the application index specified
		// in the application configuration from the generated URL.
		if (($index = Config::get('application.index')) !== '')
		{
			$url = str_replace($index.'/', '', $url);
		}

		return $url;
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * <code>
	 *		// Create a URL to the "profile" named route
	 *		$url = URL::to_route('profile');
	 *
	 *		// Create a URL to the "profile" named route with wildcard parameters
	 *		$url = URL::to_route('profile', array($username));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_route($name, $parameters = array())
	{
		if (is_null($route = Routing\Router::find($name)))
		{
			throw new \Exception("Error creating URL for undefined route [$name].");
		}

		$uri = Route::destination(key($route));

		// To determine whether the URL should be HTTPS or not, we look for the "https"
		// value on the route action array. The route has control over whether the
		// URL should be generated with an HTTPS protocol.
		$https = array_get(current($route), 'https', false);

		return static::to(Route::transpose($uri, $parameters), $https);
	}

	/**
	 * Substitute the parameters in a given URI.
	 *
	 * @param  string  $uri
	 * @param  array   $parameters
	 * @return string
	 */
	public static function transpose($uri, $parameters)
	{
		// Spin through each route parameter and replace the route wildcard segment
		// with the corresponding parameter passed to the method. Afterwards, we'll
		// replace all of the remaining optional URI segments.
		foreach ((array) $parameters as $parameter)
		{
			if ( ! is_null($parameter))
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}
		}

		// If there are any remaining optional place-holders, we'll just replace
		// them with empty strings since not every optional parameter has to be
		// in the array of parameters that were passed.
		return str_replace(array_keys(Router::$optional), '', $uri);		
	}

}