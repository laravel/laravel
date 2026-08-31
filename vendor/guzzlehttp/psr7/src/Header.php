<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

final class Header
{
    /**
     * Parse an array of header values containing ";" separated data into an
     * array of associative arrays representing the header key value pair data
     * of the header. When a parameter does not contain a value, but just
     * contains a key, this function will inject a key with a '' string value.
     *
     * @param string|array $header Header to parse into components.
     */
    public static function parse($header): array
    {
        static $trimmed = "\"'  \n\t\r";
        $params = $matches = [];

        foreach ((array) $header as $value) {
            foreach (self::splitList($value) as $val) {
                $part = [];
                foreach (self::splitParameters($val) as $kvp) {
                    if (preg_match_all('/<[^>]+>|[^=]+/', $kvp, $matches)) {
                        $m = $matches[0];
                        if (isset($m[1])) {
                            $part[trim($m[0], $trimmed)] = trim($m[1], $trimmed);
                        } else {
                            $part[] = trim($m[0], $trimmed);
                        }
                    }
                }
                if ($part) {
                    $params[] = $part;
                }
            }
        }

        return $params;
    }

    /**
     * Split a header value into semicolon-separated parameters.
     *
     * @return string[]
     */
    private static function splitParameters(string $value): array
    {
        $values = [];
        $start = 0;
        $isQuoted = false;
        $isEscaped = false;

        for ($i = 0, $max = \strlen($value); $i < $max; ++$i) {
            $char = $value[$i];

            if ($isEscaped) {
                $isEscaped = false;

                continue;
            }

            if ($isQuoted && $char === '\\') {
                $isEscaped = true;

                continue;
            }

            if ($char === '"') {
                $isQuoted = !$isQuoted;

                continue;
            }

            if (!$isQuoted && $char === ';') {
                $values[] = \substr($value, $start, $i - $start);
                $start = $i + 1;
            }
        }

        $values[] = \substr($value, $start);

        return $values;
    }

    /**
     * Converts an array of header values that may contain comma separated
     * headers into an array of headers with no comma separated values.
     *
     * @param string|array $header Header to normalize.
     *
     * @deprecated Use self::splitList() instead.
     */
    public static function normalize($header): array
    {
        \trigger_deprecation('guzzlehttp/psr7', '2.3', 'Header::normalize() is deprecated and will be removed in guzzlehttp/psr7 3.0. Use Header::splitList() instead.');

        $result = [];
        foreach ((array) $header as $value) {
            foreach (self::splitList($value) as $parsed) {
                $result[] = $parsed;
            }
        }

        return $result;
    }

    /**
     * Splits a HTTP header defined to contain a comma-separated list into
     * each individual value. Empty values will be removed.
     *
     * Example headers include 'accept', 'cache-control' and 'if-none-match'.
     *
     * This method must not be used to parse headers that are not defined as
     * a list, such as 'user-agent' or 'set-cookie'.
     *
     * @param string|string[] $values Header value as returned by MessageInterface::getHeader()
     *
     * @return string[]
     */
    public static function splitList($values): array
    {
        if (!\is_array($values)) {
            $values = [$values];
        }

        $result = [];
        foreach ($values as $value) {
            if (!\is_string($value)) {
                throw new \TypeError('$header must either be a string or an array containing strings.');
            }

            $v = '';
            $isQuoted = false;
            $isEscaped = false;
            for ($i = 0, $max = \strlen($value); $i < $max; ++$i) {
                if ($isEscaped) {
                    $v .= $value[$i];
                    $isEscaped = false;

                    continue;
                }

                if (!$isQuoted && $value[$i] === ',') {
                    $v = \trim($v, " \n\r\t\0\x0B");
                    if ($v !== '') {
                        $result[] = $v;
                    }

                    $v = '';
                    continue;
                }

                if ($isQuoted && $value[$i] === '\\') {
                    $isEscaped = true;
                    $v .= $value[$i];

                    continue;
                }
                if ($value[$i] === '"') {
                    $isQuoted = !$isQuoted;
                    $v .= $value[$i];

                    continue;
                }

                $v .= $value[$i];
            }

            $v = \trim($v, " \n\r\t\0\x0B");
            if ($v !== '') {
                $result[] = $v;
            }
        }

        return $result;
    }
}
