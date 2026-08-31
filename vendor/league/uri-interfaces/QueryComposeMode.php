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

enum QueryComposeMode
{
    /**
     * Pre-PHP 8.4 Mode.
     *
     * Strictly uses get_object_vars on objects (Enum included)
     * If the value can not be serialized the entry is skipped.
     *
     * ie http_build_query behavior before PHP8.4
     */
    case Compatible;

    /**
     * PHP 8.4+ enum-compatible lenient mode.
     *
     * Provides stable support for BackedEnum values.
     * UnitEnum values are skipped.
     * Uses get_object_vars() for non-enum objects.
     * Unserializable values are skipped.
     *
     * Behaves like {@see QueryComposeMode::EnumCompatible}
     * but does not throw for UnitEnum values.
     *
     * Mirrors http_build_query behavior in PHP 8.4+,
     * except that error cases are silently ignored
     * instead of throwing.
     *
     * This mode is tolerant by design and skips entries that would otherwise
     * result in an exception in {@see QueryComposeMode::EnumCompatible}.
     */
    case EnumLenient;

    /**
     * PHP 8.4+ mode.
     *
     * Provides stable support for BackedEnum values.
     * Throws for UnitEnum.
     * Uses get_object_vars() for non-enum objects.
     * Unserializable values are skipped.
     *
     * http_build_query behavior in PHP 8.4+.
     */
    case EnumCompatible;

    /**
     * Use PHP version http_build_query algorithm.
     *
     * In pre-PHP8.4 you get the same results as `Compatible`
     * In PHP PHP8.4+ you get the same results as `EnumCompatible`
     */
    case Native;

    /**
     * Validation-first mode.
     *
     * Guarantees that only scalar values, BackedEnum, and null are accepted.
     * Any object, UnitEnum, resource, or recursive structure
     * results in an exception.
     *
     * - null: the key name is used but the separator and its content are omitted
     * - string: used as-is
     * - bool: converted to string “0” (false) or “1” (true)
     * - int: converted to numeric string (123 -> “123”)
     * - float: converted to decimal string (3.14 -> “3.14”)
     * - Backed Enum: converted to their backing value and then stringify see int and string
     * - array: empty array: An empty array has zero items, therefore empty arrays are omitted from the query parameter list.
     *     - lists: Becomes a repeated name suffixed with empty brackets (ie "a" with ["foo", false, 1.23] will result in a[]=foo&a[]=0&a[]=1.23)
     *     - maps: Becomes a repeated name suffixed with brackets containing the key (ie "a" with ["b" => "foo", "c" => false, "d" => 1.23] will result in a[b]=foo&a[c]=0&a[d]=1.23)
     *
     * This contract is stable and independent of PHP's http_build_query implementation.
     */
    case Safe;
}
