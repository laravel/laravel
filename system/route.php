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

		// --------------------------------------------------------------
		// If the route just has a callback, call it.
		// --------------------------------------------------------------
		if (is_callable($this->route))
		{
			$response = call_user_func_array($this->route, $this->parameters);
		}
		// --------------------------------------------------------------
		// The route value is an array. We'll need to evaluate it.
		// --------------------------------------------------------------
		elseif (is_array($this->route))
		{
			// --------------------------------------------------------------
			// Call the "before" route filters.
			// --------------------------------------------------------------
			$response = isset($this->route['before']) ? Filter::call($this->route['before']) : null;

			// --------------------------------------------------------------
			// Call the route callback.
			// --------------------------------------------------------------
			if (is_null($response) and isset($this->route['do']))
			{
				$response = call_user_func_array($this->route['do'], $this->parameters);
			}
		}

		// --------------------------------------------------------------
		// Make sure the response is a Response instance.
		// --------------------------------------------------------------
		$response = ( ! $response instanceof Response) ? new Response($response) : $response;

		// --------------------------------------------------------------
		// Call the "after" route filters.
		// --------------------------------------------------------------
		if (is_array($this->route) and isset($this->route['after']))
		{
			Filter::call($this->route['after'], array($response));
		}

		return $response;
	}

}