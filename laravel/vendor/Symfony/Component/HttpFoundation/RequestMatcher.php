<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * RequestMatcher compares a pre-defined set of checks against a Request instance.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $host;

    /**
     * @var array
     */
    private $methods = array();

    /**
     * @var string
     */
    private $ip;

    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @param string|null          $path
     * @param string|null          $host
     * @param string|string[]|null $methods
     * @param string|null          $ip
     * @param array                $attributes
     */
    public function __construct($path = null, $host = null, $methods = null, $ip = null, array $attributes = array())
    {
        $this->matchPath($path);
        $this->matchHost($host);
        $this->matchMethod($methods);
        $this->matchIp($ip);
        foreach ($attributes as $k => $v) {
            $this->matchAttribute($k, $v);
        }
    }

    /**
     * Adds a check for the URL host name.
     *
     * @param string $regexp A Regexp
     */
    public function matchHost($regexp)
    {
        $this->host = $regexp;
    }

    /**
     * Adds a check for the URL path info.
     *
     * @param string $regexp A Regexp
     */
    public function matchPath($regexp)
    {
        $this->path = $regexp;
    }

    /**
     * Adds a check for the client IP.
     *
     * @param string $ip A specific IP address or a range specified using IP/netmask like 192.168.1.0/24
     */
    public function matchIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Adds a check for the HTTP method.
     *
     * @param string|string[]|null $method An HTTP method or an array of HTTP methods
     */
    public function matchMethod($method)
    {
        $this->methods = array_map('strtoupper', (array) $method);
    }

    /**
     * Adds a check for request attribute.
     *
     * @param string $key    The request attribute name
     * @param string $regexp A Regexp
     */
    public function matchAttribute($key, $regexp)
    {
        $this->attributes[$key] = $regexp;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function matches(Request $request)
    {
        if ($this->methods && !in_array($request->getMethod(), $this->methods)) {
            return false;
        }

        foreach ($this->attributes as $key => $pattern) {
            if (!preg_match('#'.str_replace('#', '\\#', $pattern).'#', $request->attributes->get($key))) {
                return false;
            }
        }

        if (null !== $this->path) {
            $path = str_replace('#', '\\#', $this->path);

            if (!preg_match('#'.$path.'#', rawurldecode($request->getPathInfo()))) {
                return false;
            }
        }

        if (null !== $this->host && !preg_match('#'.str_replace('#', '\\#', $this->host).'#i', $request->getHost())) {
            return false;
        }

        if (null !== $this->ip && !IpUtils::checkIp($request->getClientIp(), $this->ip)) {
            return false;
        }

        return true;
    }
}
