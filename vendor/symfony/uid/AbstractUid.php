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
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractUid implements \JsonSerializable, \Stringable, HashableInterface
{
    /**
     * The identifier in its canonic representation.
     */
    protected string $uid;

    /**
     * Whether the passed value is valid for the constructor of the current class.
     */
    abstract public static function isValid(string $uid): bool;

    /**
     * Creates an AbstractUid from an identifier represented in any of the supported formats.
     *
     * @throws InvalidArgumentException When the passed value is not valid
     */
    abstract public static function fromString(string $uid): static;

    /**
     * @throws InvalidArgumentException When the passed value is not valid
     */
    public static function fromBinary(string $uid): static
    {
        if (16 !== \strlen($uid)) {
            throw new InvalidArgumentException('Invalid binary uid provided.');
        }

        return static::fromString($uid);
    }

    /**
     * @throws InvalidArgumentException When the passed value is not valid
     */
    public static function fromBase58(string $uid): static
    {
        if (22 !== \strlen($uid)) {
            throw new InvalidArgumentException('Invalid base-58 uid provided.');
        }

        return static::fromString($uid);
    }

    /**
     * @throws InvalidArgumentException When the passed value is not valid
     */
    public static function fromBase32(string $uid): static
    {
        if (26 !== \strlen($uid)) {
            throw new InvalidArgumentException('Invalid base-32 uid provided.');
        }

        return static::fromString($uid);
    }

    /**
     * @param string $uid A valid RFC 9562/4122 uid
     *
     * @throws InvalidArgumentException When the passed value is not valid
     */
    public static function fromRfc4122(string $uid): static
    {
        if (36 !== \strlen($uid)) {
            throw new InvalidArgumentException('Invalid RFC4122 uid provided.');
        }

        return static::fromString($uid);
    }

    /**
     * Returns the identifier as a raw binary string.
     *
     * @return non-empty-string
     */
    abstract public function toBinary(): string;

    /**
     * Returns the identifier as a base58 case-sensitive string.
     *
     * @example 2AifFTC3zXgZzK5fPrrprL (len=22)
     *
     * @return non-empty-string
     */
    public function toBase58(): string
    {
        return strtr(\sprintf('%022s', BinaryUtil::toBase($this->toBinary(), BinaryUtil::BASE58)), '0', '1');
    }

    /**
     * Returns the identifier as a base32 case-insensitive string.
     *
     * @see https://tools.ietf.org/html/rfc4648#section-6
     *
     * @example 09EJ0S614A9FXVG9C5537Q9ZE1 (len=26)
     *
     * @return non-empty-string
     */
    public function toBase32(): string
    {
        $uid = bin2hex($this->toBinary());
        $uid = \sprintf('%02s%04s%04s%04s%04s%04s%04s',
            base_convert(substr($uid, 0, 2), 16, 32),
            base_convert(substr($uid, 2, 5), 16, 32),
            base_convert(substr($uid, 7, 5), 16, 32),
            base_convert(substr($uid, 12, 5), 16, 32),
            base_convert(substr($uid, 17, 5), 16, 32),
            base_convert(substr($uid, 22, 5), 16, 32),
            base_convert(substr($uid, 27, 5), 16, 32)
        );

        return strtr($uid, 'abcdefghijklmnopqrstuv', 'ABCDEFGHJKMNPQRSTVWXYZ');
    }

    /**
     * Returns the identifier as a RFC 9562/4122 case-insensitive string.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc9562/#section-4
     *
     * @example 09748193-048a-4bfb-b825-8528cf74fdc1 (len=36)
     *
     * @return non-empty-string
     */
    public function toRfc4122(): string
    {
        // don't use uuid_unparse(), it's slower
        $uuid = bin2hex($this->toBinary());
        $uuid = substr_replace($uuid, '-', 8, 0);
        $uuid = substr_replace($uuid, '-', 13, 0);
        $uuid = substr_replace($uuid, '-', 18, 0);

        return substr_replace($uuid, '-', 23, 0);
    }

    /**
     * Returns the identifier as a prefixed hexadecimal case insensitive string.
     *
     * @example 0x09748193048a4bfbb8258528cf74fdc1 (len=34)
     *
     * @return non-empty-string
     */
    public function toHex(): string
    {
        return '0x'.bin2hex($this->toBinary());
    }

    /**
     * Returns whether the argument is an AbstractUid and contains the same value as the current instance.
     */
    public function equals(mixed $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->uid === $other->uid;
    }

    /**
     * @return non-empty-string
     */
    public function hash(): string
    {
        return $this->uid;
    }

    public function compare(self $other): int
    {
        return (\strlen($this->uid) - \strlen($other->uid)) ?: ($this->uid <=> $other->uid);
    }

    /**
     * @return non-empty-string
     */
    final public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->uid;
    }

    /**
     * @return non-empty-string
     */
    public function jsonSerialize(): string
    {
        return $this->uid;
    }
}
