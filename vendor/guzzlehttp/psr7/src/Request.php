<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 request implementation.
 */
class Request implements RequestInterface
{
    use MessageTrait;

    /** @var string */
    private $method;

    /** @var string|null */
    private $requestTarget;

    /** @var UriInterface */
    private $uri;

    /**
     * @param string                               $method  HTTP method
     * @param string|UriInterface                  $uri     URI
     * @param (string|string[])[]                  $headers Request headers
     * @param string|resource|StreamInterface|null $body    Request body
     * @param string                               $version Protocol version
     */
    public function __construct(
        string $method,
        $uri,
        array $headers = [],
        $body = null,
        string $version = '1.1'
    ) {
        $this->assertMethod($method);
        $this->assertProtocolVersion($version);

        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }

        self::warnOnMethodCasingChange($method);
        $this->method = Utils::asciiToUpper($method);
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        if (!isset($this->headerNames['host'])) {
            $this->updateHostFromUri();
        }

        if ($body !== '' && $body !== null) {
            $this->stream = Utils::streamFor($body);
        }
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }
        if ($this->uri->getQuery() != '') {
            $target .= '?'.$this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget($requestTarget): RequestInterface
    {
        $hasWhitespace = preg_match('#\s#', $requestTarget);

        if ($hasWhitespace === false) {
            throw new \RuntimeException('Unable to validate request target: '.preg_last_error_msg());
        }

        if ($hasWhitespace === 1) {
            throw new InvalidArgumentException(
                'Invalid request target provided; cannot contain whitespace'
            );
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;

        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod($method): RequestInterface
    {
        $this->assertMethod($method);
        self::warnOnMethodCasingChange($method);
        $new = clone $this;
        $new->method = Utils::asciiToUpper($method);

        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        if (!\is_bool($preserveHost)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to RequestInterface::withUri() is deprecated; guzzlehttp/psr7 3.0 requires bool for $preserveHost.',
                \get_debug_type($preserveHost)
            );
        }

        if ($uri === $this->uri) {
            return $this;
        }

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost || !isset($this->headerNames['host'])) {
            $new->updateHostFromUri();
        }

        return $new;
    }

    private function updateHostFromUri(): void
    {
        $host = $this->uri->getHost();

        if ($host == '') {
            return;
        }

        Uri::assertValidHost($host);

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':'.$port;
        }

        $this->assertValue($host);

        if (isset($this->headerNames['host'])) {
            $header = $this->headerNames['host'];
        } else {
            $header = 'Host';
            $this->headerNames['host'] = 'Host';
        }
        // Ensure Host is the first header.
        // See: https://datatracker.ietf.org/doc/html/rfc7230#section-5.4
        $this->headers = [$header => [$host]] + $this->headers;
    }

    /**
     * @param mixed $method
     */
    private function assertMethod($method): void
    {
        if (!is_string($method) || $method === '') {
            throw new InvalidArgumentException('Method must be a non-empty string.');
        }

        $this->assertNoLineSeparators($method, 'Method');
    }

    private static function warnOnMethodCasingChange(string $method): void
    {
        if ($method !== Utils::asciiToUpper($method)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing a non-uppercase HTTP method is deprecated; guzzlehttp/psr7 3.0 preserves method casing and will no longer uppercase it. Normalize the method before constructing or modifying requests if uppercase is required.'
            );
        }
    }
}
