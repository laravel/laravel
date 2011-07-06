<?php namespace System;

class Arr {

	/**
	 * Get an item from an array.
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

}