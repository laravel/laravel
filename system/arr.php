<?php namespace System;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @param  array   $array
	 * @return mixed
	 */
	public static function get($key, $default = null, $array = array())
	{
		if (is_null($key))
		{
			return $array;
		}

		return (array_key_exists($key, $array)) ? $array[$key] : $default;
	}

}