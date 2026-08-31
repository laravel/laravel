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

use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\UuidInterface;

use function bin2hex;
use function sprintf;
use function substr;

/**
 * GuidStringCodec encodes and decodes globally unique identifiers (GUID)
 *
 * @see Guid
 *
 * @immutable
 */
class GuidStringCodec extends StringCodec
{
    public function encode(UuidInterface $uuid): string
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $hex = bin2hex($uuid->getFields()->getBytes());

        /** @var non-empty-string */
        return sprintf(
            '%02s%02s%02s%02s-%02s%02s-%02s%02s-%04s-%012s',
            substr($hex, 6, 2),
            substr($hex, 4, 2),
            substr($hex, 2, 2),
            substr($hex, 0, 2),
            substr($hex, 10, 2),
            substr($hex, 8, 2),
            substr($hex, 14, 2),
            substr($hex, 12, 2),
            substr($hex, 16, 4),
            substr($hex, 20),
        );
    }

    public function decode(string $encodedUuid): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $bytes = $this->getBytes($encodedUuid);

        /** @phpstan-ignore possiblyImpure.methodCall, possiblyImpure.methodCall */
        return $this->getBuilder()->build($this, $this->swapBytes($bytes));
    }

    public function decodeBytes(string $bytes): UuidInterface
    {
        // Call parent::decode() to preserve the correct byte order.
        return parent::decode(bin2hex($bytes));
    }

    /**
     * Swaps bytes according to the GUID rules
     */
    private function swapBytes(string $bytes): string
    {
        return $bytes[3] . $bytes[2] . $bytes[1] . $bytes[0]
            . $bytes[5] . $bytes[4] . $bytes[7] . $bytes[6]
            . substr($bytes, 8);
    }
}
