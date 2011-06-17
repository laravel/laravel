<?php namespace System;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  array   $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if (is_null($key))
		{
			return $array;
		}

		return (array_key_exists($key, $array)) ? $array[$key] : $default;
	}

}