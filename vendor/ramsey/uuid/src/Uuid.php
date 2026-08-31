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

use BadMethodCallException;
use DateTimeInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use ValueError;

use function assert;
use function bin2hex;
use function method_exists;
use function preg_match;
use function sprintf;
use function str_replace;
use function strcmp;
use function strlen;
use function strtolower;
use function substr;

/**
 * Uuid provides constants and static methods for working with and generating UUIDs
 *
 * @immutable
 */
class Uuid implements UuidInterface
{
    use DeprecatedUuidMethodsTrait;

    /**
     * When this namespace is specified, the name string is a fully qualified domain name
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.6 RFC 9562, 6.6. Namespace ID Usage and Allocation
     */
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is a URL
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.6 RFC 9562, 6.6. Namespace ID Usage and Allocation
     */
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an ISO OID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.6 RFC 9562, 6.6. Namespace ID Usage and Allocation
     */
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an X.500 DN (in DER or a text output format)
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.6 RFC 9562, 6.6. Namespace ID Usage and Allocation
     */
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * The Nil UUID is a special form of UUID that is specified to have all 128 bits set to zero
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-5.9 RFC 9562, 5.9. Nil UUID
     */
    public const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * The Max UUID is a special form of UUID that is specified to have all 128 bits set to one
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-5.10 RFC 9562, 5.10. Max UUID
     */
    public const MAX = 'ffffffff-ffff-ffff-ffff-ffffffffffff';

    /**
     * Variant: reserved, NCS backward compatibility
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     */
    public const RESERVED_NCS = 0;

    /**
     * Variant: the UUID layout specified in RFC 9562 (formerly RFC 4122)
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     * @see Uuid::RFC_9562
     */
    public const RFC_4122 = 2;

    /**
     * Variant: the UUID layout specified in RFC 9562 (formerly RFC 4122)
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     */
    public const RFC_9562 = 2;

    /**
     * Variant: reserved, Microsoft Corporation backward compatibility
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     */
    public const RESERVED_MICROSOFT = 6;

    /**
     * Variant: reserved for future definition
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     */
    public const RESERVED_FUTURE = 7;

    /**
     * @deprecated Use {@see ValidatorInterface::getPattern()} instead.
     */
    public const VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Version 1 (Gregorian time) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_TIME = 1;

    /**
     * Version 2 (DCE Security) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_DCE_SECURITY = 2;

    /**
     * @deprecated Use {@see Uuid::UUID_TYPE_DCE_SECURITY} instead.
     */
    public const UUID_TYPE_IDENTIFIER = 2;

    /**
     * Version 3 (name-based and hashed with MD5) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_HASH_MD5 = 3;

    /**
     * Version 4 (random) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_RANDOM = 4;

    /**
     * Version 5 (name-based and hashed with SHA1) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_HASH_SHA1 = 5;

    /**
     * @deprecated Use {@see Uuid::UUID_TYPE_REORDERED_TIME} instead.
     */
    public const UUID_TYPE_PEABODY = 6;

    /**
     * Version 6 (reordered Gregorian time) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_REORDERED_TIME = 6;

    /**
     * Version 7 (Unix Epoch time) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_UNIX_TIME = 7;

    /**
     * Version 8 (custom format) UUID
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     */
    public const UUID_TYPE_CUSTOM = 8;

    /**
     * DCE Security principal domain
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_PERSON = 0;

    /**
     * DCE Security group domain
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_GROUP = 1;

    /**
     * DCE Security organization domain
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_ORG = 2;

    /**
     * DCE Security domain string names
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_NAMES = [
        self::DCE_DOMAIN_PERSON => 'person',
        self::DCE_DOMAIN_GROUP => 'group',
        self::DCE_DOMAIN_ORG => 'org',
    ];

    /**
     * @phpstan-ignore property.readOnlyByPhpDocDefaultValue
     */
    private static ?UuidFactoryInterface $factory = null;

    /**
     * @var bool flag to detect if the UUID factory was replaced internally, which disables all optimizations for the
     *     default/happy path internal scenarios
     * @phpstan-ignore property.readOnlyByPhpDocDefaultValue
     */
    private static bool $factoryReplaced = false;

    protected CodecInterface $codec;
    protected NumberConverterInterface $numberConverter;
    protected Rfc4122FieldsInterface $fields;
    protected TimeConverterInterface $timeConverter;

    /**
     * Creates a universally unique identifier (UUID) from an array of fields
     *
     * Unless you're making advanced use of this library to generate identifiers that deviate from RFC 9562 (formerly
     * RFC 4122), you probably do not want to instantiate a UUID directly. Use the static methods, instead:
     *
     * ```
     * use Ramsey\Uuid\Uuid;
     *
     * $timeBasedUuid = Uuid::uuid1();
     * $namespaceMd5Uuid = Uuid::uuid3(Uuid::NAMESPACE_URL, 'http://php.net/');
     * $randomUuid = Uuid::uuid4();
     * $namespaceSha1Uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, 'http://php.net/');
     * ```
     *
     * @param Rfc4122FieldsInterface $fields The fields from which to construct a UUID
     * @param NumberConverterInterface $numberConverter The number converter to use for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use for converting timestamps extracted from a
     *     UUID to unix timestamps
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter,
    ) {
        $this->fields = $fields;
        $this->codec = $codec;
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Converts the UUID to a string for JSON serialization
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Converts the UUID to a string for PHP serialization
     */
    public function serialize(): string
    {
        return $this->codec->encode($this);
    }

    /**
     * @return array{bytes: string}
     */
    public function __serialize(): array
    {
        return ['bytes' => $this->serialize()];
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $data The serialized PHP string to unserialize into a UuidInterface instance
     */
    public function unserialize(string $data): void
    {
        if (strlen($data) === 16) {
            /** @var Uuid $uuid */
            $uuid = self::getFactory()->fromBytes($data);
        } else {
            /** @var Uuid $uuid */
            $uuid = self::getFactory()->fromString($data);
        }

        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
        $this->codec = $uuid->codec;

        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
        $this->numberConverter = $uuid->numberConverter;

        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
        $this->fields = $uuid->fields;

        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
        $this->timeConverter = $uuid->timeConverter;
    }

    /**
     * @param array{bytes?: string} $data
     */
    public function __unserialize(array $data): void
    {
        // @codeCoverageIgnoreStart
        if (!isset($data['bytes'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        // @codeCoverageIgnoreEnd

        $this->unserialize($data['bytes']);
    }

    public function compareTo(UuidInterface $other): int
    {
        $compare = strcmp($this->toString(), $other->toString());

        if ($compare < 0) {
            return -1;
        }

        if ($compare > 0) {
            return 1;
        }

        return 0;
    }

    public function equals(?object $other): bool
    {
        if (!$other instanceof UuidInterface) {
            return false;
        }

        return $this->compareTo($other) === 0;
    }

    /**
     * @return non-empty-string
     */
    public function getBytes(): string
    {
        return $this->codec->encodeBinary($this);
    }

    public function getFields(): FieldsInterface
    {
        return $this->fields;
    }

    public function getHex(): Hexadecimal
    {
        return new Hexadecimal(str_replace('-', '', $this->toString()));
    }

    public function getInteger(): IntegerObject
    {
        return new IntegerObject($this->numberConverter->fromHex($this->getHex()->toString()));
    }

    public function getUrn(): string
    {
        return 'urn:uuid:' . $this->toString();
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->codec->encode($this);
    }

    /**
     * Returns the factory used to create UUIDs
     */
    public static function getFactory(): UuidFactoryInterface
    {
        if (self::$factory === null) {
            self::$factory = new UuidFactory();
        }

        return self::$factory;
    }

    /**
     * Sets the factory used to create UUIDs
     *
     * @param UuidFactoryInterface $factory A factory that will be used by this class to create UUIDs
     */
    public static function setFactory(UuidFactoryInterface $factory): void
    {
        // Note: non-strict equality is intentional here. If the factory is configured differently, every assumption
        //       around purity is broken, and we have to internally decide everything differently.
        // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedNotEqualOperator
        self::$factoryReplaced = ($factory != new UuidFactory());

        self::$factory = $factory;
    }

    /**
     * Creates a UUID from a byte string
     *
     * @param string $bytes A binary string
     *
     * @return UuidInterface A UuidInterface instance created from a binary string representation
     *
     * @throws InvalidArgumentException
     *
     * @pure
     */
    public static function fromBytes(string $bytes): UuidInterface
    {
        /** @phpstan-ignore impure.staticPropertyAccess */
        if (!self::$factoryReplaced && strlen($bytes) === 16) {
            $base16Uuid = bin2hex($bytes);

            // Note: we are calling `fromString` internally because we don't know if the given `$bytes` is a valid UUID
            return self::fromString(
                substr($base16Uuid, 0, 8)
                    . '-'
                    . substr($base16Uuid, 8, 4)
                    . '-'
                    . substr($base16Uuid, 12, 4)
                    . '-'
                    . substr($base16Uuid, 16, 4)
                    . '-'
                    . substr($base16Uuid, 20, 12),
            );
        }

        /** @phpstan-ignore possiblyImpure.methodCall */
        return self::getFactory()->fromBytes($bytes);
    }

    /**
     * Creates a UUID from the string standard representation
     *
     * @param string $uuid A hexadecimal string
     *
     * @return UuidInterface A UuidInterface instance created from a hexadecimal string representation
     *
     * @throws InvalidArgumentException
     *
     * @pure
     */
    public static function fromString(string $uuid): UuidInterface
    {
        $uuid = strtolower($uuid);
        /** @phpstan-ignore impure.staticPropertyAccess, possiblyImpure.functionCall */
        if (!self::$factoryReplaced && preg_match(LazyUuidFromString::VALID_REGEX, $uuid) === 1) {
            /** @phpstan-ignore possiblyImpure.new */
            return new LazyUuidFromString($uuid);
        }

        /** @phpstan-ignore possiblyImpure.methodCall */
        return self::getFactory()->fromString($uuid);
    }

    /**
     * Creates a UUID from a DateTimeInterface instance
     *
     * @param DateTimeInterface $dateTime The date and time
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 1 UUID created from a DateTimeInterface instance
     */
    public static function fromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface {
        return self::getFactory()->fromDateTime($dateTime, $node, $clockSeq);
    }

    /**
     * Creates a UUID from the Hexadecimal object
     *
     * @param Hexadecimal $hex Hexadecimal object representing a hexadecimal number
     *
     * @return UuidInterface A UuidInterface instance created from the Hexadecimal object representing a hexadecimal number
     *
     * @throws InvalidArgumentException
     *
     * @pure
     */
    public static function fromHexadecimal(Hexadecimal $hex): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $factory = self::getFactory();

        if (method_exists($factory, 'fromHexadecimal')) {
            /** @phpstan-ignore possiblyImpure.methodCall */
            $uuid = $factory->fromHexadecimal($hex);
            /** @phpstan-ignore possiblyImpure.functionCall */
            assert($uuid instanceof UuidInterface);

            return $uuid;
        }

        throw new BadMethodCallException('The method fromHexadecimal() does not exist on the provided factory');
    }

    /**
     * Creates a UUID from a 128-bit integer string
     *
     * @param string $integer String representation of 128-bit integer
     *
     * @return UuidInterface A UuidInterface instance created from the string representation of a 128-bit integer
     *
     * @throws InvalidArgumentException
     *
     * @pure
     */
    public static function fromInteger(string $integer): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return self::getFactory()->fromInteger($integer);
    }

    /**
     * Returns true if the provided string is a valid UUID
     *
     * @param string $uuid A string to validate as a UUID
     *
     * @return bool True if the string is a valid UUID, false otherwise
     *
     * @phpstan-assert-if-true =non-empty-string $uuid
     *
     * @pure
     */
    public static function isValid(string $uuid): bool
    {
        /** @phpstan-ignore possiblyImpure.methodCall, possiblyImpure.methodCall */
        return self::getFactory()->getValidator()->validate($uuid);
    }

    /**
     * Returns a version 1 (Gregorian time) UUID from a host ID, sequence number, and the current time
     *
     * @param Hexadecimal | int | string | null $node A 48-bit number representing the hardware address; this number may
     *     be represented as an integer or a hexadecimal string
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 1 UUID
     */
    public static function uuid1($node = null, ?int $clockSeq = null): UuidInterface
    {
        return self::getFactory()->uuid1($node, $clockSeq);
    }

    /**
     * Returns a version 2 (DCE Security) UUID from a local domain, local identifier, host ID, clock sequence, and the current time
     *
     * @param int $localDomain The local domain to use when generating bytes, according to DCE Security
     * @param IntegerObject | null $localIdentifier The local identifier for the given domain; this may be a UID or GID
     *     on POSIX systems, if the local domain is "person" or "group," or it may be a site-defined identifier if the
     *     local domain is "org"
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes (in a version 2 UUID, the lower 8 bits of this number are
     *     replaced with the domain).
     *
     * @return UuidInterface A UuidInterface instance that represents a version 2 UUID
     */
    public static function uuid2(
        int $localDomain,
        ?IntegerObject $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface {
        return self::getFactory()->uuid2($localDomain, $localIdentifier, $node, $clockSeq);
    }

    /**
     * Returns a version 3 (name-based) UUID based on the MD5 hash of a namespace ID and a name
     *
     * @param UuidInterface | string $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a version 3 UUID
     *
     * @pure
     */
    public static function uuid3($ns, string $name): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return self::getFactory()->uuid3($ns, $name);
    }

    /**
     * Returns a version 4 (random) UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a version 4 UUID
     */
    public static function uuid4(): UuidInterface
    {
        return self::getFactory()->uuid4();
    }

    /**
     * Returns a version 5 (name-based) UUID based on the SHA-1 hash of a namespace ID and a name
     *
     * @param UuidInterface | string $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a version 5 UUID
     *
     * @pure
     */
    public static function uuid5($ns, string $name): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return self::getFactory()->uuid5($ns, $name);
    }

    /**
     * Returns a version 6 (reordered Gregorian time) UUID from a host ID, sequence number, and the current time
     *
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 6 UUID
     */
    public static function uuid6(
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface {
        return self::getFactory()->uuid6($node, $clockSeq);
    }

    /**
     * Returns a version 7 (Unix Epoch time) UUID
     *
     * @param DateTimeInterface | null $dateTime An optional date/time from which to create the version 7 UUID. If not
     *     provided, the UUID is generated using the current date/time.
     *
     * @return UuidInterface A UuidInterface instance that represents a version 7 UUID
     */
    public static function uuid7(?DateTimeInterface $dateTime = null): UuidInterface
    {
        $factory = self::getFactory();

        if (method_exists($factory, 'uuid7')) {
            /** @var UuidInterface */
            return $factory->uuid7($dateTime);
        }

        throw new UnsupportedOperationException('The provided factory does not support the uuid7() method');
    }

    /**
     * Returns a version 8 (custom format) UUID
     *
     * The bytes provided may contain any value according to your application's needs. Be aware, however, that other
     * applications may not understand the semantics of the value.
     *
     * @param string $bytes A 16-byte octet string. This is an open blob of data that you may fill with 128 bits of
     *     information. Be aware, however, bits 48 through 51 will be replaced with the UUID version field, and bits 64
     *     and 65 will be replaced with the UUID variant. You MUST NOT rely on these bits for your application needs.
     *
     * @return UuidInterface A UuidInterface instance that represents a version 8 UUID
     *
     * @pure
     */
    public static function uuid8(string $bytes): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $factory = self::getFactory();

        if (method_exists($factory, 'uuid8')) {
            /**
             * @var UuidInterface
             * @phpstan-ignore possiblyImpure.methodCall
             */
            return $factory->uuid8($bytes);
        }

        throw new UnsupportedOperationException('The provided factory does not support the uuid8() method');
    }
}
