<?php

namespace Faker\Calculator;

/**
 * Utility class for validating EAN-8 and EAN-13 numbers
 */
class Ean
{
    /**
     * @var string EAN validation pattern
     */
    public const PATTERN = '/^(?:\d{8}|\d{13})$/';

    /**
     * Computes the checksum of an EAN number.
     *
     * @see https://en.wikipedia.org/wiki/International_Article_Number
     *
     * @return int
     */
    public static function checksum(string $digits)
    {
        $sequence = (strlen($digits) + 1) === 8 ? [3, 1] : [1, 3];
        $sums = 0;

        foreach (str_split($digits) as $n => $digit) {
            $sums += ((int) $digit) * $sequence[$n % 2];
        }

        return (10 - $sums % 10) % 10;
    }

    /**
     * Checks whether the provided number is an EAN compliant number and that
     * the checksum is correct.
     *
     * @param string $ean An EAN number
     *
     * @return bool
     */
    public static function isValid(string $ean)
    {
        if (!preg_match(self::PATTERN, $ean)) {
            return false;
        }

        return self::checksum(substr($ean, 0, -1)) === (int) substr($ean, -1);
    }
}
