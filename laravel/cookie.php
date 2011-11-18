<?php namespace Laravel; use Closure;

if (trim(Config::$items['application']['key']) === '')
{
	throw new \LogicException('The cookie class may not be used without an application key.');
}

class Cookie {

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
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($name, $default = null)
	{
		$value = Arr::get($_COOKIE, $name);

		if ( ! is_null($value))
		{
			// All Laravel managed cookies are "signed" with a fingerprint hash.
			// The hash serves to verify that the contents of the cookie have not
			// been modified by the user. We can verify the integrity of the cookie
			// by extracting the value and re-hashing it, then comparing that hash
			// against the hash stored in the cookie.
			if (isset($value[40]) and $value[40] === '~')
			{
				list($hash, $value) = explode('~', $value, 2);

				if (static::hash($name, $value) === $hash)
				{
					return $value;
				}
			}
		}

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Set a "permanent" cookie. The cookie will last for one year.
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
		return static::put($name, $value, 525600, $path, $domain, $secure, $http_only);
	}

	/**
	 * Set the value of a cookie. 
	 *
	 * If a negative number of minutes is specified, the cookie will be deleted.
	 *
	 * This method's signature is very similar to the PHP setcookie method.
	 * However, you simply need to pass the number of minutes for which you
	 * wish the cookie to be valid. No funky time calculation is required.
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
		if (headers_sent()) return false;

		if ($minutes < 0)
		{
			unset($_COOKIE[$name]);
		}
		else
		{
			$_COOKIE[$name] = $value;
		}

		$time = ($minutes !== 0) ? time() + ($minutes * 60) : 0;

		$value = static::hash($name, $value).'~'.$value;

		return setcookie($name, $value, $time, $path, $domain, $secure, $http_only);
	}

	/**
	 * Generate a cookie hash.
	 *
	 * Cookie salts are used to verify that the contents of the cookie have not
	 * been modified by the user, since they serve as a fingerprint of the cookie
	 * contents. The application key is used to salt the salts.
	 *
	 * When the cookie is read using the "get" method, the value will be extracted
	 * from the cookie and hashed, if the hash in the cookie and the hashed value
	 * do not match, we know the cookie has been changed on the client.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @return string
	 */
	protected static function hash($name, $value)
	{
		return sha1($name.$value.Config::$items['application']['key']);
	}

	/**
	 * Delete a cookie.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function forget($name)
	{
		return static::put($name, null, -2000);
	}

}