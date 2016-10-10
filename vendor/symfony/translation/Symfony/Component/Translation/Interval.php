<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

/**
 * Tests if a given number belongs to a given math interval.
 *
 * An interval can represent a finite set of numbers:
 *
 *  {1,2,3,4}
 *
 * An interval can represent numbers between two numbers:
 *
 *  [1, +Inf]
 *  ]-1,2[
 *
 * The left delimiter can be [ (inclusive) or ] (exclusive).
 * The right delimiter can be [ (exclusive) or ] (inclusive).
 * Beside numbers, you can use -Inf and +Inf for the infinite.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see    http://en.wikipedia.org/wiki/Interval_%28mathematics%29#The_ISO_notation
 */
class Interval
{
    /**
     * Tests if the given number is in the math interval.
     *
     * @param int     $number   A number
     * @param string  $interval An interval
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public static function test($number, $interval)
    {
        $interval = trim($interval);

        if (!preg_match('/^'.self::getIntervalRegexp().'$/x', $interval, $matches)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid interval.', $interval));
        }

        if ($matches[1]) {
            foreach (explode(',', $matches[2]) as $n) {
                if ($number == $n) {
                    return true;
                }
            }
        } else {
            $leftNumber = self::convertNumber($matches['left']);
            $rightNumber = self::convertNumber($matches['right']);

            return
                ('[' === $matches['left_delimiter'] ? $number >= $leftNumber : $number > $leftNumber)
                && (']' === $matches['right_delimiter'] ? $number <= $rightNumber : $number < $rightNumber)
            ;
        }

        return false;
    }

    /**
     * Returns a Regexp that matches valid intervals.
     *
     * @return string A Regexp (without the delimiters)
     */
    public static function getIntervalRegexp()
    {
        return <<<EOF
        ({\s*
            (\-?\d+(\.\d+)?[\s*,\s*\-?\d+(\.\d+)?]*)
        \s*})

            |

        (?P<left_delimiter>[\[\]])
            \s*
            (?P<left>-Inf|\-?\d+(\.\d+)?)
            \s*,\s*
            (?P<right>\+?Inf|\-?\d+(\.\d+)?)
            \s*
        (?P<right_delimiter>[\[\]])
EOF;
    }

    private static function convertNumber($number)
    {
        if ('-Inf' === $number) {
            return log(0);
        } elseif ('+Inf' === $number || 'Inf' === $number) {
            return -log(0);
        }

        return (float) $number;
    }
}
