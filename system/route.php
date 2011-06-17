<?php namespace System;

class Route {

	/**
	 * The route callback or array.
	 *
	 * @var mixed
	 */
	public $route;

	/**
	 * The parameters that will passed to the route function.
	 *
	 * @var array
	 */
	public $parameters;

	/**
	 * Create a new Route instance.
	 *
	 * @param  mixed  $route
	 * @param  array  $parameters
	 * @return void
	 */
	public function __construct($route, $parameters = array())
	{
		$this->route = $route;
		$this->parameters = $parameters;
	}

	/**
	 * Execute the route function.
	 *
	 * @param  mixed     $route
	 * @param  array     $parameters
	 * @return mixed
	 */
	public function call()
	{
		$response = null;

		// ------------------------------------------------------------
		// If the route value is just a function, all we have to do
		// is execute the function! There are no filters to call.
		// ------------------------------------------------------------
		if (is_callable($this->route))
		{
			$response = call_user_func_array($this->route, $this->parameters);
		}
		// ------------------------------------------------------------
		// If the route value is an array, we'll need to check it for
		// any filters that may be attached.
		// ------------------------------------------------------------
		elseif (is_array($this->route))
		{
			$response = isset($this->route['before']) ? Filter::call($this->route['before'], array(), true) : null;

			// ------------------------------------------------------------
			// We verify that the before filters did not return a response
			// Before filters can override the request cycle to make things
			// like authentication convenient to implement.
			// ------------------------------------------------------------
			if (is_null($response) and isset($this->route['do']))
			{
				$response = call_user_func_array($this->route['do'], $this->parameters);
			}
		}

		$response = Response::prepare($response);

		if (is_array($this->route) and isset($this->route['after']))
		{
			Filter::call($this->route['after'], array($response));
		}

		return $response;
	}

}