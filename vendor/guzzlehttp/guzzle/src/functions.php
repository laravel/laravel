<?php

namespace GuzzleHttp;

/**
 * Debug function used to describe the provided value type and class.
 *
 * @param mixed $input Any type of variable to describe the type of. This
 *                     parameter misses a typehint because of that.
 *
 * @return string Returns a string containing the type of the variable and
 *                if a class is provided, the class name.
 *
 * @deprecated describe_type will be removed in guzzlehttp/guzzle:8.0. Use get_debug_type() instead.
 */
function describe_type($input): string
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use get_debug_type() instead.', __FUNCTION__);

    switch (\gettype($input)) {
        case 'object':
            return 'object('.\get_class($input).')';
        case 'array':
            return 'array('.\count($input).')';
        default:
            \ob_start();
            \var_dump($input);
            // normalize float vs double
            /** @var string $varDumpContent */
            $varDumpContent = \ob_get_clean();

            return \str_replace('double(', 'float(', \rtrim($varDumpContent, " \n\r\t\0\x0B"));
    }
}

/**
 * Parses an array of header lines into an associative array of headers.
 *
 * @param iterable $lines Header lines array of strings in the following
 *                        format: "Name: Value"
 *
 * @deprecated headers_from_lines will be removed in guzzlehttp/guzzle:8.0. Use Utils::headersFromLines instead.
 */
function headers_from_lines(iterable $lines): array
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use Utils::headersFromLines() instead.', __FUNCTION__);

    return Utils::headersFromLines($lines);
}

/**
 * Returns a debug stream based on the provided variable.
 *
 * @param mixed $value Optional value
 *
 * @return resource
 *
 * @deprecated debug_resource will be removed in guzzlehttp/guzzle:8.0. Use Utils::debugResource instead.
 */
function debug_resource($value = null)
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use Utils::debugResource() instead.', __FUNCTION__);

    return Utils::debugResource($value);
}

/**
 * Chooses and creates a default handler to use based on the environment.
 *
 * The returned handler is not wrapped by any default middlewares.
 *
 * @return callable(\Psr\Http\Message\RequestInterface, array): Promise\PromiseInterface Returns the best handler for the given system.
 *
 * @throws \RuntimeException if no viable Handler is available.
 *
 * @deprecated choose_handler will be removed in guzzlehttp/guzzle:8.0. Use Utils::chooseHandler instead.
 */
function choose_handler(): callable
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use Utils::chooseHandler() instead.', __FUNCTION__);

    return Utils::chooseHandler();
}

/**
 * Get the default User-Agent string to use with Guzzle.
 *
 * @deprecated default_user_agent will be removed in guzzlehttp/guzzle:8.0. Use Utils::defaultUserAgent instead.
 */
function default_user_agent(): string
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use Utils::defaultUserAgent() instead.', __FUNCTION__);

    return Utils::defaultUserAgent();
}

/**
 * Returns the default cacert bundle for the current system.
 *
 * First, the openssl.cafile and curl.cainfo php.ini settings are checked.
 * If those settings are not configured, then the common locations for
 * bundles found on Red Hat, CentOS, Fedora, Ubuntu, Debian, FreeBSD, OS X
 * and Windows are checked. If any of these file locations are found on
 * disk, they will be utilized.
 *
 * Note: the result of this function is cached for subsequent calls.
 *
 * @throws \RuntimeException if no bundle can be found.
 *
 * @deprecated default_ca_bundle will be removed in guzzlehttp/guzzle:8.0. This function is not needed in PHP 5.6+.
 */
function default_ca_bundle(): string
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. This function is not needed in PHP 5.6+.', __FUNCTION__);

    static $cached = null;
    static $cafiles = [
        // Red Hat, CentOS, Fedora (provided by the ca-certificates package)
        '/etc/pki/tls/certs/ca-bundle.crt',
        // Ubuntu, Debian (provided by the ca-certificates package)
        '/etc/ssl/certs/ca-certificates.crt',
        // FreeBSD (provided by the ca_root_nss package)
        '/usr/local/share/certs/ca-root-nss.crt',
        // SLES 12 (provided by the ca-certificates package)
        '/var/lib/ca-certificates/ca-bundle.pem',
        // OS X provided by homebrew (using the default path)
        '/usr/local/etc/openssl/cert.pem',
        // Google app engine
        '/etc/ca-certificates.crt',
        // Windows?
        'C:\\windows\\system32\\curl-ca-bundle.crt',
        'C:\\windows\\curl-ca-bundle.crt',
    ];

    if ($cached) {
        return $cached;
    }

    if ($ca = \ini_get('openssl.cafile')) {
        return $cached = $ca;
    }

    if ($ca = \ini_get('curl.cainfo')) {
        return $cached = $ca;
    }

    foreach ($cafiles as $filename) {
        if (\file_exists($filename)) {
            return $cached = $filename;
        }
    }

    throw new \RuntimeException(
        <<< EOT
No system CA bundle could be found in any of the the common system locations.
PHP versions earlier than 5.6 are not properly configured to use the system's
CA bundle by default. In order to verify peer certificates, you will need to
supply the path on disk to a certificate bundle to the 'verify' request option:
https://github.com/guzzle/guzzle/blob/7.15/docs/request-options.md#verify. If
you do not need a specific certificate bundle, then Mozilla provides a commonly
used CA bundle which can be downloaded here (provided by the maintainer of
cURL): https://curl.se/ca/cacert.pem. Once you have a CA bundle available on
disk, you can set the 'openssl.cafile' PHP ini setting to point to the path to
the file, allowing you to omit the 'verify' request option. See
https://curl.se/docs/sslcerts.html for more information.
EOT
    );
}

/**
 * Creates an associative array of lowercase header names to the actual
 * header casing.
 *
 * @deprecated normalize_header_keys will be removed in guzzlehttp/guzzle:8.0. Use Utils::normalizeHeaderKeys instead.
 */
function normalize_header_keys(array $headers): array
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use Utils::normalizeHeaderKeys() instead.', __FUNCTION__);

    return Utils::normalizeHeaderKeys($headers);
}

/**
 * Returns true if the provided host matches any of the no proxy areas.
 *
 * This method will strip a port from the host if it is present. Each pattern
 * can be matched with an exact match (e.g., "foo.com" == "foo.com") or a
 * partial match: (e.g., "foo.com" == "baz.foo.com" and ".foo.com" ==
 * "baz.foo.com", but ".foo.com" != "foo.com").
 *
 * Areas are matched in the following cases:
 * 1. "*" (without quotes) always matches any hosts.
 * 2. An exact match.
 * 3. The area starts with "." and the area is the last part of the host. e.g.
 *    '.mit.edu' will match any host that ends with '.mit.edu'.
 *
 * @param string   $host         Host to check against the patterns.
 * @param string[] $noProxyArray An array of host patterns.
 *
 * @throws Exception\InvalidArgumentException
 *
 * @deprecated is_host_in_noproxy will be removed in guzzlehttp/guzzle:8.0. Use Utils::isHostInNoProxy instead.
 */
function is_host_in_noproxy(string $host, array $noProxyArray): bool
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use Utils::isHostInNoProxy() instead.', __FUNCTION__);

    return Utils::isHostInNoProxy($host, $noProxyArray);
}

/**
 * Wrapper for json_decode that throws when an error occurs.
 *
 * @param string $json    JSON data to parse
 * @param bool   $assoc   When true, returned objects will be converted
 *                        into associative arrays.
 * @param int    $depth   User specified recursion depth.
 * @param int    $options Bitmask of JSON decode options.
 *
 * @return object|array|string|int|float|bool|null
 *
 * @throws Exception\InvalidArgumentException if the JSON cannot be decoded.
 *
 * @see https://www.php.net/manual/en/function.json-decode.php
 * @deprecated json_decode will be removed in guzzlehttp/guzzle:8.0. Use PHP's json_decode() instead.
 */
function json_decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use PHP\'s json_decode() instead.', __FUNCTION__);

    if ($depth < 1) {
        throw new Exception\InvalidArgumentException('json_decode error: Maximum stack depth exceeded');
    }

    $data = \json_decode($json, $assoc, $depth, $options);
    if (\JSON_ERROR_NONE !== \json_last_error()) {
        throw new Exception\InvalidArgumentException('json_decode error: '.\json_last_error_msg());
    }

    /** @var object|array|string|int|float|bool|null $data */
    return $data;
}

/**
 * Wrapper for JSON encoding that throws when an error occurs.
 *
 * @param mixed $value   The value being encoded
 * @param int   $options JSON encode option bitmask
 * @param int   $depth   Set the maximum depth. Must be greater than zero.
 *
 * @throws Exception\InvalidArgumentException if the JSON cannot be encoded.
 *
 * @see https://www.php.net/manual/en/function.json-encode.php
 * @deprecated json_encode will be removed in guzzlehttp/guzzle:8.0. Use PHP's json_encode() instead.
 */
function json_encode($value, int $options = 0, int $depth = 512): string
{
    \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. Use PHP\'s json_encode() instead.', __FUNCTION__);

    /** @var positive-int $depth */
    $json = \json_encode($value, $options, $depth);
    if (\JSON_ERROR_NONE !== \json_last_error()) {
        throw new Exception\InvalidArgumentException('json_encode error: '.\json_last_error_msg());
    }

    /** @var non-empty-string $json */
    return $json;
}
