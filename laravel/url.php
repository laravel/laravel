<?php namespace Laravel; use Laravel\Routing\Router, Laravel\Routing\Route;

class URL {

	/**
	 * The cached base URL.
	 *
	 * @var string
	 */
	public static $base;

	/**
	 * Get the full URI including the query string.
	 *
	 * @return string
	 */
	public static function full()
	{
		return static::to(URI::full());
	}

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
	 * Get the URL for the application root.
	 *
	 * @param  bool    $https
	 * @return string
	 */
	public static function home($https = false)
	{
		$route = Router::find('home');

		// If a route named "home" exists, we'll route to that instead of using
		// the single slash root URI. THis allows the HTTPS attribute to be
		// respected instead of being hard-coded in the redirect.
		if ( ! is_null($route))
		{
			return static::to_route('home');
		}

		return static::to('/', $https);
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

		// If the application URL configuration is set, we will just use that
		// instead of trying to guess the URL from the $_SERVER array's host
		// and script variables as this is more reliable.
		if (($url = Config::get('application.url')) !== '')
		{
			$base = $url;
		}
		elseif (isset($_SERVER['HTTP_HOST']))
		{
			$base = static::guess();
		}

		return static::$base = $base;
	}

	/**
	 * Guess the application URL based on the $_SERVER variables.
	 *
	 * @return string
	 */
	protected static function guess()
	{
		$protocol = (Request::secure()) ? 'https://' : 'http://';

		// Basically, by removing the basename, we are removing everything after
		// the and including the front controller from the URI. Leaving us with
		// the installation path for the application.
		$script = $_SERVER['SCRIPT_NAME'];

		$path = str_replace(basename($script), '', $script);

		// Now that we have the URL, all we need to do is attach the protocol
		// protocol and HTTP_HOST to build the URL for the application, and
		// we also trim off trailing slashes for cleanliness.
		$uri = $protocol.$_SERVER['HTTP_HOST'].$path;

		return rtrim($uri, '/');
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
		// If the given URL is already valid or begins with a hash, we'll just return
		// the URL unchanged since it is already well formed. Otherwise we will add
		// the base URL of the application and return the full URL.
		if (static::valid($url) or starts_with($url, '#'))
		{
			return $url;
		}

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
	 * @return string
	 */
	public static function to_action($action, $parameters = array())
	{
		// This allows us to use true reverse routing to controllers, since
		// URIs may be setup to handle the action that do not follow the
		// typical Laravel controller URI conventions.
		$route = Router::uses($action);

		if ( ! is_null($route))
		{
			return static::explicit($route, $action, $parameters);
		}
		// If no route was found that handled the given action, we'll just
		// generate the URL using the typical controller routing setup
		// for URIs and turn SSL to false.
		else
		{
			return static::convention($action, $parameters);
		}
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

		return static::to(static::transpose(key($route), $parameters), $https);
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
		// URIs that begin with that string and no others.
		$root = $bundle['handles'] ?: '';

		$https = false;

		$parameters = implode('/', $parameters);

		// We'll replace both dots and @ signs in the URI since both are used
		// to specify the controller and action, and by convention should be
		// translated into URI slashes for the URL.
		$uri = $root.'/'.str_replace(array('.', '@'), '/', $action);

		$uri = static::to(str_finish($uri, '/').$parameters);

		return trim($uri, '/');
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
		// in the application config from the generated URL.
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

		// To determine whether the URL should be HTTPS or not, we look for the "https"
		// value on the route action array. The route has control over whether the URL
		// should be generated with an HTTPS protocol string or just HTTP.
		$https = array_get(current($route), 'https', false);

		$uri = trim(static::transpose(key($route), $parameters), '/');

		return static::to($uri, $https);
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
		// in the array of parameters that were passed to us.
		$uri = preg_replace('/\(.+?\)/', '', $uri);

		return trim($uri, '/');
	}

	/**
	 * Determine if the given URL is valid.
	 *
	 * @param  string  $url
	 * @return bool
	 */
	public static function valid($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL) !== false;
	}

}