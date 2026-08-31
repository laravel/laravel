<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php80;

/**
 * @author Ion Bazan <ion.bazan@gmail.com>
 * @author Nico Oelgart <nicoswd@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class Php80
{
    public static function fdiv(float $dividend, float $divisor): float
    {
        return @($dividend / $divisor);
    }

    public static function get_debug_type($value): string
    {
        switch (true) {
            case null === $value: return 'null';
            case \is_bool($value): return 'bool';
            case \is_string($value): return 'string';
            case \is_array($value): return 'array';
            case \is_int($value): return 'int';
            case \is_float($value): return 'float';
            case \is_object($value): break;
            case $value instanceof \__PHP_Incomplete_Class: return '__PHP_Incomplete_Class';
            default:
                if (null === $type = @get_resource_type($value)) {
                    return 'unknown';
                }

                if ('Unknown' === $type) {
                    $type = 'closed';
                }

                return "resource ($type)";
        }

        $class = \get_class($value);

        if (false === strpos($class, '@')) {
            return $class;
        }

        return (get_parent_class($class) ?: key(class_implements($class)) ?: 'class').'@anonymous';
    }

    public static function get_resource_id($res): int
    {
        if (!\is_resource($res) && null === @get_resource_type($res)) {
            throw new \TypeError(\sprintf('Argument 1 passed to get_resource_id() must be of the type resource, %s given', get_debug_type($res)));
        }

        return (int) $res;
    }

    public static function preg_last_error_msg(): string
    {
        switch (preg_last_error()) {
            case \PREG_INTERNAL_ERROR:
                return 'Internal error';
            case \PREG_BAD_UTF8_ERROR:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            case \PREG_BAD_UTF8_OFFSET_ERROR:
                return 'The offset did not correspond to the beginning of a valid UTF-8 code point';
            case \PREG_BACKTRACK_LIMIT_ERROR:
                return 'Backtrack limit exhausted';
            case \PREG_RECURSION_LIMIT_ERROR:
                return 'Recursion limit exhausted';
            case \PREG_JIT_STACKLIMIT_ERROR:
                return 'JIT stack limit exhausted';
            case \PREG_NO_ERROR:
                return 'No error';
            default:
                return 'Unknown error';
        }
    }

    public static function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }

    public static function str_starts_with(string $haystack, string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }

    public static function str_ends_with(string $haystack, string $needle): bool
    {
        if ('' === $needle || $needle === $haystack) {
            return true;
        }

        if ('' === $haystack) {
            return false;
        }

        $needleLength = \strlen($needle);

        return $needleLength <= \strlen($haystack) && 0 === substr_compare($haystack, $needle, -$needleLength);
    }
}
