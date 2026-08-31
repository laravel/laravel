<?php

declare(strict_types=1);

namespace GuzzleHttp\UriTemplate;

/**
 * Expands URI templates. Userland implementation of PECL uri_template.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc6570
 */
final class UriTemplate
{
    /**
     * @var array<string, array{prefix:string, joiner:string, query:bool}> Hash for quick operator lookups
     */
    private static $operatorHash = [
        '' => ['prefix' => '', 'joiner' => ',', 'query' => false],
        '+' => ['prefix' => '', 'joiner' => ',', 'query' => false],
        '#' => ['prefix' => '#', 'joiner' => ',', 'query' => false],
        '.' => ['prefix' => '.', 'joiner' => '.', 'query' => false],
        '/' => ['prefix' => '/', 'joiner' => '/', 'query' => false],
        ';' => ['prefix' => ';', 'joiner' => ';', 'query' => true],
        '?' => ['prefix' => '?', 'joiner' => '&', 'query' => true],
        '&' => ['prefix' => '&', 'joiner' => '&', 'query' => true],
    ];

    /**
     * @param array<string,mixed> $variables Variables to use in the template expansion
     *
     * @throws \RuntimeException
     */
    public static function expand(string $template, array $variables): string
    {
        if (false === \strpos($template, '{')) {
            return $template;
        }

        /** @var string|null */
        $result = \preg_replace_callback(
            '/\{([^\}]+)\}/',
            self::expandMatchCallback($variables),
            $template
        );

        if (null === $result) {
            throw new \RuntimeException(\sprintf('Unable to process template: %s', \preg_last_error_msg()));
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $variables Variables to use in the template expansion
     *
     * @return callable(string[]): string
     */
    private static function expandMatchCallback(array $variables): callable
    {
        return static function (array $matches) use ($variables): string {
            return self::expandMatch($matches, $variables);
        };
    }

    /**
     * Process an expansion
     *
     * @param array<string,mixed> $variables Variables to use in the template expansion
     * @param string[]            $matches   Matches met in the preg_replace_callback
     *
     * @return string Returns the replacement string
     */
    private static function expandMatch(array $matches, array $variables): string
    {
        $replacements = [];
        $parsed = self::parseExpression($matches[1]);
        $prefix = self::$operatorHash[$parsed['operator']]['prefix'];
        $joiner = self::$operatorHash[$parsed['operator']]['joiner'];
        $useQuery = self::$operatorHash[$parsed['operator']]['query'];
        $allowReserved = $parsed['operator'] === '+' || $parsed['operator'] === '#';
        $hasDefinedVariable = false;

        foreach ($parsed['values'] as $value) {
            if (!isset($variables[$value['value']])) {
                continue;
            }

            $variable = $variables[$value['value']];
            $actuallyUseQuery = $useQuery;
            $expanded = '';

            if (\is_array($variable)) {
                $isAssoc = self::isAssoc($variable);
                $kvp = [];
                /** @var mixed $var */
                foreach ($variable as $key => $var) {
                    if ($isAssoc) {
                        $rawKey = (string) $key;
                        $key = \rawurlencode($rawKey);
                        $isNestedArray = \is_array($var);
                    } else {
                        $isNestedArray = false;
                    }

                    if (!$isNestedArray) {
                        $var = self::encodeValue(self::stringifyValue($var), $allowReserved);
                    }

                    if ($value['modifier'] === '*') {
                        if ($isAssoc) {
                            if ($isNestedArray) {
                                // Nested arrays must allow for deeply nested structures.
                                $var = \http_build_query([$rawKey => self::stringifyNonFiniteFloats($var)], '', '&', \PHP_QUERY_RFC3986);
                                if ($var === '') {
                                    continue;
                                }
                            } else {
                                $var = \sprintf('%s=%s', (string) $key, (string) $var);
                            }
                        } elseif ($key > 0 && $actuallyUseQuery) {
                            $var = \sprintf('%s=%s', $value['value'], (string) $var);
                        }
                    }

                    /** @var string $var */
                    $kvp[$key] = $var;
                }

                if ($kvp === []) {
                    continue;
                } elseif ($value['modifier'] === '*') {
                    $expanded = \implode($joiner, $kvp);
                    if ($isAssoc) {
                        // Don't prepend the value name when using the explode
                        // modifier with an associative array.
                        $actuallyUseQuery = false;
                    }
                } else {
                    if ($isAssoc) {
                        // When an associative array is encountered and the
                        // explode modifier is not set, then the result must be
                        // a comma separated list of keys followed by their
                        // respective values.
                        foreach ($kvp as $k => &$v) {
                            $v = \sprintf('%s,%s', $k, $v);
                        }
                    }
                    $expanded = \implode(',', $kvp);
                }
            } else {
                $variable = self::stringifyValue($variable);

                if ($value['modifier'] === ':' && isset($value['position'])) {
                    $variable = self::prefixValue($variable, $value['position']);
                }
                $expanded = self::encodeValue($variable, $allowReserved);
            }

            if ($actuallyUseQuery) {
                if ($expanded === '' && $joiner !== '&') {
                    $expanded = $value['value'];
                } else {
                    $expanded = \sprintf('%s=%s', $value['value'], $expanded);
                }
            }

            $hasDefinedVariable = true;

            $replacements[] = $expanded;
        }

        $ret = \implode($joiner, $replacements);

        // Spec section 3.2.1 and appendix A: the operator's first string is
        // appended once any variable in the expression is defined, even when
        // every defined value expands to an empty string.
        if ('' !== $prefix && $hasDefinedVariable) {
            return \sprintf('%s%s', $prefix, $ret);
        }

        return $ret;
    }

    /**
     * Parse an expression into parts
     *
     * @param string $expression Expression to parse
     *
     * @return array{operator:string, values:array<array{value:string, modifier:(''|'*'|':'), position?:int}>}
     */
    private static function parseExpression(string $expression): array
    {
        $result = [];

        if (isset(self::$operatorHash[$expression[0]])) {
            $result['operator'] = $expression[0];
            /** @var string */
            $expression = \substr($expression, 1);
        } else {
            $result['operator'] = '';
        }

        $result['values'] = [];
        foreach (\explode(',', $expression) as $value) {
            $value = \trim($value, " \n\r\t\0\x0B");
            $varspec = [];
            if ($colonPos = \strpos($value, ':')) {
                $varspec['value'] = (string) \substr($value, 0, $colonPos);
                $varspec['modifier'] = ':';
                $varspec['position'] = (int) \substr($value, $colonPos + 1);
            } elseif (\substr($value, -1) === '*') {
                $varspec['modifier'] = '*';
                $varspec['value'] = (string) \substr($value, 0, -1);
            } else {
                $varspec['value'] = $value;
                $varspec['modifier'] = '';
            }
            $result['values'][] = $varspec;
        }

        return $result;
    }

    /**
     * Determines if an array is associative.
     *
     * This makes the assumption that input arrays are sequences or hashes.
     * This assumption is a tradeoff for accuracy in favor of speed, but it
     * should work in almost every case where input is supplied for a URI
     * template.
     */
    private static function isAssoc(array $array): bool
    {
        return $array && \array_keys($array)[0] !== 0;
    }

    /**
     * Cast a variable value to its expansion string.
     *
     * Non-finite floats are converted explicitly because coercing them to
     * string triggers a warning on PHP 8.5.
     *
     * @param mixed $value
     */
    private static function stringifyValue($value): string
    {
        if (\is_float($value) && !\is_finite($value)) {
            return \is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
        }

        return (string) $value;
    }

    /**
     * Stringify non-finite float members of a nested array so that
     * http_build_query does not trigger coercion warnings on PHP 8.5.
     *
     * @param array<array-key,mixed> $value
     *
     * @return array<array-key,mixed>
     */
    private static function stringifyNonFiniteFloats(array $value): array
    {
        /** @var mixed $member */
        foreach ($value as $key => $member) {
            if (\is_float($member) && !\is_finite($member)) {
                $value[$key] = self::stringifyValue($member);
            } elseif (\is_array($member)) {
                $value[$key] = self::stringifyNonFiniteFloats($member);
            }
        }

        return $value;
    }

    /**
     * Select a prefix by Unicode code points and pct-encoded characters.
     *
     * Malformed bytes continue to count individually.
     */
    private static function prefixValue(string $value, int $length): string
    {
        if ($length < 1) {
            return \substr($value, 0, $length);
        }

        $valueLength = \strlen($value);
        if ($valueLength <= $length) {
            return $value;
        }

        $offset = 0;

        for ($taken = 0; $taken < $length && $offset < $valueLength; ++$taken) {
            $offset += self::prefixCharacterByteLength($value, $offset, $valueLength);
        }

        return \substr($value, 0, $offset);
    }

    private static function prefixCharacterByteLength(string $value, int $offset, int $valueLength): int
    {
        if ($value[$offset] === '%' && $offset + 2 < $valueLength && \strspn($value, '0123456789ABCDEFabcdef', $offset + 1, 2) === 2) {
            $lead = (int) \hexdec(\substr($value, $offset + 1, 2));
            $octets = self::utf8SequenceByteLength($lead);

            if ($octets === 1) {
                return 3;
            }

            $candidate = \chr($lead);

            for ($index = 1; $index < $octets; ++$index) {
                $tripletOffset = $offset + 3 * $index;

                if ($tripletOffset + 2 >= $valueLength || $value[$tripletOffset] !== '%' || \strspn($value, '0123456789ABCDEFabcdef', $tripletOffset + 1, 2) !== 2) {
                    return 3;
                }

                $candidate .= \chr((int) \hexdec(\substr($value, $tripletOffset + 1, 2)));
            }

            return self::isSingleUtf8CodePoint($candidate) ? 3 * $octets : 3;
        }

        $octets = self::utf8SequenceByteLength(\ord($value[$offset]));
        if ($octets === 1 || $offset + $octets > $valueLength) {
            return 1;
        }

        return self::isSingleUtf8CodePoint(\substr($value, $offset, $octets)) ? $octets : 1;
    }

    private static function utf8SequenceByteLength(int $lead): int
    {
        if ($lead >= 0xC2 && $lead <= 0xDF) {
            return 2;
        }

        if ($lead >= 0xE0 && $lead <= 0xEF) {
            return 3;
        }

        return $lead >= 0xF0 && $lead <= 0xF4 ? 4 : 1;
    }

    private static function isSingleUtf8CodePoint(string $candidate): bool
    {
        $result = \preg_match('/\A.\z/us', $candidate);

        if ($result !== false) {
            return $result === 1;
        }

        if (\preg_last_error() === \PREG_BAD_UTF8_ERROR) {
            return false;
        }

        throw new \RuntimeException(\sprintf('Unable to process template: %s', \preg_last_error_msg()));
    }

    private static function encodeValue(string $value, bool $allowReserved): string
    {
        if ($value === '') {
            return '';
        }

        $matches = [];
        if (\preg_match_all('/%[0-9A-Fa-f]{2}|./s', $value, $matches) === false) {
            throw new \RuntimeException(\sprintf('Unable to encode URI template value: %s', \preg_last_error_msg()));
        }

        $encoded = '';

        foreach ($matches[0] as $token) {
            if ($allowReserved && \preg_match('/\A%[0-9A-Fa-f]{2}\z/', $token) === 1) {
                $encoded .= $token;
                continue;
            }

            if (\preg_match('/\A[A-Za-z0-9._~-]\z/', $token) === 1) {
                $encoded .= $token;
                continue;
            }

            if ($allowReserved && \strlen($token) === 1 && \strpos(":/?#[]@!$&'()*+,;=", $token) !== false) {
                $encoded .= $token;
                continue;
            }

            $encoded .= \rawurlencode($token);
        }

        return $encoded;
    }
}
