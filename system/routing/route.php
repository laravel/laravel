<?php namespace System\Routing;

use System\Response;

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
	 * Execute the route function.
	 *
	 * @param  mixed     $route
	 * @param  array     $parameters
	 * @return Response
	 */
	public function call()
	{
		$response = null;

		// The callback may be in array form, meaning it has attached filters or is named.
		// However, the callback may also simply be a closure. If it is just a closure,
		// we can execute it here. Otherwise, we will need to evaluate the route for any
		// filters that need to be called.
		if (is_callable($this->callback))
		{
			$response = call_user_func_array($this->callback, $this->parameters);
		}
		elseif (is_array($this->callback))
		{
			$response = isset($this->callback['before']) ? Filter::call($this->callback['before'], array(), true) : null;

			if (is_null($response) and ! is_null($handler = $this->find_route_function()))
			{
				$response = call_user_func_array($handler, $this->parameters);
			}
		}

		$response = Response::prepare($response);

		if (is_array($this->callback) and isset($this->callback['after']))
		{
			Filter::call($this->callback['after'], array($response));
		}

		return $response;
	}

	/**
	 * Extract the route function from the route.
	 *
	 * If a "do" index is specified on the callback, that is the handler.
	 * Otherwise, we will return the first callable array value.
	 *
	 * @return Closure
	 */
	private function find_route_function()
	{
		if (isset($this->callback['do'])) return $this->callback['do'];

		foreach ($this->callback as $value)
		{
			if (is_callable($value)) return $value;
		}
	}

}