<?php

namespace Illuminate\Cookie;

use Illuminate\Contracts\Cookie\QueueingFactory as JarContract;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\HttpFoundation\Cookie;

class CookieJar implements JarContract
{
    use InteractsWithTime, Macroable;

    /**
     * The default path (if specified).
     *
     * @var string
     */
    protected $path = '/';

    /**
     * The default domain (if specified).
     *
     * @var string|null
     */
    protected $domain;

    /**
     * The default secure setting (defaults to null).
     *
     * @var bool|null
     */
    protected $secure;

    /**
     * The default SameSite option (defaults to lax).
     *
     * @var string
     */
    protected $sameSite = 'lax';

    /**
     * All of the cookies queued for sending.
     *
     * @var \Symfony\Component\HttpFoundation\Cookie[]
     */
    protected $queued = [];

    /**
     * Create a new cookie instance.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int  $minutes
     * @param  string|null  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  bool  $httpOnly
     * @param  bool  $raw
     * @param  string|null  $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {
        [$path, $domain, $secure, $sameSite] = $this->getPathAndDomain($path, $domain, $secure, $sameSite);

        $time = ($minutes == 0) ? 0 : $this->availableAt($minutes * 60);

        return new Cookie($name, $value, $time, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * Create a cookie that lasts "forever" (400 days).
     *
     * @param  string  $name
     * @param  string  $value
     * @param  string|null  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  bool  $httpOnly
     * @param  bool  $raw
     * @param  string|null  $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {
        return $this->make($name, $value, 576000, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * Expire the given cookie.
     *
     * @param  string  $name
     * @param  string|null  $path
     * @param  string|null  $domain
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
     * @param  string|null  $path
     * @return bool
     */
    public function hasQueued($key, $path = null)
    {
        return ! is_null($this->queued($key, null, $path));
    }

    /**
     * Get a queued cookie instance.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @param  string|null  $path
     * @return \Symfony\Component\HttpFoundation\Cookie|null
     */
    public function queued($key, $default = null, $path = null)
    {
        $queued = Arr::get($this->queued, $key, $default);

        if ($path === null) {
            return Arr::last($queued, null, $default);
        }

        return Arr::get($queued, $path, $default);
    }

    /**
     * Queue a cookie to send with the next response.
     *
     * @param  mixed  ...$parameters
     * @return void
     */
    public function queue(...$parameters)
    {
        if (isset($parameters[0]) && $parameters[0] instanceof Cookie) {
            $cookie = $parameters[0];
        } else {
            $cookie = $this->make(...array_values($parameters));
        }

        if (! isset($this->queued[$cookie->getName()])) {
            $this->queued[$cookie->getName()] = [];
        }

        $this->queued[$cookie->getName()][$cookie->getPath()] = $cookie;
    }

    /**
     * Queue a cookie to expire with the next response.
     *
     * @param  string  $name
     * @param  string|null  $path
     * @param  string|null  $domain
     * @return void
     */
    public function expire($name, $path = null, $domain = null)
    {
        $this->queue($this->forget($name, $path, $domain));
    }

    /**
     * Remove a cookie from the queue.
     *
     * @param  string  $name
     * @param  string|null  $path
     * @return void
     */
    public function unqueue($name, $path = null)
    {
        if ($path === null) {
            unset($this->queued[$name]);

            return;
        }

        unset($this->queued[$name][$path]);

        if (empty($this->queued[$name])) {
            unset($this->queued[$name]);
        }
    }

    /**
     * Get the path and domain, or the default values.
     *
     * @param  string  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  string|null  $sameSite
     * @return array
     */
    protected function getPathAndDomain($path, $domain, $secure = null, $sameSite = null)
    {
        return [$path ?: $this->path, $domain ?: $this->domain, is_bool($secure) ? $secure : $this->secure, $sameSite ?: $this->sameSite];
    }

    /**
     * Set the default path and domain for the jar.
     *
     * @param  string  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  string|null  $sameSite
     * @return $this
     */
    public function setDefaultPathAndDomain($path, $domain, $secure = false, $sameSite = null)
    {
        [$this->path, $this->domain, $this->secure, $this->sameSite] = [$path, $domain, $secure, $sameSite];

        return $this;
    }

    /**
     * Get the cookies which have been queued for the next request.
     *
     * @return \Symfony\Component\HttpFoundation\Cookie[]
     */
    public function getQueuedCookies()
    {
        return Arr::flatten($this->queued);
    }

    /**
     * Flush the cookies which have been queued for the next request.
     *
     * @return $this
     */
    public function flushQueuedCookies()
    {
        $this->queued = [];

        return $this;
    }
}
