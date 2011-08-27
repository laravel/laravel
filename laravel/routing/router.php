<?php namespace Laravel\Routing;

use Laravel\Request;

class Router {

	/**
	 * All of the routes available to the router.
	 *
	 * @var array
	 */
	public $routes;

	/**
	 * The current request instance.
	 *
	 * @var Request
	 */
	protected $request;

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
	protected $controller_path;

	/**
	 * Create a new router for a request method and URI.
	 *
	 * @param  Request  $request
	 * @param  array    $routes
	 * @return void
	 */
	public function __construct(Request $request, $routes, $controller_path)
	{
		$this->routes = $routes;
		$this->request = $request;
		$this->controller_path = $controller_path;
	}

	/**
	 * Find a route by name.
	 *
	 * The returned array will be identical the array defined in the routes.php file.
	 *
	 * <code>
	 *		// Find the "login" named route
	 *		$route = $router->find('login');
	 *
	 *		// Find the "login" named route through the IoC container
	 *		$route = IoC::resolve('laravel.routing.router')->find('login');
	 * </code>
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function find($name)
	{
		if (array_key_exists($name, $this->names)) return $this->names[$name];

		$arrayIterator = new \RecursiveArrayIterator($this->routes);

		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

		foreach ($recursiveIterator as $iterator)
		{
			$route = $recursiveIterator->getSubIterator();

			if (isset($route['name']) and $route['name'] === $name)
			{
				return $this->names[$name] = array($arrayIterator->key() => iterator_to_array($route));
			}
		}
	}

	/**
	 * Search the routes for the route matching a method and URI.
	 *
	 * If no route can be found, the application controllers will be searched.
	 *
	 * @return Route
	 */
	public function route()
	{
		// Put the request method and URI in route form. Routes begin with
		// the request method and a forward slash.
		$destination = $this->request->method.' /'.trim($this->request->uri, '/');

		// Check for a literal route match first. If we find one, there is
		// no need to spin through all of the routes.
		if (isset($this->routes[$destination]))
		{
			return $this->request->route = new Route($destination, $this->routes[$destination], array(), $this->controller_path);
		}

		foreach ($this->routes as $keys => $callback)
		{
			// Only check routes that have multiple URIs or wildcards.
			// Other routes would have been caught by the check for literal matches.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				foreach (explode(', ', $keys) as $key)
				{
					if (preg_match('#^'.$this->translate_wildcards($key).'$#', $destination))
					{
						return $this->request->route = new Route($keys, $callback, $this->parameters($destination, $key), $this->controller_path);
					}
				}				
			}
		}

		return $this->route_to_controller();
	}

	/**
	 * Attempt to find a controller for the incoming request.
	 *
	 * If no corresponding controller can be found, NULL will be returned.
	 *
	 * @return Route
	 */
	protected function route_to_controller()
	{
		$segments = explode('/', trim($this->request->uri, '/'));

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

			// Now we're ready to dummy up a controller delegating route callback. This
			// callback will look exactly like the callback the developer would create
			// were they to code the controller delegation manually.
			$callback = function() use ($controller, $method) { return array($controller, $method); };

			return new Route($controller, $callback, $segments, $this->controller_path);
		}
	}

	/**
	 * Search the controllers for the application and determine if an applicable
	 * controller exists for the current request.
	 *
	 * If a controller is found, the array key for the controller name in the URI
	 * segments will be returned by the method, otherwise NULL will be returned.
	 *
	 * @param  array  $segments
	 * @return int
	 */
	protected function controller_key($segments)
	{
		// Work backwards through the URI segments until we find the deepest possible
		// matching controller. Once we find it, we will return those routes.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (file_exists($path = $this->controller_path.implode('/', array_slice($segments, 0, $key + 1)).EXT))
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
	protected function translate_wildcards($key)
	{
		$replacements = 0;

		// For optional parameters, first translate the wildcards to their
		// regex equivalent, sans the ")?" ending. We will add the endings
		// back on after we know how many replacements we made.
		$key = str_replace(array('/(:num?)', '/(:any?)'), array('(?:/([0-9]+)', '(?:/([a-zA-Z0-9\.\-_]+)'), $key, $replacements);

		$key .= ($replacements > 0) ? str_repeat(')?', $replacements) : '';

		return str_replace(array(':num', ':any'), array('[0-9]+', '[a-zA-Z0-9\.\-_]+'), $key);
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