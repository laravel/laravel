<?php

namespace Illuminate\Contracts\Cookie;

interface Factory
{
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
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null);

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
    public function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null);

    /**
     * Expire the given cookie.
     *
     * @param  string  $name
     * @param  string|null  $path
     * @param  string|null  $domain
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forget($name, $path = null, $domain = null);
}
