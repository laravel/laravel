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
 * A v7 UUID is lexicographically sortable and contains a 58-bit timestamp and 64 extra unique bits.
 *
 * Within the same millisecond, the unique bits are incremented by a 24-bit random number.
 * This method provides microsecond precision for the timestamp, and minimizes both the
 * risk of collisions and the consumption of the OS' entropy pool.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class UuidV7 extends Uuid implements TimeBasedUidInterface
{
    protected const TYPE = 7;

    private static string $time = '';
    private static int $subMs = 0;
    private static array $rand = [];
    private static string $seed;
    private static array $seedParts;
    private static int $seedIndex = 0;

    public function __construct(?string $uuid = null)
    {
        if (null === $uuid) {
            $this->uid = static::generate();
        } else {
            parent::__construct($uuid, true);
        }
    }

    public function getDateTime(): \DateTimeImmutable
    {
        $time = substr($this->uid, 0, 8).substr($this->uid, 9, 4);
        $time = \PHP_INT_SIZE >= 8 ? (string) hexdec($time) : BinaryUtil::toBase(hex2bin($time), BinaryUtil::BASE10);

        if (4 > \strlen($time)) {
            $time = '000'.$time;
        }
        $time .= substr(1000 + (hexdec(substr($this->uid, 14, 4)) >> 2 & 0x3FF), -3);

        return \DateTimeImmutable::createFromFormat('U.u', substr_replace($time, '.', -6, 0));
    }

    public static function generate(?\DateTimeInterface $time = null): string
    {
        if (null === $mtime = $time) {
            $time = microtime(false);
            $subMs = (int) substr($time, 5, 3);
            $time = substr($time, 11).substr($time, 2, 3);
        } elseif (0 > $time = $time->format('Uu')) {
            throw new InvalidArgumentException('The timestamp must be positive.');
        } else {
            $subMs = (int) substr($time, -3);
            $time = substr($time, 0, -3);
        }

        if ($time > self::$time || (null !== $mtime && $time !== self::$time)) {
            randomize:
            self::$rand = unpack(\PHP_INT_SIZE >= 8 ? 'L*' : 'S*', isset(self::$seed) ? random_bytes(8) : self::$seed = random_bytes(16));
            self::$time = $time;
        } else {
            // Within the same ms, we increment the rand part by a random 24-bit number.
            // Instead of getting this number from random_bytes(), which is slow, we get
            // it by sha512-hashing self::$seed. This produces 64 bytes of entropy,
            // which we need to split in a list of 24-bit numbers. unpack() first splits
            // them into 16 x 32-bit numbers; we take the first byte of each of these
            // numbers to get 5 extra 24-bit numbers. Then, we consume those numbers
            // one-by-one and run this logic every 21 iterations.
            // self::$rand holds the random part of the UUID, split into 2 x 32-bit numbers
            // or 4 x 16-bit for x86 portability. We increment this random part by the next
            // 24-bit number in the self::$seedParts list and decrement self::$seedIndex.

            if (!self::$seedIndex) {
                $s = unpack(\PHP_INT_SIZE >= 8 ? 'L*' : 'l*', self::$seed = hash('sha512', self::$seed, true));
                $s[] = ($s[1] >> 8 & 0xFF0000) | ($s[2] >> 16 & 0xFF00) | ($s[3] >> 24 & 0xFF);
                $s[] = ($s[4] >> 8 & 0xFF0000) | ($s[5] >> 16 & 0xFF00) | ($s[6] >> 24 & 0xFF);
                $s[] = ($s[7] >> 8 & 0xFF0000) | ($s[8] >> 16 & 0xFF00) | ($s[9] >> 24 & 0xFF);
                $s[] = ($s[10] >> 8 & 0xFF0000) | ($s[11] >> 16 & 0xFF00) | ($s[12] >> 24 & 0xFF);
                $s[] = ($s[13] >> 8 & 0xFF0000) | ($s[14] >> 16 & 0xFF00) | ($s[15] >> 24 & 0xFF);
                self::$seedParts = $s;
                self::$seedIndex = 21;
            }

            if (\PHP_INT_SIZE >= 8) {
                self::$rand[2] = 0xFFFFFFFF & $carry = self::$rand[2] + 1 + (self::$seedParts[self::$seedIndex--] & 0xFFFFFF);
                self::$rand[1] = 0xFFFFFFFF & $carry = self::$rand[1] + ($carry >> 32);
                $carry >>= 32;
            } else {
                self::$rand[4] = 0xFFFF & $carry = self::$rand[4] + 1 + (self::$seedParts[self::$seedIndex--] & 0xFFFFFF);
                self::$rand[3] = 0xFFFF & $carry = self::$rand[3] + ($carry >> 16);
                self::$rand[2] = 0xFFFF & $carry = self::$rand[2] + ($carry >> 16);
                self::$rand[1] = 0xFFFF & $carry = self::$rand[1] + ($carry >> 16);
                $carry >>= 16;
            }

            if ($carry && $subMs <= self::$subMs) {
                usleep(1);

                if (1024 <= ++$subMs) {
                    if (\PHP_INT_SIZE >= 8 || 10 > \strlen($time = self::$time)) {
                        $time = (string) (1 + $time);
                    } elseif ('999999999' === $mtime = substr($time, -9)) {
                        $time = (1 + substr($time, 0, -9)).'000000000';
                    } else {
                        $time = substr_replace($time, str_pad(++$mtime, 9, '0', \STR_PAD_LEFT), -9);
                    }

                    goto randomize;
                }
            }

            $time = self::$time;
        }
        self::$subMs = $subMs;

        if (\PHP_INT_SIZE >= 8) {
            return substr_replace(\sprintf('%012x-%04x-%04x-%04x%08x',
                $time,
                0x7000 | ($subMs << 2) | (self::$rand[1] >> 30),
                0x8000 | (self::$rand[1] >> 16 & 0x3FFF),
                self::$rand[1] & 0xFFFF,
                self::$rand[2],
            ), '-', 8, 0);
        }

        return substr_replace(\sprintf('%012s-%04x-%04x-%04x%04x%04x',
            bin2hex(BinaryUtil::fromBase($time, BinaryUtil::BASE10)),
            0x7000 | ($subMs << 2) | (self::$rand[1] >> 14),
            0x8000 | (self::$rand[1] & 0x3FFF),
            self::$rand[2],
            self::$rand[3],
            self::$rand[4],
        ), '-', 8, 0);
    }
}
