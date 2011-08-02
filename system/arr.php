<?php namespace System;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * If the specified key is null, the entire array will be returned.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if (is_null($key))
		{
			return $array;
		}

		if (array_key_exists($key, $array))
		{
			return $array[$key];
		}

		return is_callable($default) ? call_user_func($default) : $default;
	}

	/**
	 * Get an item from an array using JavaScript style "dot" notation.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function dot($array, $key, $default = null)
	{
		foreach (explode('.', $key) as $segment)
		{
			if ( ! isset($array[$segment]))
			{
				return is_callable($default) ? call_user_func($default) : $default;
			}

			$array = $array[$segment];
		}

		return $array;
	}

}