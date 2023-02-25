<?php

namespace Faker\Calculator;

/**
 * Utility class for validating ISBN-10
 */
class Isbn
{
    /**
     * @var string ISBN-10 validation pattern
     */
    public const PATTERN = '/^\d{9}[0-9X]$/';

    /**
     * ISBN-10 check digit
     *
     * @see http://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-10_check_digits
     *
     * @param string $input ISBN without check-digit
     *
     * @throws \LengthException When wrong input length passed
     */
    public static function checksum(string $input): string
    {
        // We're calculating check digit for ISBN-10
        // so, the length of the input should be 9
        $length = 9;

        if (strlen($input) !== $length) {
            throw new \LengthException(sprintf('Input length should be equal to %d', $length));
        }

        $digits = str_split($input);
        array_walk(
            $digits,
            static function (&$digit, $position) {
                $digit = (10 - $position) * $digit;
            }
        );
        $result = (11 - array_sum($digits) % 11) % 11;

        // 10 is replaced by X
        return ($result < 10) ? (string) $result : 'X';
    }

    /**
     * Checks whether the provided number is a valid ISBN-10 number
     *
     * @param string $isbn ISBN to check
     */
    public static function isValid(string $isbn): bool
    {
        if (!preg_match(self::PATTERN, $isbn)) {
            return false;
        }

        return self::checksum(substr($isbn, 0, -1)) === substr($isbn, -1);
    }
}
