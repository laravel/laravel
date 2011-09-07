<?php namespace Laravel\Routing;

use Closure;
use Laravel\Container;

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
	 * The parameters that will passed to the route function.
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
	 * Call the route closure.
	 *
	 * If no closure is defined for the route, null will be returned.
	 *
	 * @return mixed
	 */
	public function call()
	{
		if (is_null($closure = $this->find_closure())) return;

		return call_user_func_array($closure, $this->parameters);
	}

	/**
	 * Extract the route closure from the route.
	 *
	 * @return Closure|null
	 */
	protected function find_closure()
	{
		if ($this->callback instanceof Closure) return $this->callback;

		if (isset($this->callback['do'])) return $this->callback['do'];

		foreach ($this->callback as $value) { if ($value instanceof Closure) return $value; }
	}

	/**
	 * Get an array of filter names defined for a route.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name)
	{
		return (is_array($this->callback) and isset($this->callback[$name])) ? explode(', ', $this->callback[$name]) : array();
	}

	/**
	 * Determine if the route handling has a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function is($name)
	{
		if ( ! is_array($this->callback) or ! isset($this->callback['name'])) return false;

		return $this->callback['name'] === $name;
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
	 * Parse the route key and return an array of URIs the route responds to.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function parse_uris($key)
	{
		if (strpos($key, ', ') === false) return array($this->extract_uri($key));

		foreach (explode(', ', $key) as $segment)
		{
			$uris[] = $this->extract_uri($segment);
		}

		return $uris;
	}

	/**
	 * Extract the URI from a route destination.
	 *
	 * Route destinations include the request method the route responds to, so this method
	 * will only remove it from the string. Unless the URI is root, the forward slash will
	 * be removed to make searching the URIs more convenient.
	 *
	 * @param  string  $segment
	 * @return string
	 */
	protected function extract_uri($segment)
	{
		$segment = substr($segment, strpos($segment, ' ') + 1);

		return ($segment !== '/') ? trim($segment, '/') : $segment;
	}

	/**
	 * Magic Method to handle dynamic method calls to determine the name of the route.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'is_') === 0) { return $this->is(substr($method, 3)); }
	}

}