<?php

namespace GuzzleHttp;

use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Handler\CurlShareHandleState;
use GuzzleHttp\Handler\CurlVersion;
use GuzzleHttp\Handler\Proxy;
use GuzzleHttp\Handler\StreamHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class Utils
{
    /**
     * Debug function used to describe the provided value type and class.
     *
     * @param mixed $input
     *
     * @return string Returns a string containing the type of the variable and
     *                if a class is provided, the class name.
     *
     * @deprecated Utils::describeType() will be removed in guzzlehttp/guzzle:8.0. Use get_debug_type() instead.
     */
    public static function describeType($input): string
    {
        \trigger_deprecation(
            'guzzlehttp/guzzle',
            '7.12',
            '%s() is deprecated and will be removed in 8.0. Use get_debug_type() instead.',
            __METHOD__
        );

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
     */
    public static function headersFromLines(iterable $lines): array
    {
        $headers = [];

        foreach ($lines as $line) {
            $parts = \explode(':', $line, 2);
            $headers[\trim($parts[0], " \n\r\t\0\x0B")][] = isset($parts[1]) ? \trim($parts[1], " \n\r\t\0\x0B") : null;
        }

        return $headers;
    }

    /**
     * Returns a debug stream based on the provided variable.
     *
     * @param mixed $value Optional value
     *
     * @return resource
     */
    public static function debugResource($value = null)
    {
        if (\is_resource($value)) {
            return $value;
        }
        if (\defined('STDOUT')) {
            return \STDOUT;
        }

        return Psr7\Utils::tryFopen('php://output', 'w');
    }

    /**
     * Chooses and creates a default handler to use based on the environment.
     *
     * The returned handler is not wrapped by any default middlewares.
     *
     * @param array{transport_sharing?: mixed, max_host_connections?: mixed, max_total_connections?: mixed, multiplex?: mixed} $handlerOptions Handler constructor options.
     *
     * @return callable(RequestInterface, array): Promise\PromiseInterface Returns the best handler for the given system.
     *
     * @throws \RuntimeException if no viable Handler is available.
     */
    public static function chooseHandler(array $handlerOptions = []): callable
    {
        $sharingMode = CurlShareHandleState::normalizeMode($handlerOptions['transport_sharing'] ?? null, 'transport_sharing');
        $sharingRequired = self::isTransportSharingRequired($sharingMode);
        $connectionCapsRequired = self::hasConnectionCapOptions($handlerOptions);
        $handler = self::createCurlHandler($sharingMode, $handlerOptions);

        if ($sharingRequired && $handler === null) {
            throw new \RuntimeException('Required transport sharing requires the PHP cURL extension, curl_exec() or curl_multi_exec(), and libcurl 7.21.2 or higher.');
        }

        if (\ini_get('allow_url_fopen')) {
            return self::addStreamHandler($handler, $sharingMode, $sharingRequired, self::connectionCapOptions($handlerOptions));
        }

        if ($handler !== null) {
            return $handler;
        }

        if ($connectionCapsRequired) {
            throw new \RuntimeException('Connection cap options require a cap-capable cURL multi handler or the allow_url_fopen ini setting for stream fallback.');
        }

        throw new \RuntimeException('GuzzleHttp requires cURL, the allow_url_fopen ini setting, or a custom HTTP handler.');
    }

    private static function isTransportSharingRequired(string $sharingMode): bool
    {
        return $sharingMode === TransportSharing::HANDLER_REQUIRE;
    }

    /**
     * @param array{max_host_connections?: mixed, max_total_connections?: mixed} $handlerOptions
     */
    private static function hasConnectionCapOptions(array $handlerOptions): bool
    {
        return self::connectionCapOptions($handlerOptions) !== [];
    }

    /**
     * @param array{max_host_connections?: mixed, max_total_connections?: mixed, multiplex?: mixed} $handlerOptions
     *
     * @return (callable(RequestInterface, array): Promise\PromiseInterface)|null
     */
    private static function createCurlHandler(string $sharingMode, array $handlerOptions): ?callable
    {
        if (!\defined('CURLOPT_CUSTOMREQUEST') || !CurlVersion::supportsCurlHandler()) {
            return null;
        }

        $connectionCapOptions = self::connectionCapOptions($handlerOptions);
        if ($connectionCapOptions !== [] && (!CurlVersion::supportsConnectionCaps() || !\function_exists('curl_multi_exec'))) {
            return null;
        }

        $curlHandlerOptions = self::createCurlHandlerOptions($sharingMode);
        $curlMultiHandlerOptions = $curlHandlerOptions + $connectionCapOptions;
        if (($handlerOptions['multiplex'] ?? null) === Multiplexing::NONE) {
            // Forwarded to the CurlMultiHandler only: CurlHandler and
            // StreamHandler validate known options, and both satisfy NONE
            // per-request without a handler option.
            $curlMultiHandlerOptions['multiplex'] = Multiplexing::NONE;
        }

        if (\function_exists('curl_multi_exec') && \function_exists('curl_exec')) {
            $multiHandler = new CurlMultiHandler($curlMultiHandlerOptions);

            if ($connectionCapOptions !== []) {
                // Connection caps only govern transfers on the multi handle, so
                // the synchronous CurlHandler fast path would escape them.
                return $multiHandler;
            }

            return Proxy::wrapSync($multiHandler, new CurlHandler($curlHandlerOptions));
        }

        if ($connectionCapOptions === [] && \function_exists('curl_exec')) {
            return new CurlHandler($curlHandlerOptions);
        }

        if (\function_exists('curl_multi_exec')) {
            return new CurlMultiHandler($curlMultiHandlerOptions);
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function createCurlHandlerOptions(string $sharingMode): array
    {
        if ($sharingMode === TransportSharing::NONE) {
            return [];
        }

        $shareState = CurlShareHandleState::fromOption($sharingMode);

        return $shareState === null ? [] : ['transport_sharing' => $shareState];
    }

    /**
     * @param array{max_host_connections?: mixed, max_total_connections?: mixed} $handlerOptions
     *
     * @return array{max_host_connections?: int, max_total_connections?: int}
     */
    private static function connectionCapOptions(array $handlerOptions): array
    {
        $options = [];
        foreach (['max_host_connections', 'max_total_connections'] as $capOption) {
            $value = $handlerOptions[$capOption] ?? null;
            if ($value === null) {
                continue;
            }

            if (!\is_int($value) || $value < 1) {
                throw new InvalidArgumentException(\sprintf('%s must be a positive integer.', $capOption));
            }

            $options[$capOption] = $value;
        }

        return $options;
    }

    /**
     * @param (callable(RequestInterface, array): Promise\PromiseInterface)|null $handler
     * @param array{max_host_connections?: int, max_total_connections?: int}     $connectionCapOptions
     *
     * @return callable(RequestInterface, array): Promise\PromiseInterface
     */
    private static function addStreamHandler(?callable $handler, string $sharingMode, bool $sharingRequired, array $connectionCapOptions): callable
    {
        $streamHandler = new StreamHandler(['transport_sharing' => $sharingMode] + $connectionCapOptions);

        if ($handler === null) {
            return $streamHandler;
        }

        if (!$sharingRequired) {
            $handler = Proxy::wrapTlsFallback($handler, $streamHandler);
        }

        return Proxy::wrapStreaming($handler, $streamHandler);
    }

    /**
     * Get the default User-Agent string to use with Guzzle.
     */
    public static function defaultUserAgent(): string
    {
        return sprintf('GuzzleHttp/%d', ClientInterface::MAJOR_VERSION);
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
     * @deprecated Utils::defaultCaBundle will be removed in guzzlehttp/guzzle:8.0. This method is not needed in PHP 5.6+.
     */
    public static function defaultCaBundle(): string
    {
        \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s() is deprecated and will be removed in 8.0. This method is not needed in PHP 5.6+.', __METHOD__);

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
     */
    public static function normalizeHeaderKeys(array $headers): array
    {
        $result = [];
        foreach (\array_keys($headers) as $key) {
            $result[Psr7\Utils::asciiToLower((string) $key)] = $key;
        }

        return $result;
    }

    /**
     * @param mixed $protocols
     *
     * @return string[]
     *
     * @throws InvalidArgumentException
     */
    public static function normalizeProtocols($protocols): array
    {
        if (!\is_array($protocols) || $protocols === []) {
            throw new InvalidArgumentException('protocols must be a non-empty array of "http" and/or "https"');
        }

        $normalized = [];

        foreach ($protocols as $protocol) {
            if (!\is_string($protocol)) {
                throw new InvalidArgumentException('protocols must contain only strings');
            }

            if ($protocol !== 'http' && $protocol !== 'https') {
                throw new InvalidArgumentException('protocols may only contain "http" and "https"');
            }

            $normalized[$protocol] = true;
        }

        return \array_keys($normalized);
    }

    /**
     * Returns true if the provided host matches any of the no proxy areas.
     *
     * This method will strip a port from the host if it is present. Domain
     * patterns are matched case-insensitively. Exact IP literal patterns are
     * matched by their normalized binary address.
     *
     * Areas are matched in the following cases:
     * 1. "*" (without quotes) always matches any hosts.
     * 2. An exact domain or IP literal match.
     * 3. A bare domain matches itself and its subdomains. e.g. 'mit.edu' will
     *    match 'mit.edu' and 'foo.mit.edu'.
     * 4. The area starts with "." and the area is the last part of the host. e.g.
     *    '.mit.edu' will match any host that ends with '.mit.edu'.
     * 5. IP CIDR entries match IP literal hosts. e.g. '192.168.0.0/16' will
     *    match '192.168.1.10' and 'fd00::/8' will match '[fd00::1]'.
     *
     * @param string   $host         Host to check against the patterns.
     * @param string[] $noProxyArray An array of host or CIDR patterns.
     *
     * @throws InvalidArgumentException
     */
    public static function isHostInNoProxy(string $host, array $noProxyArray): bool
    {
        if (\strlen($host) === 0) {
            throw new InvalidArgumentException('Empty host provided');
        }

        $target = self::parseNoProxyHostString($host);
        if ($target === null) {
            return false;
        }

        return self::matchesNoProxyList($target, $noProxyArray);
    }

    /**
     * Returns true if the provided URI matches any of the no proxy areas.
     *
     * Matching follows the same rules as isHostInNoProxy(), with the
     * addition that areas may carry a port (e.g. "example.com:8080" or
     * "[::1]:8080") which is compared against the URI port (or the scheme
     * default port when the URI has none).
     *
     * @param mixed $noProxy No-proxy host, host-and-port, or CIDR patterns.
     *
     * @internal
     */
    public static function isUriInNoProxy(UriInterface $uri, $noProxy): bool
    {
        if (\is_string($noProxy)) {
            $noProxy = \explode(',', $noProxy);
        }

        if (!\is_array($noProxy)) {
            return false;
        }

        $target = self::parseNoProxyTarget($uri);
        if ($target === null) {
            return false;
        }

        return self::matchesNoProxyList($target, $noProxy);
    }

    /**
     * @param array{type: string, value: string, port: int|null, matchesRoot: bool} $target
     * @param array<array-key, mixed>                                               $noProxy
     */
    private static function matchesNoProxyList(array $target, array $noProxy): bool
    {
        foreach ($noProxy as $area) {
            if (!\is_string($area)) {
                continue;
            }

            $area = \trim($area, " \n\r\t\0\x0B");

            // Always match on wildcards.
            if ($area === '*') {
                return true;
            }

            $rule = self::parseNoProxyRule($area);
            if ($rule !== null && self::noProxyRuleMatches($target, $rule)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{type: string, value: string, port: int|null, matchesRoot: bool}|null
     */
    private static function parseNoProxyTarget(UriInterface $uri): ?array
    {
        $host = $uri->getHost();
        if ($host === '') {
            return null;
        }

        return self::parseNoProxyHost($host, $uri->getPort() ?? self::getDefaultPort($uri->getScheme()), true);
    }

    /**
     * @return array{type: string, value: string, port: int|null, matchesRoot: bool}|null
     */
    private static function parseNoProxyHostString(string $host): ?array
    {
        $hostAndPort = self::splitNoProxyHostAndPort($host);
        if ($hostAndPort === null) {
            return null;
        }

        [$host] = $hostAndPort;

        return self::parseNoProxyHost($host, null, true);
    }

    /**
     * @return array{type: string, value: string, port: int|null, matchesRoot: bool}|array{type: string, value: string, prefix: int}|null
     */
    private static function parseNoProxyRule(string $area): ?array
    {
        $area = \trim($area, " \n\r\t\0\x0B");
        if ($area === '' || $area === '*') {
            return null;
        }

        if (\strpos($area, '/') !== false) {
            return self::parseNoProxyCidrRule($area);
        }

        $matchesRoot = true;
        if ($area[0] === '.') {
            $matchesRoot = false;
            $area = \substr($area, 1);
        }

        $hostAndPort = self::splitNoProxyHostAndPort($area);
        if ($hostAndPort === null) {
            return null;
        }

        [$host, $port] = $hostAndPort;

        if ($host === '*') {
            if (!$matchesRoot) {
                return null;
            }

            return [
                'type' => 'wildcard',
                'value' => '*',
                'port' => $port,
                'matchesRoot' => true,
            ];
        }

        $rule = self::parseNoProxyHost($host, $port, $matchesRoot);
        if ($rule !== null && !$matchesRoot && $rule['type'] === 'ip') {
            return null;
        }

        return $rule;
    }

    /**
     * @return array{type: string, value: string, port: int|null, matchesRoot: bool}|null
     */
    private static function parseNoProxyHost(string $host, ?int $port, bool $matchesRoot): ?array
    {
        if ($host !== '' && $host[0] === '[') {
            if (\substr($host, -1) !== ']') {
                return null;
            }

            $address = \substr($host, 1, -1);
            if (!\filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
                return null;
            }

            $host = $address;
        }

        $packedIp = self::packIpAddress($host);
        if ($packedIp !== false) {
            return [
                'type' => 'ip',
                'value' => $packedIp,
                'port' => $port,
                'matchesRoot' => $matchesRoot,
            ];
        }

        if ($host === '' || \strpos($host, ':') !== false) {
            return null;
        }

        // Normalize a single DNS root dot for no-proxy domain matching.
        if (\substr($host, -1) === '.') {
            $host = \substr($host, 0, -1);
            if ($host === '') {
                return null;
            }
        }

        return [
            'type' => 'domain',
            'value' => Psr7\Utils::asciiToLower($host),
            'port' => $port,
            'matchesRoot' => $matchesRoot,
        ];
    }

    /**
     * @return array{0: string, 1: int|null}|null
     */
    private static function splitNoProxyHostAndPort(string $area): ?array
    {
        if ($area !== '' && $area[0] === '[') {
            $closingBracket = \strpos($area, ']');
            if ($closingBracket === false) {
                return null;
            }

            $host = \substr($area, 0, $closingBracket + 1);
            $tail = \substr($area, $closingBracket + 1);
            if ($tail === '') {
                return [$host, null];
            }

            if ($tail[0] !== ':') {
                return null;
            }

            $port = self::parseNoProxyPort(\substr($tail, 1));

            return $port === null ? null : [$host, $port];
        }

        if (self::packIpAddress($area) !== false) {
            return [$area, null];
        }

        $colon = \strrpos($area, ':');
        if ($colon === false) {
            return [$area, null];
        }

        $port = self::parseNoProxyPort(\substr($area, $colon + 1));
        if ($port === null) {
            return null;
        }

        return [\substr($area, 0, $colon), $port];
    }

    private static function parseNoProxyPort(string $port): ?int
    {
        return self::parseBoundedUnsignedInteger($port, 65535);
    }

    /**
     * @return array{type: string, value: string, prefix: int}|null
     */
    private static function parseNoProxyCidrRule(string $area): ?array
    {
        $slash = \strpos($area, '/');
        if ($slash === false) {
            return null;
        }

        $prefix = \substr($area, $slash + 1);

        $network = \substr($area, 0, $slash);
        if ($network !== '' && $network[0] === '[' && \substr($network, -1) === ']') {
            $network = \substr($network, 1, -1);
        }

        $network = self::packIpAddress($network);
        if ($network === false) {
            return null;
        }

        $prefix = self::parseBoundedUnsignedInteger($prefix, \strlen($network) * 8);
        if ($prefix === null) {
            return null;
        }

        return [
            'type' => 'cidr',
            'value' => $network,
            'prefix' => $prefix,
        ];
    }

    private static function parseBoundedUnsignedInteger(string $value, int $max): ?int
    {
        if ($value === '' || !\ctype_digit($value)) {
            return null;
        }

        $normalized = \ltrim($value, '0');
        $normalized = $normalized === '' ? '0' : $normalized;
        $limit = (string) $max;

        if (\strlen($normalized) > \strlen($limit) || (\strlen($normalized) === \strlen($limit) && \strcmp($normalized, $limit) > 0)) {
            return null;
        }

        return (int) $normalized;
    }

    /**
     * @param array{type: string, value: string, port: int|null, matchesRoot: bool}                      $target
     * @param array{type: string, value: string, port?: int|null, matchesRoot?: bool, prefix?: int|null} $rule
     */
    private static function noProxyRuleMatches(array $target, array $rule): bool
    {
        if ($rule['type'] === 'wildcard') {
            return ($rule['port'] ?? null) === null || $rule['port'] === $target['port'];
        }

        if ($rule['type'] === 'cidr') {
            if ($target['type'] !== 'ip' || !isset($rule['prefix'])) {
                return false;
            }

            if (\strlen($target['value']) !== \strlen($rule['value'])) {
                return false;
            }

            return self::ipMatchesPrefix($target['value'], $rule['value'], $rule['prefix']);
        }

        if (($rule['port'] ?? null) !== null && $rule['port'] !== $target['port']) {
            return false;
        }

        if ($rule['type'] !== $target['type']) {
            return false;
        }

        if ($rule['type'] === 'ip') {
            return $rule['value'] === $target['value'];
        }

        if (($rule['matchesRoot'] ?? false) && $target['value'] === $rule['value']) {
            return true;
        }

        $suffix = '.'.$rule['value'];

        return \substr($target['value'], -\strlen($suffix)) === $suffix;
    }

    /**
     * @return string|false
     */
    private static function packIpAddress(string $ip)
    {
        if (!\filter_var($ip, \FILTER_VALIDATE_IP)) {
            return false;
        }

        return \inet_pton($ip);
    }

    private static function ipMatchesPrefix(string $address, string $network, int $prefix): bool
    {
        $fullBytes = \intdiv($prefix, 8);
        $remainingBits = $prefix % 8;

        if ($fullBytes > 0 && \substr($address, 0, $fullBytes) !== \substr($network, 0, $fullBytes)) {
            return false;
        }

        if ($remainingBits === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remainingBits)) & 0xFF;

        return (\ord($address[$fullBytes]) & $mask) === (\ord($network[$fullBytes]) & $mask);
    }

    private static function getDefaultPort(string $scheme): ?int
    {
        if ($scheme === 'http') {
            return 80;
        }

        if ($scheme === 'https') {
            return 443;
        }

        return null;
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
     * @throws InvalidArgumentException if the JSON cannot be decoded.
     *
     * @see https://www.php.net/manual/en/function.json-decode.php
     * @deprecated Utils::jsonDecode() will be removed in guzzlehttp/guzzle:8.0. Use PHP's json_decode() instead.
     */
    public static function jsonDecode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        \trigger_deprecation('guzzlehttp/guzzle', '7.15', '%s() is deprecated and will be removed in 8.0. Use PHP\'s json_decode() instead.', __METHOD__);

        if ($depth < 1) {
            throw new InvalidArgumentException('json_decode error: Maximum stack depth exceeded');
        }

        $data = \json_decode($json, $assoc, $depth, $options);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new InvalidArgumentException('json_decode error: '.\json_last_error_msg());
        }

        return $data;
    }

    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value   The value being encoded
     * @param int   $options JSON encode option bitmask
     * @param int   $depth   Set the maximum depth. Must be greater than zero.
     *
     * @throws InvalidArgumentException if the JSON cannot be encoded.
     *
     * @see https://www.php.net/manual/en/function.json-encode.php
     * @deprecated Utils::jsonEncode() will be removed in guzzlehttp/guzzle:8.0. Use PHP's json_encode() instead.
     */
    public static function jsonEncode($value, int $options = 0, int $depth = 512): string
    {
        \trigger_deprecation('guzzlehttp/guzzle', '7.15', '%s() is deprecated and will be removed in 8.0. Use PHP\'s json_encode() instead.', __METHOD__);

        $json = \json_encode($value, $options, $depth);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new InvalidArgumentException('json_encode error: '.\json_last_error_msg());
        }

        /** @var string */
        return $json;
    }

    /**
     * Wrapper for the hrtime() or microtime() functions
     * (depending on the PHP version, one of the two is used)
     *
     * @return float UNIX timestamp
     *
     * @internal
     */
    public static function currentTime(): float
    {
        return (float) \function_exists('hrtime') ? \hrtime(true) / 1e9 : \microtime(true);
    }

    /**
     * @param mixed $value
     *
     * @internal
     */
    public static function normalizeIdnConversionOption($value): ?int
    {
        if ($value === null || $value === false) {
            return null;
        }

        if ($value === true) {
            return \IDNA_DEFAULT;
        }

        if (\is_int($value)) {
            return $value;
        }

        if ((\is_string($value) && \is_numeric($value)) || (\is_float($value) && \is_finite($value))) {
            \trigger_deprecation(
                'guzzlehttp/guzzle',
                '7.11',
                'Passing %s as the "idn_conversion" request option is deprecated; guzzlehttp/guzzle 8.0 will reject values that are not true, false, null, or an integer IDNA_* bitmask.',
                \get_debug_type($value)
            );

            return (int) $value;
        }

        throw new InvalidArgumentException('idn_conversion must be true, false, null, or an integer IDNA_* bitmask');
    }

    /**
     * @throws InvalidArgumentException
     *
     * @internal
     */
    public static function idnUriConvert(UriInterface $uri, int $options = 0): UriInterface
    {
        if ($uri->getHost()) {
            $asciiHost = self::idnToAsci($uri->getHost(), $options, $info);
            if ($asciiHost === false) {
                $errorBitSet = $info['errors'] ?? 0;

                $errorConstants = array_filter(array_keys(get_defined_constants()), static function (string $name): bool {
                    return substr($name, 0, 11) === 'IDNA_ERROR_';
                });

                $errors = [];
                foreach ($errorConstants as $errorConstant) {
                    if ($errorBitSet & constant($errorConstant)) {
                        $errors[] = $errorConstant;
                    }
                }

                $errorMessage = 'IDN conversion failed';
                if ($errors) {
                    $errorMessage .= ' (errors: '.implode(', ', $errors).')';
                }

                throw new InvalidArgumentException($errorMessage);
            }
            if ($uri->getHost() !== $asciiHost) {
                // Replace URI only if the ASCII version is different
                $uri = $uri->withHost($asciiHost);
            }
        }

        return $uri;
    }

    /**
     * @internal
     */
    public static function getenv(string $name): ?string
    {
        if (isset($_SERVER[$name])) {
            return (string) $_SERVER[$name];
        }

        if (\PHP_SAPI === 'cli' && ($value = \getenv($name)) !== false && $value !== null) {
            return (string) $value;
        }

        return null;
    }

    /**
     * @return string|false
     */
    private static function idnToAsci(string $domain, int $options, ?array &$info = [])
    {
        if (\function_exists('idn_to_ascii') && \defined('INTL_IDNA_VARIANT_UTS46')) {
            return \idn_to_ascii($domain, $options, \INTL_IDNA_VARIANT_UTS46, $info);
        }

        throw new \Error('ext-idn or symfony/polyfill-intl-idn not loaded or too old');
    }
}
