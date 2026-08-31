<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\IPv4;

interface Calculator
{
    /**
     * Add numbers.
     *
     * @param mixed $value1 a number that will be added to $value2
     * @param mixed $value2 a number that will be added to $value1
     *
     * @return mixed the addition result
     */
    public function add(mixed $value1, mixed $value2);

    /**
     * Subtract one number from another.
     *
     * @param mixed $value1 a number that will be subtracted of $value2
     * @param mixed $value2 a number that will be subtracted to $value1
     *
     * @return mixed the subtraction result
     */
    public function sub(mixed $value1, mixed $value2);

    /**
     * Multiply numbers.
     *
     * @param mixed $value1 a number that will be multiplied by $value2
     * @param mixed $value2 a number that will be multiplied by $value1
     *
     * @return mixed the multiplication result
     */
    public function multiply(mixed $value1, mixed $value2);

    /**
     * Divide numbers.
     *
     * @param mixed $value The number being divided.
     * @param mixed $base The number that $value is being divided by.
     *
     * @return mixed the result of the division
     */
    public function div(mixed $value, mixed $base);

    /**
     * Raise an number to the power of exponent.
     *
     * @param mixed $value scalar, the base to use
     *
     * @return mixed the value raised to the power of exp.
     */
    public function pow(mixed $value, int $exponent);

    /**
     * Returns the int point remainder (modulo) of the division of the arguments.
     *
     * @param mixed $value The dividend
     * @param mixed $base The divisor
     *
     * @return mixed the remainder
     */
    public function mod(mixed $value, mixed $base);

    /**
     * Number comparison.
     *
     * @param mixed $value1 the first value
     * @param mixed $value2 the second value
     *
     * @return int Returns < 0 if value1 is less than value2; > 0 if value1 is greater than value2, and 0 if they are equal.
     */
    public function compare(mixed $value1, mixed $value2): int;

    /**
     * Get the decimal integer value of a variable.
     *
     * @param mixed $value The scalar value being converted to an integer
     *
     * @return mixed the integer value
     */
    public function baseConvert(mixed $value, int $base);
}
