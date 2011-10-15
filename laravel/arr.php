<?php namespace Laravel; use Closure;

class Arr {

	/**
	 * Get an item from an array.
	 *
	 * "Dot" notation may be used to dig deep into the array.
	 *
	 * <code>
	 *		// Get the $array['user']['name'] value from the array
	 *		$name = Arr::get($array, 'user.name');
	 *
	 *		// Return a default from if the specified item doesn't exist
	 *		$name = Arr::get($array, 'user.name', 'Taylor');
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
	 * The same "dot" syntax used by the "get" method may be used here.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * <code>
	 *		// Set the $array['user']['name'] value on the array
	 *		Arr::set($array, 'user.name', 'Taylor');
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
	 * <code>
	 *		// Return the first array element that equals "Taylor"
	 *		$value = Arr::first($array, function($k, $v) {return $v === 'Taylor';});
	 *
	 *		// Return a default value if no matching element is found
	 *		$value = Arr::first($array, function($k, $v) {return $v === 'Taylor'}, 'Default');
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
	 * Remove all array values that are contained within a given array of values.
	 *
	 * <code>
	 *		// Remove all array values that are empty strings
	 *		$array = Arr::without($array, '');
	 *
	 *		// Remove all array values that are "One", "Two", or "Three"
	 *		$array = Arr::without($array, array('One', 'Two', 'Three'));
	 * </code>
	 *
	 * @param  array  $array
	 * @param  array  $without
	 * @return array
	 */
	public static function without($array, $without = array())
	{
		$without = (array) $without;

		foreach ((array) $array as $key => $value)
		{
			if (in_array($value, $without)) unset($array[$key]);
		}

		return $array;
	}

}