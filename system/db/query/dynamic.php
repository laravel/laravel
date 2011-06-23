<?php namespace System\DB\Query;

use System\Str;

class Dynamic {

	/**
	 * Add conditions to a query from a dynamic method call.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @param  Query   $query
	 * @return Query
	 */
	public static function build($method, $parameters, $query)
	{
		// ---------------------------------------------------------
		// Strip the "where_" off of the method.
		// ---------------------------------------------------------
		$method = substr($method, 6);

		// ---------------------------------------------------------
		// Split the column names from the connectors.
		// ---------------------------------------------------------
		$segments = preg_split('/(_and_|_or_)/i', $method, -1, PREG_SPLIT_DELIM_CAPTURE);

		// ---------------------------------------------------------
		// The connector variable will determine which connector
		// will be used for the condition. We'll change it as we
		// come across new connectors in the dynamic method string.
		//
		// The index variable helps us get the correct parameter
		// value for the where condition. We increment it each time
		// we add a condition.
		// ---------------------------------------------------------
		$connector = 'AND';

		$index = 0;

		// ---------------------------------------------------------
		// Iterate through each segment and add the conditions.
		// ---------------------------------------------------------
		foreach ($segments as $segment)
		{
			if ($segment != '_and_' and $segment != '_or_')
			{
				$query->where($segment, '=', $parameters[$index], $connector);

				$index++;
			}
			else
			{
				$connector = trim(Str::upper($segment), '_');
			}
		}

		return $query;
	}

}