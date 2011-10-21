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
		'(:num)' => '[0-9]+',
		'(:any)' => '[a-zA-Z0-9\.\-_]+',
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

		// Put the request method and URI in route form. Routes begin with
		// the request method and a forward slash followed by the URI.
		$destination = $method.' /'.trim($uri, '/');

		// Check for a literal route match first...
		if (isset($routes[$destination]))
		{
			return Request::$route = new Route($destination, $routes[$destination], array());
		}

		foreach ($routes as $keys => $callback)
		{
			// Only check routes that have multiple URIs or wildcards since other
			// routes would have been caught by the check for literal matches.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false)
			{
				if ( ! is_null($route = $this->match($destination, $keys, $callback)))
				{
					return Request::$route = $route;
				}	
			}
		}

		return Request::$route = $this->controller($method, $uri, $destination);
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
			// Append the provided formats to the route as an optional regular expression.
			if ( ! is_null($formats = $this->provides($callback)))
			{
				$key .= '(\.('.implode('|', $formats).'))?';
			}

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

		if ( ! is_null($key = $this->controller_key($segments)))
		{
			// Create the controller name for the current request. This controller
			// name will be returned by the anonymous route we will create. Instead
			// of using directory slashes, dots will be used to specify the controller
			// location with the controllers directory.
			$controller = implode('.', array_slice($segments, 0, $key));

			// Now that we have the controller path and name, we can slice the controller
			// section of the URI from the array of segments.
			$segments = array_slice($segments, $key);

			// Extract the controller method from the URI segments. If no more segments
			// are remaining after slicing off the controller, the "index" method will
			// be used as the default controller method.
			$method = (count($segments) > 0) ? array_shift($segments) : 'index';

			return new Route($destination, $controller.'@'.$method, $segments);
		}
	}

	/**
	 * Search the controllers for the application and determine if an applicable
	 * controller exists for the current request.
	 *
	 * If a controller is found, the array key for the controller name in the URI
	 * segments will be returned by the method, otherwise NULL will be returned.
	 * The deepest possible matching controller will be considered the controller
	 * that should handle the request.
	 *
	 * @param  array  $segments
	 * @return int
	 */
	protected function controller_key($segments)
	{
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
	 * Get the request formats for which the route provides responses.
	 *
	 * @param  mixed  $callback
	 * @return array
	 */
	protected function provides($callback)
	{
		if (is_array($callback) and isset($callback['provides']))
		{
			return (is_string($provides = $callback['provides'])) ? explode('|', $provides) : $provides;
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
		$replacements = 0;

		// For optional parameters, first translate the wildcards to their
		// regex equivalent, sans the ")?" ending. We will add the endings
		// back on after we know how many replacements we made.
		$key = str_replace(array_keys($this->optional), array_values($this->optional), $key, $replacements);

		$key .= ($replacements > 0) ? str_repeat(')?', $replacements) : '';

		// After replacing all of the optional wildcards, we can replace all
		// of the "regular" wildcards and return the fully regexed string.
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
		return array_values(array_intersect_key(explode('/', $uri), preg_grep('/\(.+\)/', explode('/', $route))));	
	}	

}