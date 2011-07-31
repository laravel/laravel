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

		if (is_callable($this->callback))
		{
			$response = call_user_func_array($this->callback, $this->parameters);
		}
		elseif (is_array($this->callback))
		{
			$response = isset($this->callback['before']) ? Filter::call($this->callback['before'], array(), true) : null;

			if (is_null($response) and isset($this->callback['do']))
			{
				$response = call_user_func_array($this->callback['do'], $this->parameters);
			}
		}

		$response = Response::prepare($response);

		if (is_array($this->callback) and isset($this->callback['after']))
		{
			Filter::call($this->callback['after'], array($response));
		}

		return $response;
	}

}