<?php namespace Illuminate\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

class CookieJar {

	/**
	 * The default path (if specified).
	 *
	 * @var string
	 */
	protected $path = '/';

	/**
	 * The default domain (if specified).
	 *
	 * @var string
	 */
	protected $domain = null;

	/**
	 * All of the cookies queued for sending.
	 *
	 * @var array
	 */
	protected $queued = array();

	/**
	 * Create a new cookie instance.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $httpOnly
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
	{
		list($path, $domain) = $this->getPathAndDomain($path, $domain);

		$time = ($minutes == 0) ? 0 : time() + ($minutes * 60);

		return new Cookie($name, $value, $time, $path, $domain, $secure, $httpOnly);
	}

	/**
	 * Create a cookie that lasts "forever" (five years).
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $httpOnly
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function forever($name, $value, $path = null, $domain = null, $secure = false, $httpOnly = true)
	{
		return $this->make($name, $value, 2628000, $path, $domain, $secure, $httpOnly);
	}

	/**
	 * Expire the given cookie.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @param  string  $domain
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function forget($name, $path = null, $domain = null)
	{
		return $this->make($name, null, -2628000, $path, $domain);
	}

	/**
	 * Determine if a cookie has been queued.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasQueued($key)
	{
		return ! is_null($this->queued($key));
	}

	/**
	 * Get a queued cookie instance.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function queued($key, $default = null)
	{
		return array_get($this->queued, $key, $default);
	}

	/**
	 * Queue a cookie to send with the next response.
	 *
	 * @param  dynamic
	 * @return void
	 */
	public function queue()
	{
		if (head(func_get_args()) instanceof Cookie)
		{
			$cookie = head(func_get_args());
		}
		else
		{
			$cookie = call_user_func_array(array($this, 'make'), func_get_args());
		}

		$this->queued[$cookie->getName()] = $cookie;
	}

	/**
	 * Remove a cookie from the queue.
	 *
	 * @param $cookieName
	 */
	public function unqueue($name)
	{
		unset($this->queued[$name]);
	}

	/**
	 * Get the path and domain, or the default values.
	 *
	 * @param  string  $path
	 * @param  string  $domain
	 * @return array
	 */
	protected function getPathAndDomain($path, $domain)
	{
		return array($path ?: $this->path, $domain ?: $this->domain);
	}

	/**
	 * Set the default path and domain for the jar.
	 *
	 * @param  string  $path
	 * @param  string  $domain
	 * @return self
	 */
	public function setDefaultPathAndDomain($path, $domain)
	{
		list($this->path, $this->domain) = array($path, $domain);

		return $this;
	}

	/**
	 * Get the cookies which have been queued for the next request
	 *
	 * @return array
	 */
	public function getQueuedCookies()
	{
		return $this->queued;
	}

}
