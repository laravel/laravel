<?php namespace Laravel\Routing;

class Filterer {

	/**
	 * All of the route filters for the application.
	 *
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Create a new route filterer instance.
	 *
	 * @param  array  $filters
	 * @return void
	 */
	public function __construct($filters)
	{
		$this->filters = $filters;
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  array  $filters
	 * @param  array  $parameters
	 * @param  bool   $override
	 * @return mixed
	 */
	public function filter($filters, $parameters = array(), $override = false)
	{
		foreach ((array) $filters as $filter)
		{
			if ( ! isset($this->filters[$filter])) continue;

			$response = call_user_func_array($this->filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example, an authentication
			// filter may redirect a user to a login view if they are not logged in. Because of
			// this, we will return the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override) return $response;
		}
	}

}