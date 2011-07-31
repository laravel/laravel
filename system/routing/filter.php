<?php namespace System\Routing;

class Filter {

	/**
	 * The loaded route filters.
	 *
	 * @var array
	 */
	public static $filters;

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
		if (is_null(static::$filters))
		{
			static::$filters = require APP_PATH.'filters'.EXT;
		}

		foreach (explode(', ', $filters) as $filter)
		{
			if ( ! isset(static::$filters[$filter]))
			{
				throw new \Exception("Route filter [$filter] is not defined.");						
			}

			$response = call_user_func_array(static::$filters[$filter], $parameters);

			if ( ! is_null($response) and $override)
			{
				return $response;
			}
		}
	}

}