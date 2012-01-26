<?php namespace Laravel\Routing; use Closure, Laravel\Str, Laravel\Bundle;

class Router {

	/**
	 * All of the routes that have been registered.
	 *
	 * @var array
	 */
	public static $routes = array();

	/**
	 * All of the route names that have been matched with URIs.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * The wildcard patterns supported by the router.
	 *
	 * @var array
	 */
	public static $patterns = array(
		'(:num)' => '([0-9]+)',
		'(:any)' => '([a-zA-Z0-9\.\-_]+)',
	);

	/**
	 * The optional wildcard patterns supported by the router.
	 *
	 * @var array
	 */
	public static $optional = array(
		'/(:num?)' => '(?:/([0-9]+)',
		'/(:any?)' => '(?:/([a-zA-Z0-9\.\-_]+)',
	);

	/**
	 * Register a route with the router.
	 *
	 * <code>
	 *		// Register a route with the router
	 *		Router::register('GET /', function() {return 'Home!';});
	 *
	 *		// Register a route that handles multiple URIs with the router
	 *		Router::register(array('GET /', 'GET /home'), function() {return 'Home!';});
	 * </code>
	 *
	 * @param  string|array  $route
	 * @param  string        $action
	 * @return void
	 */
	public static function register($route, $action)
	{
		foreach ((array) $route as $uri)
		{
			// If the action is a string, it is a pointer to a controller, so we
			// need to add it to the action array as a "uses" clause, which will
			// indicate to the route to call the controller when the route is
			// executed by the application.
			//
			// Note that all route actions are converted to arrays. This just
			// gives us a convenient and consistent way of accessing it since
			// we can always make an assumption that the action is an array,
			// and it lets us store the URIs on the action for each route.
			if (is_string($action))
			{
				static::$routes[$uri]['uses'] = $action;
			}
			// If the action is not a string, we can just simply cast it as an
			// array, then we will add all of the URIs to the action array as
			// the "handes" clause so we can easily check which URIs are
			// handled by the route instance.
			else
			{
				// PHP 5.3.2 has a bug that causes closures cast as arrays
				// to yield an empty array. We will work around this by
				// manually adding the Closure instance to an array.
				if ($action instanceof Closure) $action = array($action);

				static::$routes[$uri] = (array) $action;
			}

			static::$routes[$uri]['handles'] = (array) $route;
		}
	}

	/**
	 * Find a route by the route's assigned name.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function find($name)
	{
		if (isset(static::$names[$name])) return static::$names[$name];

		// If no route names have been found at all, we will assume no reverse
		// routing has been done, and we will load the routes file for all of
		// the bundle that are installed for the application.
		if (count(static::$names) == 0)
		{
			foreach (Bundle::all() as $bundle)
			{
				Bundle::routes($bundle);
			}
		}

		// To find a named route, we will iterate through every route defined
		// for the application. We will cache the routes by name so we can
		// load them very quickly if we need to find them a second time.
		foreach (static::$routes as $key => $value)
		{
			if (isset($value['name']) and $value['name'] == $name)
			{
				return static::$names[$name] = array($key => $value);
			}
		}
	}

	/**
	 * Search the routes for the route matching a request method and URI.
	 *
	 * @param  string   $method
	 * @param  string   $uri
	 * @return Route
	 */
	public static function route($method, $uri)
	{
		// First we will make sure the bundle that handles the given URI has
		// been started for the current request. Bundles may handle any URI
		// as long as it begins with the string in the "handles" item of
		// the bundle's registration array.
		Bundle::start($bundle = Bundle::handles($uri));

		// All route URIs begin with the request method and have a leading
		// slash before the URI. We'll put the request method and URI in
		// that format so we can easily check for literal matches.
		$destination = $method.' /'.trim($uri, '/');

		if (array_key_exists($destination, static::$routes))
		{
			return new Route($destination, static::$routes[$destination], array());
		}

		// If we can't find a literal match, we'll iterate through all of
		// the registered routes to find a matching route that uses some
		// regular expressions or wildcards.
		if ( ! is_null($route = static::match($destination)))
		{
			return $route;
		}

		// If the bundle handling the request is not the default bundle,
		// we want to remove the root "handles" string from the URI so
		// it will not interfere with searching for a controller.
		//
		// If we left it on the URI, the root controller for the bundle
		// would need to be nested in directories matching the clause.
		// This will not intefere with the Route::handles method
		// as the destination is used to set the route's URIs.
		if ($bundle !== DEFAULT_BUNDLE)
		{
			$uri = str_replace(Bundle::get($bundle)->handles, '', $uri);

			$uri = ltrim($uri, '/');
		}

		return static::controller($bundle, $method, $destination, Str::segments($uri));
	}

	/**
	 * Iterate through every route to find a matching route.
	 *
	 * @param  string  $destination
	 * @return Route
	 */
	protected static function match($destination)
	{
		foreach (static::$routes as $route => $action)
		{
			// We only need to check routes with regular expressions since
			// all other routes would have been able to be caught by the
			// check for literal matches we just did.
			if (strpos($route, '(') !== false)
			{
				$pattern = '#^'.static::wildcards($route).'$#';

				if (preg_match($pattern, $destination, $parameters))
				{
					return new Route($route, $action, array_slice($parameters, 1));
				}
			}
		}
	}

	/**
	 * Attempt to find a controller for the incoming request.
	 *
	 * @param  string  $bundle
	 * @param  string  $method
	 * @param  string  $destination
	 * @param  array   $segments
	 * @return Route
	 */
	protected static function controller($bundle, $method, $destination, $segments)
	{
		// If there are no more segments in the URI, we will just create a route
		// for the default controller of the bundle, which is "home". We'll also
		// use the default method, which is "index".
		if (count($segments) == 0)
		{
			$uri = ($bundle == DEFAULT_BUNDLE) ? '/' : '/'.Bundle::get($bundle)->handles;

			$action = array('uses' => Bundle::prefix($bundle).'home@index');

			return new Route($method.' '.$uri, $action);
		}

		$directory = Bundle::path($bundle).'controllers/';

		if ( ! is_null($key = static::controller_key($segments, $directory)))
		{
			// First, we'll extract the controller name, then, since we need
			// to extract the method and parameters, we will remove the name
			// of the controller from the URI. Then we can shift the method
			// off of the array of segments. Any remaining segments are the
			// parameters that should be passed to the controller method.
			$controller = implode('.', array_slice($segments, 0, $key));

			$segments = array_slice($segments, $key);

			$method = (count($segments) > 0) ? array_shift($segments) : 'index';

			// We need to grab the prefix to the bundle so we can prefix
			// the route identifier with it. This informs the controller
			// class out of which bundle the controller instance should
			// be resolved when it is needed by the application.
			$prefix = Bundle::prefix($bundle);

			$action = array('uses' => $prefix.$controller.'@'.$method);

			return new Route($destination, $action, $segments);
		}
	}

	/**
	 * Get the URI index for the controller that should handle the request.
	 *
	 * @param  string  $directory
	 * @param  array   $segments
	 * @return int
	 */
	protected static function controller_key($segments, $directory)
	{
		$reverse = array_reverse($segments, true);

		// To find the proper controller, we need to iterate backwards through
		// the URI segments and take the first file that matches. That file
		// should be the deepest possible controller matched by the URI.
		// Once we find it, we'll return its index key.
		foreach ($reverse as $key => $value)
		{
			$controller = implode('/', array_slice($segments, 0, $key + 1)).EXT;

			if (file_exists($directory.$controller))
			{
				return $key + 1;
			}
		}
	}

	/**
	 * Translate route URI wildcards into actual regular expressions.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected static function wildcards($key)
	{
		// For optional parameters, first translate the wildcards to their
		// regex equivalent, sans the ")?" ending. We'll add the endings
		// back on after we know how many replacements we made.
		$key = str_replace(array_keys(static::$optional), array_values(static::$optional), $key, $count);

		$key .= ($count > 0) ? str_repeat(')?', $count) : '';

		return str_replace(array_keys(static::$patterns), array_values(static::$patterns), $key);
	}

}