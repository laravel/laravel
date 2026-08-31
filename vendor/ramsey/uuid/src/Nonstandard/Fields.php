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

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\SerializableFieldsTrait;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\VariantTrait;
use Ramsey\Uuid\Type\Hexadecimal;

use function bin2hex;
use function dechex;
use function hexdec;
use function sprintf;
use function str_pad;
use function strlen;
use function substr;

use const STR_PAD_LEFT;

/**
 * Nonstandard UUID fields do not conform to the RFC 9562 (formerly RFC 4122) standard
 *
 * Since some systems may create nonstandard UUIDs, this implements the {@see FieldsInterface}, so that functionality of
 * a nonstandard UUID is not degraded, in the event these UUIDs are expected to contain RFC 9562 (formerly RFC 4122) fields.
 *
 * Internally, this class represents the fields together as a 16-byte binary string.
 *
 * @immutable
 */
final class Fields implements FieldsInterface
{
    use SerializableFieldsTrait;
    use VariantTrait;

    /**
     * @param string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     */
    public function __construct(private string $bytes)
    {
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException(
                'The byte string must be 16 bytes long; received ' . strlen($this->bytes) . ' bytes',
            );
        }
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getClockSeq(): Hexadecimal
    {
        $clockSeq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;

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

    public function getTimeHiAndVersion(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 6, 2)));
    }

    public function getTimeLow(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 0, 4)));
    }

    public function getTimeMid(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 4, 2)));
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

    public function getVersion(): ?int
    {
        return null;
    }

    public function isNil(): bool
    {
        return false;
    }

    public function isMax(): bool
    {
        return false;
    }
}
