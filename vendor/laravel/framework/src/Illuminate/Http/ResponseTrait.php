<?php

namespace Illuminate\Http;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Throwable;

trait ResponseTrait
{
    /**
     * The original content of the response.
     *
     * @var mixed
     */
    public $original;

    /**
     * The exception that triggered the error response (if applicable).
     *
     * @var \Throwable|null
     */
    public $exception;

    /**
     * Get the status code for the response.
     *
     * @return int
     */
    public function status()
    {
        return $this->getStatusCode();
    }

    /**
     * Get the status text for the response.
     *
     * @return string
     */
    public function statusText()
    {
        return $this->statusText;
    }

    /**
     * Get the content of the response.
     *
     * @return string
     */
    public function content()
    {
        return $this->getContent();
    }

    /**
     * Get the original response content.
     *
     * @return mixed
     */
    public function getOriginalContent()
    {
        $original = $this->original;

        return $original instanceof self ? $original->{__FUNCTION__}() : $original;
    }

    /**
     * Set a header on the Response.
     *
     * @param  string  $key
     * @param  array|string  $values
     * @param  bool  $replace
     * @return $this
     */
    public function header($key, $values, $replace = true)
    {
        $this->headers->set($key, $values, $replace);

        return $this;
    }

    /**
     * Add an array of headers to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\HeaderBag|array  $headers
     * @return $this
     */
    public function withHeaders($headers)
    {
        if ($headers instanceof HeaderBag) {
            $headers = $headers->all();
        }

        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * Remove a header(s) from the response.
     *
     * @param  array|string  $key
     * @return $this
     */
    public function withoutHeader($key)
    {
        foreach ((array) $key as $header) {
            $this->headers->remove($header);
        }

        return $this;
    }

    /**
     * Add a cookie to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Cookie|mixed  $cookie
     * @return $this
     */
    public function cookie($cookie)
    {
        return $this->withCookie(...func_get_args());
    }

    /**
     * Add a cookie to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Cookie|mixed  $cookie
     * @return $this
     */
    public function withCookie($cookie)
    {
        if (is_string($cookie) && function_exists('cookie')) {
            $cookie = cookie(...func_get_args());
        }

        $this->headers->setCookie($cookie);

        return $this;
    }

    /**
     * Expire a cookie when sending the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Cookie|mixed  $cookie
     * @param  string|null  $path
     * @param  string|null  $domain
     * @return $this
     */
    public function withoutCookie($cookie, $path = null, $domain = null)
    {
        if (is_string($cookie) && function_exists('cookie')) {
            $cookie = cookie($cookie, null, -2628000, $path, $domain);
        }

        $this->headers->setCookie($cookie);

        return $this;
    }

    /**
     * Get the callback of the response.
     *
     * @return string|null
     */
    public function getCallback()
    {
        return $this->callback ?? null;
    }

    /**
     * Set the exception to attach to the response.
     *
     * @param  \Throwable  $e
     * @return $this
     */
    public function withException(Throwable $e)
    {
        $this->exception = $e;

        return $this;
    }

    /**
     * Throws the response in a HttpResponseException instance.
     *
     * @return never
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function throwResponse()
    {
        throw new HttpResponseException($this);
    }
}
