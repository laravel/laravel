<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use BackedEnum;
use Deprecated;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Idna\Converter as IdnaConverter;
use Stringable;
use Throwable;

use function array_map;
use function array_merge;
use function array_pop;
use function array_reduce;
use function defined;
use function explode;
use function filter_var;
use function function_exists;
use function implode;
use function preg_match;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function substr;

use const FILTER_FLAG_IPV4;
use const FILTER_VALIDATE_IP;

/**
 * A class to parse a URI string according to RFC3986.
 *
 * @link    https://tools.ietf.org/html/rfc3986
 * @package League\Uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   6.0.0
 *
 * @phpstan-type AuthorityMap array{user: ?string, pass: ?string, host: ?string, port: ?int}
 * @phpstan-type ComponentMap array{scheme: ?string, user: ?string, pass: ?string, host: ?string, port: ?int, path: string, query: ?string, fragment: ?string}
 * @phpstan-type InputComponentMap array{scheme? : ?string, user? : ?string, pass? : ?string, host? : ?string, port? : ?int, path? : ?string, query? : ?string, fragment? : ?string}
 */
final class UriString
{
    /**
     * Default URI component values.
     *
     * @var ComponentMap
     */
    private const URI_COMPONENTS = [
        'scheme' => null, 'user' => null, 'pass' => null, 'host' => null,
        'port' => null, 'path' => '', 'query' => null, 'fragment' => null,
    ];

    /**
     * Simple URI which do not need any parsing.
     *
     * @var array<string, array<string>>
     */
    private const URI_SHORTCUTS = [
        '' => ['path' => ''],
        '#' => ['fragment' => ''],
        '?' => ['query' => ''],
        '?#' => ['query' => '', 'fragment' => ''],
        '/' => ['path' => '/'],
        '//' => ['host' => ''],
        '///' => ['host' => '', 'path' => '/'],
    ];

    /**
     * Range of invalid characters in URI 3986 string.
     *
     * @var string
     */
    private const REGEXP_VALID_URI_RFC3986_CHARS = '/^(?:[A-Za-z0-9\-._~:\/?#[\]@!$&\'()*+,;=%]|%[0-9A-Fa-f]{2})*$/';

    /**
     * Range of invalid characters in URI 3987 string.
     *
     * @var string
     */
    private const REGEXP_INVALID_URI_RFC3987_CHARS = '/[\x00-\x1f\x7f\s]/';

    /**
     * RFC3986 regular expression URI splitter.
     *
     * @link https://tools.ietf.org/html/rfc3986#appendix-B
     * @var string
     */
    private const REGEXP_URI_PARTS = ',^
        (?<scheme>(?<scontent>[^:/?\#]+):)?    # URI scheme component
        (?<authority>//(?<acontent>[^/?\#]*))? # URI authority part
        (?<path>[^?\#]*)                       # URI path component
        (?<query>\?(?<qcontent>[^\#]*))?       # URI query component
        (?<fragment>\#(?<fcontent>.*))?        # URI fragment component
    ,x';

    /**
     * URI scheme regular expression.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.1
     * @var string
     */
    private const REGEXP_URI_SCHEME = '/^([a-z][a-z\d+.-]*)?$/i';

    /**
     * Invalid path for URI without scheme and authority regular expression.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.3
     * @var string
     */
    private const REGEXP_INVALID_PATH = ',^(([^/]*):)(.*)?/,';

    /**
     * Host and Port splitter regular expression.
     *
     * @var string
     */
    private const REGEXP_HOST_PORT = ',^(?<host>\[.*\]|[^:]*)(:(?<port>.*))?$,';

    /** @var array<string,int> */
    private const DOT_SEGMENTS = ['.' => 1, '..' => 1];

    /**
     * Generate an IRI string representation (RFC3987) from its parsed representation
     * returned by League\UriString::parse() or PHP's parse_url.
     *
     * If you supply your own array, you are responsible for providing
     * valid components without their URI delimiters.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-5.3
     * @link https://tools.ietf.org/html/rfc3986#section-7.5
     */
    public static function toIriString(BackedEnum|Stringable|string $uri): string
    {
        $components = self::parse($uri);
        $port = null;
        if (isset($components['port'])) {
            $port = (int) $components['port'];
            unset($components['port']);
        }

        if (null !== $components['host']) {
            $components['host'] = IdnaConverter::toUnicode($components['host'])->domain();
        }

        $components['path'] = Encoder::decodePath($components['path']);
        $components['user'] = Encoder::decodeNecessary($components['user']);
        $components['pass'] = Encoder::decodeNecessary($components['pass']);
        $components['query'] = Encoder::decodeQuery($components['query']);
        $components['fragment'] = Encoder::decodeFragment($components['fragment']);

        return self::build([
            ...array_map(fn (?string $value) => match (true) {
                null === $value,
                !str_contains($value, '%20') => $value,
                default => str_replace('%20', ' ', $value),
            }, $components),
            ...['port' => $port],
        ]);
    }

    /**
     * Generate a URI string representation from its parsed representation
     * returned by League\UriString::parse() or PHP's parse_url.
     *
     * If you supply your own array, you are responsible for providing
     * valid components without their URI delimiters.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-5.3
     * @link https://tools.ietf.org/html/rfc3986#section-7.5
     *
     * @param InputComponentMap $components
     */
    public static function build(array $components): string
    {
        return self::buildUri(
            $components['scheme'] ?? null,
            self::buildAuthority($components),
            $components['path'] ?? null,
            $components['query'] ?? null,
            $components['fragment'] ?? null,
        );
    }

    /**
     * Generates a URI string representation based on RFC3986 algorithm.
     *
     * Valid URI component MUST be provided without their URI delimiters
     * but properly encoded.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-5.3
     * @link https://tools.ietf.org/html/rfc3986#section-7.5§
     */
    public static function buildUri(
        ?string $scheme = null,
        ?string $authority = null,
        ?string $path = null,
        ?string $query = null,
        ?string $fragment = null,
    ): string {
        self::validateComponents($scheme, $authority, $path);
        $uri = '';
        if (null !== $scheme) {
            $uri .= $scheme.':';
        }

        if (null !== $authority) {
            $uri .= '//'.$authority;
        }

        $uri .= $path;
        if (null !== $query) {
            $uri .= '?'.$query;
        }

        if (null !== $fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * Generate a URI authority representation from its parsed representation.
     *
     * @param InputComponentMap $components
     */
    public static function buildAuthority(array $components): ?string
    {
        if (!isset($components['host'])) {
            (!isset($components['user']) && !isset($components['pass'])) || throw new SyntaxError('The user info component must not be set if the host is not defined.');
            !isset($components['port']) || throw new SyntaxError('The port component must not be set if the host is not defined.');

            return null;
        }

        $userInfo = $components['user'] ?? null;
        if (isset($components['pass'])) {
            $userInfo .= ':'.$components['pass'];
        }

        $authority = '';
        if (isset($userInfo)) {
            $authority .= $userInfo.'@';
        }

        $authority .= $components['host'];
        if (isset($components['port'])) {
            $authority .= ':'.$components['port'];
        }

        return $authority;
    }

    /**
     * Parses and normalizes the URI following RFC3986 destructive and non-destructive constraints.
     *
     * @throws SyntaxError if the URI is not parsable
     *
     * @return ComponentMap
     */
    public static function parseNormalized(Stringable|string $uri): array
    {
        $components = self::parse($uri);
        if (null !== $components['scheme']) {
            $components['scheme'] = strtolower($components['scheme']);
        }

        $components['host'] = self::normalizeHost($components['host']);
        $path = $components['path'];
        $authority = self::buildAuthority($components);
        //dot segment only happens when:
        // - the path is absolute
        // - the scheme and/or the authority are defined
        if ('/' === ($path[0] ?? '') || '' !== $components['scheme'].$authority) {
            $path = self::removeDotSegments($path);
        }

        // if there is an authority, the path must be absolute
        if ('' !== $path && '/' !== $path[0]) {
            if (null !== $authority) {
                $path = '/'.$path;
            }
        }

        $components['path'] = (string) Encoder::normalizePath($path);
        $components['query'] = Encoder::normalizeQuery($components['query']);
        $components['fragment'] = Encoder::normalizeFragment($components['fragment']);
        $components['user'] = Encoder::normalizeUser($components['user']);
        $components['pass'] = Encoder::normalizePassword($components['pass']);

        return $components;
    }

    /**
     * Parses and normalizes the URI following RFC3986 destructive and non-destructive constraints.
     *
     * @throws SyntaxError if the URI is not parsable
     */
    public static function normalize(Stringable|string $uri): string
    {
        return self::build(self::parseNormalized($uri));
    }

    /**
     * Parses and normalizes the URI following RFC3986 destructive and non-destructive constraints.
     *
     * @throws SyntaxError if the URI is not parsable
     */
    public static function normalizeAuthority(Stringable|string|null $authority): ?string
    {
        if (null === $authority) {
            return null;
        }

        $components = self::parseAuthority($authority);
        $components['host'] = self::normalizeHost($components['host'] ?? null);
        $components['user'] = Encoder::normalizeUser($components['user']);
        $components['pass'] = Encoder::normalizePassword($components['pass']);

        return (string) self::buildAuthority($components);
    }

    /**
     * Resolves a URI against a base URI using RFC3986 rules.
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * a URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with error and exceptions.
     * It MUST not alter or silence them apart from validating its own parameters.
     *
     * @see https://www.rfc-editor.org/rfc/rfc3986.html#section-5
     *
     * @throws SyntaxError if the BaseUri is not absolute or in absence of a BaseUri if the uri is not absolute
     */
    public static function resolve(BackedEnum|Stringable|string $uri, BackedEnum|Stringable|string|null $baseUri = null): string
    {
        if ($uri instanceof BackedEnum) {
            $uri = (string) $uri->value;
        }

        if ($baseUri instanceof BackedEnum) {
            $baseUri = (string) $baseUri->value;
        }

        $uri = (string) $uri;
        if ('' === $uri) {
            $uri = $baseUri ?? throw new SyntaxError("The uri can not be the empty string when there's no base URI.");
        }

        $uriComponents = self::parse($uri);
        $baseUriComponents = $uriComponents;
        if (null !== $baseUri && $uri !== (string) $baseUri) {
            $baseUriComponents = self::parse($baseUri);
        }

        null !== $baseUriComponents['scheme'] || throw new SyntaxError('The base URI must be an absolute URI or null; If the base URI is null the URI must be an absolute URI.');

        $authority = self::buildAuthority($uriComponents);
        $path = self::removeDotSegments($uriComponents['path']);
        if ('' !== $path && '/' !== $path[0] && (null !== $authority || $uriComponents['path'] !== $path)) {
            $path = '/'.$path;
        }

        if (null !== $uriComponents['scheme'] && '' !== $uriComponents['scheme']) {
            return self::buildUri($uriComponents['scheme'], $authority, $path, $uriComponents['query'], $uriComponents['fragment']);
        }

        if (null !== $authority) {
            return self::buildUri($baseUriComponents['scheme'], $authority, $path, $uriComponents['query'], $uriComponents['fragment']);
        }

        [$resolvedPath, $query] = self::resolvePathAndQuery($uriComponents, $baseUriComponents);
        $baseAuthority = self::buildAuthority($baseUriComponents);
        $path = self::removeDotSegments($resolvedPath);
        if ('' !== $path && '/' !== $path[0] && (null !== $baseAuthority || $resolvedPath !== $path)) {
            $path = '/'.$path;
        }

        return self::buildUri($baseUriComponents['scheme'], $baseAuthority, $path, $query, $uriComponents['fragment']);
    }

    /**
     * Filter Dot segment according to RFC3986.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    public static function removeDotSegments(Stringable|string $path): string
    {
        $path = (string) $path;
        if (!str_contains($path, '.')) {
            return $path;
        }

        $reducer = function (array $carry, string $segment): array {
            if ('..' === $segment) {
                array_pop($carry);

                return $carry;
            }

            if (!isset(self::DOT_SEGMENTS[$segment])) {
                $carry[] = $segment;
            }

            return $carry;
        };

        $oldSegments = explode('/', $path);
        $newPath = implode('/', array_reduce($oldSegments, $reducer(...), []));
        if (isset(self::DOT_SEGMENTS[$oldSegments[array_key_last($oldSegments)]])) {
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * Resolves an URI path and query component.
     *
     * @param ComponentMap $uri
     * @param ComponentMap $baseUri
     *
     * @return array{0:string, 1:string|null}
     */
    private static function resolvePathAndQuery(array $uri, array $baseUri): array
    {
        if (str_starts_with($uri['path'], '/')) {
            return [$uri['path'], $uri['query']];
        }

        if ('' === $uri['path']) {
            return [$baseUri['path'], $uri['query'] ?? $baseUri['query']];
        }

        $targetPath = $uri['path'];
        if (null !== self::buildAuthority($baseUri) && '' === $baseUri['path']) {
            $targetPath = '/'.$targetPath;
        }

        if ('' !== $baseUri['path']) {
            $segments = explode('/', $baseUri['path']);
            array_pop($segments);
            if ([] !== $segments) {
                $targetPath = implode('/', $segments).'/'.$targetPath;
            }
        }

        return [$targetPath, $uri['query']];
    }

    public static function containsRfc3986Chars(Stringable|string $uri): bool
    {
        return 1 === preg_match(self::REGEXP_VALID_URI_RFC3986_CHARS, (string) $uri);
    }

    public static function containsRfc3987Chars(Stringable|string $uri): bool
    {
        return 1 !== preg_match(self::REGEXP_INVALID_URI_RFC3987_CHARS, (string) $uri);
    }

    /**
     * Parse a URI string into its components.
     *
     * This method parses a URI and returns an associative array containing any
     * of the various components of the URI that are present.
     *
     * <code>
     * $components = UriString::parse('http://foo@test.example.com:42?query#');
     * var_export($components);
     * //will display
     * array(
     *   'scheme' => 'http',           // the URI scheme component
     *   'user' => 'foo',              // the URI user component
     *   'pass' => null,               // the URI pass component
     *   'host' => 'test.example.com', // the URI host component
     *   'port' => 42,                 // the URI port component
     *   'path' => '',                 // the URI path component
     *   'query' => 'query',           // the URI query component
     *   'fragment' => '',             // the URI fragment component
     * );
     * </code>
     *
     * The returned array is similar to PHP's parse_url return value with the following
     * differences:
     *
     * <ul>
     * <li>All components are always present in the returned array</li>
     * <li>Empty and undefined component are treated differently. And empty component is
     *   set to the empty string while an undefined component is set to the `null` value.</li>
     * <li>The path component is never undefined</li>
     * <li>The method parses the URI following the RFC3986 rules, but you are still
     *   required to validate the returned components against its related scheme specific rules.</li>
     * </ul>
     *
     * @link https://tools.ietf.org/html/rfc3986
     *
     * @throws SyntaxError if the URI contains invalid characters
     * @throws SyntaxError if the URI contains an invalid scheme
     * @throws SyntaxError if the URI contains an invalid path
     *
     * @return ComponentMap
     */
    public static function parse(BackedEnum|Stringable|string|int $uri): array
    {
        if ($uri instanceof BackedEnum) {
            $uri = $uri->value;
        }

        $uri = (string) $uri;
        if (isset(self::URI_SHORTCUTS[$uri])) {
            /** @var ComponentMap $components */
            $components = [...self::URI_COMPONENTS, ...self::URI_SHORTCUTS[$uri]];

            return $components;
        }

        self::containsRfc3987Chars($uri) || throw new SyntaxError(sprintf('The uri `%s` contains invalid characters', $uri));

        //if the first character is a known URI delimiter, parsing can be simplified
        $first_char = $uri[0];

        //The URI is made of the fragment only
        if ('#' === $first_char) {
            [, $fragment] = explode('#', $uri, 2);
            $components = self::URI_COMPONENTS;
            $components['fragment'] = $fragment;

            return $components;
        }

        //The URI is made of the query and fragment
        if ('?' === $first_char) {
            [, $partial] = explode('?', $uri, 2);
            [$query, $fragment] = explode('#', $partial, 2) + [1 => null];
            $components = self::URI_COMPONENTS;
            $components['query'] = $query;
            $components['fragment'] = $fragment;

            return $components;
        }

        //use RFC3986 URI regexp to split the URI
        preg_match(self::REGEXP_URI_PARTS, $uri, $parts);
        $parts += ['query' => '', 'fragment' => ''];

        if (':' === ($parts['scheme']  ?? null) || 1 !== preg_match(self::REGEXP_URI_SCHEME, $parts['scontent'] ?? '')) {
            throw new SyntaxError(sprintf('The uri `%s` contains an invalid scheme', $uri));
        }

        if ('' === ($parts['scheme'] ?? '').($parts['authority'] ?? '') && 1 === preg_match(self::REGEXP_INVALID_PATH, $parts['path'] ?? '')) {
            throw new SyntaxError(sprintf('The uri `%s` contains an invalid path.', $uri));
        }

        /** @var ComponentMap $components */
        $components = array_merge(
            self::URI_COMPONENTS,
            '' === ($parts['authority'] ?? null) ? [] : self::parseAuthority($parts['acontent'] ?? null),
            [
                'path' => $parts['path'] ?? '',
                'scheme' => '' === ($parts['scheme'] ?? null) ? null : ($parts['scontent'] ?? null),
                'query' => '' === $parts['query'] ? null : ($parts['qcontent'] ?? null),
                'fragment' => '' === $parts['fragment'] ? null : ($parts['fcontent'] ?? null),
            ]
        );

        return $components;
    }

    /**
     * Assert the URI internal state is valid.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3
     * @link https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @throws SyntaxError
     */
    private static function validateComponents(?string $scheme, ?string $authority, ?string $path): void
    {
        if (null !== $authority) {
            if (null !== $path && '' !== $path && '/' !== $path[0]) {
                throw new SyntaxError('If an authority is present the path must be empty or start with a `/`.');
            }

            return;
        }

        if (null === $path || '' === $path) {
            return;
        }

        if (str_starts_with($path, '//')) {
            throw new SyntaxError('If there is no authority the path `'.$path.'` cannot start with a `//`.');
        }

        if (null !== $scheme || false === ($pos = strpos($path, ':'))) {
            return;
        }

        if (!str_contains(substr($path, 0, $pos), '/')) {
            throw new SyntaxError('In absence of a scheme and an authority the first path segment cannot contain a colon (":") character.');
        }
    }

    /**
     * Parses the URI authority part.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @throws SyntaxError If the port component is invalid
     *
     * @return AuthorityMap
     */
    public static function parseAuthority(BackedEnum|Stringable|string|null $authority): array
    {
        $components = ['user' => null, 'pass' => null, 'host' => null, 'port' => null];
        if (null === $authority) {
            return $components;
        }

        if ($authority instanceof BackedEnum) {
            $authority = $authority->value;
        }
        $authority = (string) $authority;
        $components['host'] = '';
        if ('' === $authority) {
            return $components;
        }

        $parts = explode('@', $authority, 2);
        if (isset($parts[1])) {
            [$components['user'], $components['pass']] = explode(':', $parts[0], 2) + [1 => null];
        }

        preg_match(self::REGEXP_HOST_PORT, $parts[1] ?? $parts[0], $matches);
        $matches += ['port' => ''];

        $components['port'] = self::filterPort($matches['port']);
        $components['host'] = self::filterHost($matches['host'] ?? '');

        return $components;
    }

    /**
     * Filter and format the port component.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @throws SyntaxError if the registered name is invalid
     */
    private static function filterPort(string $port): ?int
    {
        return match (true) {
            '' === $port => null,
            1 === preg_match('/^\d*$/', $port) => (int) $port,
            default => throw new SyntaxError(sprintf('The port `%s` is invalid', $port)),
        };
    }

    /**
     * Returns whether a hostname is valid.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @throws SyntaxError if the registered name is invalid
     */
    private static function filterHost(Stringable|string|null $host): ?string
    {
        try {
            return HostRecord::from($host)->value;
        } catch (Throwable) {
            throw new SyntaxError(sprintf('Host `%s` is invalid : the IP host is malformed', $host));
        }
    }

    /**
     * Tells whether the scheme component is valid.
     */
    public static function isValidScheme(BackedEnum|Stringable|string|null $scheme): bool
    {
        if ($scheme instanceof BackedEnum) {
            $scheme = $scheme->value;
        }

        return null === $scheme || 1 === preg_match('/^[A-Za-z]([-A-Za-z\d+.]+)?$/', (string) $scheme);
    }

    private static function normalizeHost(BackedEnum|Stringable|string|null $host): ?string
    {
        if ($host instanceof BackedEnum) {
            $host = $host->value;
        }

        if (null !== $host) {
            $host = (string) $host;
        }

        if (null === $host || false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $host;
        }

        $host = (string) Encoder::normalizeHost($host);
        static $isSupported = null;
        $isSupported ??= (function_exists('\idn_to_ascii') && defined('\INTL_IDNA_VARIANT_UTS46'));
        if (! $isSupported) {
            return $host;
        }

        $idnaHost = IdnaConverter::toAscii($host);
        if (!$idnaHost->hasErrors()) {
            return $idnaHost->domain();
        }

        return $host;
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.6.0
     * @codeCoverageIgnore
     * @see HostRecoord::validate()
     *
     * Create a new instance from the environment.
     */
    #[Deprecated(message:'use League\Uri\HostRecord::validate() instead', since:'league/uri:7.6.0')]
    public static function isValidHost(Stringable|string|null $host): bool
    {
        return HostRecord::isValid($host);
    }
}
