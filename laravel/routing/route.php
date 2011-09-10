<?php namespace Laravel\Routing;

use Closure;

class Route {

	/**
	 * The route key, including request method and URI.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The URIs the route responds to.
	 *
	 * @var array
	 */
	public $uris;

	/**
	 * The route callback or array.
	 *
	 * @var mixed
	 */
	public $callback;

	/**
	 * The parameters that will passed to the route callback.
	 *
	 * @var array
	 */
	public $parameters;

	/**
	 * Create a new Route instance.
	 *
	 * @param  string   $key
	 * @param  mixed    $callback
	 * @param  array    $parameters
	 * @return void
	 */
	public function __construct($key, $callback, $parameters = array())
	{
		$this->key = $key;
		$this->callback = $callback;
		$this->parameters = $parameters;
		$this->uris = $this->parse_uris($key);
	}

	/**
	 * Parse the route key and return an array of URIs the route responds to.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function parse_uris($key)
	{
		if (strpos($key, ', ') === false) return array($this->extract_uri($key));

		// The extractor closure will retrieve the URI from a given route destination.
		// If the request is to the root of the application, a single forward slash
		// will be returned, otherwise the leading slash will be removed.
		$extractor = function($segment)
		{
			$segment = substr($segment, strpos($segment, ' ') + 1);

			return ($segment !== '/') ? trim($segment, '/') : $segment;
		};

		return array_map(function($segment) use ($extractor) { return $extractor($segment); }, explode(', ', $key));
	}

	/**
	 * Call the route closure.
	 *
	 * If no closure is defined for the route, null will be returned.
	 *
	 * @return mixed
	 */
	public function call()
	{
		return ( ! is_null($closure = $this->closure())) ? call_user_func_array($closure, $this->parameters) : null;
	}

	/**
	 * Extract the route closure from the route.
	 *
	 * @return Closure|null
	 */
	protected function closure()
	{
		if ($this->callback instanceof Closure) return $this->callback;

		foreach ($this->callback as $value) { if ($value instanceof Closure) return $value; }
	}

	/**
	 * Get an array of filter names defined for the route.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name)
	{
		return (is_array($this->callback) and isset($this->callback[$name])) ? explode(', ', $this->callback[$name]) : array();
	}

	/**
	 * Determine if the route has a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function is($name)
	{
		return (is_array($this->callback) and isset($this->callback['name'])) ? $this->callback['name'] === $name : false;
	}

	/**
	 * Determine if the route handles a given URI.
	 *
	 * @param  string  $uri
	 * @return bool
	 */
	public function handles($uri)
	{
		return in_array($uri, $this->uris);
	}

	/**
	 * Magic Method to handle dynamic method calls to determine the name of the route.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'is_') === 0) { return $this->is(substr($method, 3)); }
	}

}