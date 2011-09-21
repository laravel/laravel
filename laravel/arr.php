<?php namespace Laravel;

use Closure;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * This method supports accessing arrays through JavaScript "dot" style syntax
	 * for conveniently digging deep into nested arrays. Like most other Laravel
	 * "get" methods, a default value may be provided.
	 *
	 * <code>
	 *		// Get the value of $array['user']['name']
	 *		$value = Arr::get($array, 'user.name');
	 *
	 *		// Get a value from the array, but return a default if it doesn't exist
	 *		$value = Arr::get($array, 'user.name', 'Taylor');
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
			if ( ! is_array($array) or ! array_key_exists($segment, $array))
			{
				return ($default instanceof Closure) ? call_user_func($default) : $default;
			}

			$array = $array[$segment];
		}

		return $array;
	}

	/**
	 * Set an array item to a given value.
	 *
	 * This method supports accessing arrays through JavaScript "dot" style syntax
	 * for conveniently digging deep into nested arrays.
	 *
	 * <code>
	 *		// Set the $array['user']['name'] value in the array
	 *		Arr::set($array, 'user.name', 'Taylor');
	 *
	 *		// Set the $array['db']['driver']['name'] value in the array
	 *		Arr::set($array, 'db.driver.name', 'SQLite');
	 * </code>
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set(&$array, $key, $value)
	{
		if (is_null($key)) return $array = $value;

		$keys = explode('.', $key);

		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			if ( ! isset($array[$key]) or ! is_array($array[$key]))
			{
				$array[$key] = array();
			}

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;
	}

	/**
	 * Return the first element in an array which passes a given truth test.
	 *
	 * The truth test is passed as a closure, and simply returns true or false.
	 * The array key and value will be passed to the closure on each iteration.
	 *
	 * Like the "get" method, a default value may be specified, and will be
	 * returned if no matching array elements are found by the method.
	 *
	 * <code>
	 *		// Get the first string from an array with a length of 3
	 *		$value = Arr::first($array, function($k, $v) {return strlen($v) == 3;});
	 *
	 *		// Return a default value if no matching array elements are found
	 *		$value = Arr::first($array, function($k, $v) {return;}, 'Default');
	 * </code>
	 *
	 * @param  array    $array
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public static function first($array, $callback, $default = null)
	{
		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value)) return $value;
		}

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Remove all values in the array that are contained within a given array of values.
	 *
	 * <code>
	 *		// Remove all empty string values from an array
	 *		$array = Arr::without($array, array(''));
	 *
	 *		// Remove all array values that are "3", "2", or "1"
	 *		$array = Arr::without($array, array(3, 2, 1));
	 * </code>
	 *
	 * @param  array  $array
	 * @param  array  $without
	 * @return array
	 */
	public static function without($array, $without = array())
	{
		foreach ($array as $key => $value)
		{
			if (in_array($value, $without)) unset($array[$key]);
		}

		return $array;
	}

}