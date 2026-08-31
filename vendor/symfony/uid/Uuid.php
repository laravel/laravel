<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid;

use Symfony\Component\Uid\Exception\InvalidArgumentException;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 *
 * @see https://datatracker.ietf.org/doc/html/rfc9562/#section-6.6 for details about namespaces
 */
class Uuid extends AbstractUid
{
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    public const FORMAT_BINARY = 1;
    public const FORMAT_BASE_32 = 1 << 1;
    public const FORMAT_BASE_58 = 1 << 2;
    public const FORMAT_RFC_4122 = 1 << 3;
    public const FORMAT_RFC_9562 = self::FORMAT_RFC_4122;
    public const FORMAT_ALL = -1;

    protected const TYPE = 0;
    protected const NIL = '00000000-0000-0000-0000-000000000000';
    protected const MAX = 'ffffffff-ffff-ffff-ffff-ffffffffffff';

    public function __construct(string $uuid, bool $checkVariant = false)
    {
        $type = preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$}Di', $uuid) ? (int) $uuid[14] : false;

        if (false === $type || (static::TYPE ?: $type) !== $type) {
            throw new InvalidArgumentException(\sprintf('Invalid UUID%s.', static::TYPE ? 'v'.static::TYPE : ''));
        }

        $this->uid = strtolower($uuid);

        if ($checkVariant && !\in_array($this->uid[19], ['8', '9', 'a', 'b'], true)) {
            throw new InvalidArgumentException(\sprintf('Invalid UUID%s.', static::TYPE ? 'v'.static::TYPE : ''));
        }
    }

    public static function fromString(string $uuid): static
    {
        $uuid = self::transformToRfc9562($uuid, self::FORMAT_ALL);

        if (__CLASS__ !== static::class || 36 !== \strlen($uuid)) {
            return new static($uuid);
        }

        if (self::NIL === $uuid) {
            return new NilUuid();
        }

        if (self::MAX === $uuid = strtr($uuid, 'F', 'f')) {
            return new MaxUuid();
        }

        if (!\in_array($uuid[19], ['8', '9', 'a', 'b', 'A', 'B'], true)) {
            return new self($uuid);
        }

        return match ((int) $uuid[14]) {
            UuidV1::TYPE => new UuidV1($uuid),
            UuidV3::TYPE => new UuidV3($uuid),
            UuidV4::TYPE => new UuidV4($uuid),
            UuidV5::TYPE => new UuidV5($uuid),
            UuidV6::TYPE => new UuidV6($uuid),
            UuidV7::TYPE => new UuidV7($uuid),
            UuidV8::TYPE => new UuidV8($uuid),
            default => new self($uuid),
        };
    }

    final public static function v1(): UuidV1
    {
        return new UuidV1();
    }

    final public static function v3(self $namespace, string $name): UuidV3
    {
        // don't use uuid_generate_md5(), some versions are buggy
        $uuid = md5(hex2bin(str_replace('-', '', $namespace->uid)).$name, true);

        return new UuidV3(self::format($uuid, '-3'));
    }

    final public static function v4(): UuidV4
    {
        return new UuidV4();
    }

    final public static function v5(self $namespace, string $name): UuidV5
    {
        // don't use uuid_generate_sha1(), some versions are buggy
        $uuid = substr(sha1(hex2bin(str_replace('-', '', $namespace->uid)).$name, true), 0, 16);

        return new UuidV5(self::format($uuid, '-5'));
    }

    final public static function v6(): UuidV6
    {
        return new UuidV6();
    }

    final public static function v7(): UuidV7
    {
        return new UuidV7();
    }

    final public static function v8(string $uuid): UuidV8
    {
        return new UuidV8($uuid);
    }

    /**
     * @param int-mask-of<Uuid::FORMAT_*> $format
     */
    public static function isValid(string $uuid /* , int $format = self::FORMAT_RFC_9562 */): bool
    {
        $format = 1 < \func_num_args() ? func_get_arg(1) : self::FORMAT_RFC_9562;

        if (36 === \strlen($uuid) && !($format & self::FORMAT_RFC_9562)) {
            return false;
        }

        if (false === $uuid = self::transformToRfc9562($uuid, $format)) {
            return false;
        }

        if (self::NIL === $uuid && \in_array(static::class, [__CLASS__, NilUuid::class], true)) {
            return true;
        }

        if (self::MAX === strtr($uuid, 'F', 'f') && \in_array(static::class, [__CLASS__, MaxUuid::class], true)) {
            return true;
        }

        if (!preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){2}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$}Di', $uuid)) {
            return false;
        }

        return __CLASS__ === static::class || static::TYPE === (int) $uuid[14];
    }

    public function toBinary(): string
    {
        return hex2bin(str_replace('-', '', $this->uid));
    }

    /**
     * Returns the identifier as a RFC 9562/4122 case insensitive string.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc9562/#section-4
     *
     * @example 09748193-048a-4bfb-b825-8528cf74fdc1 (len=36)
     */
    public function toRfc4122(): string
    {
        return $this->uid;
    }

    public function compare(AbstractUid $other): int
    {
        if (false !== $cmp = uuid_compare($this->uid, $other->uid)) {
            return $cmp;
        }

        return parent::compare($other);
    }

    private static function format(string $uuid, string $version): string
    {
        $uuid[8] = $uuid[8] & "\x3F" | "\x80";
        $uuid = substr_replace(bin2hex($uuid), '-', 8, 0);
        $uuid = substr_replace($uuid, $version, 13, 1);
        $uuid = substr_replace($uuid, '-', 18, 0);

        return substr_replace($uuid, '-', 23, 0);
    }

    /**
     * Transforms a binary string, a base-32 string or a base-58 string to a RFC9562 string.
     *
     * @param int-mask-of<Uuid::FORMAT_*> $format
     *
     * @return string|false The RFC9562 string or false if the format doesn't match the input
     */
    private static function transformToRfc9562(string $uuid, int $format): string|false
    {
        $inputUuid = $uuid;
        $fromBase58 = false;
        if (22 === \strlen($uuid) && 22 === strspn($uuid, BinaryUtil::BASE58['']) && $format & self::FORMAT_BASE_58) {
            $uuid = str_pad(BinaryUtil::fromBase($uuid, BinaryUtil::BASE58), 16, "\0", \STR_PAD_LEFT);
            $fromBase58 = true;
        }

        // base-58 are always transformed to binary string, but they must only be valid when the format is FORMAT_BASE_58
        if (16 === \strlen($uuid) && $format & self::FORMAT_BINARY || $fromBase58 && $format & self::FORMAT_BASE_58) {
            // don't use uuid_unparse(), it's slower
            $uuid = bin2hex($uuid);
            $uuid = substr_replace($uuid, '-', 8, 0);
            $uuid = substr_replace($uuid, '-', 13, 0);
            $uuid = substr_replace($uuid, '-', 18, 0);
            $uuid = substr_replace($uuid, '-', 23, 0);
        } elseif (26 === \strlen($uuid) && Ulid::isValid($uuid) && $format & self::FORMAT_BASE_32) {
            $ulid = new NilUlid();
            $ulid->uid = strtoupper($uuid);
            $uuid = $ulid->toRfc4122();
        }

        if ($inputUuid === $uuid && !($format & self::FORMAT_RFC_9562)) {
            // input format doesn't match the input string
            return false;
        }

        return $uuid;
    }
}
