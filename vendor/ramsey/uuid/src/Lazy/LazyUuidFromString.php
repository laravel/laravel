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

namespace Ramsey\Uuid\Lazy;

use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use ValueError;

use function assert;
use function bin2hex;
use function hex2bin;
use function sprintf;
use function str_replace;
use function substr;

/**
 * Lazy version of a UUID: its format has not been determined yet, so it is mostly only usable for string/bytes
 * conversion. This object optimizes instantiation, serialization and string conversion time, at the cost of increased
 * overhead for more advanced UUID operations.
 *
 * > [!NOTE]
 * > The {@see FieldsInterface} does not declare methods that deprecated API relies upon: the API has been ported from
 * > the {@see \Ramsey\Uuid\Uuid} definition, and is deprecated anyway.
 *
 * > [!NOTE]
 * > The deprecated API from {@see \Ramsey\Uuid\Uuid} is in use here (on purpose): it will be removed once the
 * > deprecated API is gone from this class too.
 *
 * @internal this type is used internally for performance reasons and is not supposed to be directly referenced in consumer libraries.
 */
final class LazyUuidFromString implements UuidInterface
{
    public const VALID_REGEX = '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/ms';

    private ?UuidInterface $unwrapped = null;

    /**
     * @param non-empty-string $uuid
     */
    public function __construct(private string $uuid)
    {
    }

    public static function fromBytes(string $bytes): self
    {
        $base16Uuid = bin2hex($bytes);

        return new self(
            substr($base16Uuid, 0, 8)
            . '-'
            . substr($base16Uuid, 8, 4)
            . '-'
            . substr($base16Uuid, 12, 4)
            . '-'
            . substr($base16Uuid, 16, 4)
            . '-'
            . substr($base16Uuid, 20, 12)
        );
    }

    public function serialize(): string
    {
        return $this->uuid;
    }

    /**
     * @return array{string: non-empty-string}
     */
    public function __serialize(): array
    {
        return ['string' => $this->uuid];
    }

    /**
     * {@inheritDoc}
     *
     * @param non-empty-string $data
     */
    public function unserialize(string $data): void
    {
        $this->uuid = $data;
    }

    /**
     * @param array{string?: non-empty-string} $data
     */
    public function __unserialize(array $data): void
    {
        // @codeCoverageIgnoreStart
        if (!isset($data['string'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        // @codeCoverageIgnoreEnd

        $this->unserialize($data['string']);
    }

    public function getNumberConverter(): NumberConverterInterface
    {
        return ($this->unwrapped ?? $this->unwrap())->getNumberConverter();
    }

    /**
     * @inheritDoc
     */
    public function getFieldsHex(): array
    {
        return ($this->unwrapped ?? $this->unwrap())->getFieldsHex();
    }

    public function getClockSeqHiAndReservedHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getClockSeqHiAndReservedHex();
    }

    public function getClockSeqLowHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getClockSeqLowHex();
    }

    public function getClockSequenceHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getClockSequenceHex();
    }

    public function getDateTime(): DateTimeInterface
    {
        return ($this->unwrapped ?? $this->unwrap())->getDateTime();
    }

    public function getLeastSignificantBitsHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getLeastSignificantBitsHex();
    }

    public function getMostSignificantBitsHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getMostSignificantBitsHex();
    }

    public function getNodeHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getNodeHex();
    }

    public function getTimeHiAndVersionHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getTimeHiAndVersionHex();
    }

    public function getTimeLowHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getTimeLowHex();
    }

    public function getTimeMidHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getTimeMidHex();
    }

    public function getTimestampHex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getTimestampHex();
    }

    public function getUrn(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getUrn();
    }

    public function getVariant(): ?int
    {
        return ($this->unwrapped ?? $this->unwrap())->getVariant();
    }

    public function getVersion(): ?int
    {
        return ($this->unwrapped ?? $this->unwrap())->getVersion();
    }

    public function compareTo(UuidInterface $other): int
    {
        return ($this->unwrapped ?? $this->unwrap())->compareTo($other);
    }

    public function equals(?object $other): bool
    {
        if (!$other instanceof UuidInterface) {
            return false;
        }

        return $this->uuid === $other->toString();
    }

    public function getBytes(): string
    {
        /**
         * @var non-empty-string
         * @phpstan-ignore possiblyImpure.functionCall, possiblyImpure.functionCall
         */
        return (string) hex2bin(str_replace('-', '', $this->uuid));
    }

    public function getFields(): FieldsInterface
    {
        return ($this->unwrapped ?? $this->unwrap())->getFields();
    }

    public function getHex(): Hexadecimal
    {
        return ($this->unwrapped ?? $this->unwrap())->getHex();
    }

    public function getInteger(): IntegerObject
    {
        return ($this->unwrapped ?? $this->unwrap())->getInteger();
    }

    public function toString(): string
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function jsonSerialize(): string
    {
        return $this->uuid;
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getClockSeqHiAndReserved()}
     *     and use the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getClockSeqHiAndReserved(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getClockSeqHiAndReserved()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getClockSeqLow()} and use
     *     the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getClockSeqLow(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getClockSeqLow()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getClockSeq()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getClockSequence(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getClockSeq()->toString());
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getLeastSignificantBits(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        return $instance->getNumberConverter()->fromHex(substr($instance->getHex()->toString(), 16));
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getMostSignificantBits(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        return $instance->getNumberConverter()->fromHex(substr($instance->getHex()->toString(), 0, 16));
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getNode()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getNode(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getNode()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimeHiAndVersion()} and
     *     use the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getTimeHiAndVersion(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getTimeHiAndVersion()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimeLow()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getTimeLow(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getTimeLow()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimeMid()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getTimeMid(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        return $instance->getNumberConverter()->fromHex($fields->getTimeMid()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimestamp()} and use
     *     the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function getTimestamp(): string
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        $fields = $instance->getFields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

        if ($fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return $instance->getNumberConverter()->fromHex($fields->getTimestamp()->toString());
    }

    public function toUuidV1(): UuidV1
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        if ($instance instanceof UuidV1) {
            return $instance;
        }

        assert($instance instanceof UuidV6);

        return $instance->toUuidV1();
    }

    public function toUuidV6(): UuidV6
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        assert($instance instanceof UuidV6);

        return $instance;
    }

    private function unwrap(): UuidInterface
    {
        return $this->unwrapped = (new UuidFactory())->fromString($this->uuid);
    }
}
