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

namespace Ramsey\Uuid\Codec;

use Ramsey\Uuid\UuidInterface;

/**
 * A codec encodes and decodes a UUID according to defined rules
 *
 * @immutable
 */
interface CodecInterface
{
    /**
     * Returns a hexadecimal string representation of a UuidInterface
     *
     * @param UuidInterface $uuid The UUID for which to create a hexadecimal string representation
     *
     * @return non-empty-string Hexadecimal string representation of a UUID
     *
     * @pure
     */
    public function encode(UuidInterface $uuid): string;

    /**
     * Returns a binary string representation of a UuidInterface
     *
     * @param UuidInterface $uuid The UUID for which to create a binary string representation
     *
     * @return non-empty-string Binary string representation of a UUID
     *
     * @pure
     */
    public function encodeBinary(UuidInterface $uuid): string;

    /**
     * Returns a UuidInterface derived from a hexadecimal string representation
     *
     * @param string $encodedUuid The hexadecimal string representation to convert into a UuidInterface instance
     *
     * @return UuidInterface An instance of a UUID decoded from a hexadecimal string representation
     *
     * @pure
     */
    public function decode(string $encodedUuid): UuidInterface;

    /**
     * Returns a UuidInterface derived from a binary string representation
     *
     * @param string $bytes The binary string representation to convert into a UuidInterface instance
     *
     * @return UuidInterface An instance of a UUID decoded from a binary string representation
     *
     * @pure
     */
    public function decodeBytes(string $bytes): UuidInterface;
}
