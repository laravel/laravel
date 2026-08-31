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

namespace Ramsey\Uuid\Generator;

use Brick\Math\BigInteger;
use DateTimeInterface;
use Ramsey\Uuid\Type\Hexadecimal;

use function assert;
use function hash;
use function pack;
use function str_pad;
use function strlen;
use function substr;
use function substr_replace;
use function unpack;

use const PHP_INT_SIZE;
use const STR_PAD_LEFT;

/**
 * UnixTimeGenerator generates bytes, combining a 48-bit timestamp in milliseconds since the Unix Epoch with 80 random bits
 *
 * Code and concepts within this class are borrowed from the symfony/uid package and are used under the terms of the MIT
 * license distributed with symfony/uid.
 *
 * symfony/uid is copyright (c) Fabien Potencier.
 *
 * @link https://symfony.com/components/Uid Symfony Uid component
 * @link https://github.com/symfony/uid/blob/4f9f537e57261519808a7ce1d941490736522bbc/UuidV7.php Symfony UuidV7 class
 * @link https://github.com/symfony/uid/blob/6.2/LICENSE MIT License
 */
class UnixTimeGenerator implements TimeGeneratorInterface
{
    private static string $time = '';
    private static ?string $seed = null;
    private static int $seedIndex = 0;

    /** @var int[] */
    private static array $rand = [];

    /** @var int[] */
    private static array $seedParts;

    public function __construct(
        private RandomGeneratorInterface $randomGenerator,
        private int $intSize = PHP_INT_SIZE,
    ) {
    }

    /**
     * @param Hexadecimal | int | string | null $node Unused in this generator
     * @param int | null $clockSeq Unused in this generator
     * @param DateTimeInterface | null $dateTime A date-time instance to use when generating bytes
     */
    public function generate($node = null, ?int $clockSeq = null, ?DateTimeInterface $dateTime = null): string
    {
        if ($dateTime === null) {
            $time = microtime(false);
            $time = substr($time, 11) . substr($time, 2, 3);
        } else {
            $time = $dateTime->format('Uv');
        }

        if ($time > self::$time || ($dateTime !== null && $time !== self::$time)) {
            $this->randomize($time);
        } else {
            $time = $this->increment();
        }

        if ($this->intSize >= 8) {
            $time = substr(pack('J', (int) $time), -6);
        } else {
            $time = str_pad(BigInteger::of($time)->toBytes(false), 6, "\x00", STR_PAD_LEFT);
        }

        assert(strlen($time) === 6);

        return $time . pack('n*', self::$rand[1], self::$rand[2], self::$rand[3], self::$rand[4], self::$rand[5]);
    }

    private function randomize(string $time): void
    {
        if (self::$seed === null) {
            $seed = $this->randomGenerator->generate(16);
            self::$seed = $seed;
        } else {
            $seed = $this->randomGenerator->generate(10);
        }

        /** @var int[] $rand */
        $rand = unpack('n*', $seed);
        $rand[1] &= 0x03ff;

        self::$rand = $rand;
        self::$time = $time;
    }

    /**
     * Special thanks to Nicolas Grekas (<https://github.com/nicolas-grekas>) for sharing the following information:
     *
     * Within the same ms, we increment the rand part by a random 24-bit number.
     *
     * Instead of getting this number from random_bytes(), which is slow, we get it by sha512-hashing self::$seed. This
     * produces 64 bytes of entropy, which we need to split in a list of 24-bit numbers. `unpack()` first splits them
     * into 16 x 32-bit numbers; we take the first byte of each number to get 5 extra 24-bit numbers. Then, we consume
     * each number one-by-one and run this logic every 21 iterations.
     *
     * `self::$rand` holds the random part of the UUID, split into 5 x 16-bit numbers for x86 portability. We increment
     * this random part by the next 24-bit number in the `self::$seedParts` list and decrement `self::$seedIndex`.
     */
    private function increment(): string
    {
        if (self::$seedIndex === 0 && self::$seed !== null) {
            self::$seed = hash('sha512', self::$seed, true);

            /** @var int[] $s */
            $s = unpack('l*', self::$seed);
            $s[] = ($s[1] >> 8 & 0xff0000) | ($s[2] >> 16 & 0xff00) | ($s[3] >> 24 & 0xff);
            $s[] = ($s[4] >> 8 & 0xff0000) | ($s[5] >> 16 & 0xff00) | ($s[6] >> 24 & 0xff);
            $s[] = ($s[7] >> 8 & 0xff0000) | ($s[8] >> 16 & 0xff00) | ($s[9] >> 24 & 0xff);
            $s[] = ($s[10] >> 8 & 0xff0000) | ($s[11] >> 16 & 0xff00) | ($s[12] >> 24 & 0xff);
            $s[] = ($s[13] >> 8 & 0xff0000) | ($s[14] >> 16 & 0xff00) | ($s[15] >> 24 & 0xff);

            self::$seedParts = $s;
            self::$seedIndex = 21;
        }

        self::$rand[5] = 0xffff & $carry = self::$rand[5] + 1 + (self::$seedParts[self::$seedIndex--] & 0xffffff);
        self::$rand[4] = 0xffff & $carry = self::$rand[4] + ($carry >> 16);
        self::$rand[3] = 0xffff & $carry = self::$rand[3] + ($carry >> 16);
        self::$rand[2] = 0xffff & $carry = self::$rand[2] + ($carry >> 16);
        self::$rand[1] += $carry >> 16;

        if (0xfc00 & self::$rand[1]) {
            $time = self::$time;
            $mtime = (int) substr($time, -9);

            if ($this->intSize >= 8 || strlen($time) < 10) {
                $time = (string) ((int) $time + 1);
            } elseif ($mtime === 999999999) {
                $time = (1 + (int) substr($time, 0, -9)) . '000000000';
            } else {
                $mtime++;
                $time = substr_replace($time, str_pad((string) $mtime, 9, '0', STR_PAD_LEFT), -9);
            }

            $this->randomize($time);
        }

        return self::$time;
    }
}
