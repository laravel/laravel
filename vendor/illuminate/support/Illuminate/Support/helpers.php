<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if ( ! function_exists('action'))
{
	/**
	 * Generate a URL to a controller action.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return string
	 */
	function action($name, $parameters = array())
	{
		return app('url')->action($name, $parameters);
	}
}

if ( ! function_exists('app'))
{
	/**
	 * Get the root Facade application instance.
	 *
	 * @param  string  $make
	 * @return mixed
	 */
	function app($make = null)
	{
		if ( ! is_null($make))
		{
			return app()->make($make);
		}

		return Illuminate\Support\Facades\Facade::getFacadeApplication();
	}
}

if ( ! function_exists('app_path'))
{
	/**
	 * Get the path to the application folder.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function app_path($path = '')
	{
		return app('path').($path ? '/'.$path : $path);
	}
}

if ( ! function_exists('append_config'))
{
	/**
	 * Assign high numeric IDs to a config item to force appending.
	 *
	 * @param  array  $array
	 * @return array
	 */
	function append_config(array $array)
	{
		$start = 9999;

		foreach ($array as $key => $value)
		{
			if (is_numeric($key))
			{
				$start++;

				$array[$start] = array_pull($array, $key);
			}
		}

		return $array;
	}
}

if ( ! function_exists('array_add'))
{
	/**
	 * Add an element to an array using "dot" notation if it doesn't exist.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array
	 */
	function array_add($array, $key, $value)
	{
		return Arr::add($array, $key, $value);
	}
}

if ( ! function_exists('array_build'))
{
	/**
	 * Build a new array using a callback.
	 *
	 * @param  array     $array
	 * @param  \Closure  $callback
	 * @return array
	 */
	function array_build($array, Closure $callback)
	{
		return Arr::build($array, $callback);
	}
}

if ( ! function_exists('array_divide'))
{
	/**
	 * Divide an array into two arrays. One with keys and the other with values.
	 *
	 * @param  array  $array
	 * @return array
	 */
	function array_divide($array)
	{
		return Arr::divide($array);
	}
}

if ( ! function_exists('array_dot'))
{
	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @param  array   $array
	 * @param  string  $prepend
	 * @return array
	 */
	function array_dot($array, $prepend = '')
	{
		return Arr::dot($array, $prepend);
	}
}

if ( ! function_exists('array_except'))
{
	/**
	 * Get all of the given array except for a specified array of items.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return array
	 */
	function array_except($array, $keys)
	{
		return Arr::except($array, $keys);
	}
}

if ( ! function_exists('array_fetch'))
{
	/**
	 * Fetch a flattened array of a nested array element.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @return array
	 */
	function array_fetch($array, $key)
	{
		return Arr::fetch($array, $key);
	}
}

if ( ! function_exists('array_first'))
{
	/**
	 * Return the first element in an array passing a given truth test.
	 *
	 * @param  array     $array
	 * @param  \Closure  $callback
	 * @param  mixed     $default
	 * @return mixed
	 */
	function array_first($array, $callback, $default = null)
	{
		return Arr::first($array, $callback, $default);
	}
}

if ( ! function_exists('array_last'))
{
	/**
	 * Return the last element in an array passing a given truth test.
	 *
	 * @param  array     $array
	 * @param  \Closure  $callback
	 * @param  mixed     $default
	 * @return mixed
	 */
	function array_last($array, $callback, $default = null)
	{
		return Arr::last($array, $callback, $default);
	}
}

if ( ! function_exists('array_flatten'))
{
	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @param  array  $array
	 * @return array
	 */
	function array_flatten($array)
	{
		return Arr::flatten($array);
	}
}

if ( ! function_exists('array_forget'))
{
	/**
	 * Remove one or many array items from a given array using "dot" notation.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return void
	 */
	function array_forget(&$array, $keys)
	{
		return Arr::forget($array, $keys);
	}
}

if ( ! function_exists('array_get'))
{
	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function array_get($array, $key, $default = null)
	{
		return Arr::get($array, $key, $default);
	}
}

if ( ! function_exists('array_has'))
{
	/**
	 * Check if an item exists in an array using "dot" notation.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @return bool
	 */
	function array_has($array, $key)
	{
		return Arr::has($array, $key);
	}
}

if ( ! function_exists('array_only'))
{
	/**
	 * Get a subset of the items from the given array.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return array
	 */
	function array_only($array, $keys)
	{
		return Arr::only($array, $keys);
	}
}

if ( ! function_exists('array_pluck'))
{
	/**
	 * Pluck an array of values from an array.
	 *
	 * @param  array   $array
	 * @param  string  $value
	 * @param  string  $key
	 * @return array
	 */
	function array_pluck($array, $value, $key = null)
	{
		return Arr::pluck($array, $value, $key);
	}
}

if ( ! function_exists('array_pull'))
{
	/**
	 * Get a value from the array, and remove it.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function array_pull(&$array, $key, $default = null)
	{
		return Arr::pull($array, $key, $default);
	}
}

if ( ! function_exists('array_set'))
{
	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array
	 */
	function array_set(&$array, $key, $value)
	{
		return Arr::set($array, $key, $value);
	}
}

if ( ! function_exists('array_sort'))
{
	/**
	 * Sort the array using the given Closure.
	 *
	 * @param  array     $array
	 * @param  \Closure  $callback
	 * @return array
	 */
	function array_sort($array, Closure $callback)
	{
		return Arr::sort($array, $callback);
	}
}

if ( ! function_exists('array_where'))
{
	/**
	 * Filter the array using the given Closure.
	 *
	 * @param  array     $array
	 * @param  \Closure  $callback
	 * @return array
	 */
	function array_where($array, Closure $callback)
	{
		return Arr::where($array, $callback);
	}
}

if ( ! function_exists('asset'))
{
	/**
	 * Generate an asset path for the application.
	 *
	 * @param  string  $path
	 * @param  bool    $secure
	 * @return string
	 */
	function asset($path, $secure = null)
	{
		return app('url')->asset($path, $secure);
	}
}

if ( ! function_exists('base_path'))
{
	/**
	 * Get the path to the base of the install.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function base_path($path = '')
	{
		return app()->make('path.base').($path ? '/'.$path : $path);
	}
}

if ( ! function_exists('camel_case'))
{
	/**
	 * Convert a value to camel case.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function camel_case($value)
	{
		return Str::camel($value);
	}
}

if ( ! function_exists('class_basename'))
{
	/**
	 * Get the class "basename" of the given object / class.
	 *
	 * @param  string|object  $class
	 * @return string
	 */
	function class_basename($class)
	{
		$class = is_object($class) ? get_class($class) : $class;

		return basename(str_replace('\\', '/', $class));
	}
}

if ( ! function_exists('class_uses_recursive'))
{
	/**
	 * Returns all traits used by a class, it's subclasses and trait of their traits
	 *
	 * @param  string  $class
	 * @return array
	 */
	function class_uses_recursive($class)
	{
		$results = [];

		foreach (array_merge([$class => $class], class_parents($class)) as $class)
		{
			$results += trait_uses_recursive($class);
		}

		return array_unique($results);
	}
}

if ( ! function_exists('csrf_token'))
{
	/**
	 * Get the CSRF token value.
	 *
	 * @return string
	 *
	 * @throws RuntimeException
	 */
	function csrf_token()
	{
		$session = app('session');

		if (isset($session))
		{
			return $session->getToken();
		}

		throw new RuntimeException("Application session store not set.");
	}
}

if ( ! function_exists('data_get'))
{
	/**
	 * Get an item from an array or object using "dot" notation.
	 *
	 * @param  mixed   $target
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function data_get($target, $key, $default = null)
	{
		if (is_null($key)) return $target;

		foreach (explode('.', $key) as $segment)
		{
			if (is_array($target))
			{
				if ( ! array_key_exists($segment, $target))
				{
					return value($default);
				}

				$target = $target[$segment];
			}
			elseif (is_object($target))
			{
				if ( ! isset($target->{$segment}))
				{
					return value($default);
				}

				$target = $target->{$segment};
			}
			else
			{
				return value($default);
			}
		}

		return $target;
	}
}

if ( ! function_exists('dd'))
{
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param  mixed
	 * @return void
	 */
	function dd()
	{
		array_map(function($x) { var_dump($x); }, func_get_args()); die;
	}
}

if ( ! function_exists('e'))
{
	/**
	 * Escape HTML entities in a string.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function e($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}
}

if ( ! function_exists('ends_with'))
{
	/**
	 * Determine if a given string ends with a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	function ends_with($haystack, $needles)
	{
		return Str::endsWith($haystack, $needles);
	}
}

if ( ! function_exists('head'))
{
	/**
	 * Get the first element of an array. Useful for method chaining.
	 *
	 * @param  array  $array
	 * @return mixed
	 */
	function head($array)
	{
		return reset($array);
	}
}

if ( ! function_exists('link_to'))
{
	/**
	 * Generate a HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	function link_to($url, $title = null, $attributes = array(), $secure = null)
	{
		return app('html')->link($url, $title, $attributes, $secure);
	}
}

if ( ! function_exists('last'))
{
	/**
	 * Get the last element from an array.
	 *
	 * @param  array  $array
	 * @return mixed
	 */
	function last($array)
	{
		return end($array);
	}
}

if ( ! function_exists('link_to_asset'))
{
	/**
	 * Generate a HTML link to an asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	function link_to_asset($url, $title = null, $attributes = array(), $secure = null)
	{
		return app('html')->linkAsset($url, $title, $attributes, $secure);
	}
}

if ( ! function_exists('link_to_route'))
{
	/**
	 * Generate a HTML link to a named route.
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	function link_to_route($name, $title = null, $parameters = array(), $attributes = array())
	{
		return app('html')->linkRoute($name, $title, $parameters, $attributes);
	}
}

if ( ! function_exists('link_to_action'))
{
	/**
	 * Generate a HTML link to a controller action.
	 *
	 * @param  string  $action
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	function link_to_action($action, $title = null, $parameters = array(), $attributes = array())
	{
		return app('html')->linkAction($action, $title, $parameters, $attributes);
	}
}

if ( ! function_exists('object_get'))
{
	/**
	 * Get an item from an object using "dot" notation.
	 *
	 * @param  object  $object
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function object_get($object, $key, $default = null)
	{
		if (is_null($key) || trim($key) == '') return $object;

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_object($object) || ! isset($object->{$segment}))
			{
				return value($default);
			}

			$object = $object->{$segment};
		}

		return $object;
	}
}

if ( ! function_exists('preg_replace_sub'))
{
	/**
	 * Replace a given pattern with each value in the array in sequentially.
	 *
	 * @param  string  $pattern
	 * @param  array   $replacements
	 * @param  string  $subject
	 * @return string
	 */
	function preg_replace_sub($pattern, &$replacements, $subject)
	{
		return preg_replace_callback($pattern, function($match) use (&$replacements)
		{
			return array_shift($replacements);

		}, $subject);
	}
}

if ( ! function_exists('public_path'))
{
	/**
	 * Get the path to the public folder.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function public_path($path = '')
	{
		return app()->make('path.public').($path ? '/'.$path : $path);
	}
}

if ( ! function_exists('route'))
{
	/**
	 * Generate a URL to a named route.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool  $absolute
	 * @param  \Illuminate\Routing\Route $route
	 * @return string
	 */
	function route($name, $parameters = array(), $absolute = true, $route = null)
	{
		return app('url')->route($name, $parameters, $absolute, $route);
	}
}

if ( ! function_exists('secure_asset'))
{
	/**
	 * Generate an asset path for the application.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function secure_asset($path)
	{
		return asset($path, true);
	}
}

if ( ! function_exists('secure_url'))
{
	/**
	 * Generate a HTTPS url for the application.
	 *
	 * @param  string  $path
	 * @param  mixed   $parameters
	 * @return string
	 */
	function secure_url($path, $parameters = array())
	{
		return url($path, $parameters, true);
	}
}

if ( ! function_exists('snake_case'))
{
	/**
	 * Convert a string to snake case.
	 *
	 * @param  string  $value
	 * @param  string  $delimiter
	 * @return string
	 */
	function snake_case($value, $delimiter = '_')
	{
		return Str::snake($value, $delimiter);
	}
}

if ( ! function_exists('starts_with'))
{
	/**
	 * Determine if a given string starts with a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	function starts_with($haystack, $needles)
	{
		return Str::startsWith($haystack, $needles);
	}
}

if ( ! function_exists('storage_path'))
{
	/**
	 * Get the path to the storage folder.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	function storage_path($path = '')
	{
		return app('path.storage').($path ? '/'.$path : $path);
	}
}

if ( ! function_exists('str_contains'))
{
	/**
	 * Determine if a given string contains a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	function str_contains($haystack, $needles)
	{
		return Str::contains($haystack, $needles);
	}
}

if ( ! function_exists('str_finish'))
{
	/**
	 * Cap a string with a single instance of a given value.
	 *
	 * @param  string  $value
	 * @param  string  $cap
	 * @return string
	 */
	function str_finish($value, $cap)
	{
		return Str::finish($value, $cap);
	}
}

if ( ! function_exists('str_is'))
{
	/**
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string  $pattern
	 * @param  string  $value
	 * @return bool
	 */
	function str_is($pattern, $value)
	{
		return Str::is($pattern, $value);
	}
}

if ( ! function_exists('str_limit'))
{
	/**
	 * Limit the number of characters in a string.
	 *
	 * @param  string  $value
	 * @param  int     $limit
	 * @param  string  $end
	 * @return string
	 */
	function str_limit($value, $limit = 100, $end = '...')
	{
		return Str::limit($value, $limit, $end);
	}
}

if ( ! function_exists('str_plural'))
{
	/**
	 * Get the plural form of an English word.
	 *
	 * @param  string  $value
	 * @param  int     $count
	 * @return string
	 */
	function str_plural($value, $count = 2)
	{
		return Str::plural($value, $count);
	}
}

if ( ! function_exists('str_random'))
{
	/**
	 * Generate a more truly "random" alpha-numeric string.
	 *
	 * @param  int  $length
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	function str_random($length = 16)
	{
		return Str::random($length);
	}
}

if ( ! function_exists('str_replace_array'))
{
	/**
	 * Replace a given value in the string sequentially with an array.
	 *
	 * @param  string  $search
	 * @param  array   $replace
	 * @param  string  $subject
	 * @return string
	 */
	function str_replace_array($search, array $replace, $subject)
	{
		foreach ($replace as $value)
		{
			$subject = preg_replace('/'.$search.'/', $value, $subject, 1);
		}

		return $subject;
	}
}

if ( ! function_exists('str_singular'))
{
	/**
	 * Get the singular form of an English word.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function str_singular($value)
	{
		return Str::singular($value);
	}
}

if ( ! function_exists('studly_case'))
{
	/**
	 * Convert a value to studly caps case.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function studly_case($value)
	{
		return Str::studly($value);
	}
}

if ( ! function_exists('trait_uses_recursive'))
{
	/**
	 * Returns all traits used by a trait and its traits
	 *
	 * @param  string  $trait
	 * @return array
	 */
	function trait_uses_recursive($trait)
	{
		$traits = class_uses($trait);

		foreach ($traits as $trait)
		{
			$traits += trait_uses_recursive($trait);
		}

		return $traits;
	}
}

if ( ! function_exists('trans'))
{
	/**
	 * Translate the given message.
	 *
	 * @param  string  $id
	 * @param  array   $parameters
	 * @param  string  $domain
	 * @param  string  $locale
	 * @return string
	 */
	function trans($id, $parameters = array(), $domain = 'messages', $locale = null)
	{
		return app('translator')->trans($id, $parameters, $domain, $locale);
	}
}

if ( ! function_exists('trans_choice'))
{
	/**
	 * Translates the given message based on a count.
	 *
	 * @param  string  $id
	 * @param  int     $number
	 * @param  array   $parameters
	 * @param  string  $domain
	 * @param  string  $locale
	 * @return string
	 */
	function trans_choice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
	{
		return app('translator')->transChoice($id, $number, $parameters, $domain, $locale);
	}
}

if ( ! function_exists('url'))
{
	/**
	 * Generate a url for the application.
	 *
	 * @param  string  $path
	 * @param  mixed   $parameters
	 * @param  bool    $secure
	 * @return string
	 */
	function url($path = null, $parameters = array(), $secure = null)
	{
		return app('url')->to($path, $parameters, $secure);
	}
}

if ( ! function_exists('value'))
{
	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	function value($value)
	{
		return $value instanceof Closure ? $value() : $value;
	}
}

if ( ! function_exists('with'))
{
	/**
	 * Return the given object. Useful for chaining.
	 *
	 * @param  mixed  $object
	 * @return mixed
	 */
	function with($object)
	{
		return $object;
	}
}
