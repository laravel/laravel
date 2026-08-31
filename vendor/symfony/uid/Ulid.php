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
 * A ULID is lexicographically sortable and contains a 48-bit timestamp and 80-bit of crypto-random entropy.
 *
 * @see https://github.com/ulid/spec
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Ulid extends AbstractUid implements TimeBasedUidInterface
{
    protected const NIL = '00000000000000000000000000';
    protected const MAX = '7ZZZZZZZZZZZZZZZZZZZZZZZZZ';

    private static string $time = '';
    private static array $rand = [];

    public function __construct(?string $ulid = null)
    {
        if (null === $ulid) {
            $this->uid = static::generate();
        } elseif (self::NIL === $ulid) {
            $this->uid = self::NIL;
        } else {
            $this->uid = strtoupper($ulid);

            if (self::MAX === $this->uid) {
                $this->uid = self::MAX;
            } elseif (!self::isValid($ulid)) {
                throw new InvalidArgumentException('Invalid ULID.');
            }
        }
    }

    public static function isValid(string $ulid): bool
    {
        if (26 !== \strlen($ulid)) {
            return false;
        }

        if (26 !== strspn($ulid, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz')) {
            return false;
        }

        return $ulid[0] <= '7';
    }

    public static function fromString(string $ulid): static
    {
        if (36 === \strlen($ulid) && preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$}Di', $ulid)) {
            $ulid = hex2bin(str_replace('-', '', $ulid));
        } elseif (22 === \strlen($ulid) && 22 === strspn($ulid, BinaryUtil::BASE58[''])) {
            $ulid = str_pad(BinaryUtil::fromBase($ulid, BinaryUtil::BASE58), 16, "\0", \STR_PAD_LEFT);
        }

        if (16 !== \strlen($ulid)) {
            return match (strtr($ulid, 'z', 'Z')) {
                self::NIL => new NilUlid(),
                self::MAX => new MaxUlid(),
                default => new static($ulid),
            };
        }

        $ulid = bin2hex($ulid);
        $ulid = \sprintf('%02s%04s%04s%04s%04s%04s%04s',
            base_convert(substr($ulid, 0, 2), 16, 32),
            base_convert(substr($ulid, 2, 5), 16, 32),
            base_convert(substr($ulid, 7, 5), 16, 32),
            base_convert(substr($ulid, 12, 5), 16, 32),
            base_convert(substr($ulid, 17, 5), 16, 32),
            base_convert(substr($ulid, 22, 5), 16, 32),
            base_convert(substr($ulid, 27, 5), 16, 32)
        );

        if (self::NIL === $ulid) {
            return new NilUlid();
        }

        if (self::MAX === $ulid = strtr($ulid, 'abcdefghijklmnopqrstuv', 'ABCDEFGHJKMNPQRSTVWXYZ')) {
            return new MaxUlid();
        }

        $u = new static(self::NIL);
        $u->uid = $ulid;

        return $u;
    }

    public function toBinary(): string
    {
        $ulid = strtr($this->uid, 'ABCDEFGHJKMNPQRSTVWXYZ', 'abcdefghijklmnopqrstuv');

        $ulid = \sprintf('%02s%05s%05s%05s%05s%05s%05s',
            base_convert(substr($ulid, 0, 2), 32, 16),
            base_convert(substr($ulid, 2, 4), 32, 16),
            base_convert(substr($ulid, 6, 4), 32, 16),
            base_convert(substr($ulid, 10, 4), 32, 16),
            base_convert(substr($ulid, 14, 4), 32, 16),
            base_convert(substr($ulid, 18, 4), 32, 16),
            base_convert(substr($ulid, 22, 4), 32, 16)
        );

        return hex2bin($ulid);
    }

    /**
     * Returns the identifier as a base32 case insensitive string.
     *
     * @see https://tools.ietf.org/html/rfc4648#section-6
     *
     * @example 09EJ0S614A9FXVG9C5537Q9ZE1 (len=26)
     */
    public function toBase32(): string
    {
        return $this->uid;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        $time = strtr(substr($this->uid, 0, 10), 'ABCDEFGHJKMNPQRSTVWXYZ', 'abcdefghijklmnopqrstuv');

        if (\PHP_INT_SIZE >= 8) {
            $time = (string) hexdec(base_convert($time, 32, 16));
        } else {
            $time = \sprintf('%02s%05s%05s',
                base_convert(substr($time, 0, 2), 32, 16),
                base_convert(substr($time, 2, 4), 32, 16),
                base_convert(substr($time, 6, 4), 32, 16)
            );
            $time = BinaryUtil::toBase(hex2bin($time), BinaryUtil::BASE10);
        }

        if (4 > \strlen($time)) {
            $time = '000'.$time;
        }

        return \DateTimeImmutable::createFromFormat('U.u', substr_replace($time, '.', -3, 0));
    }

    public static function generate(?\DateTimeInterface $time = null): string
    {
        if (null === $mtime = $time) {
            $time = microtime(false);
            $time = substr($time, 11).substr($time, 2, 3);
        } elseif (0 > $time = $time->format('Uv')) {
            throw new InvalidArgumentException('The timestamp must be positive.');
        }

        if ($time > self::$time || (null !== $mtime && $time !== self::$time)) {
            randomize:
            $r = unpack('n*', random_bytes(10));
            $r[1] |= ($r[5] <<= 4) & 0xF0000;
            $r[2] |= ($r[5] <<= 4) & 0xF0000;
            $r[3] |= ($r[5] <<= 4) & 0xF0000;
            $r[4] |= ($r[5] <<= 4) & 0xF0000;
            unset($r[5]);
            self::$rand = $r;
            self::$time = $time;
        } elseif ([1 => 0xFFFFF, 0xFFFFF, 0xFFFFF, 0xFFFFF] === self::$rand) {
            if (\PHP_INT_SIZE >= 8 || 10 > \strlen($time = self::$time)) {
                $time = (string) (1 + $time);
            } elseif ('999999999' === $mtime = substr($time, -9)) {
                $time = (1 + substr($time, 0, -9)).'000000000';
            } else {
                $time = substr_replace($time, str_pad(++$mtime, 9, '0', \STR_PAD_LEFT), -9);
            }

            goto randomize;
        } else {
            for ($i = 4; $i > 0 && 0xFFFFF === self::$rand[$i]; --$i) {
                self::$rand[$i] = 0;
            }

            ++self::$rand[$i];
            $time = self::$time;
        }

        if (\PHP_INT_SIZE >= 8) {
            $time = base_convert($time, 10, 32);
        } else {
            $time = str_pad(bin2hex(BinaryUtil::fromBase($time, BinaryUtil::BASE10)), 12, '0', \STR_PAD_LEFT);
            $time = \sprintf('%s%04s%04s',
                base_convert(substr($time, 0, 2), 16, 32),
                base_convert(substr($time, 2, 5), 16, 32),
                base_convert(substr($time, 7, 5), 16, 32)
            );
        }

        return strtr(\sprintf('%010s%04s%04s%04s%04s',
            $time,
            base_convert(self::$rand[1], 10, 32),
            base_convert(self::$rand[2], 10, 32),
            base_convert(self::$rand[3], 10, 32),
            base_convert(self::$rand[4], 10, 32)
        ), 'abcdefghijklmnopqrstuv', 'ABCDEFGHJKMNPQRSTVWXYZ');
    }
}
