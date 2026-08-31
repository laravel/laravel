<?php

namespace GuzzleHttp\Cookie;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Stores HTTP cookies.
 *
 * It extracts cookies from HTTP requests, and returns them in HTTP responses.
 * CookieJarInterface instances automatically expire contained cookies when
 * necessary. Subclasses are also responsible for storing and retrieving
 * cookies from a file, database, etc.
 *
 * @see https://docs.python.org/2/library/cookielib.html Inspiration
 *
 * @extends \IteratorAggregate<SetCookie>
 */
interface CookieJarInterface extends \Countable, \IteratorAggregate
{
    /**
     * Create a request with added cookie headers.
     *
     * If no matching cookies are found in the cookie jar, then no Cookie
     * header is added to the request and the same request is returned.
     *
     * @param RequestInterface $request Request object to modify.
     *
     * @return RequestInterface returns the modified request.
     */
    public function withCookieHeader(RequestInterface $request): RequestInterface;

    /**
     * Extract cookies from an HTTP response and store them in the CookieJar.
     *
     * @param RequestInterface  $request  Request that was sent
     * @param ResponseInterface $response Response that was received
     */
    public function extractCookies(RequestInterface $request, ResponseInterface $response): void;

    /**
     * Sets a cookie in the cookie jar.
     *
     * @param SetCookie $cookie Cookie to set.
     *
     * @return bool Returns true on success or false on failure
     */
    public function setCookie(SetCookie $cookie): bool;

    /**
     * Remove cookies currently held in the cookie jar.
     *
     * Invoking this method without arguments will empty the whole cookie jar.
     * If given a $domain argument only cookies belonging to that domain will
     * be removed. If given a $domain and $path argument, cookies belonging to
     * the specified path within that domain are removed. If given all three
     * arguments, then the cookie with the specified name, path and domain is
     * removed.
     *
     * @param string|null $domain Clears cookies matching a domain
     * @param string|null $path   Clears cookies matching a domain and path
     * @param string|null $name   Clears cookies matching a domain, path, and name
     */
    public function clear(?string $domain = null, ?string $path = null, ?string $name = null): void;

    /**
     * Discard all sessions cookies.
     *
     * Removes cookies that don't have an expire field or a have a discard
     * field set to true. To be called when the user agent shuts down according
     * to RFC 2965.
     */
    public function clearSessionCookies(): void;

    /**
     * Converts the cookie jar to an array.
     */
    public function toArray(): array;
}
