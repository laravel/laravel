<?php namespace Laravel\Routing;

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
	 * The route filters for the application.
	 *
	 * @param  array  $filters
	 */
	public $filters = array();

	/**
	 * Create a new Route instance.
	 *
	 * @param  string   $key
	 * @param  mixed    $callback
	 * @param  array    $parameters
	 * @return void
	 */
	public function __construct($key, $callback, $parameters)
	{
		$this->key = $key;
		$this->callback = $callback;
		$this->parameters = $parameters;

		// Extract each URI handled by the URI. These will be used to find the route by
		// URI when requested. The leading slash will be removed for convenience.
		foreach (explode(', ', $key) as $segment)
		{
			$segment = substr($segment, strpos($segment, ' ') + 1);

			$this->uris[] = ($segment !== '/') ? trim($segment, '/') : $segment;
		}
	}

	/**
	 * Get all of the "before" filters defined for the route.
	 *
	 * @return array
	 */
	public function before()
	{
		return $this->filters('before');
	}

	/**
	 * Get all of the "after" filters defined for the route.
	 *
	 * @return array
	 */
	public function after()
	{
		return $this->filters('after');
	}

	/**
	 * Get an array of filters defined for the route.
	 *
	 * @param  string  $name
	 * @return array
	 */
	private function filters($name)
	{
		return (is_array($this->callback) and isset($this->callback[$name])) ? explode(', ', $this->callback[$name]) : array();
	}

}