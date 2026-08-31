<?php

namespace GuzzleHttp\Handler;

use GuzzleHttp\Psr7;

/**
 * Resolves proxy configuration from the process environment with the same
 * semantics libcurl applies, so the cURL handlers can pin CURLOPT_PROXY and
 * CURLOPT_NOPROXY explicitly and libcurl never reads the environment itself.
 *
 * @internal
 */
final class ProxyEnvironment
{
    private function __construct()
    {
    }

    /**
     * Resolves the proxy to use for the given request scheme.
     *
     * The lookup mirrors libcurl for the http and https schemes the handlers
     * accept: the lowercase scheme-specific variable first, its uppercase
     * variant next (except for "http", where uppercase HTTP_PROXY is never
     * read), then all_proxy/ALL_PROXY.
     *
     * @return string|null The proxy to use; null when the environment
     *                     configures none.
     */
    public static function getProxyForScheme(string $scheme): ?string
    {
        $scheme = Psr7\Utils::asciiToLower($scheme);
        $candidates = [$scheme.'_proxy'];
        if ($scheme !== 'http') {
            // Uppercase HTTP_PROXY is deliberately never consulted: a CGI
            // request header "Proxy:" becomes HTTP_PROXY in the environment.
            // See https://httpoxy.org for more information.
            $candidates[] = Psr7\Utils::asciiToUpper($scheme).'_PROXY';
        }
        $candidates[] = 'all_proxy';
        $candidates[] = 'ALL_PROXY';

        foreach ($candidates as $name) {
            $value = self::getenv($name);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return string|null The no-proxy list; null when nothing is set.
     */
    public static function getNoProxy(): ?string
    {
        foreach (['no_proxy', 'NO_PROXY'] as $name) {
            $value = self::getenv($name);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Splits a no_proxy environment value into matchable entries.
     *
     * Mirrors libcurl's tokenization: entries may be separated by commas or
     * blanks, and a single leading dot is ignored, so ".example.com" bypasses
     * example.com and its subdomains exactly as a bare domain entry does.
     *
     * @return string[]
     */
    public static function splitNoProxy(string $noProxy): array
    {
        $entries = [];

        $split = \preg_split('/[\s,]+/', $noProxy);

        if ($split === false) {
            throw new \RuntimeException('Unable to split the no_proxy value: '.\preg_last_error_msg());
        }

        foreach ($split as $entry) {
            if ($entry !== '' && $entry[0] === '.') {
                $entry = \substr($entry, 1);
            }

            if ($entry !== '') {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    private static function getenv(string $name): ?string
    {
        // Windows environment variables are case-insensitive, so the
        // lowercase-only httpoxy defence does not hold there. Outside the
        // CLI SAPI on Windows, environment proxies are not resolved at all
        // (a safe-side divergence from libcurl).
        if (\PHP_OS_FAMILY === 'Windows' && \PHP_SAPI !== 'cli') {
            return null;
        }

        // local_only: the OS environment and putenv() only - the same
        // environ(7) libcurl reads. SAPI request environments such as
        // fastcgi_param or SetEnv are deliberately excluded.
        $value = \getenv($name, true);

        // libcurl's GetEnv (lib/getenv.c) treats variables set to an empty
        // string as unset on every version, so the lookup falls through to
        // the next candidate.
        return $value === false || $value === '' ? null : $value;
    }
}
