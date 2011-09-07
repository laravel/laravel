<?php namespace Laravel;

class Cookie {

	/**
	 * All of the cookies for the current request.
	 *
	 * @var array
	 */
	private $cookies;

	/**
	 * Create a new cookie manager instance.
	 *
	 * @param  array  $cookies
	 * @return void
	 */
	public function __construct(&$cookies)
	{
		$this->cookies = &$cookies;
	}

	/**
	 * Determine if a cookie exists.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function has($name)
	{
		return ! is_null($this->get($name));
	}

	/**
	 * Get the value of a cookie.
	 *
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return string
	 */
	public function get($name, $default = null)
	{
		return Arr::get($this->cookies, $name, $default);
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
	public function forever($name, $value, $path = '/', $domain = null, $secure = false, $http_only = false)
	{
		return $this->put($name, $value, 2628000, $path, $domain, $secure, $http_only);
	}

	/**
	 * Set the value of a cookie. If a negative number of minutes is
	 * specified, the cookie will be deleted.
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
	public function put($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $http_only = false)
	{
		if ($minutes < 0) unset($_COOKIE[$name]);

		return setcookie($name, $value, ($minutes != 0) ? time() + ($minutes * 60) : 0, $path, $domain, $secure, $http_only);
	}

	/**
	 * Delete a cookie.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function forget($name)
	{
		return $this->put($name, null, -60);
	}

}