<?php namespace System\Routing;

class Filter {

	/**
	 * The loaded route filters.
	 *
	 * @var array
	 */
	private static $filters = array();

	/**
	 * Register a set of route filters.
	 *
	 * @param  array  $filters
	 * @return void
	 */
	public static function register($filters)
	{
		static::$filters = array_merge(static::$filters, $filters);
	}

	/**
	 * Clear all of the registered route filters.
	 *
	 * @return void
	 */
	public static function clear()
	{
		static::$filters = array();
	}

	/**
	 * Call a set of route filters.
	 *
	 * @param  string  $filter
	 * @param  array   $parameters
	 * @param  bool    $override
	 * @return mixed
	 */
	public static function call($filters, $parameters = array(), $override = false)
	{
		foreach (explode(', ', $filters) as $filter)
		{
			if ( ! isset(static::$filters[$filter])) continue;

			$response = call_user_func_array(static::$filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example, an authentication
			// filter may redirect a user to a login view if they are not logged in. Because of
			// this, we will return the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override)
			{
				return $response;
			}
		}
	}

}