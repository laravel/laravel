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

namespace Ramsey\Uuid\Guid;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\SerializableFieldsTrait;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\MaxTrait;
use Ramsey\Uuid\Rfc4122\NilTrait;
use Ramsey\Uuid\Rfc4122\VariantTrait;
use Ramsey\Uuid\Rfc4122\VersionTrait;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

use function bin2hex;
use function dechex;
use function hexdec;
use function pack;
use function sprintf;
use function str_pad;
use function strlen;
use function substr;
use function unpack;

use const STR_PAD_LEFT;

/**
 * GUIDs consist of a set of named fields, according to RFC 9562 (formerly RFC 4122)
 *
 * @see Guid
 *
 * @immutable
 */
final class Fields implements FieldsInterface
{
    use MaxTrait;
    use NilTrait;
    use SerializableFieldsTrait;
    use VariantTrait;
    use VersionTrait;

    /**
     * @param string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     * @throws InvalidArgumentException if the byte string does not represent a GUID
     * @throws InvalidArgumentException if the byte string does not contain a valid version
     */
    public function __construct(private string $bytes)
    {
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException(
                'The byte string must be 16 bytes long; received ' . strlen($this->bytes) . ' bytes',
            );
        }

        if (!$this->isCorrectVariant()) {
            throw new InvalidArgumentException(
                'The byte string received does not conform to the RFC 9562 (formerly RFC 4122) '
                . 'or Microsoft Corporation variants',
            );
        }

        if (!$this->isCorrectVersion()) {
            throw new InvalidArgumentException('The byte string received does not contain a valid version');
        }
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getTimeLow(): Hexadecimal
    {
        // Swap the bytes from little endian to network byte order.
        /** @var string[] $hex */
        $hex = unpack(
            'H*',
            pack(
                'v*',
                hexdec(bin2hex(substr($this->bytes, 2, 2))),
                hexdec(bin2hex(substr($this->bytes, 0, 2))),
            ),
        );

        return new Hexadecimal($hex[1] ?? '');
    }

    public function getTimeMid(): Hexadecimal
    {
        // Swap the bytes from little endian to network byte order.
        /** @var string[] $hex */
        $hex = unpack('H*', pack('v', hexdec(bin2hex(substr($this->bytes, 4, 2)))));

        return new Hexadecimal($hex[1] ?? '');
    }

    public function getTimeHiAndVersion(): Hexadecimal
    {
        // Swap the bytes from little endian to network byte order.
        /** @var string[] $hex */
        $hex = unpack('H*', pack('v', hexdec(bin2hex(substr($this->bytes, 6, 2)))));

        return new Hexadecimal($hex[1] ?? '');
    }

    public function getTimestamp(): Hexadecimal
    {
        return new Hexadecimal(sprintf(
            '%03x%04s%08s',
            hexdec($this->getTimeHiAndVersion()->toString()) & 0x0fff,
            $this->getTimeMid()->toString(),
            $this->getTimeLow()->toString()
        ));
    }

    public function getClockSeq(): Hexadecimal
    {
        if ($this->isMax()) {
            $clockSeq = 0xffff;
        } elseif ($this->isNil()) {
            $clockSeq = 0x0000;
        } else {
            $clockSeq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;
        }

        return new Hexadecimal(str_pad(dechex($clockSeq), 4, '0', STR_PAD_LEFT));
    }

    public function getClockSeqHiAndReserved(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 8, 1)));
    }

    public function getClockSeqLow(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 9, 1)));
    }

    public function getNode(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 10)));
    }

    public function getVersion(): ?int
    {
        if ($this->isNil() || $this->isMax()) {
            return null;
        }

        /** @var int[] $parts */
        $parts = unpack('n*', $this->bytes);

        return ($parts[4] >> 4) & 0x00f;
    }

    private function isCorrectVariant(): bool
    {
        if ($this->isNil() || $this->isMax()) {
            return true;
        }

        $variant = $this->getVariant();

        return $variant === Uuid::RFC_4122 || $variant === Uuid::RESERVED_MICROSOFT;
    }
}
