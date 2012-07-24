<?php namespace Laravel;

class Cookie {

	/**
	 * How long is forever (in minutes).
	 *
	 * @var int
	 */
	const forever = 525600;

	/**
	 * The cookies that have been set.
	 *
	 * @var array
	 */
	public static $jar = array();

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
	 *		// Get the value of the "favorite" cookie
	 *		$favorite = Cookie::get('favorite');
	 *
	 *		// Get the value of a cookie or return a default value
	 *		$favorite = Cookie::get('framework', 'Laravel');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($name, $default = null)
	{
		if (isset(static::$jar[$name])) return static::$jar[$name]['value'];

		return array_get(Request::foundation()->cookies->all(), $name, $default);
	}

	/**
	 * Set the value of a cookie.
	 *
	 * <code>
	 *		// Set the value of the "favorite" cookie
	 *		Cookie::put('favorite', 'Laravel');
	 *
	 *		// Set the value of the "favorite" cookie for twenty minutes
	 *		Cookie::put('favorite', 'Laravel', 20);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $expiration
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @return void
	 */
	public static function put($name, $value, $expiration = 0, $path = '/', $domain = null, $secure = false)
	{
		if ($expiration !== 0)
		{
			$expiration = time() + ($expiration * 60);
		}

		// If the secure option is set to true, yet the request is not over HTTPS
		// we'll throw an exception to let the developer know that they are
		// attempting to send a secure cookie over the insecure HTTP.
		if ($secure and ! Request::secure())
		{
			throw new \Exception("Attempting to set secure cookie over HTTP.");
		}

		static::$jar[$name] = compact('name', 'value', 'expiration', 'path', 'domain', 'secure');
	}

	/**
	 * Set a "permanent" cookie. The cookie will last for one year.
	 *
	 * <code>
	 *		// Set a cookie that should last one year
	 *		Cookie::forever('favorite', 'Blue');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @return bool
	 */
	public static function forever($name, $value, $path = '/', $domain = null, $secure = false)
	{
		return static::put($name, $value, static::forever, $path, $domain, $secure);
	}

	/**
	 * Delete a cookie.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @return bool
	 */
	public static function forget($name, $path = '/', $domain = null, $secure = false)
	{
		return static::put($name, null, -2000, $path, $domain, $secure);
	}

}