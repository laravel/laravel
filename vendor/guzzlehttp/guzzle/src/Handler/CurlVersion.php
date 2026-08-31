<?php

namespace GuzzleHttp\Handler;

/**
 * @internal
 */
final class CurlVersion
{
    private const MIN_VERSION = '7.21.2';

    private const TLS_12_VERSION = '7.34.0';

    private const TLS_13_VERSION = '7.52.0';

    private const CONNECTION_CAP_VERSION = '7.30.0';

    // CURLOPT_PIPEWAIT exists since libcurl 7.43.0, and multi handles have
    // multiplexed by default since 7.62.0 - but a 7.65.0-7.65.1 regression
    // dropped that default, which 7.65.2 restored, so 7.65.2 is the floor at
    // which PIPEWAIT is reliably effective.
    private const MULTIPLEX_VERSION = '7.65.2';

    // libcurl's connection matcher refuses to hand a transfer wanting
    // HTTP/1.x a pooled connection that already negotiated HTTP/2 or newer
    // from 7.77.0: ConnectionExists() in lib/url.c gained the check between
    // the 7.76.0 and 7.77.0 releases. The HTTP/2 branch of the check
    // regressed to a debug log in 8.11.0 (curl commit 433d730) and was
    // restored in 8.13.0 via the negotiation mask (curl commit db72b8d), so
    // 8.11.0 through 8.12.1 are vulnerable again.
    private const HTTP_VERSION_REUSE_MATCH_VERSION = '7.77.0';

    private const HTTP_VERSION_REUSE_MATCH_REGRESSION = '8.11.0';

    private const HTTP_VERSION_REUSE_MATCH_RESTORED = '8.13.0';

    // CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE restricts the ALPN offer to h2 only
    // since libcurl 8.10.0, and connection reuse matching stopped handing
    // lower-version connections to prior-knowledge transfers in 8.14.0; below
    // that, a required request could silently be sent over a reused HTTP/1.1
    // connection.
    private const REQUIRED_MULTIPLEX_VERSION = '8.14.0';

    // curl 7.52.0 introduced HTTPS proxy support, advertised by a feature bit
    // (a build can meet the version yet lack the feature). Earlier libcurl
    // mishandles an https:// proxy: before 7.50.2 it silently downgrades to a
    // plaintext HTTP proxy, and 7.50.2 through 7.51 reject it at connect time.
    private const HTTPS_PROXY_VERSION = '7.52.0';

    private const HANDLER_SHARING_VERSION = '7.35.0';

    private const SSL_SESSION_SHARING_VERSION = '8.6.0';

    // curl 7.57.0 added share-handle connection caches through
    // CURL_LOCK_DATA_CONNECT; older share objects can only hold DNS, TLS
    // session, and cookie data, never connections.
    private const SHARE_CONNECTION_CACHE_VERSION = '7.57.0';

    // curl 7.83.1 added proxy TLS-SRP to the connection-reuse match
    // (CVE-2022-27782); the proxy client certificate was matched from 7.52.0,
    // so proxy TLS credentials are trusted from 7.83.1 onwards.
    private const PROXY_TLS_CREDENTIAL_REUSE_VERSION = '7.83.1';

    // curl 8.19.0 fixed proxy tunnel reuse after credential changes
    // (CVE-2026-3784), but related proxy credential leak flaws were only
    // fixed in 8.20.0, so connection reuse is trusted from 8.20.0 onwards.
    private const PROXY_CREDENTIAL_REUSE_VERSION = '8.20.0';

    // curl 7.69.0 started comparing SOCKS proxy credentials when matching
    // connections for reuse (curl #4835); older libcurl matches a SOCKS proxy
    // by type, host, and port only.
    private const SOCKS_PROXY_CREDENTIAL_REUSE_VERSION = '7.69.0';

    private const PROXY_HEADER_SEPARATION_VERSION = '7.37.0';

    /**
     * @var array{version: string, features: int}|false|null
     */
    private static $versionInfo;

    private function __construct()
    {
    }

    public static function supportsCurlHandler(): bool
    {
        $version = self::getVersion();

        return $version !== null && \version_compare($version, self::MIN_VERSION, '>=');
    }

    public static function supportsTls12(): bool
    {
        $version = self::getVersion();

        return self::supportsSsl()
            && \defined('CURL_SSLVERSION_TLSv1_2')
            && $version !== null
            && \version_compare($version, self::TLS_12_VERSION, '>=');
    }

    public static function supportsTls13(): bool
    {
        $version = self::getVersion();

        return self::supportsSsl()
            && \defined('CURL_SSLVERSION_TLSv1_3')
            && $version !== null
            && \version_compare($version, self::TLS_13_VERSION, '>=');
    }

    public static function supportsHttp2(): bool
    {
        $versionInfo = self::getVersionInfo();

        return self::supportsTls12()
            && \defined('CURL_VERSION_HTTP2')
            && $versionInfo !== null
            && 0 !== (\CURL_VERSION_HTTP2 & $versionInfo['features']);
    }

    public static function supportsMultiplex(): bool
    {
        $version = self::getVersion();

        return \defined('CURLOPT_PIPEWAIT')
            && $version !== null
            && \version_compare($version, self::MULTIPLEX_VERSION, '>=');
    }

    public static function supportsHttpVersionReuseMatching(): bool
    {
        $version = self::getVersion();

        if ($version === null || \version_compare($version, self::HTTP_VERSION_REUSE_MATCH_VERSION, '<')) {
            return false;
        }

        return \version_compare($version, self::HTTP_VERSION_REUSE_MATCH_REGRESSION, '<')
            || \version_compare($version, self::HTTP_VERSION_REUSE_MATCH_RESTORED, '>=');
    }

    public static function supportsConnectionCaps(): bool
    {
        $version = self::getVersion();

        return \defined('CURLMOPT_MAX_HOST_CONNECTIONS')
            && \defined('CURLMOPT_MAX_TOTAL_CONNECTIONS')
            && $version !== null
            && \version_compare($version, self::CONNECTION_CAP_VERSION, '>=');
    }

    public static function ensureConnectionCapsSupported(string $option): void
    {
        if (self::supportsConnectionCaps()) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'The "%s" option requires PHP cURL support for CURLMOPT_MAX_HOST_CONNECTIONS and CURLMOPT_MAX_TOTAL_CONNECTIONS with libcurl %s or newer.',
            $option,
            self::CONNECTION_CAP_VERSION
        ));
    }

    public static function supportsRequiredMultiplex(): bool
    {
        $version = self::getVersion();

        return \defined('CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE')
            && $version !== null
            && self::supportsHttp2()
            && \version_compare($version, self::REQUIRED_MULTIPLEX_VERSION, '>=');
    }

    public static function supportsHttpsProxy(): bool
    {
        $versionInfo = self::getVersionInfo();

        // CURL_VERSION_HTTPS_PROXY is not defined on every supported PHP
        // version; fall back to the curl.h bit value.
        $httpsProxyFeature = \defined('CURL_VERSION_HTTPS_PROXY') ? \CURL_VERSION_HTTPS_PROXY : (1 << 21);

        return $versionInfo !== null
            && \version_compare($versionInfo['version'], self::HTTPS_PROXY_VERSION, '>=')
            && 0 !== ($httpsProxyFeature & $versionInfo['features']);
    }

    public static function supportsNtlm(): bool
    {
        $versionInfo = self::getVersionInfo();

        // CURL_VERSION_NTLM is not defined on every supported PHP version; fall
        // back to the curl.h bit value.
        $ntlmFeature = \defined('CURL_VERSION_NTLM') ? \CURL_VERSION_NTLM : (1 << 4);

        return \defined('CURLAUTH_NTLM')
            && $versionInfo !== null
            && 0 !== ($ntlmFeature & $versionInfo['features']);
    }

    public static function supportsHandlerSharing(): bool
    {
        $version = self::getVersion();

        return $version !== null && \version_compare($version, self::HANDLER_SHARING_VERSION, '>=');
    }

    public static function ensureHandlerSharingSupported(): void
    {
        if (!self::supportsHandlerSharing()) {
            throw new \InvalidArgumentException(\sprintf(
                'The "transport_sharing" option requires libcurl %s or higher for cURL share handles.',
                self::HANDLER_SHARING_VERSION
            ));
        }
    }

    public static function supportsSslSessionSharing(): bool
    {
        $version = self::getVersion();

        return self::supportsSsl()
            && $version !== null
            && \version_compare($version, self::SSL_SESSION_SHARING_VERSION, '>=');
    }

    public static function ensureSslSessionSharingSupported(): void
    {
        if (!self::supportsSslSessionSharing()) {
            throw new \InvalidArgumentException(\sprintf(
                'The "transport_sharing" option requires libcurl %s or higher with SSL support for SSL session sharing.',
                self::SSL_SESSION_SHARING_VERSION
            ));
        }
    }

    public static function supportsShareConnectionCaches(): bool
    {
        $version = self::getVersion();

        // An undetectable libcurl version is treated as capable so the
        // opaque share safeguards fail closed.
        return $version === null || \version_compare($version, self::SHARE_CONNECTION_CACHE_VERSION, '>=');
    }

    public static function supportsProxyTlsCredentialAwareConnectionReuse(): bool
    {
        $version = self::getVersion();

        return $version !== null
            && \version_compare($version, self::PROXY_TLS_CREDENTIAL_REUSE_VERSION, '>=');
    }

    public static function supportsProxyCredentialAwareConnectionReuse(): bool
    {
        $version = self::getVersion();

        return $version !== null
            && \version_compare($version, self::PROXY_CREDENTIAL_REUSE_VERSION, '>=');
    }

    public static function supportsSocksProxyCredentialAwareConnectionReuse(): bool
    {
        $version = self::getVersion();

        return $version !== null
            && \version_compare($version, self::SOCKS_PROXY_CREDENTIAL_REUSE_VERSION, '>=');
    }

    public static function supportsProxyHeaderSeparation(): bool
    {
        $version = self::getVersion();

        return $version !== null
            && \version_compare($version, self::PROXY_HEADER_SEPARATION_VERSION, '>=')
            && \defined('CURLOPT_PROXYHEADER')
            && \defined('CURLOPT_HEADEROPT')
            && \defined('CURLHEADER_SEPARATE');
    }

    private static function supportsSsl(): bool
    {
        $versionInfo = self::getVersionInfo();

        return \defined('CURL_VERSION_SSL')
            && $versionInfo !== null
            && 0 !== (\CURL_VERSION_SSL & $versionInfo['features']);
    }

    public static function getVersion(): ?string
    {
        $versionInfo = self::getVersionInfo();

        return $versionInfo === null ? null : $versionInfo['version'];
    }

    /**
     * @return array{version: string, features: int}|null
     */
    private static function getVersionInfo(): ?array
    {
        if (self::$versionInfo === null) {
            if (!\function_exists('curl_version')) {
                self::$versionInfo = false;
            } else {
                $versionInfo = \curl_version();
                self::$versionInfo = \is_array($versionInfo)
                    && isset($versionInfo['version'], $versionInfo['features'])
                    && \is_string($versionInfo['version'])
                    && \is_int($versionInfo['features'])
                        ? [
                            'version' => $versionInfo['version'],
                            'features' => $versionInfo['features'],
                        ]
                        : false;
            }
        }

        return self::$versionInfo === false ? null : self::$versionInfo;
    }
}
