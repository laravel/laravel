<?php namespace System;

class Cookie {

	/**
	 * Determine if a cookie exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return ( ! is_null(static::get($key)));
	}

	/**
	 * Get the value of a cookie.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($key, $default = null)
	{
		return (array_key_exists($key, $_COOKIE)) ? $_COOKIE[$key] : $default;
	}

	/**
	 * Set a "permanent" cookie. The cookie will last 5 years.
	 *
	 * @param  string   $key
	 * @param  string   $value
	 * @param  string   $path
	 * @param  string   $domain
	 * @param  bool     $secure
	 * @return bool
	 */
	public static function forever($key, $value, $path = '/', $domain = null, $secure = false)
	{
		return static::put($key, $value, 2628000, $path, $domain, $secure);
	}

	/**
	 * Set the value of a cookie.
	 *
	 * @param  string   $key
	 * @param  string   $value
	 * @param  int      $minutes
	 * @param  string   $path
	 * @param  string   $domain
	 * @param  bool     $secure
	 * @return bool
	 */
	public static function put($key, $value, $minutes = 0, $path = '/', $domain = null, $secure = false)
	{
		// ----------------------------------------------------------
		// If the lifetime is less than zero, delete the cookie.
		// ----------------------------------------------------------
		if ($minutes < 0)
		{
			unset($_COOKIE[$key]);
		}

		return setcookie($key, $value, ($minutes != 0) ? time() + ($minutes * 60) : 0, $path, $domain, $secure);
	}

	/**
	 * Delete a cookie.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function forget($key)
	{
		return static::put($key, null, -60);
	}

}