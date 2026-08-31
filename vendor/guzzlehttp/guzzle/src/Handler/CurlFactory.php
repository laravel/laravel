<?php

namespace GuzzleHttp\Handler;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Multiplexing;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\TransferStats;
use GuzzleHttp\TransportSharing;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Creates curl resources from a request
 *
 * @final
 */
class CurlFactory implements CurlFactoryInterface
{
    public const CURL_VERSION_STR = 'curl_version';

    private const DELEGATED_PROXY_TUNNEL_OWNER = 'proxy-tunnel:delegated-to-libcurl';

    /**
     * String-valued proxy credential cURL options whose values feed the
     * connection-reuse section signatures. Stringable values are cast
     * exactly once, before signature computation, so the signature and
     * ext-curl observe the same string; a stateful __toString() could
     * otherwise produce one value for the signature and a different one on
     * the wire, giving two credentials the same section. Numeric options
     * (CURLOPT_PROXYTYPE, CURLOPT_PROXY_SSLVERSION) and blob options
     * (CURLOPT_PROXY_SSLCERT_BLOB) are deliberately excluded.
     */
    private const STRINGABLE_PROXY_CREDENTIAL_OPTIONS = [
        'CURLOPT_PROXYUSERPWD',
        'CURLOPT_PROXYUSERNAME',
        'CURLOPT_PROXYPASSWORD',
        'CURLOPT_PROXY_SSLCERT',
        'CURLOPT_PROXY_SSLKEY',
        'CURLOPT_PROXY_KEYPASSWD',
        'CURLOPT_PROXY_TLSAUTH_USERNAME',
        'CURLOPT_PROXY_TLSAUTH_PASSWORD',
    ];

    /**
     * @deprecated
     */
    public const LOW_CURL_VERSION_NUMBER = '7.21.2';

    /**
     * @var resource[]|\CurlHandle[]
     */
    private $handles = [];

    /**
     * @var string|null Owner signature of the proxy tunnels that pooled idle
     *                  handles may still hold
     */
    private $proxyTunnelOwner;

    /**
     * @var bool Whether an in-domain handle has been pooled since the last purge
     */
    private $poolMayHoldTunnels = false;

    /**
     * @var int Total number of idle handles to keep in cache
     */
    private $maxHandles;

    /**
     * @var resource|\CurlShareHandle|null
     */
    private $shareHandle;

    /**
     * @var string
     */
    private $shareMode;

    /**
     * @var bool Whether the configured share handle may own a connection
     *           cache populated outside this factory
     */
    private $opaqueShareConnectionCache = false;

    /**
     * @param int                                                 $maxHandles  Maximum number of idle handles.
     * @param resource|\CurlShareHandle|CurlShareHandleState|null $shareHandle
     */
    public function __construct(int $maxHandles, string $shareMode = TransportSharing::NONE, $shareHandle = null)
    {
        $this->maxHandles = $maxHandles;
        $this->shareMode = CurlShareHandleState::normalizeMode($shareMode, 'transport_sharing');

        if ($shareHandle instanceof CurlShareHandleState) {
            if ($shareHandle->mode !== $this->shareMode) {
                throw new \InvalidArgumentException('The cURL share handle state mode does not match the configured transport sharing mode.');
            }

            // A Guzzle-created handler-lifetime state locks only DNS and TLS
            // session data, so its handle can never own a connection cache.
            $shareHandle = $shareHandle->handle;
        } elseif ($shareHandle !== null) {
            // An externally supplied handle's lock set and cached contents
            // cannot be inspected from PHP, so it may own a connection cache
            // populated outside this factory.
            $this->opaqueShareConnectionCache = true;
        }

        if ($this->shareMode === TransportSharing::NONE && $shareHandle !== null) {
            throw new \InvalidArgumentException('A cURL share handle cannot be provided when transport sharing is disabled.');
        }

        if ($this->shareMode !== TransportSharing::NONE && $shareHandle === null) {
            throw new \InvalidArgumentException('A cURL share handle is required when transport sharing is enabled.');
        }

        if ($shareHandle !== null && !self::isCurlShareHandle($shareHandle)) {
            throw new \InvalidArgumentException('A cURL share handle must be an instance of CurlShareHandle or a curl_share resource.');
        }

        $this->shareHandle = $shareHandle;
    }

    /**
     * @param mixed $value
     */
    private static function isCurlShareHandle($value): bool
    {
        if (\PHP_VERSION_ID < 80000) {
            return \is_resource($value) && \get_resource_type($value) === 'curl_share';
        }

        return $value instanceof \CurlShareHandle;
    }

    public function create(RequestInterface $request, array $options): EasyHandle
    {
        self::validateRequestUriScheme($request);

        if (isset($options['on_trailers']) && !\is_callable($options['on_trailers'])) {
            throw new \InvalidArgumentException('on_trailers must be callable');
        }

        $protocolVersion = $request->getProtocolVersion();

        if ('' === $protocolVersion) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.11', 'Sending a request with an empty protocol version is deprecated; guzzlehttp/guzzle 8.0 will reject empty protocol versions.');

            $protocolVersion = '1.1';
            $request = Psr7\Utils::modifyRequest($request, ['version' => $protocolVersion]);
        }

        $multiplex = self::normalizeMultiplex($options);
        $requiredMultiplex = \in_array($multiplex, [Multiplexing::REQUIRE_EAGER, Multiplexing::REQUIRE_WAIT], true);

        if ($requiredMultiplex && isset($options['curl']) && \is_array($options['curl'])) {
            $requiredModeConflicts = [
                \CURLOPT_HTTP_VERSION => ['CURLOPT_HTTP_VERSION', 'the request protocol version'],
                \CURLOPT_URL => ['CURLOPT_URL', 'the request URI'],
                \CURLOPT_FOLLOWLOCATION => ['CURLOPT_FOLLOWLOCATION', 'the "allow_redirects" request option'],
            ];

            foreach ($requiredModeConflicts as $option => [$name, $replacement]) {
                if (\array_key_exists($option, $options['curl'])) {
                    // Key presence alone conflicts: whatever the raw value,
                    // it is a second authority over the protocol or route,
                    // applied after the required mode's decisions.
                    throw new \InvalidArgumentException(\sprintf('The "multiplex" request option cannot be required when the raw %s cURL option is set; remove the raw option and use %s instead.', $name, $replacement));
                }
            }
        }

        if ('2' === $protocolVersion || '2.0' === $protocolVersion) {
            if (!CurlVersion::supportsHttp2()) {
                if ($requiredMultiplex) {
                    throw new ConnectException('Required multiplexing needs libcurl 8.14.0 or newer built with HTTP/2 support.', $request);
                }

                throw new ConnectException('HTTP/2 is supported by the cURL handler, however libcurl is built without HTTP/2 support.', $request);
            }
        } elseif ('1.0' !== $protocolVersion && '1.1' !== $protocolVersion) {
            throw new ConnectException(sprintf('HTTP/%s is not supported by the cURL handler.', $protocolVersion), $request);
        }

        if (isset($options['curl']['body_as_string'])) {
            $options['_body_as_string'] = $options['curl']['body_as_string'];
            unset($options['curl']['body_as_string']);
        }

        self::triggerUnsupportedRequestOptionDeprecations($options);
        self::triggerUnsupportedCurlOptionDeprecations($options);
        self::triggerConflictingCurlOptionDeprecations($options);

        // Capture the managed Proxy-Authorization values before header
        // serialization so they never enter the origin header list, and
        // record whether a deprecated raw CURLOPT_HTTPHEADER value replaces
        // every generated header, the managed values included. Key presence
        // alone replaces: an empty or null raw value still suppresses the
        // generated list.
        $managedProxyAuthorization = self::managedProxyAuthorizationHeaderLines($request);
        $rawHttpHeadersReplaceManaged = isset($options['curl'])
            && \is_array($options['curl'])
            && \array_key_exists(\CURLOPT_HTTPHEADER, $options['curl']);

        $easy = new EasyHandle();
        $easy->request = $request;
        $easy->options = $options;
        $conf = $this->getDefaultConf($easy);
        $this->applyMethod($easy, $conf);
        $this->applyHandlerOptions($easy, $conf);
        $this->applyHeaders($easy, $conf);
        unset($conf['_headers']);

        // Add handler options from the request configuration options
        if (isset($options['curl'])) {
            $conf = \array_replace($conf, $options['curl']);
        }

        self::assertFinalProxyOptionTypes($conf, $requiredMultiplex && 'https' !== $request->getUri()->getScheme());
        self::isolatePreProxyOnAffectedCurl($conf);
        self::normalizeStringableProxyCredentialOptions($conf);

        if ($requiredMultiplex) {
            self::assertRequiredMultiplexRouteDirect($easy, $conf);
            self::assertRequiredMultiplexAuthSupported($conf);
        }

        self::normalizeCurlHeaderOptions($conf);
        self::applyProxyAuthorizationHeaderHandling($request, $conf);
        self::applyManagedProxyAuthorization($request, $conf, $managedProxyAuthorization, $rawHttpHeadersReplaceManaged);
        // Validate the appended managed lines too: a custom RequestInterface
        // can bypass a normal PSR-7 implementation's header validation.
        self::normalizeCurlHeaderOptions($conf);
        $this->rejectRequestLevelShareConflict($options);
        self::rejectRequestLevelShareWithProxyAuth($request, $options, $conf);

        if ($this->shareHandle !== null) {
            // Conservative blanket mode: a configured share handle hides the
            // pooled connections' provenance, so sectioned reuse cannot reason
            // about them.
            self::forceFreshConnectionForAuthenticatedProxy($request, $conf);
            $this->isolateOpaqueShareAnonymousProxyTunnel($request, $conf);
        } else {
            $signature = self::proxyTunnelSignature($request, $conf);
            $easy->proxyTunnelSignature = $signature;
            if ($signature !== null && $signature !== $this->proxyTunnelOwner) {
                if ($this->poolMayHoldTunnels) {
                    // Pooled idle handles may hold a different owner's tunnel.
                    $this->discardIdleHandles();
                    $this->poolMayHoldTunnels = false;
                }
                // The first in-domain owner latches without purging: the pool
                // provably holds no in-domain tunnel yet.
                $this->proxyTunnelOwner = $signature;
            }
        }

        $easy->effectiveProxy = self::getEffectiveProxy($conf);

        $conf[\CURLOPT_HEADERFUNCTION] = $this->createHeaderFn($easy);
        if ($this->shareHandle !== null) {
            if (!\defined('CURLOPT_SHARE')) {
                throw new \InvalidArgumentException('The configured cURL share handle requires CURLOPT_SHARE, but it is not available in the installed PHP cURL extension.');
            }

            $conf[(int) \constant('CURLOPT_SHARE')] = $this->shareHandle;
        }

        if (\defined('CURLOPT_PIPEWAIT')) {
            $easy->usesPipewait = !empty($conf[(int) \constant('CURLOPT_PIPEWAIT')]);
        }

        $handle = $this->handles ? \array_pop($this->handles) : \curl_init();
        if (false === $handle) {
            throw new \RuntimeException('Can not initialize cURL handle.');
        }
        $easy->handle = $handle;

        try {
            $this->applyCurlOptions($handle, $conf);
        } catch (\Throwable $e) {
            if (PHP_VERSION_ID < 80000 && \is_resource($handle)) {
                \curl_close($handle);
            }
            unset($easy->handle);

            throw $e;
        }

        return $easy;
    }

    /**
     * @param resource|\CurlHandle     $handle
     * @param array<int|string, mixed> $conf
     */
    private function applyCurlOptions($handle, array $conf): void
    {
        foreach ($conf as $option => $value) {
            if (!\is_int($option)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Invalid cURL option %s.',
                    self::formatCurlOption($option)
                ));
            }

            try {
                $success = curl_setopt($handle, $option, $value);
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Unable to set cURL option %s: %s',
                        self::formatCurlOption($option),
                        $e->getMessage()
                    ),
                    0,
                    $e
                );
            }

            if (!$success) {
                throw new \InvalidArgumentException(\sprintf(
                    'Unable to set cURL option %s.',
                    self::formatCurlOption($option)
                ));
            }
        }
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function normalizeStringableProxyCredentialOptions(array &$conf): void
    {
        foreach (self::STRINGABLE_PROXY_CREDENTIAL_OPTIONS as $name) {
            if (!\defined($name)) {
                continue;
            }

            $option = (int) \constant($name);
            if (!isset($conf[$option]) || !\is_object($conf[$option]) || !\method_exists($conf[$option], '__toString')) {
                continue;
            }

            try {
                $conf[$option] = (string) $conf[$option];
            } catch (\Throwable $e) {
                // Wrap the failure exactly as applyCurlOptions() does for a
                // value that cannot be applied.
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Unable to set cURL option %s: %s',
                        self::formatCurlOption($option),
                        $e->getMessage()
                    ),
                    0,
                    $e
                );
            }
        }
    }

    private function rejectRequestLevelShareConflict(array $options): void
    {
        if ($this->shareHandle === null) {
            return;
        }

        if (
            !\defined('CURLOPT_SHARE')
            || !isset($options['curl'])
            || !\is_array($options['curl'])
            || !\array_key_exists((int) \constant('CURLOPT_SHARE'), $options['curl'])
        ) {
            return;
        }

        throw new \InvalidArgumentException('The request-level CURLOPT_SHARE cURL option cannot be combined with configured transport sharing.');
    }

    private static function normalizeMultiplex(array $options): ?string
    {
        $multiplex = $options['multiplex'] ?? null;

        if ($multiplex === null) {
            // Absent/null leaves multiplexing to libcurl: no CURLOPT_PIPEWAIT
            // is written and no guarantees apply.
            return null;
        }

        if (!\in_array($multiplex, [Multiplexing::NONE, Multiplexing::EAGER, Multiplexing::WAIT, Multiplexing::REQUIRE_EAGER, Multiplexing::REQUIRE_WAIT], true)) {
            throw new \InvalidArgumentException(\sprintf(
                'The "multiplex" option must be null or a GuzzleHttp\\Multiplexing::* constant; received %s.',
                \get_debug_type($multiplex)
            ));
        }

        return $multiplex;
    }

    private static function assertRequiredMultiplexSupported(EasyHandle $easy): void
    {
        if (!CurlVersion::supportsRequiredMultiplex()) {
            throw new ConnectException('Required multiplexing needs libcurl 8.14.0 or newer built with HTTP/2 support.', $easy->request);
        }
    }

    /**
     * Required multiplexing sends cleartext requests with HTTP/2 prior
     * knowledge, which an HTTP proxy hop silently downgrades, so the request
     * must reach the origin directly. The check runs against the final
     * merged cURL configuration because deprecated raw proxy options are
     * applied after Guzzle's own decisions and may add, replace, or disable
     * the selected proxy. Value types that ext-curl would coerce are
     * rejected as ambiguous, and only the exact CURLOPT_NOPROXY wildcard '*'
     * counts as disabling the primary proxy and pre-proxy: host-specific
     * patterns are conservatively treated as leaving them active.
     *
     * @param array<int|string, mixed> $conf
     */
    private static function assertRequiredMultiplexRouteDirect(EasyHandle $easy, array $conf): void
    {
        if ('https' === $easy->request->getUri()->getScheme()) {
            return;
        }

        $proxyOptions = [\CURLOPT_PROXY => 'CURLOPT_PROXY'];
        if (\defined('CURLOPT_NOPROXY')) {
            $proxyOptions[(int) \constant('CURLOPT_NOPROXY')] = 'CURLOPT_NOPROXY';
        }
        if (\defined('CURLOPT_PRE_PROXY')) {
            $proxyOptions[(int) \constant('CURLOPT_PRE_PROXY')] = 'CURLOPT_PRE_PROXY';
        }

        foreach ($proxyOptions as $option => $name) {
            if (\array_key_exists($option, $conf) && !\is_string($conf[$option])) {
                throw new \InvalidArgumentException(\sprintf('The "multiplex" request option cannot be required when the final %s cURL option value is not a string.', $name));
            }
        }

        if (\defined('CURLOPT_NOPROXY') && ($conf[(int) \constant('CURLOPT_NOPROXY')] ?? null) === '*') {
            // libcurl's exact wildcard disables the primary proxy and the
            // pre-proxy together, leaving a direct route.
            return;
        }

        if (self::getEffectiveProxy($conf) !== null
            || (\defined('CURLOPT_PRE_PROXY') && ($conf[(int) \constant('CURLOPT_PRE_PROXY')] ?? '') !== '')
        ) {
            throw new ConnectException('Required multiplexing cannot be guaranteed for cleartext requests sent through a proxy.', $easy->request);
        }
    }

    /**
     * libcurl forces NTLM-authenticated transfers onto HTTP/1.1: when the
     * server picks NTLM from the offered mask, the connection is closed and
     * the request is retried over HTTP/1.1 whatever HTTP version was asked
     * for, silently defeating the required protocol guarantee on both
     * cleartext and TLS routes. The final merged mask is checked so the
     * deprecated "auth" request option and the raw CURLOPT_HTTPAUTH cURL
     * option are both covered, and any mask permitting NTLM, such as
     * CURLAUTH_ANY, is rejected because the selection is server-controlled.
     *
     * @param array<int|string, mixed> $conf
     */
    private static function assertRequiredMultiplexAuthSupported(array $conf): void
    {
        if (!\array_key_exists(\CURLOPT_HTTPAUTH, $conf)) {
            return;
        }

        $auth = $conf[\CURLOPT_HTTPAUTH];
        if (!\is_scalar($auth)) {
            throw new \InvalidArgumentException('The "multiplex" request option cannot be required when the final CURLOPT_HTTPAUTH cURL option value is not an integer.');
        }

        $ntlmBits = \CURLAUTH_NTLM;
        if (\defined('CURLAUTH_NTLM_WB')) {
            $ntlmBits |= (int) \constant('CURLAUTH_NTLM_WB');
        }

        if (((int) $auth & $ntlmBits) !== 0) {
            throw new \InvalidArgumentException('The "multiplex" request option cannot be required when the final CURLOPT_HTTPAUTH cURL option value permits NTLM; libcurl retries NTLM authentication over HTTP/1.1.');
        }
    }

    /**
     * @param mixed $proxyConf
     */
    private static function assertResolvedProxySupported(RequestInterface $request, $proxyConf): void
    {
        if (!\is_string($proxyConf) || $proxyConf === '') {
            return;
        }

        $scheme = self::proxyScheme($proxyConf);
        if ($scheme !== null && \preg_match('/^[a-z][a-z0-9.+-]*$/D', $scheme) !== 1) {
            throw new RequestException('The proxy URL is malformed.', $request);
        }

        if ($scheme === 'https' && !CurlVersion::supportsHttpsProxy()) {
            throw new RequestException('HTTPS proxies are not supported by the installed libcurl; libcurl 7.52.0 or newer built with HTTPS-proxy support is required.', $request);
        }
    }

    /**
     * @return array{0: mixed, 1: string}
     */
    private static function resolveProxy(RequestInterface $request, array $options): array
    {
        $proxyConf = null;
        $noProxyConf = '';

        if (isset($options['proxy'])) {
            if (!\is_array($options['proxy'])) {
                $proxyConf = $options['proxy'];
            } else {
                $scheme = $request->getUri()->getScheme();
                if (isset($options['proxy'][$scheme])) {
                    if (
                        isset($options['proxy']['no'])
                        && Utils::isUriInNoProxy($request->getUri(), $options['proxy']['no'])
                    ) {
                        $proxyConf = '';
                        $noProxyConf = '*';
                    } else {
                        $proxyConf = $options['proxy'][$scheme];
                    }
                }
            }
        }

        if ($proxyConf === null) {
            $proxyConf = ProxyEnvironment::getProxyForScheme($request->getUri()->getScheme());
            if ($proxyConf === null) {
                $proxyConf = '';
            } elseif (
                ($noProxy = ProxyEnvironment::getNoProxy()) !== null
                && Utils::isUriInNoProxy($request->getUri(), ProxyEnvironment::splitNoProxy($noProxy))
            ) {
                $proxyConf = '';
                $noProxyConf = '*';
            }
        }

        return [$proxyConf, $noProxyConf];
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function rejectRequestLevelShareWithProxyAuth(RequestInterface $request, array $options, array $conf): void
    {
        if (!self::hasRequestLevelCurlShare($options)) {
            return;
        }

        $proxy = self::getEffectiveProxy($conf);
        if ($proxy === null) {
            return;
        }

        // An external share handle may pool SOCKS connections where no section
        // signature can reach them. On affected libcurl, even an anonymous
        // request could inherit authenticated state already in that pool.
        if (self::isSocksProxy($proxy, $conf)) {
            if (!CurlVersion::supportsSocksProxyCredentialAwareConnectionReuse()) {
                throw new \InvalidArgumentException('The request-level CURLOPT_SHARE cURL option cannot be combined with SOCKS proxy configuration on libcurl before 7.69.0; use Guzzle-managed "transport_sharing" or a custom handler/factory instead.');
            }

            if (self::hasAuthenticatedSocksProxyState($proxy, $conf)) {
                throw new \InvalidArgumentException('The request-level CURLOPT_SHARE cURL option cannot be combined with authenticated SOCKS proxy configuration; use Guzzle-managed "transport_sharing" or a custom handler/factory instead.');
            }
        }

        if (
            !self::usesProxyTunnel($request, $conf)
            || !self::isHttpProxyForConnectionReuse($proxy, $conf)
        ) {
            return;
        }

        if (self::hasAuthenticatedHttpProxyState($proxy, $conf)) {
            throw new \InvalidArgumentException('The request-level CURLOPT_SHARE cURL option cannot be combined with authenticated HTTP/HTTPS proxy tunnel configuration; use Guzzle-managed "transport_sharing" or a custom handler/factory instead.');
        }

        // From libcurl 7.57.0 the external share can also own a connection
        // cache seeded outside Guzzle with tunnel identity libcurl cannot
        // key, so anonymous tunnels are rejected there too.
        if (CurlVersion::supportsShareConnectionCaches()) {
            throw new \InvalidArgumentException('The request-level CURLOPT_SHARE cURL option cannot be combined with HTTP/HTTPS proxy tunnel configuration on libcurl 7.57.0 or newer; use Guzzle-managed "transport_sharing" or a custom handler/factory instead.');
        }
    }

    private static function hasRequestLevelCurlShare(array $options): bool
    {
        return \defined('CURLOPT_SHARE')
            && isset($options['curl'])
            && \is_array($options['curl'])
            && \array_key_exists((int) \constant('CURLOPT_SHARE'), $options['curl']);
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasAuthenticatedHttpProxyState(string $proxy, array $conf): bool
    {
        $proxyForParsing = \strpos($proxy, '://') === false ? 'http://'.$proxy : $proxy;
        $proxyParts = \parse_url($proxyForParsing);

        if (
            \is_array($proxyParts)
            && (\array_key_exists('user', $proxyParts) || \array_key_exists('pass', $proxyParts))
        ) {
            return true;
        }

        if (self::hasCurlProxyCredentials($conf)) {
            return true;
        }

        if (self::hasCurlProxyAuthorizationHeader($conf)) {
            return true;
        }

        $httpHeaders = $conf[\CURLOPT_HTTPHEADER] ?? [];
        if (\is_array($httpHeaders) && self::proxyAuthorizationHeaderValuesFromList($httpHeaders) !== []) {
            return true;
        }

        return self::hasCurlProxyTlsCredentials($conf);
    }

    /**
     * @param int|string $option
     */
    private static function formatCurlOption($option): string
    {
        if (!\is_int($option)) {
            return \sprintf('"%s"', $option);
        }

        static $names = null;

        if (null === $names) {
            $names = [];
            foreach (\get_defined_constants(true)['curl'] ?? [] as $name => $value) {
                if (\is_int($value) && \strpos($name, 'CURLOPT_') === 0 && !isset($names[$value])) {
                    $names[$value] = $name;
                }
            }
        }

        if (isset($names[$option])) {
            return \sprintf('%s (%d)', $names[$option], $option);
        }

        return (string) $option;
    }

    private static function triggerConflictingCurlOptionDeprecations(array $options): void
    {
        if (!isset($options['curl']) || !\is_array($options['curl']) || $options['curl'] === []) {
            return;
        }

        $conflictingOptions = self::conflictingCurlOptions();
        $sinceOverrides = self::conflictingCurlOptionSinceOverrides();

        foreach ($options['curl'] as $option => $_) {
            if (!\array_key_exists($option, $conflictingOptions)) {
                continue;
            }

            $name = self::formatCurlOption($option);
            $replacement = $conflictingOptions[$option];
            $since = $sinceOverrides[$option] ?? '7.11';
            if ($replacement !== null) {
                \trigger_deprecation(
                    'guzzlehttp/guzzle',
                    $since,
                    \sprintf(
                        'Passing %s in the "curl" request option is deprecated; guzzlehttp/guzzle 8.0 will reject this option because it conflicts with Guzzle-managed request handling. Use %s instead.',
                        $name,
                        $replacement
                    )
                );

                continue;
            }

            \trigger_deprecation(
                'guzzlehttp/guzzle',
                $since,
                \sprintf(
                    'Passing %s in the "curl" request option is deprecated; guzzlehttp/guzzle 8.0 will reject this option because it conflicts with Guzzle-managed cURL internals.',
                    $name
                )
            );
        }
    }

    private static function triggerUnsupportedCurlOptionDeprecations(array $options): void
    {
        if (!isset($options['curl']) || !\is_array($options['curl']) || $options['curl'] === []) {
            return;
        }

        if (
            \defined('CURLOPT_PROXYHEADER')
            && \array_key_exists((int) \constant('CURLOPT_PROXYHEADER'), $options['curl'])
            && !CurlVersion::supportsProxyHeaderSeparation()
        ) {
            \trigger_deprecation(
                'guzzlehttp/guzzle',
                '7.15',
                \sprintf(
                    'Passing %s in the "curl" request option on a build without proxy header separation support is deprecated; guzzlehttp/guzzle 8.0 will reject this configuration because proxy headers require libcurl 7.37.0 or newer built with proxy header separation support.',
                    self::formatCurlOption((int) \constant('CURLOPT_PROXYHEADER'))
                )
            );
        }

        $supportedOptions = self::supportedCurlOptions();
        $conflictingOptions = self::conflictingCurlOptions();

        foreach ($options['curl'] as $option => $_) {
            if (
                !\is_int($option)
                || \array_key_exists($option, $supportedOptions)
                || \array_key_exists($option, $conflictingOptions)
            ) {
                continue;
            }

            \trigger_deprecation(
                'guzzlehttp/guzzle',
                '7.12',
                \sprintf(
                    'Passing %s in the "curl" request option is deprecated; guzzlehttp/guzzle 8.0 will reject raw cURL options outside the built-in cURL handlers\' allow-list.',
                    self::formatCurlOption($option)
                )
            );
        }
    }

    private static function triggerUnsupportedRequestOptionDeprecations(array $options): void
    {
        if (\array_key_exists('stream_context', $options)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.11', 'Passing the "stream_context" request option to a cURL handler is deprecated; guzzlehttp/guzzle 8.0 will reject this option because cURL handlers ignore PHP stream context options.');
        }
    }

    /**
     * @return array<int, string|null>
     */
    private static function conflictingCurlOptions(): array
    {
        static $options = null;

        if ($options !== null) {
            return $options;
        }

        $options = [];

        self::addConflictingCurlOption($options, 'CURLOPT_SHARE', 'the "transport_sharing" client option or cURL handler option');
        self::addConflictingCurlOption($options, 'CURLOPT_URL', 'the request URI');
        self::addConflictingCurlOption($options, 'CURLOPT_PORT', 'the request URI');
        self::addConflictingCurlOption($options, 'CURLOPT_CUSTOMREQUEST', 'the request method');
        self::addConflictingCurlOption($options, 'CURLOPT_HTTPGET', 'the request method');
        self::addConflictingCurlOption($options, 'CURLOPT_POST', 'the request method and body');
        self::addConflictingCurlOption($options, 'CURLOPT_PUT', 'the request method and body');
        self::addConflictingCurlOption($options, 'CURLOPT_NOBODY', 'the request method');
        self::addConflictingCurlOption($options, 'CURLOPT_UPLOAD', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_POSTFIELDS', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_READFUNCTION', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_READDATA', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_INFILE', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_INFILESIZE', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_INFILESIZE_LARGE', 'the request body');
        self::addConflictingCurlOption($options, 'CURLOPT_HTTPHEADER', 'the request headers');
        self::addConflictingCurlOption($options, 'CURLOPT_USERAGENT', 'the request headers');
        self::addConflictingCurlOption($options, 'CURLOPT_REFERER', 'the request headers');
        self::addConflictingCurlOption($options, 'CURLOPT_HEADERFUNCTION', 'the "on_headers" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_WRITEFUNCTION', 'the "sink" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_FILE', 'the "sink" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_TIMEOUT', 'the "timeout" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_TIMEOUT_MS', 'the "timeout" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_CONNECTTIMEOUT', 'the "connect_timeout" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_CONNECTTIMEOUT_MS', 'the "connect_timeout" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_NOSIGNAL', 'the "timeout" or "connect_timeout" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_NOPROGRESS', 'the "progress" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_PROGRESSFUNCTION', 'the "progress" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_XFERINFOFUNCTION', 'the "progress" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_VERBOSE', 'the "debug" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_STDERR', 'the "debug" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_PROXY', 'the "proxy" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_NOPROXY', 'the "proxy" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_PROXYTYPE', 'the "proxy" request option with a scheme-prefixed URL');
        self::addConflictingCurlOption($options, 'CURLOPT_FOLLOWLOCATION', 'the "allow_redirects" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_MAXREDIRS', 'the "allow_redirects" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_POSTREDIR', 'the "allow_redirects" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_REDIR_PROTOCOLS', 'the "allow_redirects" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_REDIR_PROTOCOLS_STR', 'the "allow_redirects" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_PROTOCOLS', 'the "protocols" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_PROTOCOLS_STR', 'the "protocols" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_HTTP_VERSION', 'the request protocol version');
        self::addConflictingCurlOption($options, 'CURLOPT_PIPEWAIT', 'the "multiplex" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_IPRESOLVE', 'the "force_ip_resolve" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSL_VERIFYPEER', 'the "verify" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSL_VERIFYHOST', 'the "verify" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_CAINFO', 'the "verify" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_CAPATH', 'the "verify" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLVERSION', 'the "crypto_method" or "crypto_method_max" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLCERT', 'the "cert" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLCERTPASSWD', 'the "cert" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLCERTTYPE', 'the "cert_type" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLKEY', 'the "ssl_key" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLKEYPASSWD', 'the "ssl_key" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_KEYPASSWD', 'the "ssl_key" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_SSLKEYTYPE', 'the "ssl_key_type" request option');
        self::addConflictingCurlOption($options, 'CURLOPT_COOKIE', 'the "Cookie" request header or Guzzle cookie middleware');
        self::addConflictingCurlOption($options, 'CURLOPT_COOKIEFILE', 'Guzzle cookie middleware');
        self::addConflictingCurlOption($options, 'CURLOPT_COOKIEJAR', 'Guzzle cookie middleware');
        self::addConflictingCurlOption($options, 'CURLOPT_COOKIELIST', 'Guzzle cookie middleware');
        self::addConflictingCurlOption($options, 'CURLOPT_COOKIESESSION', 'Guzzle cookie middleware');

        return $options;
    }

    /**
     * @return array<int, string>
     */
    private static function conflictingCurlOptionSinceOverrides(): array
    {
        static $options = null;

        if ($options !== null) {
            return $options;
        }

        $options = [];

        if (\defined('CURLOPT_PROXYTYPE')) {
            $options[\CURLOPT_PROXYTYPE] = '7.12';
        }

        if (\defined('CURLOPT_PIPEWAIT')) {
            $options[\CURLOPT_PIPEWAIT] = '7.14';
        }

        return $options;
    }

    /**
     * @return array<int, true>
     */
    private static function supportedCurlOptions(): array
    {
        static $options = null;

        if ($options !== null) {
            return $options;
        }

        $options = [];

        self::addSupportedCurlOption($options, 'CURLOPT_ADDRESS_SCOPE');
        self::addSupportedCurlOption($options, 'CURLOPT_CERTINFO');
        self::addSupportedCurlOption($options, 'CURLOPT_CONNECT_TO');
        self::addSupportedCurlOption($options, 'CURLOPT_DNS_CACHE_TIMEOUT');
        self::addSupportedCurlOption($options, 'CURLOPT_DNS_INTERFACE');
        self::addSupportedCurlOption($options, 'CURLOPT_DNS_LOCAL_IP4');
        self::addSupportedCurlOption($options, 'CURLOPT_DNS_LOCAL_IP6');
        self::addSupportedCurlOption($options, 'CURLOPT_DNS_SERVERS');
        self::addSupportedCurlOption($options, 'CURLOPT_DNS_SHUFFLE_ADDRESSES');
        self::addSupportedCurlOption($options, 'CURLOPT_ENCODING');
        self::addSupportedCurlOption($options, 'CURLOPT_FORBID_REUSE');
        self::addSupportedCurlOption($options, 'CURLOPT_FRESH_CONNECT');
        self::addSupportedCurlOption($options, 'CURLOPT_HAPPY_EYEBALLS_TIMEOUT_MS');
        self::addSupportedCurlOption($options, 'CURLOPT_HTTPAUTH');
        self::addSupportedCurlOption($options, 'CURLOPT_INTERFACE');
        self::addSupportedCurlOption($options, 'CURLOPT_LOCALPORT');
        self::addSupportedCurlOption($options, 'CURLOPT_LOCALPORTRANGE');
        self::addSupportedCurlOption($options, 'CURLOPT_LOW_SPEED_LIMIT');
        self::addSupportedCurlOption($options, 'CURLOPT_LOW_SPEED_TIME');
        self::addSupportedCurlOption($options, 'CURLOPT_MAXAGE_CONN');
        self::addSupportedCurlOption($options, 'CURLOPT_MAXCONNECTS');
        self::addSupportedCurlOption($options, 'CURLOPT_MAXLIFETIME_CONN');
        self::addSupportedCurlOption($options, 'CURLOPT_HTTPPROXYTUNNEL');
        self::addSupportedCurlOption($options, 'CURLOPT_PREREQFUNCTION');
        self::addSupportedCurlOption($options, 'CURLOPT_PROXYHEADER');
        self::addSupportedCurlOption($options, 'CURLOPT_PROXYUSERPWD');
        self::addSupportedCurlOption($options, 'CURLOPT_RESOLVE');
        self::addSupportedCurlOption($options, 'CURLOPT_SSL_CIPHER_LIST');
        self::addSupportedCurlOption($options, 'CURLOPT_SSL_EC_CURVES');
        self::addSupportedCurlOption($options, 'CURLOPT_TCP_FASTOPEN');
        self::addSupportedCurlOption($options, 'CURLOPT_TCP_KEEPALIVE');
        self::addSupportedCurlOption($options, 'CURLOPT_TCP_KEEPIDLE');
        self::addSupportedCurlOption($options, 'CURLOPT_TCP_KEEPINTVL');
        self::addSupportedCurlOption($options, 'CURLOPT_TCP_KEEPCNT');
        self::addSupportedCurlOption($options, 'CURLOPT_TCP_NODELAY');
        self::addSupportedCurlOption($options, 'CURLOPT_TLS13_CIPHERS');
        self::addSupportedCurlOption($options, 'CURLOPT_UNIX_SOCKET_PATH');
        self::addSupportedCurlOption($options, 'CURLOPT_USERPWD');

        return $options;
    }

    /**
     * @param array<int, true> $options
     */
    private static function addSupportedCurlOption(array &$options, string $constant): void
    {
        if (!\defined($constant)) {
            return;
        }

        $value = \constant($constant);
        if (\is_int($value)) {
            $options[$value] = true;
        }
    }

    /**
     * @param array<int, string|null> $options
     */
    private static function addConflictingCurlOption(array &$options, string $constant, ?string $replacement): void
    {
        if (!\defined($constant)) {
            return;
        }

        $value = \constant($constant);
        if (\is_int($value)) {
            $options[$value] = $replacement;
        }
    }

    public function release(EasyHandle $easy): void
    {
        $resource = $easy->handle;
        unset($easy->handle);

        if (
            \count($this->handles) >= $this->maxHandles
            || ($easy->proxyTunnelSignature !== null && $easy->proxyTunnelSignature !== $this->proxyTunnelOwner)
        ) {
            // Pool is full, or this handle belongs to a superseded tunnel
            // owner (an async create/release overlap can hand a stale-owner
            // handle back after a purge) - drop it instead of pooling it.
            if (PHP_VERSION_ID < 80000) {
                \curl_close($resource);
            }

            return;
        }

        if ($easy->proxyTunnelSignature !== null) {
            // A pooled handle now carries the current owner's tunnel.
            $this->poolMayHoldTunnels = true;
        }

        // Remove all callback functions as they can hold onto references and
        // are not cleaned up by curl_reset. Using curl_setopt_array does not
        // work for some reason, so removing each one individually.
        \curl_setopt($resource, \CURLOPT_HEADERFUNCTION, null);
        \curl_setopt($resource, \CURLOPT_READFUNCTION, null);
        \curl_setopt($resource, \CURLOPT_WRITEFUNCTION, null);
        \curl_setopt($resource, \CURLOPT_PROGRESSFUNCTION, null);

        if (\defined('CURLOPT_PREREQFUNCTION')) {
            \curl_setopt($resource, (int) \constant('CURLOPT_PREREQFUNCTION'), null);
        }

        \curl_reset($resource);
        $this->handles[] = $resource;
    }

    /**
     * Completes a cURL transaction, either returning a response promise or a
     * rejected promise.
     *
     * @param callable(RequestInterface, array): PromiseInterface $handler
     * @param CurlFactoryInterface                                $factory Dictates how the handle is released
     */
    public static function finish(callable $handler, EasyHandle $easy, CurlFactoryInterface $factory): PromiseInterface
    {
        if (isset($easy->options['on_stats'])) {
            try {
                self::invokeStats($easy);
            } catch (\Throwable $e) {
                try {
                    $factory->release($easy);
                } catch (\Throwable $releaseFailure) {
                    // Keep the on_stats throwable as the visible failure.
                }

                throw $e;
            }
        }

        if (!$easy->response || $easy->errno) {
            return self::finishError($handler, $easy, $factory);
        }

        // Return the response if it is present and there is no error.
        $factory->release($easy);

        // Rewind the body of the response if possible.
        $body = $easy->response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }

        if (isset($easy->options['on_trailers'])) {
            try {
                ($easy->options['on_trailers'])(self::headersFromTrailerLines($easy->trailers), $easy->response);
            } catch (\Throwable $e) {
                return P\Create::rejectionFor(
                    new RequestException(
                        'An error was encountered during the on_trailers event',
                        $easy->request,
                        $easy->response,
                        $e
                    )
                );
            }
        }

        return new FulfilledPromise($easy->response);
    }

    private static function invokeStats(EasyHandle $easy): void
    {
        $curlStats = \curl_getinfo($easy->handle);
        $curlStats['appconnect_time'] = \curl_getinfo($easy->handle, \CURLINFO_APPCONNECT_TIME);
        $stats = new TransferStats(
            $easy->request,
            $easy->response,
            $curlStats['total_time'],
            $easy->errno,
            $curlStats
        );
        ($easy->options['on_stats'])($stats);
    }

    /**
     * @param callable(RequestInterface, array): PromiseInterface $handler
     */
    private static function finishError(callable $handler, EasyHandle $easy, CurlFactoryInterface $factory): PromiseInterface
    {
        // Get error information and release the handle to the factory.
        $ctx = [
            'errno' => $easy->errno,
            'error' => \curl_error($easy->handle),
            'appconnect_time' => \curl_getinfo($easy->handle, \CURLINFO_APPCONNECT_TIME),
        ] + \curl_getinfo($easy->handle);
        $ctx[self::CURL_VERSION_STR] = CurlVersion::getVersion() ?? '';
        $factory->release($easy);

        // Retry when nothing is present or when curl failed to rewind.
        if (empty($easy->options['_err_message']) && (!$easy->errno || $easy->errno == 65)) {
            return self::retryFailedRewind($handler, $easy, $ctx);
        }

        return self::createRejection($easy, $ctx);
    }

    private static function createRejection(EasyHandle $easy, array $ctx): PromiseInterface
    {
        static $connectionErrors = [
            \CURLE_OPERATION_TIMEOUTED => true,
            \CURLE_COULDNT_RESOLVE_HOST => true,
            \CURLE_COULDNT_CONNECT => true,
            \CURLE_SSL_CONNECT_ERROR => true,
            \CURLE_GOT_NOTHING => true,
        ];

        $uri = $easy->request->getUri();

        // Redact the native error before it reaches any exception so the
        // handler context matches the sanitized exception message.
        $ctx['error'] = self::sanitizeCurlError((string) ($ctx['error'] ?? ''), $uri, $easy->effectiveProxy);

        if ($easy->createResponseException) {
            return P\Create::rejectionFor(
                new RequestException(
                    'An error was encountered while creating the response',
                    $easy->request,
                    null,
                    $easy->createResponseException,
                    $ctx
                )
            );
        }

        // If an exception was encountered during the onHeaders event, then
        // return a rejected promise that wraps that exception.
        if ($easy->onHeadersException) {
            return P\Create::rejectionFor(
                new RequestException(
                    'An error was encountered during the on_headers event',
                    $easy->request,
                    $easy->response,
                    $easy->onHeadersException,
                    $ctx
                )
            );
        }

        $sanitizedError = $ctx['error'];

        $message = \sprintf(
            'cURL error %s: %s (%s)',
            $ctx['errno'],
            $sanitizedError,
            'see https://curl.se/libcurl/c/libcurl-errors.html'
        );

        if ('' !== $sanitizedError) {
            $redactedUriString = Psr7\Utils::redactUserInfo($uri)->__toString();
            if ($redactedUriString !== '' && false === \strpos($sanitizedError, $redactedUriString)) {
                $message .= \sprintf(' for %s', $redactedUriString);
            }
        }

        // Create a connection exception if it was a specific error code.
        $error = isset($connectionErrors[$easy->errno])
            ? new ConnectException($message, $easy->request, null, $ctx)
            : new RequestException($message, $easy->request, $easy->response, null, $ctx);

        return P\Create::rejectionFor($error);
    }

    private static function sanitizeCurlError(string $error, UriInterface $uri, ?string $proxy = null): string
    {
        if ('' === $error) {
            return $error;
        }

        $error = self::redactProxyUserInfo($error, $proxy);

        $baseUri = $uri->withQuery('')->withFragment('');
        $baseUriString = $baseUri->__toString();

        if ('' === $baseUriString) {
            return $error;
        }

        $redactedUriString = Psr7\Utils::redactUserInfo($baseUri)->__toString();

        return str_replace($baseUriString, $redactedUriString, $error);
    }

    private static function redactProxyUserInfo(string $error, ?string $proxy): string
    {
        if ($proxy === null || $proxy === '' || \strpos($proxy, '@') === false) {
            return $error;
        }

        // The error message embeds the proxy string exactly as configured, so
        // the userinfo needle is taken verbatim from the raw string:
        // parse_url() and Psr7\Uri normalize the components, e.g. by rewriting
        // raw control bytes to '_', which could make the replacement miss.
        $proxyForParsing = \strpos($proxy, '://') === false ? 'http://'.$proxy : $proxy;
        $remainder = \substr($proxyForParsing, \strpos($proxyForParsing, '://') + 3);

        if (\parse_url($proxyForParsing) === false) {
            // Raw '/', '?', or '#' separators may sit inside the credentials
            // of a proxy that defeats parse_url(), so the redaction cannot
            // stop at the apparent authority.
            $atPosition = \strrpos($remainder, '@');

            if ($atPosition === false || $atPosition === 0) {
                return $error;
            }

            return \str_replace(\substr($remainder, 0, $atPosition).'@', '***@', $error);
        }

        $authority = \substr($remainder, 0, \strcspn($remainder, '/?#'));
        $atPosition = \strrpos($authority, '@');

        if ($atPosition === false || $atPosition === 0) {
            // A parseable proxy URL with '@' only past its authority, or with
            // an empty userinfo, carries no credentials to redact.
            return $error;
        }

        $rawUserInfo = \substr($authority, 0, $atPosition);

        // Redact with the same policy Psr7\Utils::redactUserInfo() applies to
        // request URIs, so the bundled psr7 version governs the redacted form.
        $redactedUserInfo = '***';

        try {
            $proxyUri = new Uri($proxyForParsing);
            $redactedUserInfo = Psr7\Utils::redactUserInfo($proxyUri)->getUserInfo();

            if ($redactedUserInfo === $proxyUri->getUserInfo()) {
                return $error;
            }
        } catch (\InvalidArgumentException $e) {
            // Unparseable as a URI: fall back to redacting the whole userinfo.
        }

        return \str_replace($rawUserInfo.'@', $redactedUserInfo.'@', $error);
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function forceFreshConnectionForAuthenticatedProxy(RequestInterface $request, array &$conf): void
    {
        $proxy = self::getEffectiveProxy($conf);

        if ($proxy === null || !self::requiresFreshConnectionForAuthenticatedProxy($request, $proxy, $conf)) {
            return;
        }

        $conf[\CURLOPT_FRESH_CONNECT] = true;
        $conf[\CURLOPT_FORBID_REUSE] = true;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private function isolateOpaqueShareAnonymousProxyTunnel(RequestInterface $request, array &$conf): void
    {
        if (!$this->opaqueShareConnectionCache || !CurlVersion::supportsShareConnectionCaches()) {
            return;
        }

        $proxy = self::getEffectiveProxy($conf);
        if (
            $proxy === null
            || !self::usesProxyTunnel($request, $conf)
            || !self::isHttpProxyForConnectionReuse($proxy, $conf)
            || self::hasAuthenticatedHttpProxyState($proxy, $conf)
        ) {
            return;
        }

        // From libcurl 7.57.0 an opaque share handle can own a connection
        // cache, and a tunnel seeded there with a literal Proxy-Authorization
        // header is never keyed on credentials, so an anonymous request could
        // inherit it on every later libcurl version. Requests carrying
        // recognized credential state keep the version-gated channel
        // safeguards above.
        $conf[\CURLOPT_FRESH_CONNECT] = true;
        $conf[\CURLOPT_FORBID_REUSE] = true;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function assertFinalProxyOptionTypes(array $conf, bool $requiredCleartextMultiplex): void
    {
        if (\array_key_exists(\CURLOPT_PROXYTYPE, $conf) && !\is_int($conf[\CURLOPT_PROXYTYPE])) {
            throw new \InvalidArgumentException('CURLOPT_PROXYTYPE must be an integer.');
        }

        foreach (['CURLOPT_PROXY', 'CURLOPT_NOPROXY', 'CURLOPT_PRE_PROXY'] as $name) {
            if (!\defined($name)) {
                continue;
            }

            $option = (int) \constant($name);
            if (\array_key_exists($option, $conf) && !\is_string($conf[$option])) {
                if ($requiredCleartextMultiplex) {
                    throw new \InvalidArgumentException(\sprintf('The "multiplex" request option cannot be required when the final %s cURL option value is not a string.', $name));
                }

                throw new \InvalidArgumentException($name.' must be a string.');
            }
        }
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function isolatePreProxyOnAffectedCurl(array &$conf): void
    {
        if (CurlVersion::supportsSocksProxyCredentialAwareConnectionReuse() || !\defined('CURLOPT_PRE_PROXY')) {
            return;
        }

        $option = (int) \constant('CURLOPT_PRE_PROXY');
        if (!\array_key_exists($option, $conf) || $conf[$option] === '') {
            return;
        }

        $conf[\CURLOPT_FRESH_CONNECT] = true;
        $conf[\CURLOPT_FORBID_REUSE] = true;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function getEffectiveProxy(array $conf): ?string
    {
        if (!\array_key_exists(\CURLOPT_PROXY, $conf)) {
            return null;
        }

        $proxy = $conf[\CURLOPT_PROXY];
        if (!\is_string($proxy) || $proxy === '') {
            return null;
        }

        // Only the exact raw wildcard is modeled here: libcurl treats '*' as
        // bypass-all by whole-string comparison, without trimming or host matching.
        if (\defined('CURLOPT_NOPROXY')) {
            $noProxy = $conf[(int) \constant('CURLOPT_NOPROXY')] ?? null;
            if (\is_string($noProxy) && $noProxy === '*') {
                return null;
            }
        }

        return $proxy;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function normalizeCurlHeaderOptions(array &$conf): void
    {
        $options = [\CURLOPT_HTTPHEADER => 'CURLOPT_HTTPHEADER'];
        if (\defined('CURLOPT_PROXYHEADER')) {
            $options[(int) \constant('CURLOPT_PROXYHEADER')] = 'CURLOPT_PROXYHEADER';
        }

        foreach ($options as $option => $label) {
            if (!\array_key_exists($option, $conf) || !\is_array($conf[$option])) {
                continue;
            }

            $normalized = [];
            foreach ($conf[$option] as $key => $entry) {
                if (\is_object($entry) && \method_exists($entry, '__toString')) {
                    $entry = (string) $entry;
                } elseif (\is_float($entry) && !\is_finite($entry)) {
                    $entry = \is_nan($entry) ? 'NAN' : ($entry > 0 ? 'INF' : '-INF');
                } elseif (\is_scalar($entry)) {
                    $entry = (string) $entry;
                } else {
                    throw new \InvalidArgumentException(\sprintf('%s entries must be strings, stringable objects, or scalar values.', $label));
                }

                if (\strpbrk($entry, "\r\n") !== false) {
                    throw new \InvalidArgumentException(\sprintf('%s entries must not contain a carriage return or line feed.', $label));
                }

                $normalized[$key] = $entry;
            }

            $conf[$option] = $normalized;
        }
    }

    private static function proxyScheme(string $proxy): ?string
    {
        $position = \strpos($proxy, '://');

        return $position === false ? null : Psr7\Utils::asciiToLower(\substr($proxy, 0, $position));
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function requiresFreshConnectionForAuthenticatedProxy(RequestInterface $request, string $proxy, array $conf): bool
    {
        // SOCKS authentication binds an identity to the connection itself, and
        // below 7.69.0 an opaque configured share may already contain a SOCKS
        // connection whose credential state Guzzle cannot inspect. Isolate
        // authenticated and anonymous requests so neither can inherit it.
        if (self::isSocksProxy($proxy, $conf)) {
            return !CurlVersion::supportsSocksProxyCredentialAwareConnectionReuse();
        }

        if (!self::usesProxyTunnel($request, $conf) || !self::isHttpProxyForConnectionReuse($proxy, $conf)) {
            return false;
        }

        $proxyForParsing = \strpos($proxy, '://') === false ? 'http://'.$proxy : $proxy;
        $proxyParts = \parse_url($proxyForParsing);

        if (!\is_array($proxyParts)) {
            return false;
        }

        if (self::hasCurlProxyAuthorizationHeader($conf)) {
            return true;
        }

        // A proxy client certificate or TLS-SRP authenticates the client to the
        // HTTPS proxy at the TLS layer; libcurl ignored TLS-SRP before 7.83.1
        // (CVE-2022-27782), so an old build can reuse a tunnel across those
        // identities. Force a fresh one, as the non-share signature path does.
        if (
            !CurlVersion::supportsProxyTlsCredentialAwareConnectionReuse()
            && self::hasCurlProxyTlsCredentials($conf)
        ) {
            return true;
        }

        if (CurlVersion::supportsProxyCredentialAwareConnectionReuse()) {
            return false;
        }

        return \array_key_exists('user', $proxyParts)
            || \array_key_exists('pass', $proxyParts)
            || self::hasCurlProxyCredentials($conf);
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasAuthenticatedSocksProxyState(string $proxy, array $conf): bool
    {
        $proxyForParsing = \strpos($proxy, '://') === false ? 'http://'.$proxy : $proxy;
        $proxyParts = \parse_url($proxyForParsing);

        if (
            \is_array($proxyParts)
            && (\array_key_exists('user', $proxyParts) || \array_key_exists('pass', $proxyParts))
        ) {
            return true;
        }

        return self::hasCurlProxyCredentials($conf);
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function usesProxyTunnel(RequestInterface $request, array $conf): bool
    {
        $scheme = $request->getUri()->getScheme();

        if ('https' === $scheme) {
            return true;
        }

        // An HTTP proxy auto-switches to a CONNECT tunnel when CONNECT_TO
        // redirects the origin, so an http:// target with it set tunnels too.
        if ('http' === $scheme && self::hasCurlConnectTo($conf)) {
            return true;
        }

        return \defined('CURLOPT_HTTPPROXYTUNNEL')
            && \array_key_exists((int) \constant('CURLOPT_HTTPPROXYTUNNEL'), $conf)
            && (bool) $conf[(int) \constant('CURLOPT_HTTPPROXYTUNNEL')];
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasCurlConnectTo(array $conf): bool
    {
        if (!\defined('CURLOPT_CONNECT_TO')) {
            return false;
        }

        $option = (int) \constant('CURLOPT_CONNECT_TO');
        if (!\array_key_exists($option, $conf)) {
            return false;
        }

        $value = $conf[$option];

        return \is_array($value)
            ? $value !== []
            : $value !== null && $value !== false && $value !== '';
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function isHttpProxyForConnectionReuse(string $proxy, array $conf): bool
    {
        if (\strpos($proxy, '://') !== false) {
            $proxyParts = \parse_url($proxy);

            if (!\is_array($proxyParts) || !isset($proxyParts['scheme'])) {
                return false;
            }

            $proxyScheme = Psr7\Utils::asciiToLower($proxyParts['scheme']);

            return $proxyScheme === 'http' || $proxyScheme === 'https';
        }

        return !self::isSocksProxyType($conf[\CURLOPT_PROXYTYPE] ?? null);
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function isSocksProxy(string $proxy, array $conf): bool
    {
        $scheme = self::proxyScheme($proxy);
        if ($scheme !== null) {
            if (\in_array($scheme, ['socks', 'socks4', 'socks4a', 'socks5', 'socks5h'], true)) {
                return true;
            }

            // libcurl preserves a raw SOCKS CURLOPT_PROXYTYPE behind an http
            // scheme, while every other scheme overrides the proxy type.
            if ($scheme !== 'http') {
                return false;
            }
        }

        return self::isSocksProxyType($conf[\CURLOPT_PROXYTYPE] ?? null);
    }

    /**
     * Computes the connection-reuse section signature for a SOCKS proxy.
     * libcurl compares SOCKS credentials on connection reuse from 7.69.0 (curl
     * #4835), so no sectioning is needed there. Older libcurl matches a SOCKS
     * proxy by type, host, and port only, so every SOCKS request is sectioned
     * by its credential state; hashing the credential-less state too keeps an
     * unauthenticated request from inheriting an authenticated connection.
     *
     * @param array<int|string, mixed> $conf
     */
    private static function socksProxySignature(string $proxy, array $conf): ?string
    {
        if (CurlVersion::supportsSocksProxyCredentialAwareConnectionReuse()) {
            return null;
        }

        $credentialState = [];
        foreach (['CURLOPT_PROXYUSERPWD', 'CURLOPT_PROXYUSERNAME', 'CURLOPT_PROXYPASSWORD', 'CURLOPT_PROXYTYPE'] as $name) {
            $credentialState[$name] = \defined($name)
                ? ($conf[(int) \constant($name)] ?? null)
                : null;
        }

        return \hash('sha256', \serialize(['socks', $proxy, $credentialState]));
    }

    /**
     * @param mixed $proxyType
     */
    private static function isSocksProxyType($proxyType): bool
    {
        if (!\is_int($proxyType)) {
            return false;
        }

        foreach ([
            'CURLPROXY_SOCKS4' => 4,
            'CURLPROXY_SOCKS5' => 5,
            'CURLPROXY_SOCKS4A' => 6,
            'CURLPROXY_SOCKS5_HOSTNAME' => 7,
        ] as $name => $fallback) {
            $value = \defined($name) ? (int) \constant($name) : $fallback;
            if ($proxyType === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasCurlProxyCredentials(array $conf): bool
    {
        foreach (['CURLOPT_PROXYUSERPWD', 'CURLOPT_PROXYUSERNAME', 'CURLOPT_PROXYPASSWORD'] as $option) {
            if (\defined($option) && \array_key_exists((int) \constant($option), $conf)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasCurlProxyTlsCredentials(array $conf): bool
    {
        foreach ([
            'CURLOPT_PROXY_SSLCERT',
            'CURLOPT_PROXY_SSLCERT_BLOB',
            'CURLOPT_PROXY_TLSAUTH_USERNAME',
            'CURLOPT_PROXY_TLSAUTH_PASSWORD',
        ] as $option) {
            if (\defined($option) && \array_key_exists((int) \constant($option), $conf)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasCurlProxyAuthorizationHeader(array $conf): bool
    {
        return self::curlProxyAuthorizationHeaderValues($conf) !== [];
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function applyProxyAuthorizationHeaderHandling(RequestInterface $request, array &$conf): void
    {
        $proxy = self::getEffectiveProxy($conf);
        if ($proxy === null || !self::isHttpProxyForConnectionReuse($proxy, $conf)) {
            return;
        }

        $httpHeaders = $conf[\CURLOPT_HTTPHEADER] ?? null;
        $movedHeaders = [];
        $originHeaders = [];

        if (\is_array($httpHeaders)) {
            foreach ($httpHeaders as $header) {
                if (\is_string($header) && self::isProxyAuthorizationHeaderLine($header)) {
                    $movedHeaders[] = $header;

                    continue;
                }

                $originHeaders[] = $header;
            }
        }

        if (CurlVersion::supportsProxyHeaderSeparation()) {
            if ($movedHeaders !== []) {
                $conf[\CURLOPT_HTTPHEADER] = $originHeaders;
                self::appendCurlProxyHeaders($conf, $movedHeaders);
            }

            // On libcurl 7.37.0-7.42.0 the default is CURLHEADER_UNIFIED.
            if ($movedHeaders !== [] || self::hasCurlProxyHeaderOption($conf) || self::usesProxyTunnel($request, $conf)) {
                $conf[(int) \constant('CURLOPT_HEADEROPT')] = (int) \constant('CURLHEADER_SEPARATE');
            }

            return;
        }

        if (\is_array($httpHeaders) && self::proxyAuthorizationHeaderValuesFromList($httpHeaders) !== []) {
            $conf[\CURLOPT_FRESH_CONNECT] = true;
            $conf[\CURLOPT_FORBID_REUSE] = true;
        }
    }

    /**
     * Routes the managed first-class Proxy-Authorization values to libcurl's
     * proxy-only header channel, independently of Guzzle's proxy prediction:
     * libcurl alone decides whether the proxy-only list is used for the
     * actual transfer, so the credential can never reach an origin through
     * CURLOPT_HTTPHEADER. Without proxy header separation support, values are
     * safely omitted on known direct, bypassed, and SOCKS routes; a route that
     * may use an HTTP(S) proxy is rejected before cURL initialization and
     * network I/O. A deprecated raw CURLOPT_HTTPHEADER replacement suppresses
     * every generated header, the managed values included.
     *
     * @param array<int|string, mixed> $conf
     * @param list<string>             $headers
     */
    private static function applyManagedProxyAuthorization(RequestInterface $request, array &$conf, array $headers, bool $rawHttpHeadersReplaceManaged): void
    {
        if ($rawHttpHeadersReplaceManaged || $headers === []) {
            return;
        }

        if (!CurlVersion::supportsProxyHeaderSeparation()) {
            $proxy = self::getEffectiveProxy($conf);
            if ($proxy !== null && !self::isSocksProxy($proxy, $conf)) {
                throw new RequestException('Proxy-Authorization request headers through a possible HTTP or HTTPS proxy require libcurl 7.37.0 or newer built with proxy header separation support.', $request);
            }

            return;
        }

        self::appendCurlProxyHeaders($conf, $headers);
        $conf[(int) \constant('CURLOPT_HEADEROPT')] = (int) \constant('CURLHEADER_SEPARATE');
    }

    /**
     * @return list<string>
     */
    private static function managedProxyAuthorizationHeaderLines(RequestInterface $request): array
    {
        $headers = [];

        foreach ($request->getHeader('Proxy-Authorization') as $value) {
            $headers[] = $value === ''
                ? 'Proxy-Authorization;'
                : 'Proxy-Authorization: '.$value;
        }

        return $headers;
    }

    /**
     * @param array<int|string, mixed> $conf
     * @param list<string>             $headers
     */
    private static function appendCurlProxyHeaders(array &$conf, array $headers): void
    {
        $option = (int) \constant('CURLOPT_PROXYHEADER');

        if (\array_key_exists($option, $conf)) {
            if (!\is_array($conf[$option])) {
                throw new \InvalidArgumentException('CURLOPT_PROXYHEADER must be an array when a Proxy-Authorization request header is routed to the proxy header channel.');
            }

            $headers = \array_merge($conf[$option], $headers);
        }

        $conf[$option] = $headers;
    }

    /**
     * @param array<int|string, mixed> $conf
     */
    private static function hasCurlProxyHeaderOption(array $conf): bool
    {
        return \defined('CURLOPT_PROXYHEADER')
            && \array_key_exists((int) \constant('CURLOPT_PROXYHEADER'), $conf);
    }

    private static function isProxyAuthorizationHeaderLine(string $header): bool
    {
        $length = \strcspn($header, ':;');

        if ($length === \strlen($header)) {
            return false;
        }

        return Psr7\Utils::caselessEquals(\trim(\substr($header, 0, $length), " \n\r\t\0\x0B"), 'Proxy-Authorization');
    }

    private static function proxyAuthorizationHeaderValue(string $header): ?string
    {
        $position = \strpos($header, ':');
        if ($position === false) {
            return null;
        }

        if (!Psr7\Utils::caselessEquals(\trim(\substr($header, 0, $position), " \n\r\t\0\x0B"), 'Proxy-Authorization')) {
            return null;
        }

        $value = \trim(\substr($header, $position + 1), " \n\r\t\0\x0B");

        return $value === '' ? null : $value;
    }

    /**
     * @param mixed[] $headers
     *
     * @return list<string>
     */
    private static function proxyAuthorizationHeaderValuesFromList(array $headers): array
    {
        $values = [];

        foreach ($headers as $header) {
            if (!\is_string($header)) {
                continue;
            }

            $value = self::proxyAuthorizationHeaderValue($header);
            if ($value !== null) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Computes the connection-reuse section signature for a proxy tunnel or
     * SOCKS proxy, or null when the request does not require sectioning.
     *
     * @param array<int|string, mixed> $conf
     */
    private static function proxyTunnelSignature(RequestInterface $request, array $conf): ?string
    {
        $proxy = self::getEffectiveProxy($conf);
        if ($proxy === null) {
            return null;
        }

        // SOCKS authentication binds an identity to the connection itself, for
        // plain http:// requests as much as https://, so it sections ahead of
        // the CONNECT tunnel domain checks.
        if (self::isSocksProxy($proxy, $conf)) {
            return self::socksProxySignature($proxy, $conf);
        }

        if (
            !self::usesProxyTunnel($request, $conf)
            || !self::isHttpProxyForConnectionReuse($proxy, $conf)
        ) {
            return null;
        }

        $headerAuth = self::curlProxyAuthorizationHeaderValues($conf);
        if ($headerAuth === [] && CurlVersion::supportsProxyCredentialAwareConnectionReuse()) {
            // libcurl keys reuse on parsed proxy credentials only from 8.19.0,
            // trusted from 8.20.0 (PROXY_CREDENTIAL_REUSE_VERSION); a literal
            // Proxy-Authorization header is never keyed and always sections.
            return self::DELEGATED_PROXY_TUNNEL_OWNER;
        }

        // Hash every proxy channel an old libcurl might not key reuse on. A
        // changed signature only forces a fresh connection, never relaxes
        // reuse, so over-covering is always safe; under-covering leaks. Proxy
        // credentials are the channel CVE-2026-3784 missed; the proxy-TLS
        // options are load-bearing on builds before the proxy-TLS reuse fixes
        // (the proxy client cert is keyed from 7.52.0, libcurl's first
        // HTTPS-proxy release; CVE-2016-5420 (7.50.1) is only the origin-cert
        // precedent; TLS-SRP from 7.83.1, CVE-2022-27782) and harmless after.
        // The private-key file and passphrase are hashed on this non-delegated
        // path too, as fallback hardening: libcurl's mTLS private-key matching
        // on reuse was incomplete before 8.21.0 (CVE-2026-8932). This does not
        // cover the delegated path (the early return above) or configured share
        // handles, so it is not a complete pre-8.21.0 mitigation. The key blob
        // and cert/key type encodings (PROXY_SSLKEY_BLOB, PROXY_SSLKEYTYPE,
        // PROXY_SSLCERTTYPE) are not hashed and are an accepted residual.
        $credentialState = [];
        foreach ([
            'CURLOPT_PROXYUSERPWD', 'CURLOPT_PROXYUSERNAME', 'CURLOPT_PROXYPASSWORD',
            'CURLOPT_PROXYTYPE',
            'CURLOPT_PROXY_SSLCERT', 'CURLOPT_PROXY_SSLCERT_BLOB', 'CURLOPT_PROXY_SSLKEY',
            'CURLOPT_PROXY_KEYPASSWD', 'CURLOPT_PROXY_TLSAUTH_USERNAME',
            'CURLOPT_PROXY_TLSAUTH_PASSWORD', 'CURLOPT_PROXY_SSLVERSION',
        ] as $name) {
            $credentialState[$name] = \defined($name)
                ? ($conf[(int) \constant($name)] ?? null)
                : null;
        }

        return \hash('sha256', \serialize([$proxy, $credentialState, $headerAuth]));
    }

    /**
     * @param array<int|string, mixed> $conf
     *
     * @return list<string>
     */
    private static function curlProxyAuthorizationHeaderValues(array $conf): array
    {
        if (!\defined('CURLOPT_PROXYHEADER')) {
            return [];
        }

        $option = (int) \constant('CURLOPT_PROXYHEADER');
        if (!\array_key_exists($option, $conf)) {
            return [];
        }

        $headers = $conf[$option];
        if (!\is_array($headers)) {
            return [];
        }

        return self::proxyAuthorizationHeaderValuesFromList($headers);
    }

    private function discardIdleHandles(): void
    {
        foreach ($this->handles as $id => $handle) {
            if (PHP_VERSION_ID < 80000) {
                \curl_close($handle);
            }

            unset($this->handles[$id]);
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    private function getDefaultConf(EasyHandle $easy): array
    {
        $uri = $easy->request->getUri();
        $protocols = Utils::normalizeProtocols($easy->options['protocols'] ?? ['http', 'https']);
        $scheme = $uri->getScheme();
        if (!\in_array($scheme, $protocols, true)) {
            throw new RequestException(\sprintf('The scheme "%s" is not allowed by the protocols request option.', $scheme), $easy->request);
        }

        if ($uri->getHost() === '') {
            throw new RequestException('URI must include a scheme and host. Use an absolute URI, a network-path reference starting with //, or configure a base_uri.', $easy->request);
        }

        $conf = [
            '_headers' => $easy->request->getHeaders(),
            \CURLOPT_CUSTOMREQUEST => $easy->request->getMethod(),
            \CURLOPT_URL => (string) $uri->withFragment(''),
            \CURLOPT_RETURNTRANSFER => false,
            \CURLOPT_HEADER => false,
            \CURLOPT_CONNECTTIMEOUT => 300,
        ];

        if (\defined('CURLOPT_PROTOCOLS')) {
            $conf[\CURLOPT_PROTOCOLS] = self::curlProtocolMask($protocols);
        }

        $version = $easy->request->getProtocolVersion();
        $multiplex = self::normalizeMultiplex($easy->options);

        if ('2' === $version || '2.0' === $version) {
            if (\in_array($multiplex, [Multiplexing::REQUIRE_EAGER, Multiplexing::REQUIRE_WAIT], true)) {
                self::assertRequiredMultiplexSupported($easy);
                // New HTTP/2 connections cannot negotiate HTTP/1.x here, and
                // the 8.14.0 floor's version-aware reuse matching keeps
                // reused connections on HTTP/2 as well.
                $conf[\CURLOPT_HTTP_VERSION] = (int) \constant('CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE');
            } else {
                $conf[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_2_0;
            }

            if (\in_array($multiplex, [Multiplexing::WAIT, Multiplexing::REQUIRE_WAIT], true) && CurlVersion::supportsMultiplex()) {
                // Wait for a connection that is still being established to the
                // same origin to reveal whether it can be multiplexed instead
                // of immediately opening another connection.
                $conf[(int) \constant('CURLOPT_PIPEWAIT')] = true;
            }
        } elseif ('1.1' === $version) {
            if (\in_array($multiplex, [Multiplexing::REQUIRE_EAGER, Multiplexing::REQUIRE_WAIT], true)) {
                throw new ConnectException(\sprintf('The "multiplex" request option cannot be required for HTTP/%s requests; use protocol version 2.', $version), $easy->request);
            }
            $conf[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_1;
        } else {
            if (\in_array($multiplex, [Multiplexing::REQUIRE_EAGER, Multiplexing::REQUIRE_WAIT], true)) {
                throw new ConnectException(\sprintf('The "multiplex" request option cannot be required for HTTP/%s requests; use protocol version 2.', $version), $easy->request);
            }
            $conf[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_0;
        }

        return $conf;
    }

    /**
     * @param string[] $protocols
     */
    private static function curlProtocolMask(array $protocols): int
    {
        $mask = 0;

        if (\in_array('http', $protocols, true)) {
            $mask |= \CURLPROTO_HTTP;
        }

        if (\in_array('https', $protocols, true)) {
            $mask |= \CURLPROTO_HTTPS;
        }

        return $mask;
    }

    /**
     * @param mixed $type
     */
    private static function normalizeTlsFileType(string $option, $type): string
    {
        if (!\is_string($type) || $type === '') {
            throw new \InvalidArgumentException(\sprintf('%s must be a non-empty string', $option));
        }

        return Psr7\Utils::asciiToUpper($type);
    }

    private static function shouldValidateSslKeyFile(?string $type): bool
    {
        return $type !== 'ENG' && $type !== 'PROV';
    }

    private function applyMethod(EasyHandle $easy, array &$conf): void
    {
        if ($easy->request->getMethod() === 'HEAD') {
            // libcurl stops at HEAD response headers only when CURLOPT_NOBODY
            // is set; CURLOPT_CUSTOMREQUEST changes only the method string.
            // NOBODY also suppresses request upload, so strip non-zero body
            // length, transfer coding, and a 100-continue expectation.
            $conf[\CURLOPT_CUSTOMREQUEST] = null;
            $conf[\CURLOPT_NOBODY] = true;
            unset(
                $conf[\CURLOPT_WRITEFUNCTION],
                $conf[\CURLOPT_READFUNCTION],
                $conf[\CURLOPT_FILE],
                $conf[\CURLOPT_INFILE]
            );
            if (\trim($easy->request->getHeaderLine('Content-Length'), " \n\r\t\0\x0B") !== '0') {
                $this->removeHeader('Content-Length', $conf);
            }
            $this->removeHeader('Transfer-Encoding', $conf);
            if (Psr7\Utils::caselessEquals(\trim($easy->request->getHeaderLine('Expect'), " \n\r\t\0\x0B"), '100-continue')) {
                $this->removeHeader('Expect', $conf);
            }

            return;
        }

        $body = $easy->request->getBody();
        $size = $body->getSize();

        if ($size === null || $size > 0) {
            $this->applyBody($easy->request, $easy->options, $conf);

            return;
        }

        $method = $easy->request->getMethod();
        if ($method === 'PUT' || $method === 'POST') {
            // See https://datatracker.ietf.org/doc/html/rfc7230#section-3.3.2
            if (!$easy->request->hasHeader('Content-Length')) {
                $conf[\CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
            }
        }
    }

    private function applyBody(RequestInterface $request, array $options, array &$conf): void
    {
        $size = $request->hasHeader('Content-Length')
            ? (int) $request->getHeaderLine('Content-Length')
            : null;

        // Send the body as a string if the size is less than 1MB OR if the
        // [curl][body_as_string] request value is set.
        if (($size !== null && $size < 1000000) || !empty($options['_body_as_string'])) {
            $conf[\CURLOPT_POSTFIELDS] = (string) $request->getBody();
            // Don't duplicate the Content-Length header
            $this->removeHeader('Content-Length', $conf);
            $this->removeHeader('Transfer-Encoding', $conf);
        } else {
            $conf[\CURLOPT_UPLOAD] = true;
            if ($size !== null) {
                $conf[\CURLOPT_INFILESIZE] = $size;
                $this->removeHeader('Content-Length', $conf);
            }
            $body = $request->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }
            $remaining = $size;
            $conf[\CURLOPT_READFUNCTION] = static function ($ch, $fd, $length) use ($body, &$remaining) {
                if ($remaining === 0) {
                    return '';
                }

                $limit = $remaining === null ? $length : \min($length, $remaining);
                $data = $body->read($limit);
                if ($remaining !== null) {
                    $remaining -= \strlen($data);
                }

                return $data;
            };
        }

        // If the Expect header is not present, prevent curl from adding it
        if (!$request->hasHeader('Expect')) {
            $conf[\CURLOPT_HTTPHEADER][] = 'Expect:';
        }

        // cURL sometimes adds a content-type by default. Prevent this.
        if (!$request->hasHeader('Content-Type')) {
            $conf[\CURLOPT_HTTPHEADER][] = 'Content-Type:';
        }
    }

    private function applyHeaders(EasyHandle $easy, array &$conf): void
    {
        foreach ($conf['_headers'] as $name => $values) {
            // A first-class Proxy-Authorization header is proxy-scoped and
            // must never be generated in the origin header list; managed
            // handling routes it to CURLOPT_PROXYHEADER or safely omits it on
            // a legacy non-HTTP-proxy route. The
            // caselessEquals() helper is locale-independent, unlike
            // strcasecmp(), so a locale cannot make this match miss and
            // re-leak the credential.
            if (Psr7\Utils::caselessEquals((string) $name, 'Proxy-Authorization')) {
                continue;
            }

            foreach ($values as $value) {
                $value = (string) $value;
                if ($value === '') {
                    // cURL requires a special format for empty headers.
                    // See https://github.com/guzzle/guzzle/issues/1882 for more details.
                    $conf[\CURLOPT_HTTPHEADER][] = "$name;";
                } else {
                    $conf[\CURLOPT_HTTPHEADER][] = "$name: $value";
                }
            }
        }

        // Remove the Accept header if one was not set
        if (!$easy->request->hasHeader('Accept')) {
            $conf[\CURLOPT_HTTPHEADER][] = 'Accept:';
        }
    }

    /**
     * Remove a header from the options array.
     *
     * @param string $name    Case-insensitive header to remove
     * @param array  $options Array of options to modify
     */
    private function removeHeader(string $name, array &$options): void
    {
        foreach (\array_keys($options['_headers']) as $key) {
            if (Psr7\Utils::caselessEquals((string) $key, $name)) {
                unset($options['_headers'][$key]);

                return;
            }
        }
    }

    private function applyHandlerOptions(EasyHandle $easy, array &$conf): void
    {
        $options = $easy->options;
        if (isset($options['verify'])) {
            if ($options['verify'] === false) {
                unset($conf[\CURLOPT_CAINFO]);
                $conf[\CURLOPT_SSL_VERIFYHOST] = 0;
                $conf[\CURLOPT_SSL_VERIFYPEER] = false;
            } else {
                $conf[\CURLOPT_SSL_VERIFYHOST] = 2;
                $conf[\CURLOPT_SSL_VERIFYPEER] = true;
                if (\is_string($options['verify'])) {
                    // Throw an error if the file/folder/link path is not valid or doesn't exist.
                    if (!\file_exists($options['verify'])) {
                        throw new \InvalidArgumentException("SSL CA bundle not found: {$options['verify']}");
                    }
                    // If it's a directory or a link to a directory use CURLOPT_CAPATH.
                    // If not, it's probably a file, or a link to a file, so use CURLOPT_CAINFO.
                    if (
                        \is_dir($options['verify'])
                        || (
                            \is_link($options['verify']) === true
                            && ($verifyLink = \readlink($options['verify'])) !== false
                            && \is_dir($verifyLink)
                        )
                    ) {
                        $conf[\CURLOPT_CAPATH] = $options['verify'];
                    } else {
                        $conf[\CURLOPT_CAINFO] = $options['verify'];
                    }
                }
            }
        }

        if (!isset($options['curl'][\CURLOPT_ENCODING]) && isset($options['decode_content']) && $options['decode_content'] !== false) {
            $accept = $easy->request->getHeaderLine('Accept-Encoding');
            if ($accept !== '') {
                $conf[\CURLOPT_ENCODING] = $accept;
            } else {
                // The empty string enables all available decoders and implicitly
                // sets a matching 'Accept-Encoding' header.
                $conf[\CURLOPT_ENCODING] = '';
                // But as the user did not specify any encoding preference,
                // let's leave it up to server by preventing curl from sending
                // the header, which will be interpreted as 'Accept-Encoding: *'.
                // https://www.rfc-editor.org/rfc/rfc9110#field.accept-encoding
                $conf[\CURLOPT_HTTPHEADER][] = 'Accept-Encoding:';
            }
        }

        if (!isset($options['sink'])) {
            // Use a default temp stream if no sink was set.
            $options['sink'] = Psr7\Utils::tryFopen('php://temp', 'w+');
        }
        $sink = $options['sink'];
        if (!\is_string($sink)) {
            $sink = Psr7\Utils::streamFor($sink);
        } elseif (!\is_dir(\dirname($sink))) {
            // Ensure that the directory exists before failing in curl.
            throw new \RuntimeException(\sprintf('Directory %s does not exist for sink value of %s', \dirname($sink), $sink));
        } else {
            $sink = new LazyOpenStream($sink, 'w+');
        }
        $easy->sink = $sink;
        $conf[\CURLOPT_WRITEFUNCTION] = static function ($ch, $write) use ($sink): int {
            return $sink->write($write);
        };

        $timeoutRequiresNoSignal = false;
        if (isset($options['timeout'])) {
            $timeoutRequiresNoSignal |= $options['timeout'] < 1;
            $conf[\CURLOPT_TIMEOUT_MS] = $options['timeout'] * 1000;
        }

        // CURL default value is CURL_IPRESOLVE_WHATEVER
        if (isset($options['force_ip_resolve'])) {
            if ('v4' === $options['force_ip_resolve']) {
                $conf[\CURLOPT_IPRESOLVE] = \CURL_IPRESOLVE_V4;
            } elseif ('v6' === $options['force_ip_resolve']) {
                $conf[\CURLOPT_IPRESOLVE] = \CURL_IPRESOLVE_V6;
            }
        }

        if (isset($options['connect_timeout'])) {
            $timeoutRequiresNoSignal |= $options['connect_timeout'] < 1;
            $conf[\CURLOPT_CONNECTTIMEOUT_MS] = $options['connect_timeout'] * 1000;
        }

        if ($timeoutRequiresNoSignal && Psr7\Utils::asciiToUpper(\substr(\PHP_OS, 0, 3)) !== 'WIN') {
            $conf[\CURLOPT_NOSIGNAL] = true;
        }

        // Always pin CURLOPT_PROXY (and CURLOPT_NOPROXY when available) so
        // that libcurl never falls back to reading proxy environment
        // variables itself. When the proxy request option makes no decision,
        // the environment is resolved here with libcurl's own semantics.
        [$proxyConf, $noProxyConf] = self::resolveProxy($easy->request, $options);
        self::assertResolvedProxySupported($easy->request, $proxyConf);

        $conf[\CURLOPT_PROXY] = $proxyConf;
        if (\defined('CURLOPT_NOPROXY')) {
            $conf[(int) \constant('CURLOPT_NOPROXY')] = $noProxyConf;
        }

        $this->applyTlsVersionRange($easy, $conf);

        $certType = null;
        if (isset($options['cert_type'])) {
            $certType = self::normalizeTlsFileType('cert_type', $options['cert_type']);
            $conf[\CURLOPT_SSLCERTTYPE] = $certType;
        }

        if (isset($options['cert'])) {
            $cert = $options['cert'];
            if (\is_array($cert)) {
                if (!isset($cert[0]) || !\is_string($cert[0])) {
                    throw new \InvalidArgumentException('Invalid cert request option');
                }
                if (isset($cert[1])) {
                    if (!\is_string($cert[1])) {
                        throw new \InvalidArgumentException('Invalid cert request option');
                    }
                    $conf[\CURLOPT_SSLCERTPASSWD] = $cert[1];
                }
                $cert = $cert[0];
            }
            if (!\is_string($cert)) {
                throw new \InvalidArgumentException('Invalid cert request option');
            }
            if (!\file_exists($cert)) {
                throw new \InvalidArgumentException("SSL certificate not found: {$cert}");
            }
            // OpenSSL (versions 0.9.3 and later) also support "P12" for PKCS#12-encoded files.
            // see https://curl.se/libcurl/c/CURLOPT_SSLCERTTYPE.html
            $ext = pathinfo($cert, \PATHINFO_EXTENSION);
            if ($certType === null && preg_match('#^(der|p12)$#iD', $ext)) {
                $conf[\CURLOPT_SSLCERTTYPE] = Psr7\Utils::asciiToUpper($ext);
            }
            $conf[\CURLOPT_SSLCERT] = $cert;
        }

        $sslKeyType = null;
        if (isset($options['ssl_key_type'])) {
            $sslKeyType = self::normalizeTlsFileType('ssl_key_type', $options['ssl_key_type']);
            $conf[\CURLOPT_SSLKEYTYPE] = $sslKeyType;
        }

        if (isset($options['ssl_key'])) {
            if (\is_array($options['ssl_key'])) {
                if (!isset($options['ssl_key'][0]) || !\is_string($options['ssl_key'][0])) {
                    throw new \InvalidArgumentException('Invalid ssl_key request option');
                }
                if (isset($options['ssl_key'][1])) {
                    if (!\is_string($options['ssl_key'][1])) {
                        throw new \InvalidArgumentException('Invalid ssl_key request option');
                    }
                    $conf[\CURLOPT_SSLKEYPASSWD] = $options['ssl_key'][1];
                }
                $sslKey = $options['ssl_key'][0];
            }

            $sslKey = $sslKey ?? $options['ssl_key'];

            if (!\is_string($sslKey)) {
                throw new \InvalidArgumentException('Invalid ssl_key request option');
            }

            if (self::shouldValidateSslKeyFile($sslKeyType) && !\file_exists($sslKey)) {
                throw new \InvalidArgumentException("SSL private key not found: {$sslKey}");
            }
            $conf[\CURLOPT_SSLKEY] = $sslKey;
        }

        if (isset($options['progress'])) {
            $progress = $options['progress'];
            if (!\is_callable($progress)) {
                throw new \InvalidArgumentException('progress client option must be callable');
            }
            $conf[\CURLOPT_NOPROGRESS] = false;
            $conf[\CURLOPT_PROGRESSFUNCTION] = static function ($resource, int $downloadSize, int $downloaded, int $uploadSize, int $uploaded) use ($progress) {
                $progress($downloadSize, $downloaded, $uploadSize, $uploaded);
            };
        }

        if (!empty($options['debug'])) {
            $conf[\CURLOPT_STDERR] = Utils::debugResource($options['debug']);
            $conf[\CURLOPT_VERBOSE] = true;
        }
    }

    private function applyTlsVersionRange(EasyHandle $easy, array &$conf): void
    {
        $options = $easy->options;
        $cryptoMethod = $options['crypto_method'] ?? null;
        $cryptoMethodMax = $options['crypto_method_max'] ?? null;

        if ($cryptoMethod === null && $cryptoMethodMax === null) {
            return;
        }

        $protocolVersion = $easy->request->getProtocolVersion();
        $isHttp2 = '2' === $protocolVersion || '2.0' === $protocolVersion;

        if ($isHttp2 && $cryptoMethodMax !== null && TlsVersion::ordinal('crypto_method_max', $cryptoMethodMax) < 12) {
            throw new \InvalidArgumentException(
                'Invalid crypto_method_max request option: HTTP/2 requires TLS 1.2 or higher'
            );
        }

        if ($isHttp2 && $cryptoMethod !== null && TlsVersion::ordinal('crypto_method', $cryptoMethod) < 12) {
            $cryptoMethod = \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        }

        TlsVersion::assertRange($cryptoMethod, $cryptoMethodMax);

        $sslVersion = $cryptoMethod === null
            ? \CURL_SSLVERSION_DEFAULT
            : self::curlMinSslVersion($cryptoMethod);

        if ($cryptoMethodMax !== null) {
            $sslVersion |= self::curlMaxSslVersion($cryptoMethodMax);
        }

        $conf[\CURLOPT_SSLVERSION] = $sslVersion;
    }

    /**
     * @param mixed $value
     */
    private static function curlMinSslVersion($value): int
    {
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT) {
            return \CURL_SSLVERSION_TLSv1_0;
        }

        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT) {
            return \CURL_SSLVERSION_TLSv1_1;
        }

        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT) {
            if (!CurlVersion::supportsTls12()) {
                throw new \InvalidArgumentException('Invalid crypto_method request option: TLS 1.2 not supported by your version of cURL');
            }

            return \CURL_SSLVERSION_TLSv1_2;
        }

        if (\defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') && $value === \STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT) {
            if (!CurlVersion::supportsTls13()) {
                throw new \InvalidArgumentException('Invalid crypto_method request option: TLS 1.3 not supported by your version of cURL');
            }

            return \CURL_SSLVERSION_TLSv1_3;
        }

        throw new \InvalidArgumentException('Invalid crypto_method request option: unknown version provided');
    }

    /**
     * @param mixed $value
     */
    private static function curlMaxSslVersion($value): int
    {
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT) {
            return self::requireCurlMaxSslVersion('CURL_SSLVERSION_MAX_TLSv1_0');
        }

        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT) {
            return self::requireCurlMaxSslVersion('CURL_SSLVERSION_MAX_TLSv1_1');
        }

        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT) {
            return self::requireCurlMaxSslVersion('CURL_SSLVERSION_MAX_TLSv1_2');
        }

        if (\defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') && $value === \STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT) {
            return self::requireCurlMaxSslVersion('CURL_SSLVERSION_MAX_TLSv1_3');
        }

        throw new \InvalidArgumentException('Invalid crypto_method_max request option: unknown version provided');
    }

    private static function requireCurlMaxSslVersion(string $constant): int
    {
        if (\defined($constant)) {
            /** @var int */
            return \constant($constant);
        }

        throw new \InvalidArgumentException(
            'Invalid crypto_method_max request option: maximum TLS version control is not supported by your version of cURL'
        );
    }

    private static function validateRequestUriScheme(RequestInterface $request): void
    {
        $scheme = $request->getUri()->getScheme();
        if ($scheme === '') {
            throw new RequestException('URI must include a scheme and host. Use an absolute URI, a network-path reference starting with //, or configure a base_uri.', $request);
        }

        if (!\in_array($scheme, ['http', 'https'], true)) {
            throw new RequestException(\sprintf("The scheme '%s' is not supported.", $scheme), $request);
        }
    }

    /**
     * This function ensures that a response was set on a transaction. If one
     * was not set, then the request is retried if possible. This error
     * typically means you are sending a payload, curl encountered a
     * "Connection died, retrying a fresh connect" error, tried to rewind the
     * stream, and then encountered a "necessary data rewind wasn't possible"
     * error, causing the request to be sent through curl_multi_info_read()
     * without an error status.
     *
     * @param callable(RequestInterface, array): PromiseInterface $handler
     */
    private static function retryFailedRewind(callable $handler, EasyHandle $easy, array $ctx): PromiseInterface
    {
        try {
            // Only rewind if the body has been read from.
            $body = $easy->request->getBody();
            if ($body->tell() > 0) {
                $body->rewind();
            }
        } catch (\RuntimeException $e) {
            $ctx['error'] = 'The connection unexpectedly failed without '
                .'providing an error. The request would have been retried, '
                .'but attempting to rewind the request body failed. '
                .'Exception: '.$e;

            return self::createRejection($easy, $ctx);
        }

        // Retry no more than 3 times before giving up.
        if (!isset($easy->options['_curl_retries'])) {
            $easy->options['_curl_retries'] = 1;
        } elseif ($easy->options['_curl_retries'] == 2) {
            $ctx['error'] = 'The cURL request was retried 3 times '
                .'and did not succeed. The most likely reason for the failure '
                .'is that cURL was unable to rewind the body of the request '
                .'and subsequent retries resulted in the same error. Turn on '
                .'the debug option to see what went wrong. See '
                .'https://bugs.php.net/bug.php?id=47204 for more information.';

            return self::createRejection($easy, $ctx);
        } else {
            ++$easy->options['_curl_retries'];
        }

        return $handler($easy->request, $easy->options);
    }

    /**
     * Parses validated trailer field lines into an associative array keyed by
     * lowercased field name, preserving first-occurrence key order and wire
     * value order.
     */
    private static function headersFromTrailerLines(array $lines): array
    {
        $headers = [];

        foreach ($lines as $line) {
            [$name, $value] = \explode(':', $line, 2);
            $name = Psr7\Utils::asciiToLower(\trim($name, " \n\r\t\0\x0B"));
            $headers[$name][] = \trim($value, " \n\r\t\0\x0B");
        }

        return $headers;
    }

    private function createHeaderFn(EasyHandle $easy): callable
    {
        if (isset($easy->options['on_headers'])) {
            $onHeaders = $easy->options['on_headers'];

            if (!\is_callable($onHeaders)) {
                throw new \InvalidArgumentException('on_headers must be callable');
            }
        } else {
            $onHeaders = null;
        }

        $startingResponse = false;
        $collectingTrailers = false;
        $retainTrailers = isset($easy->options['on_trailers']);

        return static function ($ch, $h) use (
            $onHeaders,
            $easy,
            &$startingResponse,
            &$collectingTrailers,
            $retainTrailers
        ) {
            $value = \trim($h, " \n\r\t\0\x0B");
            if ($h === "\r\n" || $h === "\n" || $h === "\r" || $h === '') {
                if ($collectingTrailers) {
                    // A blank line ends the trailer section; the response has
                    // already been created.
                    return \strlen($h);
                }
                $startingResponse = true;
                try {
                    $easy->createResponse();
                } catch (\Throwable $e) {
                    $easy->response = null;
                    $easy->createResponseException = $e;

                    return -1;
                }
                if ($onHeaders !== null) {
                    try {
                        $onHeaders($easy->response);
                    } catch (\Throwable $e) {
                        // Associate the exception with the handle and trigger
                        // a curl header write error by returning 0.
                        $easy->onHeadersException = $e;

                        return -1;
                    }
                }
            } elseif ($startingResponse || $collectingTrailers) {
                if ($easy->response !== null && !HeaderProcessor::isStatusLineCandidate($h)) {
                    // Trailer fields arrive through the header callback after
                    // the body; a new header block always begins with a status
                    // line.
                    $collectingTrailers = true;

                    if ($retainTrailers && HeaderProcessor::isValidHeaderFieldLine($h)) {
                        $easy->trailers[] = $value;
                    }
                } else {
                    $collectingTrailers = false;
                    $easy->trailers = [];
                    $easy->headers = [$value];
                }
                $startingResponse = false;
            } else {
                $easy->headers[] = $value;
            }

            return \strlen($h);
        };
    }

    public function __destruct()
    {
        $this->discardIdleHandles();
    }
}
