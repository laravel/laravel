<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\BrowserKit;

/**
 * Request object.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class Request
{
    protected $uri;
    protected $method;
    protected $parameters;
    protected $files;
    protected $cookies;
    protected $server;
    protected $content;

    /**
     * Constructor.
     *
     * @param string $uri        The request URI
     * @param string $method     The HTTP method request
     * @param array  $parameters The request parameters
     * @param array  $files      An array of uploaded files
     * @param array  $cookies    An array of cookies
     * @param array  $server     An array of server parameters
     * @param string $content    The raw body data
     *
     * @api
     */
    public function __construct($uri, $method, array $parameters = array(), array $files = array(), array $cookies = array(), array $server = array(), $content = null)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->server = $server;
        $this->content = $content;
    }

    /**
     * Gets the request URI.
     *
     * @return string The request URI
     *
     * @api
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Gets the request HTTP method.
     *
     * @return string The request HTTP method
     *
     * @api
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Gets the request parameters.
     *
     * @return array The request parameters
     *
     * @api
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Gets the request server files.
     *
     * @return array The request files
     *
     * @api
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Gets the request cookies.
     *
     * @return array The request cookies
     *
     * @api
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Gets the request server parameters.
     *
     * @return array The request server parameters
     *
     * @api
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets the request raw body data.
     *
     * @return string The request raw body data.
     *
     * @api
     */
    public function getContent()
    {
        return $this->content;
    }
}
