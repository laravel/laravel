<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

/**
 * @internal
 */
final class Rfc7230
{
    /**
     * Header related regular expressions (based on amphp/http package)
     *
     * Note: header delimiter (\r\n) is modified to \r?\n to accept line feed only delimiters for BC reasons.
     *
     * @see https://github.com/amphp/http/blob/v1.0.1/src/Rfc7230.php#L12-L15
     *
     * @license https://github.com/amphp/http/blob/v1.0.1/LICENSE
     */
    public const HEADER_REGEX = "(^([^()<>@,;:\\\"/[\]?={}\x01-\x20\x7F]++):[ \t]*+((?:[ \t]*+[\x21-\x7E\x80-\xFF]++)*+)[ \t]*+\r?\n)m";
    public const HEADER_FOLD_REGEX = "(\r?\n[ \t]++)";

    /**
     * @return array{0: string, 1: int|null}|null
     */
    public static function parseHostHeader(string $authority): ?array
    {
        if ($authority === '') {
            return null;
        }

        $host = $authority;
        $port = null;

        if ($authority[0] === '[') {
            $closingBracket = strpos($authority, ']');
            if ($closingBracket === false) {
                return null;
            }

            $host = substr($authority, 0, $closingBracket + 1);
            $remainder = substr($authority, $closingBracket + 1);
            if ($remainder !== '') {
                if ($remainder[0] !== ':') {
                    return null;
                }

                $port = self::parseAuthorityPort(substr($remainder, 1));
                if ($port === null) {
                    return null;
                }
            }
        } elseif (false !== ($colon = strpos($authority, ':'))) {
            $host = substr($authority, 0, $colon);
            $port = self::parseAuthorityPort(substr($authority, $colon + 1));
            if ($port === null) {
                return null;
            }
        }

        if ($host === '' || !self::isValidHostHeaderHost($host)) {
            return null;
        }

        return [$host, $port];
    }

    private static function isValidHostHeaderHost(string $host): bool
    {
        $invalidHost = preg_match('/[\x00-\x20\x7F\/\?#@\\\\]/', $host);

        if ($invalidHost === false) {
            return false;
        }

        if ($invalidHost === 1) {
            return false;
        }

        if (strpos($host, '[') !== false || strpos($host, ']') !== false) {
            if ($host[0] !== '[' || substr($host, -1) !== ']') {
                return false;
            }

            $address = substr($host, 1, -1);

            return filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6) !== false
                || preg_match('/^v[0-9a-f]+\.['.Rfc3986::CHAR_UNRESERVED.Rfc3986::CHAR_SUB_DELIMS.':]+$/iD', $address) === 1;
        }

        return strpos($host, ':') === false;
    }

    private static function parseAuthorityPort(string $port): ?int
    {
        if ($port === '' || !ctype_digit($port)) {
            return null;
        }

        $normalized = ltrim($port, '0');
        if ($normalized === '') {
            return 0;
        }

        if (strlen($normalized) > 5 || (int) $normalized > 0xFFFF) {
            return null;
        }

        return (int) $normalized;
    }
}
