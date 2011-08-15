<?php namespace System;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * If the specified key is null, the entire array will be returned. The array may
	 * also be accessed using JavaScript "dot" style notation. Retrieving items nested
	 * in multiple arrays is also supported.
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

	/**
	 * Set an item in an array.
	 *
	 * This method is primarly helpful for setting the value in an array with
	 * a variable depth, such as configuration arrays.
	 *
	 * Like the Arr::get method, JavaScript "dot" syntax is supported.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set(&$array, $key, $value)
	{
		$reference =& $array;

		foreach (explode('.', $key) as $segment)
		{
			if ( ! isset($reference[$segment]))
			{
				$reference[$segment] = $value;
				
				return;		
			}

			$reference =& $reference[$segment];
		}

		$reference = $value;
	}

}