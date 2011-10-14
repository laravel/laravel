<?php namespace Laravel\Routing;

class Filter {

	/**
	 * The route filters for the application.
	 *
	 * @var array
	 */
	protected static $filters = array();

	/**
	 * Register an array of route filters.
	 *
	 * @param  array  $filters
	 * @return void
	 */
	public static function register($filters)
	{
		static::$filters = array_merge(static::$filters, $filters);
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  array|string  $filters
	 * @param  array         $parameters
	 * @param  bool          $override
	 * @return mixed
	 */
	public static function run($filters, $parameters = array(), $override = false)
	{
		if (is_string($filters)) $filters = explode('|', $filters);

		foreach ((array) $filters as $filter)
		{
			// Parameters may be passed into routes by specifying the list of
			// parameters after a colon. If parameters are present, we will
			// merge them into the parameter array that was passed to the
			// method and slice the parameters off of the filter string.
			if (($colon = strpos($filter, ':')) !== false)
			{
				$parameters = array_merge($parameters, explode(',', substr($filter, $colon + 1)));

				$filter = substr($filter, 0, $colon);
			}

			if ( ! isset(static::$filters[$filter])) continue;

			$response = call_user_func_array(static::$filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example,
			// an authentication filter may redirect a user to a login view
			// if they are not logged in. Because of this, we will return
			// the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override) return $response;
		}
	}

}