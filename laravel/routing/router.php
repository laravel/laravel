<?php namespace Laravel\Routing; use Closure, Laravel\Bundle;

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
	protected static $patterns = array(
		'(:num)' => '([0-9]+)',
		'(:any)' => '([a-zA-Z0-9\.\-_]+)',
	);

	/**
	 * The optional wildcard patterns supported by the router.
	 *
	 * @var array
	 */
	protected static $optional = array(
		'/(:num?)' => '(?:/([0-9]+)',
		'/(:any?)' => '(?:/([a-zA-Z0-9\.\-_]+)',
	);

	/**
	 * Register a route with the router.
	 *
	 * The specified route may either be a single URI, or an array of URIs.
	 *
	 * <code>
	 *		// Register a route with the router with an anonymous function handler
	 *		Router::register('GET /home', function() { return 'Home!'; });
	 *
	 *		// Register a route that handles more than one URI
	 *		Router::register(array('GET /', 'GET /home'), 'home@index');
	 *
	 *		// Register a route with an attached "before" filter
	 *		Router::register('GET /', array('before' => 'csrf', 'uses' => 'home@index'));
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
			static::$routes[$uri] = $action;
		}
	}

	/**
	 * Find a route by name.
	 *
	 * The returned array will be identical the array defined in the routes.php file.
	 *
	 * <code>
	 *		// Find the route that has been assigned the name "login"
	 *		$route = Router::find('login');
	 *
	 *		// Call the route that has been named "login"
	 *		$response = Router::find('login')->call();
	 * </code>
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function find($name)
	{
		if (isset(static::$names[$name])) return static::$names[$name];

		// If no route names have been found at all, we will assume no reverse routing
		// has been done, and we will load the routes file for all of the bundle that
		// are installed for the application. This will fill the routes array
		// with every route that has been defined.
		if (count(static::$names) == 0)
		{
			foreach (Bundle::all() as $bundle)
			{
				Bundle::routes($bundle);
			}
		}

		// To find a named route, we need to iterate through every route defined for
		// the application. We will cache the routes by name so we can load them
		// very quickly if we need to find them a second time.
		foreach (static::$routes as $key => $value)
		{
			if (is_array($value) and isset($value['name']) and $value['name'] === $name)
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
		// All route URIs begin with the request method and have a leading slash
		// before the URI. We'll put the request method and URI into that format
		// so we can easily check for literal matches on the route.
		$destination = $method.' /'.trim($uri, '/');

		if (isset(static::$routes[$destination]))
		{
			return new Route($destination, static::$routes[$destination], array());
		}

		// If no literal route match was found, we will iterate through all of
		// the routes and check each of them one at a time, translating any
		// wildcards in the route into actual regular expressions.
		foreach (static::$routes as $route => $action)
		{
			// Only check routes that have a wildcard or a regular expression,
			// as the rest of the routes should have been able to be matched
			// literally in the previous check for literal matches.
			if (strpos($route, '(') !== false)
			{
				if (preg_match('#^'.static::wildcards($key).'$#', $destination))
				{
					return new Route($route, $action, static::parameters($destination, $key));
				}
			}
		}

		// If no registered routes matched the request, we will use conventions
		// to look for a controller that can handle the request. Typically, the
		// first segment of a URI is the controller, the second is the action,
		// and any remaining segments are the action parameters.
		$segments = explode('/', trim($uri, '/'));

		// If there are more than 20 request segments, we will halt the request
		// and throw an exception. This is primarily to protect against DDoS
		// attacks which could overwhelm the server by feeding it too many
		// segments in the URI, causing the loops in this class to bog.
		if (count($segments) > 20)
		{
			throw new \Exception("Invalid request. There are more than 15 URI segments.");
		}

		$segments = array_filter($segments, function($v) {return $v != '';});

		return static::controller(DEFAULT_BUNDLE, $method, $segments, $destination);
	}

	/**
	 * Attempt to find a controller for the incoming request.
	 *
	 * @param  string  $bundle
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  string  $destination
	 * @return Route
	 */
	protected static function controller($bundle, $method, $segments, $destination)
	{
		// If the request is to the root of the application, an ad-hoc route will
		// be generated to the home controller's "index" method, making it the
		// default controller method for the application.
		if (count($segments) == 0)
		{
			$uri = ($bundle == DEFAULT_BUNDLE) ? '/' : "/{$bundle}";

			return new Route($method.' '.$uri, Bundle::prefix($bundle).'home@index');
		}

		$directory = Bundle::path($bundle).'controllers/';

		// We need to determine in which directory to look for the controllers.
		// If the first segment of the request corresponds to a bundle that
		// is installed for the application, we will use that bundle's
		// controller path, otherwise we'll use the application's.
		if (Bundle::routable($segments[0]))
		{
			$bundle = $segments[0];

			// We shift the bundle name off of the URI segments because it will not
			// be used to find a controller within the bundle. If we were to leave
			// it in the segments, every bundle controller would need to be nested
			// within a sub-directory matching the bundle name.
			array_shift($segments);

			return static::controller($bundle, $method, $segments, $destination);
		}

		if ( ! is_null($key = static::controller_key($segments, $directory)))
		{
			// Extract the various parts of the controller call from the URI.
			// First, we'll extract the controller name, then, since we need
			// to extract the method and parameters, we will remove the name
			// of the controller from the URI. Then we can shift the method
			// off of the array of segments. Any remaining segments are the
			// parameters that should be passed to the controller method.
			$controller = implode('.', array_slice($segments, 0, $key));

			$segments = array_slice($segments, $key);

			$method = (count($segments) > 0) ? array_shift($segments) : 'index';

			$prefix = Bundle::prefix($bundle);

			return new Route($destination, $prefix.$controller.'@'.$method, $segments);
		}
	}

	/**
	 * Get the URI index for the controller that should handle the request.
	 *
	 * If a controller is found, the array key for the controller name in the URI
	 * segments will be returned by the method, otherwise NULL will be returned.
	 * The deepest possible controller will be considered the controller that
	 * should handle the request.
	 *
	 * @param  string  $directory
	 * @param  array   $segments
	 * @return int
	 */
	protected static function controller_key($segments, $directory)
	{
		// To find the proper controller, we need to iterate backwards through
		// the URI segments and take the first file that matches. That file
		// should be the deepest possible controller matched by the URI.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			$controller = implode('/', array_slice($segments, 0, $key + 1)).EXT;

			if (file_exists($path = $directory.$controller))
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
		// regex equivalent, sans the ")?" ending. We will add the endings
		// back on after we know how many replacements we made.
		$key = str_replace(array_keys(static::$optional), array_values(static::$optional), $key, $count);

		$key .= ($count > 0) ? str_repeat(')?', $count) : '';

		return str_replace(array_keys(static::$patterns), array_values(static::$patterns), $key);
	}

	/**
	 * Extract the parameters from a URI based on a route URI.
	 *
	 * Any route segment wrapped in parentheses is considered a parameter.
	 *
	 * @param  string  $uri
	 * @param  string  $route
	 * @return array
	 */
	protected static function parameters($uri, $route)
	{
		list($uri, $route) = array(explode('/', $uri), explode('/', $route));

		$count = count($route);

		$parameters = array();

		// To find the parameters that should be passed to the route, we will
		// iterate through the route segments, and if the segment is enclosed
		// in parentheses, we will take the matching segment from the request
		// URI and add it to the array of parameters.
		for ($i = 0; $i < $count; $i++)
		{
			if (preg_match('/\(.+\)/', $route[$i]) and isset($uri[$i]))
			{
				$parameters[] = $uri[$i];
			}
		}

		return $parameters;
	}	

}