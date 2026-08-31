<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\PHP_VERSION_ID < 80100) {
    final class RoundingMode
    {
        const HalfAwayFromZero = 0;
        const HalfTowardsZero = 1;
        const HalfEven = 2;
        const HalfOdd = 3;
        const TowardsZero = 4;
        const AwayFromZero = 5;
        const NegativeInfinity = 6;
        const PositiveInfinity = 7;

        private function __construct()
        {
        }

        public static function cases(): array
        {
            return [
                self::HalfAwayFromZero,
                self::HalfTowardsZero,
                self::HalfEven,
                self::HalfOdd,
                self::TowardsZero,
                self::AwayFromZero,
                self::NegativeInfinity,
                self::PositiveInfinity,
            ];
        }
    }
} elseif (\PHP_VERSION_ID < 80400) {
    require dirname(__DIR__).'/RoundingMode.php';
}
