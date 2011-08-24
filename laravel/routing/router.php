<?php namespace Laravel\Routing;

use Laravel\Request;

class Router {

	/**
	 * The request method and URI.
	 *
	 * @var string
	 */
	public $destination;

	/**
	 * All of the loaded routes.
	 *
	 * @var array
	 */
	public $routes;

	/**
	 * The current request instance.
	 *
	 * @var Request
	 */
	private $request;

	/**
	 * The route loader instance.
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * Create a new router for a request method and URI.
	 *
	 * @param  Request  $request
	 * @param  Loader   $loader
	 * @return void
	 */
	public function __construct(Request $request, Loader $loader)
	{
		$this->loader = $loader;
		$this->request = $request;

		// Put the request method and URI in route form. Routes begin with
		// the request method and a forward slash.
		$this->destination = $request->method().' /'.trim($request->uri(), '/');
	}

	/**
	 * Create a new router for a request method and URI.
	 *
	 * @param  Request  $request
	 * @param  Loader   $loader
	 * @return Router
	 */
	public static function make(Request $request, Loader $loader)
	{
		return new static($request, $loader);
	}

	/**
	 * Search a set of routes for the route matching a method and URI.
	 *
	 * @return Route
	 */
	public function route()
	{
		if (is_null($this->routes)) $this->routes = $this->loader->load($this->request->uri());

		// Check for a literal route match first. If we find one, there is
		// no need to spin through all of the routes.
		if (isset($this->routes[$this->destination]))
		{
			return $this->request->route = new Route($this->destination, $this->routes[$this->destination]);
		}

		foreach ($this->routes as $keys => $callback)
		{
			// Only check routes that have multiple URIs or wildcards.
			// Other routes would have been caught by the check for literal matches.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				foreach (explode(', ', $keys) as $key)
				{
					if (preg_match('#^'.$this->translate_wildcards($key).'$#', $this->destination))
					{
						return $this->request->route = new Route($keys, $callback, $this->parameters($this->destination, $key));
					}
				}				
			}
		}
	}

	/**
	 * Translate route URI wildcards into actual regular expressions.
	 *
	 * @param  string  $key
	 * @return string
	 */
	private function translate_wildcards($key)
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
	private function parameters($uri, $route)
	{
		return array_values(array_intersect_key(explode('/', $uri), preg_grep('/\(.+\)/', explode('/', $route))));	
	}	

}