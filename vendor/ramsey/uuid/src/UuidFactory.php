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

use DateTimeInterface;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Generator\NameGeneratorInterface;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Validator\ValidatorInterface;

use function bin2hex;
use function hex2bin;
use function pack;
use function str_pad;
use function strtolower;
use function substr;
use function substr_replace;
use function unpack;

use const STR_PAD_LEFT;

class UuidFactory implements UuidFactoryInterface
{
    private CodecInterface $codec;
    private DceSecurityGeneratorInterface $dceSecurityGenerator;
    private NameGeneratorInterface $nameGenerator;
    private NodeProviderInterface $nodeProvider;
    private NumberConverterInterface $numberConverter;
    private RandomGeneratorInterface $randomGenerator;
    private TimeConverterInterface $timeConverter;
    private TimeGeneratorInterface $timeGenerator;
    private TimeGeneratorInterface $unixTimeGenerator;
    private UuidBuilderInterface $uuidBuilder;
    private ValidatorInterface $validator;

    /**
     * @var bool whether the feature set was provided from outside, or we can operate under "default" assumptions
     */
    private bool $isDefaultFeatureSet;

    /**
     * @param FeatureSet | null $features A set of available features in the current environment
     */
    public function __construct(?FeatureSet $features = null)
    {
        $this->isDefaultFeatureSet = $features === null;

        $features = $features ?: new FeatureSet();

        $this->codec = $features->getCodec();
        $this->dceSecurityGenerator = $features->getDceSecurityGenerator();
        $this->nameGenerator = $features->getNameGenerator();
        $this->nodeProvider = $features->getNodeProvider();
        $this->numberConverter = $features->getNumberConverter();
        $this->randomGenerator = $features->getRandomGenerator();
        $this->timeConverter = $features->getTimeConverter();
        $this->timeGenerator = $features->getTimeGenerator();
        $this->uuidBuilder = $features->getBuilder();
        $this->validator = $features->getValidator();
        $this->unixTimeGenerator = $features->getUnixTimeGenerator();
    }

    /**
     * Returns the codec used by this factory
     */
    public function getCodec(): CodecInterface
    {
        return $this->codec;
    }

    /**
     * Sets the codec to use for this factory
     *
     * @param CodecInterface $codec A UUID encoder-decoder
     */
    public function setCodec(CodecInterface $codec): void
    {
        $this->isDefaultFeatureSet = false;

        $this->codec = $codec;
    }

    /**
     * Returns the name generator used by this factory
     */
    public function getNameGenerator(): NameGeneratorInterface
    {
        return $this->nameGenerator;
    }

    /**
     * Sets the name generator to use for this factory
     *
     * @param NameGeneratorInterface $nameGenerator A generator to generate binary data, based on a namespace and name
     */
    public function setNameGenerator(NameGeneratorInterface $nameGenerator): void
    {
        $this->isDefaultFeatureSet = false;

        $this->nameGenerator = $nameGenerator;
    }

    /**
     * Returns the node provider used by this factory
     */
    public function getNodeProvider(): NodeProviderInterface
    {
        return $this->nodeProvider;
    }

    /**
     * Returns the random generator used by this factory
     */
    public function getRandomGenerator(): RandomGeneratorInterface
    {
        return $this->randomGenerator;
    }

    /**
     * Returns the time generator used by this factory
     */
    public function getTimeGenerator(): TimeGeneratorInterface
    {
        return $this->timeGenerator;
    }

    /**
     * Sets the time generator to use for this factory
     *
     * @param TimeGeneratorInterface $generator A generator to generate binary data, based on the time
     */
    public function setTimeGenerator(TimeGeneratorInterface $generator): void
    {
        $this->isDefaultFeatureSet = false;

        $this->timeGenerator = $generator;
    }

    /**
     * Returns the DCE Security generator used by this factory
     */
    public function getDceSecurityGenerator(): DceSecurityGeneratorInterface
    {
        return $this->dceSecurityGenerator;
    }

    /**
     * Sets the DCE Security generator to use for this factory
     *
     * @param DceSecurityGeneratorInterface $generator A generator to generate binary data, based on a local domain and
     *     local identifier
     */
    public function setDceSecurityGenerator(DceSecurityGeneratorInterface $generator): void
    {
        $this->isDefaultFeatureSet = false;

        $this->dceSecurityGenerator = $generator;
    }

    /**
     * Returns the number converter used by this factory
     */
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }

    /**
     * Sets the random generator to use for this factory
     *
     * @param RandomGeneratorInterface $generator A generator to generate binary data, based on some random input
     */
    public function setRandomGenerator(RandomGeneratorInterface $generator): void
    {
        $this->isDefaultFeatureSet = false;

        $this->randomGenerator = $generator;
    }

    /**
     * Sets the number converter to use for this factory
     *
     * @param NumberConverterInterface $converter A converter to use for working with large integers (i.e., integers
     *     greater than PHP_INT_MAX)
     */
    public function setNumberConverter(NumberConverterInterface $converter): void
    {
        $this->isDefaultFeatureSet = false;

        $this->numberConverter = $converter;
    }

    /**
     * Returns the UUID builder used by this factory
     */
    public function getUuidBuilder(): UuidBuilderInterface
    {
        return $this->uuidBuilder;
    }

    /**
     * Sets the UUID builder to use for this factory
     *
     * @param UuidBuilderInterface $builder A builder for constructing instances of UuidInterface
     */
    public function setUuidBuilder(UuidBuilderInterface $builder): void
    {
        $this->isDefaultFeatureSet = false;

        $this->uuidBuilder = $builder;
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * Sets the validator to use for this factory
     *
     * @param ValidatorInterface $validator A validator to use for validating whether a string is a valid UUID
     */
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->isDefaultFeatureSet = false;

        $this->validator = $validator;
    }

    /**
     * @pure
     */
    public function fromBytes(string $bytes): UuidInterface
    {
        return $this->codec->decodeBytes($bytes);
    }

    /**
     * @pure
     */
    public function fromString(string $uuid): UuidInterface
    {
        $uuid = strtolower($uuid);

        return $this->codec->decode($uuid);
    }

    /**
     * @pure
     */
    public function fromInteger(string $integer): UuidInterface
    {
        $hex = $this->numberConverter->toHex($integer);
        $hex = str_pad($hex, 32, '0', STR_PAD_LEFT);

        return $this->fromString($hex);
    }

    public function fromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null,
    ): UuidInterface {
        $timeProvider = new FixedTimeProvider(new Time($dateTime->format('U'), $dateTime->format('u')));
        $timeGenerator = new DefaultTimeGenerator($this->nodeProvider, $this->timeConverter, $timeProvider);
        $bytes = $timeGenerator->generate($node?->toString(), $clockSeq);

        return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_TIME);
    }

    /**
     * @pure
     */
    public function fromHexadecimal(Hexadecimal $hex): UuidInterface
    {
        return $this->codec->decode($hex->__toString());
    }

    /**
     * @inheritDoc
     */
    public function uuid1($node = null, ?int $clockSeq = null): UuidInterface
    {
        $bytes = $this->timeGenerator->generate($node, $clockSeq);

        return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_TIME);
    }

    public function uuid2(
        int $localDomain,
        ?IntegerObject $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null,
    ): UuidInterface {
        $bytes = $this->dceSecurityGenerator->generate($localDomain, $localIdentifier, $node, $clockSeq);

        return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_DCE_SECURITY);
    }

    /**
     * @inheritDoc
     * @pure
     */
    public function uuid3($ns, string $name): UuidInterface
    {
        return $this->uuidFromNsAndName($ns, $name, Uuid::UUID_TYPE_HASH_MD5, 'md5');
    }

    public function uuid4(): UuidInterface
    {
        $bytes = $this->randomGenerator->generate(16);

        return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_RANDOM);
    }

    /**
     * @inheritDoc
     * @pure
     */
    public function uuid5($ns, string $name): UuidInterface
    {
        return $this->uuidFromNsAndName($ns, $name, Uuid::UUID_TYPE_HASH_SHA1, 'sha1');
    }

    public function uuid6(?Hexadecimal $node = null, ?int $clockSeq = null): UuidInterface
    {
        $bytes = $this->timeGenerator->generate($node?->toString(), $clockSeq);

        // Rearrange the bytes, according to the UUID version 6 specification.
        $v6 = $bytes[6] . $bytes[7] . $bytes[4] . $bytes[5]
            . $bytes[0] . $bytes[1] . $bytes[2] . $bytes[3];
        $v6 = bin2hex($v6);

        // Drop the first four bits, while adding an empty four bits for the version field. This allows us to
        // reconstruct the correct time from the bytes of this UUID.
        $v6Bytes = hex2bin(substr($v6, 1, 12) . '0' . substr($v6, -3));
        $v6Bytes .= substr($bytes, 8);

        return $this->uuidFromBytesAndVersion($v6Bytes, Uuid::UUID_TYPE_REORDERED_TIME);
    }

    /**
     * Returns a version 7 (Unix Epoch time) UUID
     *
     * @param DateTimeInterface | null $dateTime An optional date/time from which to create the version 7 UUID. If not
     *     provided, the UUID is generated using the current date/time.
     *
     * @return UuidInterface A UuidInterface instance that represents a version 7 UUID
     */
    public function uuid7(?DateTimeInterface $dateTime = null): UuidInterface
    {
        assert($this->unixTimeGenerator instanceof UnixTimeGenerator);
        $bytes = $this->unixTimeGenerator->generate(null, null, $dateTime);

        return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_UNIX_TIME);
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
    public function uuid8(string $bytes): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_CUSTOM);
    }

    /**
     * Returns a Uuid created from the provided byte string
     *
     * Uses the configured builder and codec and the provided byte string to construct a Uuid object.
     *
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return UuidInterface An instance of UuidInterface, created from the provided bytes
     *
     * @pure
     */
    public function uuid(string $bytes): UuidInterface
    {
        return $this->uuidBuilder->build($this->codec, $bytes);
    }

    /**
     * Returns a version 3 or 5 namespaced Uuid
     *
     * @param UuidInterface | string $ns The namespace (must be a valid UUID)
     * @param string $name The name to hash together with the namespace
     * @param int $version The version of UUID to create (3 or 5)
     * @param string $hashAlgorithm The hashing algorithm to use when hashing together the namespace and name
     *
     * @return UuidInterface An instance of UuidInterface, created by hashing together the provided namespace and name
     *
     * @pure
     */
    private function uuidFromNsAndName(
        UuidInterface | string $ns,
        string $name,
        int $version,
        string $hashAlgorithm,
    ): UuidInterface {
        if (!($ns instanceof UuidInterface)) {
            $ns = $this->fromString($ns);
        }

        $bytes = $this->nameGenerator->generate($ns, $name, $hashAlgorithm);

        /** @phpstan-ignore possiblyImpure.methodCall */
        return $this->uuidFromBytesAndVersion(substr($bytes, 0, 16), $version);
    }

    /**
     * Returns a Uuid created from the provided bytes and version
     *
     * @param string $bytes The byte string to convert to a UUID
     * @param int $version The version to apply to the UUID
     *
     * @return UuidInterface An instance of UuidInterface, created from the byte string and version
     */
    private function uuidFromBytesAndVersion(string $bytes, int $version): UuidInterface
    {
        /** @var int[] $unpackedTime */
        $unpackedTime = unpack('n*', substr($bytes, 6, 2));
        $timeHi = $unpackedTime[1];
        $timeHiAndVersion = pack('n*', BinaryUtils::applyVersion($timeHi, $version));

        /** @var int[] $unpackedClockSeq */
        $unpackedClockSeq = unpack('n*', substr($bytes, 8, 2));
        $clockSeqHi = $unpackedClockSeq[1];
        $clockSeqHiAndReserved = pack('n*', BinaryUtils::applyVariant($clockSeqHi));

        $bytes = substr_replace($bytes, $timeHiAndVersion, 6, 2);
        $bytes = substr_replace($bytes, $clockSeqHiAndReserved, 8, 2);

        if ($this->isDefaultFeatureSet) {
            return LazyUuidFromString::fromBytes($bytes);
        }

        return $this->uuid($bytes);
    }
}
