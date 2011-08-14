<?php namespace System;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * If the specified key is null, the entire array will be returned. The array may
	 * also be accessed using JavaScript "dot" style notation. Retrieving items nested
	 * in multiple arrays is also supported.
	 *
	 * <code>
	 *		// Returns "taylor"
	 *		$item = Arr::get(array('name' => 'taylor'), 'name', $default);
	 *
	 *		// Returns "taylor"
	 *		$item = Arr::get(array('name' => array('is' => 'taylor')), 'name.is');
	 * </code>
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if (is_null($key)) return $array;

		foreach (explode('.', $key) as $segment)
		{
			if ( ! array_key_exists($segment, $array))
			{
				return is_callable($default) ? call_user_func($default) : $default;
			}

			$array = $array[$segment];
		}

		return $array;
	}

}