<?php

/**
 * Convert HTML characters to entities.
 *
 * The encoding specified in the application configuration file will be used.
 *
 * @param  string  $value
 * @return string
 */
function e($value)
{
	return Laravel\HTML::entities($value);
}

/**
 * Retrieve a language line.
 *
 * @param  string  $key
 * @param  array   $replacements
 * @param  string  $language
 * @return string
 */
function __($key, $replacements = array(), $language = null)
{
	return Laravel\Lang::line($key, $replacements, $language);
}

/**a
 * Get an item from an array using "dot" notation.
 *
 * <code>
 *		// Get the $array['user']['name'] value from the array
 *		$name = array_get($array, 'user.name');
 *
 *		// Return a default from if the specified item doesn't exist
 *		$name = array_get($array, 'user.name', 'Taylor');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
	if (is_null($key)) return $array;

	// To retrieve the array item using dot syntax, we'll iterate through
	// each segment in the key and look for that value. If it exists, we
	// will return it, otherwise we will set the depth of the array and
	// look for the next segment.
	foreach (explode('.', $key) as $segment)
	{
		if ( ! is_array($array) or ! array_key_exists($segment, $array))
		{
			return value($default);
		}

		$array = $array[$segment];
	}

	return $array;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * <code>
 *		// Set the $array['user']['name'] value on the array
 *		array_set($array, 'user.name', 'Taylor');
 *
 *		// Set the $array['user']['name']['first'] value on the array
 *		array_set($array, 'user.name.first', 'Michael');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return void
 */
function array_set(&$array, $key, $value)
{
	if (is_null($key)) return $array = $value;

	$keys = explode('.', $key);

	// This loop allows us to dig down into the array to a dynamic depth by
	// setting the array value for each level that we dig into. Once there
	// is one key left, we can fall out of the loop and set the value as
	// we should be at the proper depth within the array.
	while (count($keys) > 1)
	{
		$key = array_shift($keys);

		// If the key doesn't exist at this depth, we will just create an
		// empty array to hold the next value, allowing us to create the
		// arrays to hold the final value at the proper depth.
		if ( ! isset($array[$key]) or ! is_array($array[$key]))
		{
			$array[$key] = array();
		}

		$array =& $array[$key];
	}

	$array[array_shift($keys)] = $value;
}

/**
 * Remove an array item from a given array using "dot" notation.
 *
 * <code>
 *		// Remove the $array['user']['name'] item from the array
 *		array_forget($array, 'user.name');
 *
 *		// Remove the $array['user']['name']['first'] item from the array
 *		array_forget($array, 'user.name.first');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @return void
 */
function array_forget(&$array, $key)
{
	$keys = explode('.', $key);

	// This loop functions very similarly to the loop in the "set" method.
	// We will iterate over the keys, setting the array value to the new
	// depth at each iteration. Once there is only one key left, we will
	// be at the proper depth in the array to "forget" the value.
	while (count($keys) > 1)
	{
		$key = array_shift($keys);

		// Since this method is supposed to remove a value from the array,
		// if a value higher up in the chain doesn't exist, there is no
		// need to keep digging into the array, since it is impossible
		// for the final value to even exist in the array.
		if ( ! isset($array[$key]) or ! is_array($array[$key]))
		{
			return;
		}

		$array =& $array[$key];
	}

	unset($array[array_shift($keys)]);
}

/**
 * Return the first element in an array which passes a given truth test.
 *
 * <code>
 *		// Return the first array element that equals "Taylor"
 *		$value = array_first($array, function($k, $v) {return $v == 'Taylor';});
 *
 *		// Return a default value if no matching element is found
 *		$value = array_first($array, function($k, $v) {return $v == 'Taylor'}, 'Default');
 * </code>
 *
 * @param  array    $array
 * @param  Closure  $callback
 * @param  mixed    $default
 * @return mixed
 */
function array_first($array, $callback, $default = null)
{
	foreach ($array as $key => $value)
	{
		if (call_user_func($callback, $key, $value)) return $value;
	}

	return value($default);
}

/**
 * Spin through the array, executing a callback with each key and element.
 *
 * @param  array  $array
 * @param  mixed  $callback
 * @return array
 */
function array_spin($array, $callback)
{
	return array_map($callback, array_keys($array), array_values($array));
}

/**
 * Recursively remove slashes from array keys and values.
 *
 * @param  array  $array
 * @return array
 */
function array_strip_slashes($array)
{
	$result = array();

	foreach($array as $key => $value)
	{
		$key = stripslashes($key);

		// If the value is an array, we will just recurse back into the
		// function to keep stripping the slashes out of the array,
		// otherwise we will set the stripped value.
		if (is_array($value))
		{
			$result[$key] = array_strip_slashes($value);
		}
		else
		{
			$result[$key] = stripslashes($value);
		}
	}

	return $result;
}

/**
 * Divide an array into two arrays. One with keys and the other with values.
 *
 * @param  array  $array
 * @return array
 */
function array_divide($array)
{
	return array(array_keys($array), array_values($array));
}

/**
 * Determine if "Magic Quotes" are enabled on the server.
 *
 * @return bool
 */
function magic_quotes()
{
	return function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc();
}

/**
 * Return the first element of an array.
 *
 * This is simply a convenient wrapper around the "reset" method.
 *
 * @param  array  $array
 * @return mixed
 */
function head($array)
{
	return reset($array);
}

/**
 * Generate an application URL.
 *
 * <code>
 *		// Create a URL to a location within the application
 *		$url = path('user/profile');
 *
 *		// Create a HTTPS URL to a location within the application
 *		$url = path('user/profile', true);
 * </code>
 *
 * @param  string  $url
 * @param  bool    $https
 * @return string
 */
function url($url = '', $https = false)
{
	return Laravel\URL::to($url, $https);
}

/**
 * Generate an application URL to an asset.
 *
 * @param  string  $url
 * @param  bool    $https
 * @return string
 */
function asset($url, $https = false)
{
	return Laravel\URL::to_asset($url, $https);
}

/**
 * Generate a URL to a controller action.
 *
 * <code>
 *		// Generate a URL to the "index" method of the "user" controller
 *		$url = action('user@index');
 *
 *		// Generate a URL to http://example.com/user/profile/taylor
 *		$url = action('user@profile', array('taylor'));
 * </code>
 *
 * @param  string  $action
 * @param  array   $parameters
 * @param  bool    $https
 * @return string
 */
function action($action, $parameters = array(), $https = false)
{
	return Laravel\URL::to_action($action, $parameters, $https);
}

/**
 * Generate a URL from a route name.
 *
 * <code>
 *		// Create a URL to the "profile" named route
 *		$url = route('profile');
 *
 *		// Create a URL to the "profile" named route with wildcard parameters
 *		$url = route('profile', array($username));
 * </code>
 *
 * @param  string  $name
 * @param  array   $parameters
 * @param  bool    $https
 * @return string
 */
function route($name, $parameters = array(), $https = false)
{
	return Laravel\URL::to_route($name, $parameters, $https);
}

/**
 * Determine if a given string begins with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function starts_with($haystack, $needle)
{
	return strpos($haystack, $needle) === 0;
}

/**
 * Determine if a given string contains a given sub-string.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function str_contains($haystack, $needle)
{
	return strpos($haystack, $needle) !== false;
}

/**
 * Return the value of the given item.
 *
 * If the given item is a Closure the result of the Closure will be returned.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
{
	return ($value instanceof Closure) ? call_user_func($value) : $value;
}