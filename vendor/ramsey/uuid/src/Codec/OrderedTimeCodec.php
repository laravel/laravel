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

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function strlen;
use function substr;

/**
 * OrderedTimeCodec encodes and decodes a UUID, optimizing the byte order for more efficient storage
 *
 * For binary representations of version 1 UUID, this codec may be used to reorganize the time fields, making the UUID
 * closer to sequential when storing the bytes. According to Percona, this optimization can improve database INSERT and
 * SELECT statements using the UUID column as a key.
 *
 * The string representation of the UUID will remain unchanged. Only the binary representation is reordered.
 *
 * PLEASE NOTE: Binary representations of UUIDs encoded with this codec must be decoded with this codec. Decoding using
 * another codec can result in malformed UUIDs.
 *
 * @deprecated Please migrate to {@link https://uuid.ramsey.dev/en/stable/rfc4122/version6.html Version 6, reordered time-based UUIDs}.
 *
 * @link https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/ Storing UUID Values in MySQL
 *
 * @immutable
 */
class OrderedTimeCodec extends StringCodec
{
    /**
     * Returns a binary string representation of a UUID, with the timestamp fields rearranged for optimized storage
     *
     * @return non-empty-string
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        if (
            /** @phpstan-ignore possiblyImpure.methodCall */
            !($uuid->getFields() instanceof Rfc4122FieldsInterface)
            /** @phpstan-ignore possiblyImpure.methodCall */
            || $uuid->getFields()->getVersion() !== Uuid::UUID_TYPE_TIME
        ) {
            throw new InvalidArgumentException('Expected version 1 (time-based) UUID');
        }

        /** @phpstan-ignore possiblyImpure.methodCall */
        $bytes = $uuid->getFields()->getBytes();

        return $bytes[6] . $bytes[7] . $bytes[4] . $bytes[5]
            . $bytes[0] . $bytes[1] . $bytes[2] . $bytes[3]
            . substr($bytes, 8);
    }

    /**
     * Returns a UuidInterface derived from an ordered-time binary string representation
     *
     * @throws InvalidArgumentException if $bytes is an invalid length
     *
     * @inheritDoc
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }

        // Rearrange the bytes to their original order.
        $rearrangedBytes = $bytes[4] . $bytes[5] . $bytes[6] . $bytes[7]
            . $bytes[2] . $bytes[3] . $bytes[0] . $bytes[1]
            . substr($bytes, 8);

        $uuid = parent::decodeBytes($rearrangedBytes);

        /** @phpstan-ignore possiblyImpure.methodCall */
        $fields = $uuid->getFields();

        if (!$fields instanceof Rfc4122FieldsInterface || $fields->getVersion() !== Uuid::UUID_TYPE_TIME) {
            throw new UnsupportedOperationException(
                'Attempting to decode a non-time-based UUID using OrderedTimeCodec',
            );
        }

        return $uuid;
    }
}
