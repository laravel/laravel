<?php namespace Laravel\Routing;

class Route {

	/**
	 * The route key, including request method and URI.
	 *
	 * @var string
	 */
	public $key;

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
	 * @param  string  $key
	 * @param  mixed   $callback
	 * @param  array   $parameters
	 * @return void
	 */
	public function __construct($key, $callback, $parameters = array())
	{
		$this->key = $key;
		$this->callback = $callback;
		$this->parameters = $parameters;
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
	 * <code>
	 *		// Get all of the "before" filters defined for the route.
	 *		$filters = $route->filters('before');
	 * </code>
	 *
	 * @param  string  $name
	 * @return array
	 */
	private function filters($name)
	{
		return (is_array($this->callback) and isset($this->callback[$name])) ? explode(', ', $this->callback[$name]) : array();
	}

}