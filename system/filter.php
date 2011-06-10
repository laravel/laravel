<?php namespace System;

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
	 * @return mixed
	 */
	public static function call($filters, $parameters = array())
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

			// --------------------------------------------------------------
			// If the filter returned a response, return it since route
			// filters can override route methods.
			// --------------------------------------------------------------
			if ( ! is_null($response))
			{
				return $response;
			}
		}
	}

}