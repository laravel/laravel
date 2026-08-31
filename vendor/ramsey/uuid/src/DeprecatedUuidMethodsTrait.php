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

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Throwable;

use function str_pad;
use function substr;

use const STR_PAD_LEFT;

/**
 * This trait encapsulates deprecated methods for ramsey/uuid; this trait and its methods will be removed in ramsey/uuid 5.0.0.
 *
 * @deprecated This trait and its methods will be removed in ramsey/uuid 5.0.0.
 *
 * @immutable
 */
trait DeprecatedUuidMethodsTrait
{
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqHiAndReserved()} and use the arbitrary-precision math
     *     library of your choice to convert it to a string integer.
     */
    public function getClockSeqHiAndReserved(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeqHiAndReserved()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqHiAndReserved()}.
     */
    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->fields->getClockSeqHiAndReserved()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqLow()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function getClockSeqLow(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeqLow()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqLow()}.
     */
    public function getClockSeqLowHex(): string
    {
        return $this->fields->getClockSeqLow()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeq()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function getClockSequence(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeq()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeq()}.
     */
    public function getClockSequenceHex(): string
    {
        return $this->fields->getClockSeq()->toString();
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no alternative recommendation, so plan accordingly.
     */
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }

    /**
     * @deprecated In ramsey/uuid version 5.0.0, this will be removed. It is available at {@see UuidV1::getDateTime()}.
     *
     * @return DateTimeImmutable An immutable instance of DateTimeInterface
     *
     * @throws UnsupportedOperationException if UUID is not time-based
     * @throws DateTimeException if DateTime throws an exception/error
     */
    public function getDateTime(): DateTimeInterface
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        $time = $this->timeConverter->convertTime($this->fields->getTimestamp());

        try {
            return new DateTimeImmutable(
                '@'
                . $time->getSeconds()->toString()
                . '.'
                . str_pad($time->getMicroseconds()->toString(), 6, '0', STR_PAD_LEFT)
            );
        } catch (Throwable $e) {
            throw new DateTimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *
     * @return string[]
     */
    public function getFieldsHex(): array
    {
        return [
            'time_low' => $this->fields->getTimeLow()->toString(),
            'time_mid' => $this->fields->getTimeMid()->toString(),
            'time_hi_and_version' => $this->fields->getTimeHiAndVersion()->toString(),
            'clock_seq_hi_and_reserved' => $this->fields->getClockSeqHiAndReserved()->toString(),
            'clock_seq_low' => $this->fields->getClockSeqLow()->toString(),
            'node' => $this->fields->getNode()->toString(),
        ];
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getLeastSignificantBits(): string
    {
        $leastSignificantHex = substr($this->getHex()->toString(), 16);

        return $this->numberConverter->fromHex($leastSignificantHex);
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getLeastSignificantBitsHex(): string
    {
        return substr($this->getHex()->toString(), 16);
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getMostSignificantBits(): string
    {
        $mostSignificantHex = substr($this->getHex()->toString(), 0, 16);

        return $this->numberConverter->fromHex($mostSignificantHex);
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getMostSignificantBitsHex(): string
    {
        return substr($this->getHex()->toString(), 0, 16);
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getNode()} and use the arbitrary-precision math library of your
     *     choice to convert it to a string integer.
     */
    public function getNode(): string
    {
        return $this->numberConverter->fromHex($this->fields->getNode()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getNode()}.
     */
    public function getNodeHex(): string
    {
        return $this->fields->getNode()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeHiAndVersion()} and use the arbitrary-precision math
     *     library of your choice to convert it to a string integer.
     */
    public function getTimeHiAndVersion(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeHiAndVersion()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeHiAndVersion()}.
     */
    public function getTimeHiAndVersionHex(): string
    {
        return $this->fields->getTimeHiAndVersion()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeLow()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function getTimeLow(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeLow()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeLow()}.
     */
    public function getTimeLowHex(): string
    {
        return $this->fields->getTimeLow()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeMid()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function getTimeMid(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeMid()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeMid()}.
     */
    public function getTimeMidHex(): string
    {
        return $this->fields->getTimeMid()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimestamp()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function getTimestamp(): string
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return $this->numberConverter->fromHex($this->fields->getTimestamp()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimestamp()}.
     */
    public function getTimestampHex(): string
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return $this->fields->getTimestamp()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVariant()}.
     */
    public function getVariant(): ?int
    {
        return $this->fields->getVariant();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVersion()}.
     */
    public function getVersion(): ?int
    {
        return $this->fields->getVersion();
    }
}
