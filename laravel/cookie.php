<?php namespace Laravel;

class Cookie {

	/**
	 * The cookies that will be sent to the browser at the end of the request.
	 *
	 * @var array
	 */
	protected static $queue = array();

	/**
	 * Determine if a cookie exists.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function has($name)
	{
		return ! is_null(static::get($name));
	}

	/**
	 * Get the value of a cookie.
	 *
	 * <code>
	 *		// Get the value of a cookie
	 *		$value = Cookie::get('color');
	 *
	 *		// Get the value of a cookie or return a default value
	 *		$value = Cookie::get('color', 'blue');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($name, $default = null)
	{
		return Arr::get($_COOKIE, $name, $default);
	}

	/**
	 * Set a "permanent" cookie. The cookie will last 5 years.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $http_only
	 * @return bool
	 */
	public static function forever($name, $value, $path = '/', $domain = null, $secure = false, $http_only = false)
	{
		return static::put($name, $value, 2628000, $path, $domain, $secure, $http_only);
	}

	/**
	 * Set the value of a cookie. 
	 *
	 * If a negative number of minutes is specified, the cookie will be deleted.
	 *
	 * Note: This method's signature is very similar to the PHP setcookie method.
	 *       However, you simply need to pass the number of minutes for which you
	 *       wish the cookie to be valid. No funky time calculation is required.
	 *
	 * <code>
	 *		// Create a cookie that exists until the user closes their browser
	 *		Cookie::put('color', 'blue');
	 *
	 *		// Create a cookie that exists for 5 minutes
	 *		Cookie::put('name', 'blue', 5);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $http_only
	 * @return bool
	 */
	public static function put($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $http_only = false)
	{
		if ($minutes < 0) unset($_COOKIE[$name]);

		$time = ($minutes != 0) ? time() + ($minutes * 60) : 0;

		static::$queue[] = compact('name', 'value', 'time', 'path', 'domain', 'secure', 'http_only');
	}

	/**
	 * Send all of the cookies in the queue to the browser.
	 *
	 * This method is called automatically at the end of every request.
	 *
	 * @return void
	 */
	public static function send()
	{
		foreach (static::$queue as $cookie)
		{
			call_user_func_array('setcookie', $cookie);
		}
	}

	/**
	 * Delete a cookie.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function forget($name)
	{
		return static::put($name, null, -60);
	}

}