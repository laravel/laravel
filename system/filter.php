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
	 * @param  bool    $override
	 * @return mixed
	 */
	public static function call($filters, $parameters = array(), $override = false)
	{
		// --------------------------------------------------------------
		// Load the filters if necessary.
		// --------------------------------------------------------------
		if (is_null(static::$filters))
		{
			static::$filters = require APP_PATH.'filters'.EXT;
		}

		// --------------------------------------------------------------
		// Filters can be comma-delimited, so spin through each one.
		// --------------------------------------------------------------
		foreach (explode(', ', $filters) as $filter)
		{
			if ( ! isset(static::$filters[$filter]))
			{
				throw new \Exception("Route filter [$filter] is not defined.");						
			}

			$response = call_user_func_array(static::$filters[$filter], $parameters);

			// --------------------------------------------------------------
			// If overriding is set to true and the filter returned a
			// response, return that response. 
			//
			// Overriding allows for convenient halting of the request
			// flow for things like authentication, CSRF protection, etc.
			// --------------------------------------------------------------
			if ( ! is_null($response) and $override)
			{
				return $response;
			}
		}
	}

}