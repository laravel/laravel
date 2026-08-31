<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Holds information about the current request.
 *
 * This class implements a fluent interface.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class RequestContext
{
    private string $baseUrl;
    private string $pathInfo;
    private string $method;
    private string $host;
    private string $scheme;
    private int $httpPort;
    private int $httpsPort;
    private string $queryString;
    private array $parameters = [];

    public function __construct(string $baseUrl = '', string $method = 'GET', string $host = 'localhost', string $scheme = 'http', int $httpPort = 80, int $httpsPort = 443, string $path = '/', string $queryString = '', ?array $parameters = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->setMethod($method);
        $this->setHost($host);
        $this->setScheme($scheme);
        $this->setHttpPort($httpPort);
        $this->setHttpsPort($httpsPort);
        $this->setPathInfo($path);
        $this->setQueryString($queryString);
        $this->parameters = $parameters ?? $this->parameters;
    }

    public static function fromUri(string $uri, string $host = 'localhost', string $scheme = 'http', int $httpPort = 80, int $httpsPort = 443): self
    {
        if (false !== ($i = strpos($uri, '\\')) && $i < strcspn($uri, '?#')) {
            $uri = '';
        }
        if ('' !== $uri && (\ord($uri[0]) <= 32 || \ord($uri[-1]) <= 32 || \strlen($uri) !== strcspn($uri, "\r\n\t"))) {
            $uri = '';
        }

        $uri = parse_url($uri);
        $scheme = $uri['scheme'] ?? $scheme;
        $host = $uri['host'] ?? $host;

        if (isset($uri['port'])) {
            if ('http' === $scheme) {
                $httpPort = $uri['port'];
            } elseif ('https' === $scheme) {
                $httpsPort = $uri['port'];
            }
        }

        return new self($uri['path'] ?? '', 'GET', $host, $scheme, $httpPort, $httpsPort);
    }

    /**
     * Updates the RequestContext information based on a HttpFoundation Request.
     *
     * @return $this
     */
    public function fromRequest(Request $request): static
    {
        $this->setBaseUrl($request->getBaseUrl());
        $this->setPathInfo($request->getPathInfo());
        $this->setMethod($request->getMethod());
        $this->setHost($request->getHost());
        $this->setScheme($request->getScheme());
        $this->setHttpPort($request->isSecure() || null === $request->getPort() ? $this->httpPort : $request->getPort());
        $this->setHttpsPort($request->isSecure() && null !== $request->getPort() ? $request->getPort() : $this->httpsPort);
        $this->setQueryString($request->server->get('QUERY_STRING', ''));

        return $this;
    }

    /**
     * Gets the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Sets the base URL.
     *
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        return $this;
    }

    /**
     * Gets the path info.
     */
    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }

    /**
     * Sets the path info.
     *
     * @return $this
     */
    public function setPathInfo(string $pathInfo): static
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }

    /**
     * Gets the HTTP method.
     *
     * The method is always an uppercased string.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Sets the HTTP method.
     *
     * @return $this
     */
    public function setMethod(string $method): static
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Gets the HTTP host.
     *
     * The host is always lowercased because it must be treated case-insensitive.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Sets the HTTP host.
     *
     * @return $this
     */
    public function setHost(string $host): static
    {
        $this->host = strtolower($host);

        return $this;
    }

    /**
     * Gets the HTTP scheme.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Sets the HTTP scheme.
     *
     * @return $this
     */
    public function setScheme(string $scheme): static
    {
        $this->scheme = strtolower($scheme);

        return $this;
    }

    /**
     * Gets the HTTP port.
     */
    public function getHttpPort(): int
    {
        return $this->httpPort;
    }

    /**
     * Sets the HTTP port.
     *
     * @return $this
     */
    public function setHttpPort(int $httpPort): static
    {
        $this->httpPort = $httpPort;

        return $this;
    }

    /**
     * Gets the HTTPS port.
     */
    public function getHttpsPort(): int
    {
        return $this->httpsPort;
    }

    /**
     * Sets the HTTPS port.
     *
     * @return $this
     */
    public function setHttpsPort(int $httpsPort): static
    {
        $this->httpsPort = $httpsPort;

        return $this;
    }

    /**
     * Gets the query string without the "?".
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Sets the query string.
     *
     * @return $this
     */
    public function setQueryString(?string $queryString): static
    {
        // string cast to be fault-tolerant, accepting null
        $this->queryString = (string) $queryString;

        return $this;
    }

    /**
     * Returns the parameters.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Sets the parameters.
     *
     * @param array $parameters The parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Gets a parameter value.
     */
    public function getParameter(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Checks if a parameter value is set for the given parameter.
     */
    public function hasParameter(string $name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }

    /**
     * Sets a parameter value.
     *
     * @return $this
     */
    public function setParameter(string $name, mixed $parameter): static
    {
        $this->parameters[$name] = $parameter;

        return $this;
    }

    public function isSecure(): bool
    {
        return 'https' === $this->scheme;
    }
}
