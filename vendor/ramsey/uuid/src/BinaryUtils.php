<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid;

/**
 * Provides binary math utilities
 */
class BinaryUtils
{
    /**
     * Applies the variant field to the 16-bit clock sequence
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     *
     * @param int $clockSeq The 16-bit clock sequence value before the variant is applied
     *
     * @return int The 16-bit clock sequence multiplexed with the UUID variant
     *
     * @pure
     */
    public static function applyVariant(int $clockSeq): int
    {
        return ($clockSeq & 0x3fff) | 0x8000;
    }

    /**
     * Applies the version field to the 16-bit `time_hi_and_version` field
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     *
     * @param int $timeHi The value of the 16-bit `time_hi_and_version` field before the version is applied
     * @param int $version The version to apply to the `time_hi` field
     *
     * @return int The 16-bit time_hi field of the timestamp multiplexed with the UUID version number
     *
     * @pure
     */
    public static function applyVersion(int $timeHi, int $version): int
    {
        return ($timeHi & 0x0fff) | ($version << 12);
    }
}
