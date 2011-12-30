<?php namespace Laravel\Routing; use Laravel\Request;

class Delegate {

	/**
	 * The destination of the route delegate.
	 *
	 * @var string
	 */
	public $destination;

	/**
	 * Create a new route delegate instance.
	 *
	 * @param  string  $destination
	 * @return void
	 */
	public function __construct($destination)
	{
		$this->destination = $destination;
	}

}

class Router {

	/**
	 * The route loader instance.
	 *
	 * @var Loader
	 */
	public $loader;

	/**
	 * The named routes that have been found so far.
	 *
	 * @var array
	 */
	protected $names = array();

	/**
	 * The path the application controllers.
	 *
	 * @var string
	 */
	protected $controllers;

	/**
	 * The wildcard patterns supported by the router.
	 *
	 * @var array
	 */
	protected $patterns = array(
		'(:num)' => '([0-9]+)',
		'(:any)' => '([a-zA-Z0-9\.\-_]+)',
	);

	/**
	 * The optional wildcard patterns supported by the router.
	 *
	 * @var array
	 */
	protected $optional = array(
		'/(:num?)' => '(?:/([0-9]+)',
		'/(:any?)' => '(?:/([a-zA-Z0-9\.\-_]+)',
	);

	/**
	 * Create a new router for a request method and URI.
	 *
	 * @param  Loader  $loader
	 * @param  string  $controllers
	 * @return void
	 */
	public function __construct(Loader $loader, $controllers)
	{
		$this->loader = $loader;
		$this->controllers = $controllers;
	}

	/**
	 * Find a route by name.
	 *
	 * The returned array will be identical the array defined in the routes.php file.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function find($name)
	{
		if (array_key_exists($name, $this->names)) return $this->names[$name];

		// To find a named route, we need to iterate through every route defined
		// for the application. We will cache the routes by name so we can load
		// them very quickly if we need to find them a second time.
		foreach ($this->loader->everything() as $key => $value)
		{
			if (is_array($value) and isset($value['name']) and $value['name'] === $name)
			{
				return $this->names[$name] = array($key => $value);
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
	public function route($method, $uri)
	{
		$routes = $this->loader->load($uri);

		// All route URIs begin with the request method and have a leading
		// slash before the URI. We'll put the request method and URI into
		// that format so we can easily check for literal matches.
		$destination = $method.' /'.trim($uri, '/');

		if (isset($routes[$destination]))
		{
			return new Route($destination, $routes[$destination], array());
		}

		// If no literal route match was found, we will iterate through all
		// of the routes and check each of them one at a time, translating
		// any wildcards in the route into actual regular expressions.
		foreach ($routes as $keys => $callback)
		{
			// Only check the routes that couldn't be matched literally...
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false)
			{
				if ( ! is_null($route = $this->match($destination, $keys, $callback)))
				{
					return $route;
				}
			}
		}

		return $this->controller($method, $uri, $destination);
	}

	/**
	 * Attempt to match a given route destination to a given route.
	 *
	 * The destination's methods and URIs will be compared against the route's.
	 * If there is a match, the Route instance will be returned, otherwise null
	 * will be returned by the method.
	 *
	 * @param  string  $destination
	 * @param  array   $keys
	 * @param  mixed   $callback
	 * @return mixed
	 */
	protected function match($destination, $keys, $callback)
	{
		foreach (explode(', ', $keys) as $key)
		{
			if (preg_match('#^'.$this->wildcards($key).'$#', $destination))
			{
				return new Route($keys, $callback, $this->parameters($destination, $key));
			}
		}
	}

	/**
	 * Attempt to find a controller for the incoming request.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  string  $destination
	 * @return Route
	 */
	protected function controller($method, $uri, $destination)
	{
		// If the request is to the root of the application, an ad-hoc route
		// will be generated to the home controller's "index" method, making
		// it the default controller method.
		if ($uri === '/') return new Route($method.' /', 'home@index');

		$segments = explode('/', trim($uri, '/'));

		// If there are more than 20 request segments, we will halt the request
		// and throw an exception. This is primarily to protect against DDoS
		// attacks which could overwhelm the server by feeding it too many
		// segments in the URI, causing the loops in this class to bog.
		if (count($segments) > 20)
		{
			throw new \Exception("Invalid request. There are more than 20 URI segments.");
		}

		if ( ! is_null($key = $this->controller_key($segments)))
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

			return new Route($destination, $controller.'@'.$method, $segments);
		}
	}

	/**
	 * Search for a controller that can handle the current request.
	 *
	 * If a controller is found, the array key for the controller name in the URI
	 * segments will be returned by the method, otherwise NULL will be returned.
	 * The deepest possible controller will be considered the controller that
	 * should handle the request.
	 *
	 * @param  array  $segments
	 * @return int
	 */
	protected function controller_key($segments)
	{
		// To find the proper controller, we need to iterate backwards through
		// the URI segments and take the first file that matches. That file
		// should be the deepest controller matched by the URI.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			$controller = implode('/', array_slice($segments, 0, $key + 1)).EXT;

			if (file_exists($path = $this->controllers.$controller))
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
	protected function wildcards($key)
	{
		// For optional parameters, first translate the wildcards to their
		// regex equivalent, sans the ")?" ending. We will add the endings
		// back on after we know how many replacements we made.
		$key = str_replace(array_keys($this->optional), array_values($this->optional), $key, $count);

		$key .= ($count > 0) ? str_repeat(')?', $count) : '';

		return str_replace(array_keys($this->patterns), array_values($this->patterns), $key);
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
	protected function parameters($uri, $route)
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