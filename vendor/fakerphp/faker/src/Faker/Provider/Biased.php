<?php

namespace Faker\Provider;

class Biased extends Base
{
    /**
     * Returns a biased integer between $min and $max (both inclusive).
     * The distribution depends on $function.
     *
     * The algorithm creates two doubles, x ∈ [0, 1], y ∈ [0, 1) and checks whether the
     * return value of $function for x is greater than or equal to y. If this is
     * the case the number is accepted and x is mapped to the appropriate integer
     * between $min and $max. Otherwise two new doubles are created until the pair
     * is accepted.
     *
     * @param int      $min      Minimum value of the generated integers.
     * @param int      $max      Maximum value of the generated integers.
     * @param callable $function A function mapping x ∈ [0, 1] onto a double ∈ [0, 1]
     *
     * @return int An integer between $min and $max.
     */
    public function biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt')
    {
        do {
            $x = mt_rand() / mt_getrandmax();
            $y = mt_rand() / (mt_getrandmax() + 1);
        } while (call_user_func($function, $x) < $y);

        return (int) floor($x * ($max - $min + 1) + $min);
    }

    /**
     * 'unbiased' creates an unbiased distribution by giving
     * each value the same value of one.
     *
     * @return int
     */
    protected static function unbiased()
    {
        return 1;
    }

    /**
     * 'linearLow' favors lower numbers. The probability decreases
     * in a linear fashion.
     *
     * @return int
     */
    protected static function linearLow($x)
    {
        return 1 - $x;
    }

    /**
     * 'linearHigh' favors higher numbers. The probability increases
     * in a linear fashion.
     *
     * @return int
     */
    protected static function linearHigh($x)
    {
        return $x;
    }
}
