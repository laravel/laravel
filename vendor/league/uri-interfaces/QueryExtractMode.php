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

enum QueryExtractMode
{
    /**
     * Parses the query string using parse_str algorithm.
     */
    case Native;

    /**
     * Parses the query string like parse_str without mangling result keys.
     *
     * The result is similar to PHP parse_str when used with its second argument,
     * with the difference that variable names are not mangled.
     *
     * Behavior details:
     * - Empty names are ignored
     * - If a name is duplicated, the last value overwrites the previous one
     * - If no "[" is detected, the value is added using the name as the array key
     * - If "[" is detected but no matching "]" exists, the value is added using the name as the array key
     * - If bracket usage is malformed, the remaining part is dropped
     * - "." and " " are NOT converted to "_"
     * - If no "]" exists, the first "[" is not converted to "_"
     * - No whitespace trimming is performed on keys
     *
     * @see https://www.php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     */
    case Unmangled;

    /**
     * Same as QueryParsingMode::Unmangled and additionally
     * preserves null values instead of converting them
     * to empty strings.
     */
    case LossLess;
}
