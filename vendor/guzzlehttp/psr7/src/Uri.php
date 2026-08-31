<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use GuzzleHttp\Psr7\Exception\MalformedUriException;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 URI implementation.
 *
 * @author Michael Dowling
 * @author Tobias Schultze
 * @author Matthew Weier O'Phinney
 */
class Uri implements UriInterface, \JsonSerializable
{
    /**
     * Absolute http and https URIs require a host per RFC 7230 Section 2.7
     * but in generic URIs the host can be empty. So for http(s) URIs
     * we apply this default host when no host is given yet to form a
     * valid URI.
     */
    private const HTTP_DEFAULT_HOST = 'localhost';

    private const DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    private const QUERY_SEPARATORS_REPLACEMENT = ['=' => '%3D', '&' => '%26', '+' => '%2B'];

    /** @var string Uri scheme. */
    private $scheme = '';

    /** @var string Uri user info. */
    private $userInfo = '';

    /** @var string Uri host. */
    private $host = '';

    /** @var int|null Uri port. */
    private $port;

    /** @var string Uri path. */
    private $path = '';

    /** @var string Uri query string. */
    private $query = '';

    /** @var string Uri fragment. */
    private $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            $parts = self::parse($uri);
            if ($parts === false) {
                throw new MalformedUriException("Unable to parse URI: $uri");
            }
            try {
                $this->applyParts($parts);
            } catch (MalformedUriException $e) {
                throw $e;
            } catch (\InvalidArgumentException $e) {
                throw new MalformedUriException($e->getMessage(), 0, $e);
            }
        }
    }

    /**
     * UTF-8 aware \parse_url() replacement.
     *
     * The internal function produces broken output for non ASCII domain names
     * (IDN) when used with locales other than "C".
     *
     * On the other hand, cURL understands IDN correctly only when UTF-8 locale
     * is configured ("C.UTF-8", "en_US.UTF-8", etc.).
     *
     * @see https://bugs.php.net/bug.php?id=52923
     * @see https://www.php.net/manual/en/function.parse-url.php#114817
     * @see https://curl.haxx.se/libcurl/c/CURLOPT_URL.html#ENCODING
     *
     * @return array|false
     */
    private static function parse(string $url)
    {
        if (self::isPathNoSchemeReference($url)) {
            return self::parsePathNoSchemeReference($url);
        }

        // Preserve bracketed IPv6 literals before encoding, including dotted IPv4
        // tails. DEL (\x7F) is excluded so a raw-DEL host falls through to the
        // general path and is rejected rather than silently mutated by parse_url().
        $prefix = '';
        $ipv6Prefix = preg_match('%\A([0-9A-Za-z+.-]+://\[[^\]\x00-\x20\x7F/?#@]+\])(.*)\z%s', $url, $matches);

        if ($ipv6Prefix === false) {
            return false;
        }

        if ($ipv6Prefix === 1) {
            /** @var array{0:string, 1:string, 2:string} $matches */
            $suffix = $matches[2];

            // After the bracketed host only an optional numeric port and/or a
            // path, query, or fragment may follow. Anything else (for example
            // `:80@evil` or `:80x`) would let parse_url() reinterpret a
            // different host.
            if (preg_match('%\A(?::[0-9]*)?(?:[/?#].*)?\z%s', $suffix) !== 1) {
                return false;
            }

            $prefix = $matches[1];
            $url = $suffix;
        }

        /** @var string|null */
        $encodedUrl = preg_replace_callback(
            '%[^:/@?&=#]+%usD',
            static function ($matches) {
                return urlencode($matches[0]);
            },
            $url
        );

        if ($encodedUrl === null) {
            return false;
        }

        $result = parse_url($prefix.$encodedUrl);

        if ($result === false) {
            return false;
        }

        return array_map('urldecode', $result);
    }

    private static function isPathNoSchemeReference(string $url): bool
    {
        if ($url === '' || $url[0] === '/' || $url[0] === '?' || $url[0] === '#') {
            return false;
        }

        $firstSegment = substr($url, 0, strcspn($url, '/?#'));

        return strpos($firstSegment, ':') === false;
    }

    /**
     * @return array{path: string, query?: string, fragment?: string}
     */
    private static function parsePathNoSchemeReference(string $url): array
    {
        $parts = [];

        if (false !== ($fragmentPosition = strpos($url, '#'))) {
            $parts['fragment'] = substr($url, $fragmentPosition + 1);
            $url = substr($url, 0, $fragmentPosition);
        }

        if (false !== ($queryPosition = strpos($url, '?'))) {
            $parts['query'] = substr($url, $queryPosition + 1);
            $url = substr($url, 0, $queryPosition);
        }

        $parts['path'] = $url;

        return $parts;
    }

    public function __toString(): string
    {
        return self::composeComponents(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * Composes a URI reference string from its various components.
     *
     * Usually this method does not need to be called manually but instead is used indirectly via
     * `Psr\Http\Message\UriInterface::__toString`.
     *
     * PSR-7 UriInterface treats an empty component the same as a missing component as
     * getQuery(), getFragment() etc. always return a string. This explains the slight
     * difference to RFC 3986 Section 5.3.
     *
     * Another adjustment is that the authority separator is added even when the authority is missing/empty
     * for the "file" scheme. This is because PHP stream functions like `file_get_contents` only work with
     * `file:///myfile` but not with `file:/myfile` although they are equivalent according to RFC 3986. But
     * `file:///` is the more common syntax for the file scheme anyway (Chrome for example redirects to
     * that format).
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-5.3
     */
    public static function composeComponents(?string $scheme, ?string $authority, string $path, ?string $query, ?string $fragment): string
    {
        $uri = '';

        // weak type checks to also accept null until we can add scalar type hints
        if ($scheme != '') {
            $uri .= $scheme.':';
        }

        if ($authority != '' || $scheme === 'file') {
            $uri .= '//'.$authority;
        }

        if ($authority != '' && $path != '' && $path[0] != '/') {
            $path = '/'.$path;
        }

        $uri .= $path;

        if ($query != '') {
            $uri .= '?'.$query;
        }

        if ($fragment != '') {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * Whether the URI has the default port of the current scheme.
     *
     * `Psr\Http\Message\UriInterface::getPort` may return null or the standard port. This method can be used
     * independently of the implementation.
     */
    public static function isDefaultPort(UriInterface $uri): bool
    {
        return $uri->getPort() === null
            || (isset(self::DEFAULT_PORTS[$uri->getScheme()]) && $uri->getPort() === self::DEFAULT_PORTS[$uri->getScheme()]);
    }

    /**
     * Whether the URI is absolute, i.e. it has a scheme.
     *
     * An instance of UriInterface can either be an absolute URI or a relative reference. This method returns true
     * if it is the former. An absolute URI has a scheme. A relative reference is used to express a URI relative
     * to another URI, the base URI. Relative references can be divided into several forms:
     * - network-path references, e.g. '//example.com/path'
     * - absolute-path references, e.g. '/path'
     * - relative-path references, e.g. 'subpath'
     *
     * @see Uri::isNetworkPathReference
     * @see Uri::isAbsolutePathReference
     * @see Uri::isRelativePathReference
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-4
     */
    public static function isAbsolute(UriInterface $uri): bool
    {
        return $uri->getScheme() !== '';
    }

    /**
     * Whether the URI is a network-path reference.
     *
     * A relative reference that begins with two slash characters is termed an network-path reference.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-4.2
     */
    public static function isNetworkPathReference(UriInterface $uri): bool
    {
        return $uri->getScheme() === '' && $uri->getAuthority() !== '';
    }

    /**
     * Whether the URI is a absolute-path reference.
     *
     * A relative reference that begins with a single slash character is termed an absolute-path reference.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-4.2
     */
    public static function isAbsolutePathReference(UriInterface $uri): bool
    {
        return $uri->getScheme() === ''
            && $uri->getAuthority() === ''
            && isset($uri->getPath()[0])
            && $uri->getPath()[0] === '/';
    }

    /**
     * Whether the URI is a relative-path reference.
     *
     * A relative reference that does not begin with a slash character is termed a relative-path reference.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-4.2
     */
    public static function isRelativePathReference(UriInterface $uri): bool
    {
        return $uri->getScheme() === ''
            && $uri->getAuthority() === ''
            && (!isset($uri->getPath()[0]) || $uri->getPath()[0] !== '/');
    }

    /**
     * Whether the URI is a same-document reference.
     *
     * A same-document reference refers to a URI that is, aside from its fragment
     * component, identical to the base URI. When no base URI is given, only an empty
     * URI reference (apart from its fragment) is considered a same-document reference.
     *
     * @param UriInterface      $uri  The URI to check
     * @param UriInterface|null $base An optional base URI to compare against
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-4.4
     */
    public static function isSameDocumentReference(UriInterface $uri, ?UriInterface $base = null): bool
    {
        if ($base !== null) {
            $uri = UriResolver::resolve($base, $uri);

            return ($uri->getScheme() === $base->getScheme())
                && ($uri->getAuthority() === $base->getAuthority())
                && ($uri->getPath() === $base->getPath())
                && ($uri->getQuery() === $base->getQuery());
        }

        return $uri->getScheme() === '' && $uri->getAuthority() === '' && $uri->getPath() === '' && $uri->getQuery() === '';
    }

    /**
     * Creates a new URI with a specific query string value removed.
     *
     * Any existing query string values that exactly match the provided key are
     * removed.
     *
     * @param UriInterface $uri URI to use as a base.
     * @param string       $key Query string key to remove.
     */
    public static function withoutQueryValue(UriInterface $uri, string $key): UriInterface
    {
        $result = self::getFilteredQueryString($uri, [$key]);

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Creates a new URI with a specific query string value.
     *
     * Any existing query string values that exactly match the provided key are
     * removed and replaced with the given key value pair.
     *
     * A value of null will set the query string key without a value, e.g. "key"
     * instead of "key=value".
     *
     * @param UriInterface $uri   URI to use as a base.
     * @param string       $key   Key to set.
     * @param string|null  $value Value to set
     */
    public static function withQueryValue(UriInterface $uri, string $key, ?string $value): UriInterface
    {
        $result = self::getFilteredQueryString($uri, [$key]);

        $result[] = self::generateQueryString($key, $value);

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Creates a new URI with multiple specific query string values.
     *
     * It has the same behavior as withQueryValue() but for an associative array of key => value.
     *
     * @param UriInterface    $uri           URI to use as a base.
     * @param (string|null)[] $keyValueArray Associative array of key and values
     */
    public static function withQueryValues(UriInterface $uri, array $keyValueArray): UriInterface
    {
        $result = self::getFilteredQueryString($uri, array_keys($keyValueArray));

        foreach ($keyValueArray as $key => $value) {
            $result[] = self::generateQueryString((string) $key, $value !== null ? self::stringifyQueryValue($value) : null);
        }

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Stringifies a non-null query value, deprecating non-string values that
     * guzzlehttp/psr7 3.0 will reject. Non-finite floats are normalized to the
     * strings PHP coerces them to, as implicit coercion of NAN emits a warning
     * on PHP 8.5.
     *
     * @param mixed $value
     */
    private static function stringifyQueryValue($value): string
    {
        if (!is_string($value)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.12',
                'Passing %s to Uri::withQueryValues() is deprecated; cast it to a string. guzzlehttp/psr7 3.0 will only accept string or null query values.',
                \gettype($value)
            );

            if (is_float($value) && !is_finite($value)) {
                return is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
            }
        }

        return (string) $value;
    }

    /**
     * Creates a URI from a hash of `parse_url` components.
     *
     * @see https://www.php.net/manual/en/function.parse-url.php
     *
     * @throws MalformedUriException If the components do not form a valid URI.
     */
    public static function fromParts(array $parts): UriInterface
    {
        $uri = new self();
        try {
            $uri->applyParts($parts);
            $uri->validateState();
        } catch (MalformedUriException $e) {
            throw $e;
        } catch (\InvalidArgumentException $e) {
            throw new MalformedUriException($e->getMessage(), 0, $e);
        }

        return $uri;
    }

    /**
     * @throws \InvalidArgumentException If the host is invalid.
     *
     * @internal
     */
    public static function assertValidHost(string $host): void
    {
        if ($host === '') {
            return;
        }

        // Reject control characters and URI authority delimiters so getHost()
        // cannot disagree with the on-wire authority.
        $invalidHost = preg_match('/[\x00-\x20\x7F\/\?#@\\\\]/', $host);

        if ($invalidHost === false) {
            throw new \RuntimeException('Unable to validate URI host: '.preg_last_error_msg());
        }

        if ($invalidHost === 1) {
            throw new \InvalidArgumentException(sprintf('Invalid host: "%s"', $host));
        }

        if (strpos($host, '[') !== false || strpos($host, ']') !== false) {
            if ($host[0] !== '[' || substr($host, -1) !== ']') {
                throw new \InvalidArgumentException(sprintf('Invalid host: "%s"', $host));
            }

            return;
        }

        if (strpos($host, ':') !== false) {
            throw new \InvalidArgumentException(sprintf('Invalid host: "%s"', $host));
        }
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo.'@'.$authority;
        }

        if ($this->port !== null) {
            $authority .= ':'.$this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme($scheme): UriInterface
    {
        $scheme = $this->filterScheme($scheme);

        if ($this->scheme === $scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->removeDefaultPort();
        $new->validateState();

        return $new;
    }

    public function withUserInfo($user, $password = null): UriInterface
    {
        $info = $this->filterUserInfoComponent($user);
        if ($password !== null) {
            $info .= ':'.$this->filterUserInfoComponent($password);
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;
        $new->validateState();

        return $new;
    }

    public function withHost($host): UriInterface
    {
        $host = $this->filterHost($host);

        if ($this->host === $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;
        $new->validateState();

        return $new;
    }

    public function withPort($port): UriInterface
    {
        if ($port !== null && !\is_int($port)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to UriInterface::withPort() is deprecated; guzzlehttp/psr7 3.0 requires int|null.',
                \get_debug_type($port)
            );
        }

        $port = $this->filterPort($port);

        if ($this->port === $port) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;
        $new->removeDefaultPort();
        $new->validateState();

        return $new;
    }

    public function withPath($path): UriInterface
    {
        $path = $this->filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;
        $new->validateState();

        return $new;
    }

    public function withQuery($query): UriInterface
    {
        $query = $this->filterQueryAndFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    public function withFragment($fragment): UriInterface
    {
        $fragment = $this->filterQueryAndFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    /**
     * Apply parse_url parts to a URI.
     *
     * @param array $parts Array of parse_url parts to apply.
     */
    private function applyParts(array $parts): void
    {
        $this->scheme = isset($parts['scheme'])
            ? $this->filterScheme($parts['scheme'])
            : '';
        $this->userInfo = isset($parts['user'])
            ? $this->filterUserInfoComponent($parts['user'])
            : '';
        $this->host = isset($parts['host'])
            ? $this->filterHost($parts['host'])
            : '';
        $this->port = isset($parts['port'])
            ? $this->filterPort($parts['port'])
            : null;
        $this->path = isset($parts['path'])
            ? $this->filterPath($parts['path'])
            : '';
        $this->query = isset($parts['query'])
            ? $this->filterQueryAndFragment($parts['query'])
            : '';
        $this->fragment = isset($parts['fragment'])
            ? $this->filterQueryAndFragment($parts['fragment'])
            : '';
        if (isset($parts['pass'])) {
            $this->userInfo .= ':'.$this->filterUserInfoComponent($parts['pass']);
        }

        $this->removeDefaultPort();
    }

    /**
     * @param mixed $scheme
     *
     * @throws \InvalidArgumentException If the scheme is invalid.
     */
    private function filterScheme($scheme): string
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException('Scheme must be a string');
        }

        $scheme = Utils::asciiToLower($scheme);

        if ($scheme !== '' && !preg_match('/^[a-z][a-z0-9.+-]*$/D', $scheme)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing "%s" as a URI scheme is deprecated; guzzlehttp/psr7 3.0 requires URI schemes to match RFC 3986 syntax and begin with a letter.',
                $scheme
            );
        }

        return $scheme;
    }

    /**
     * @param mixed $component
     *
     * @throws \InvalidArgumentException If the user info is invalid.
     */
    private function filterUserInfoComponent($component): string
    {
        if (!is_string($component)) {
            throw new \InvalidArgumentException('User info must be a string');
        }

        return $this->filterComponent(
            '/(?:[^%'.Rfc3986::CHAR_UNRESERVED.Rfc3986::CHAR_SUB_DELIMS.']+|%(?![A-Fa-f0-9]{2}))/',
            $component,
            'Unable to filter URI user info'
        );
    }

    /**
     * @param mixed $host
     *
     * @throws \InvalidArgumentException If the host is invalid.
     */
    private function filterHost($host): string
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException('Host must be a string');
        }

        $host = Utils::asciiToLower($host);
        self::assertValidHost($host);

        return $host;
    }

    /**
     * @param mixed $port
     *
     * @throws \InvalidArgumentException If the port is invalid.
     */
    private function filterPort($port): ?int
    {
        if ($port === null) {
            return null;
        }

        $port = (int) $port;
        if (0 > $port || 0xFFFF < $port) {
            throw new \InvalidArgumentException(
                sprintf('Invalid port: %d. Must be between 0 and 65535', $port)
            );
        }

        return $port;
    }

    /**
     * @param (string|int)[] $keys
     *
     * @return string[]
     */
    private static function getFilteredQueryString(UriInterface $uri, array $keys): array
    {
        $current = $uri->getQuery();

        if ($current === '') {
            return [];
        }

        $decodedKeys = array_map(function ($k): string {
            return rawurldecode((string) $k);
        }, $keys);

        return array_filter(explode('&', $current), function ($part) use ($decodedKeys) {
            return !in_array(rawurldecode(explode('=', $part)[0]), $decodedKeys, true);
        });
    }

    private static function generateQueryString(string $key, ?string $value): string
    {
        // Query string separators ("=", "&") and literal plus signs ("+") within the
        // key or value need to be encoded
        // (while preventing double-encoding) before setting the query string. All other
        // chars that need percent-encoding will be encoded by withQuery().
        $queryString = strtr($key, self::QUERY_SEPARATORS_REPLACEMENT);

        if ($value !== null) {
            $queryString .= '='.strtr($value, self::QUERY_SEPARATORS_REPLACEMENT);
        }

        return $queryString;
    }

    private function removeDefaultPort(): void
    {
        if ($this->port !== null && self::isDefaultPort($this)) {
            $this->port = null;
        }
    }

    /**
     * Filters the path of a URI
     *
     * @param mixed $path
     *
     * @throws \InvalidArgumentException If the path is invalid.
     */
    private function filterPath($path): string
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        return $this->filterComponent(
            '/(?:[^'.Rfc3986::CHAR_UNRESERVED.Rfc3986::CHAR_SUB_DELIMS.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            $path,
            'Unable to filter URI path'
        );
    }

    /**
     * Filters the query string or fragment of a URI.
     *
     * @param mixed $str
     *
     * @throws \InvalidArgumentException If the query or fragment is invalid.
     */
    private function filterQueryAndFragment($str): string
    {
        if (!is_string($str)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        return $this->filterComponent(
            '/(?:[^'.Rfc3986::CHAR_UNRESERVED.Rfc3986::CHAR_SUB_DELIMS.'%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            $str,
            'Unable to filter URI query or fragment'
        );
    }

    private function filterComponent(string $pattern, string $component, string $context): string
    {
        $filtered = preg_replace_callback($pattern, [$this, 'rawurlencodeMatchZero'], $component);

        if ($filtered === null) {
            throw new \RuntimeException($context.': '.preg_last_error_msg());
        }

        return $filtered;
    }

    private function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }

    private function validateState(): void
    {
        if ($this->host === '' && ($this->scheme === 'http' || $this->scheme === 'https')) {
            $this->host = self::HTTP_DEFAULT_HOST;
        }

        if ($this->getAuthority() === '') {
            if (0 === strpos($this->path, '//')) {
                throw new MalformedUriException('The path of a URI without an authority must not start with two slashes "//"');
            }
            if ($this->scheme === '' && false !== strpos(explode('/', $this->path, 2)[0], ':')) {
                throw new MalformedUriException('A relative URI must not have a path beginning with a segment containing a colon');
            }
        }
    }
}
