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
use DateTimeInterface;
use League\Uri\Contracts\UriComponentInterface;
use Stringable;
use TypeError;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;
use ValueError;

use function array_is_list;
use function array_map;
use function get_debug_type;
use function implode;
use function is_array;
use function is_float;
use function is_infinite;
use function is_nan;
use function is_object;
use function is_resource;
use function is_scalar;
use function json_encode;

use const JSON_PRESERVE_ZERO_FRACTION;

enum StringCoercionMode
{
    /**
     * PHP conversion mode.
     *
     * Guarantees that only scalar values, BackedEnum, and null are accepted.
     * Any object, Non-backed enums, resource, or recursive structure results in an error.
     *
     * - null: is not converted and stays the `null` value
     * - string: used as-is
     * - bool: converted to string “0” (false) or “1” (true)
     * - int: converted to numeric string (123 -> “123”)
     * - float: converted to decimal string (3.14 -> “3.14”)
     * - Backed Enum: converted to their backing value and then stringify see int and string
     */
    case Native;

    /**
     * Ecmascript conversion mode.
     *
     * Guarantees that only scalar values, BackedEnum, and null are accepted.
     * Any resource, or recursive structure results in an error.
     *
     * - null: converted to string “null”
     * - string: used as-is
     * - bool: converted to string “false” (false) or “true” (true)
     * - int: converted to numeric string (123 -> “123”)
     * - float: converted to decimal string (3.14 -> “3.14”), "NaN", "-Infinity" or "Infinity"
     * - Backed Enum: converted to their backing value and then stringify see int and string
     * - Array as list are flatten into a string list using the "," character as separator
     * - Associative array, Non-backed enums, any object without stringification semantics is coerced to "[object Object]".
     * - DateTimeInterface implementing object are coerce to their string representation using DateTimeInterface::RFC2822 format
     */
    case Ecmascript;

    private const RECURSION_MARKER = "\0__RECURSION_INTERNAL_MARKER_WHATWG__\0";

    public function isCoercible(mixed $value): bool
    {
        return self::Ecmascript === $this
            ? !is_resource($value)
            : match (true) {
                $value instanceof Rfc3986Uri,
                $value instanceof WhatWgUrl,
                $value instanceof BackedEnum,
                $value instanceof Stringable,
                is_scalar($value),
                null === $value => true,
                default => false,
            };
    }

    /**
     * @throws TypeError if the type is not supported by the specific case
     * @throws ValueError if circular reference is detected
     */
    public function coerce(mixed $value): ?string
    {
        return match ($this) {
            self::Ecmascript => match (true) {
                $value instanceof Rfc3986Uri => $value->toString(),
                $value instanceof WhatWgUrl => $value->toAsciiString(),
                $value instanceof DateTimeInterface => $value->format(DateTimeInterface::RFC2822),
                $value instanceof BackedEnum => (string) $value->value,
                $value instanceof Stringable => $value->__toString(),
                is_object($value) => '[object Object]',
                is_array($value) => match (true) {
                    self::hasCircularReference($value) => throw new ValueError('Recursive array structure detected; unable to coerce value.'),
                    array_is_list($value) => implode(',', array_map($this->coerce(...), $value)),
                    default => '[object Object]',
                },
                true === $value => 'true',
                false === $value => 'false',
                null === $value => 'null',
                is_float($value) => match (true) {
                    is_nan($value) => 'NaN',
                    is_infinite($value) => 0 < $value ? 'Infinity' : '-Infinity',
                    default => (string) json_encode($value, JSON_PRESERVE_ZERO_FRACTION),
                },
                is_scalar($value) => (string) $value,
                default => throw new TypeError('Unable to coerce value of type "'.get_debug_type($value).'" with "'.$this->name.'" coercion.'),
            },
            self::Native => match (true) {
                $value instanceof UriComponentInterface => $value->value(),
                $value instanceof WhatWgUrl => $value->toAsciiString(),
                $value instanceof Rfc3986Uri => $value->toString(),
                $value instanceof BackedEnum => (string) $value->value,
                $value instanceof Stringable => $value->__toString(),
                false === $value => '0',
                true === $value => '1',
                null === $value => null,
                is_scalar($value) => (string) $value,
                default => throw new TypeError('Unable to coerce value of type "'.get_debug_type($value).'" with "'.$this->name.'" coercion.'),
            },
        };
    }

    /**
     * Array recursion detection.
     * @see https://stackoverflow.com/questions/9042142/detecting-infinite-array-recursion-in-php
     */
    private static function hasCircularReference(array &$arr): bool
    {
        if (isset($arr[self::RECURSION_MARKER])) {
            return true;
        }

        try {
            $arr[self::RECURSION_MARKER] = true;
            foreach ($arr as $key => &$value) {
                if (self::RECURSION_MARKER !== $key && is_array($value) && self::hasCircularReference($value)) {
                    return true;
                }
            }

            return false;
        } finally {
            unset($arr[self::RECURSION_MARKER]);
        }
    }
}
