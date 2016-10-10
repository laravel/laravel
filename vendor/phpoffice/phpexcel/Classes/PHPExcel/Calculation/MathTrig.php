<?php

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Calculation_MathTrig
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category    PHPExcel
 * @package        PHPExcel_Calculation
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version        ##VERSION##, ##DATE##
 */
class PHPExcel_Calculation_MathTrig
{
    //
    //    Private method to return an array of the factors of the input value
    //
    private static function factors($value)
    {
        $startVal = floor(sqrt($value));

        $factorArray = array();
        for ($i = $startVal; $i > 1; --$i) {
            if (($value % $i) == 0) {
                $factorArray = array_merge($factorArray, self::factors($value / $i));
                $factorArray = array_merge($factorArray, self::factors($i));
                if ($i <= sqrt($value)) {
                    break;
                }
            }
        }
        if (!empty($factorArray)) {
            rsort($factorArray);
            return $factorArray;
        } else {
            return array((integer) $value);
        }
    }


    private static function romanCut($num, $n)
    {
        return ($num - ($num % $n ) ) / $n;
    }


    /**
     * ATAN2
     *
     * This function calculates the arc tangent of the two variables x and y. It is similar to
     *        calculating the arc tangent of y ÷ x, except that the signs of both arguments are used
     *        to determine the quadrant of the result.
     * The arctangent is the angle from the x-axis to a line containing the origin (0, 0) and a
     *        point with coordinates (xCoordinate, yCoordinate). The angle is given in radians between
     *        -pi and pi, excluding -pi.
     *
     * Note that the Excel ATAN2() function accepts its arguments in the reverse order to the standard
     *        PHP atan2() function, so we need to reverse them here before calling the PHP atan() function.
     *
     * Excel Function:
     *        ATAN2(xCoordinate,yCoordinate)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $xCoordinate        The x-coordinate of the point.
     * @param    float    $yCoordinate        The y-coordinate of the point.
     * @return    float    The inverse tangent of the specified x- and y-coordinates.
     */
    public static function ATAN2($xCoordinate = null, $yCoordinate = null)
    {
        $xCoordinate = PHPExcel_Calculation_Functions::flattenSingleValue($xCoordinate);
        $yCoordinate = PHPExcel_Calculation_Functions::flattenSingleValue($yCoordinate);

        $xCoordinate = ($xCoordinate !== null) ? $xCoordinate : 0.0;
        $yCoordinate = ($yCoordinate !== null) ? $yCoordinate : 0.0;

        if (((is_numeric($xCoordinate)) || (is_bool($xCoordinate))) &&
            ((is_numeric($yCoordinate)))  || (is_bool($yCoordinate))) {
            $xCoordinate    = (float) $xCoordinate;
            $yCoordinate    = (float) $yCoordinate;

            if (($xCoordinate == 0) && ($yCoordinate == 0)) {
                return PHPExcel_Calculation_Functions::DIV0();
            }

            return atan2($yCoordinate, $xCoordinate);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * CEILING
     *
     * Returns number rounded up, away from zero, to the nearest multiple of significance.
     *        For example, if you want to avoid using pennies in your prices and your product is
     *        priced at $4.42, use the formula =CEILING(4.42,0.05) to round prices up to the
     *        nearest nickel.
     *
     * Excel Function:
     *        CEILING(number[,significance])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $number            The number you want to round.
     * @param    float    $significance    The multiple to which you want to round.
     * @return    float    Rounded Number
     */
    public static function CEILING($number, $significance = null)
    {
        $number       = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $significance = PHPExcel_Calculation_Functions::flattenSingleValue($significance);

        if ((is_null($significance)) &&
            (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC)) {
            $significance = $number / abs($number);
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if (($number == 0.0 ) || ($significance == 0.0)) {
                return 0.0;
            } elseif (self::SIGN($number) == self::SIGN($significance)) {
                return ceil($number / $significance) * $significance;
            } else {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * COMBIN
     *
     * Returns the number of combinations for a given number of items. Use COMBIN to
     *        determine the total possible number of groups for a given number of items.
     *
     * Excel Function:
     *        COMBIN(numObjs,numInSet)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    int        $numObjs    Number of different objects
     * @param    int        $numInSet    Number of objects in each combination
     * @return    int        Number of combinations
     */
    public static function COMBIN($numObjs, $numInSet)
    {
        $numObjs    = PHPExcel_Calculation_Functions::flattenSingleValue($numObjs);
        $numInSet    = PHPExcel_Calculation_Functions::flattenSingleValue($numInSet);

        if ((is_numeric($numObjs)) && (is_numeric($numInSet))) {
            if ($numObjs < $numInSet) {
                return PHPExcel_Calculation_Functions::NaN();
            } elseif ($numInSet < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return round(self::FACT($numObjs) / self::FACT($numObjs - $numInSet)) / self::FACT($numInSet);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * EVEN
     *
     * Returns number rounded up to the nearest even integer.
     * You can use this function for processing items that come in twos. For example,
     *        a packing crate accepts rows of one or two items. The crate is full when
     *        the number of items, rounded up to the nearest two, matches the crate's
     *        capacity.
     *
     * Excel Function:
     *        EVEN(number)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $number            Number to round
     * @return    int        Rounded Number
     */
    public static function EVEN($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);

        if (is_null($number)) {
            return 0;
        } elseif (is_bool($number)) {
            $number = (int) $number;
        }

        if (is_numeric($number)) {
            $significance = 2 * self::SIGN($number);
            return (int) self::CEILING($number, $significance);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * FACT
     *
     * Returns the factorial of a number.
     * The factorial of a number is equal to 1*2*3*...* number.
     *
     * Excel Function:
     *        FACT(factVal)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $factVal    Factorial Value
     * @return    int        Factorial
     */
    public static function FACT($factVal)
    {
        $factVal    = PHPExcel_Calculation_Functions::flattenSingleValue($factVal);

        if (is_numeric($factVal)) {
            if ($factVal < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $factLoop = floor($factVal);
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                if ($factVal > $factLoop) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }

            $factorial = 1;
            while ($factLoop > 1) {
                $factorial *= $factLoop--;
            }
            return $factorial ;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * FACTDOUBLE
     *
     * Returns the double factorial of a number.
     *
     * Excel Function:
     *        FACTDOUBLE(factVal)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $factVal    Factorial Value
     * @return    int        Double Factorial
     */
    public static function FACTDOUBLE($factVal)
    {
        $factLoop    = PHPExcel_Calculation_Functions::flattenSingleValue($factVal);

        if (is_numeric($factLoop)) {
            $factLoop    = floor($factLoop);
            if ($factVal < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $factorial = 1;
            while ($factLoop > 1) {
                $factorial *= $factLoop--;
                --$factLoop;
            }
            return $factorial ;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * FLOOR
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR(number[,significance])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $number            Number to round
     * @param    float    $significance    Significance
     * @return    float    Rounded Number
     */
    public static function FLOOR($number, $significance = null)
    {
        $number            = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $significance    = PHPExcel_Calculation_Functions::flattenSingleValue($significance);

        if ((is_null($significance)) &&
            (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC)) {
            $significance = $number/abs($number);
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if ($significance == 0.0) {
                return PHPExcel_Calculation_Functions::DIV0();
            } elseif ($number == 0.0) {
                return 0.0;
            } elseif (self::SIGN($number) == self::SIGN($significance)) {
                return floor($number / $significance) * $significance;
            } else {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }

        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * GCD
     *
     * Returns the greatest common divisor of a series of numbers.
     * The greatest common divisor is the largest integer that divides both
     *        number1 and number2 without a remainder.
     *
     * Excel Function:
     *        GCD(number1[,number2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed    $arg,...        Data values
     * @return    integer                    Greatest Common Divisor
     */
    public static function GCD()
    {
        $returnValue = 1;
        $allValuesFactors = array();
        // Loop through arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $value) {
            if (!is_numeric($value)) {
                return PHPExcel_Calculation_Functions::VALUE();
            } elseif ($value == 0) {
                continue;
            } elseif ($value < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $myFactors = self::factors($value);
            $myCountedFactors = array_count_values($myFactors);
            $allValuesFactors[] = $myCountedFactors;
        }
        $allValuesCount = count($allValuesFactors);
        if ($allValuesCount == 0) {
            return 0;
        }

        $mergedArray = $allValuesFactors[0];
        for ($i=1; $i < $allValuesCount; ++$i) {
            $mergedArray = array_intersect_key($mergedArray, $allValuesFactors[$i]);
        }
        $mergedArrayValues = count($mergedArray);
        if ($mergedArrayValues == 0) {
            return $returnValue;
        } elseif ($mergedArrayValues > 1) {
            foreach ($mergedArray as $mergedKey => $mergedValue) {
                foreach ($allValuesFactors as $highestPowerTest) {
                    foreach ($highestPowerTest as $testKey => $testValue) {
                        if (($testKey == $mergedKey) && ($testValue < $mergedValue)) {
                            $mergedArray[$mergedKey] = $testValue;
                            $mergedValue = $testValue;
                        }
                    }
                }
            }

            $returnValue = 1;
            foreach ($mergedArray as $key => $value) {
                $returnValue *= pow($key, $value);
            }
            return $returnValue;
        } else {
            $keys = array_keys($mergedArray);
            $key = $keys[0];
            $value = $mergedArray[$key];
            foreach ($allValuesFactors as $testValue) {
                foreach ($testValue as $mergedKey => $mergedValue) {
                    if (($mergedKey == $key) && ($mergedValue < $value)) {
                        $value = $mergedValue;
                    }
                }
            }
            return pow($key, $value);
        }
    }


    /**
     * INT
     *
     * Casts a floating point value to an integer
     *
     * Excel Function:
     *        INT(number)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $number            Number to cast to an integer
     * @return    integer    Integer value
     */
    public static function INT($number)
    {
        $number    = PHPExcel_Calculation_Functions::flattenSingleValue($number);

        if (is_null($number)) {
            return 0;
        } elseif (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            return (int) floor($number);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * LCM
     *
     * Returns the lowest common multiplier of a series of numbers
     * The least common multiple is the smallest positive integer that is a multiple
     * of all integer arguments number1, number2, and so on. Use LCM to add fractions
     * with different denominators.
     *
     * Excel Function:
     *        LCM(number1[,number2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed    $arg,...        Data values
     * @return    int        Lowest Common Multiplier
     */
    public static function LCM()
    {
        $returnValue = 1;
        $allPoweredFactors = array();
        // Loop through arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $value) {
            if (!is_numeric($value)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            if ($value == 0) {
                return 0;
            } elseif ($value < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $myFactors = self::factors(floor($value));
            $myCountedFactors = array_count_values($myFactors);
            $myPoweredFactors = array();
            foreach ($myCountedFactors as $myCountedFactor => $myCountedPower) {
                $myPoweredFactors[$myCountedFactor] = pow($myCountedFactor, $myCountedPower);
            }
            foreach ($myPoweredFactors as $myPoweredValue => $myPoweredFactor) {
                if (array_key_exists($myPoweredValue, $allPoweredFactors)) {
                    if ($allPoweredFactors[$myPoweredValue] < $myPoweredFactor) {
                        $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                    }
                } else {
                    $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                }
            }
        }
        foreach ($allPoweredFactors as $allPoweredFactor) {
            $returnValue *= (integer) $allPoweredFactor;
        }
        return $returnValue;
    }


    /**
     * LOG_BASE
     *
     * Returns the logarithm of a number to a specified base. The default base is 10.
     *
     * Excel Function:
     *        LOG(number[,base])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    float    $number        The positive real number for which you want the logarithm
     * @param    float    $base        The base of the logarithm. If base is omitted, it is assumed to be 10.
     * @return    float
     */
    public static function LOG_BASE($number = null, $base = 10)
    {
        $number    = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $base    = (is_null($base)) ? 10 : (float) PHPExcel_Calculation_Functions::flattenSingleValue($base);

        if ((!is_numeric($base)) || (!is_numeric($number))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (($base <= 0) || ($number <= 0)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return log($number, $base);
    }


    /**
     * MDETERM
     *
     * Returns the matrix determinant of an array.
     *
     * Excel Function:
     *        MDETERM(array)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    array    $matrixValues    A matrix of values
     * @return    float
     */
    public static function MDETERM($matrixValues)
    {
        $matrixData = array();
        if (!is_array($matrixValues)) {
            $matrixValues = array(array($matrixValues));
        }

        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = array($matrixRow);
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if ((is_string($matrixCell)) || ($matrixCell === null)) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $matrixData[$column][$row] = $matrixCell;
                ++$column;
            }
            if ($column > $maxColumn) {
                $maxColumn = $column;
            }
            ++$row;
        }
        if ($row != $maxColumn) {
            return PHPExcel_Calculation_Functions::VALUE();
        }

        try {
            $matrix = new PHPExcel_Shared_JAMA_Matrix($matrixData);
            return $matrix->det();
        } catch (PHPExcel_Exception $ex) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
    }


    /**
     * MINVERSE
     *
     * Returns the inverse matrix for the matrix stored in an array.
     *
     * Excel Function:
     *        MINVERSE(array)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    array    $matrixValues    A matrix of values
     * @return    array
     */
    public static function MINVERSE($matrixValues)
    {
        $matrixData = array();
        if (!is_array($matrixValues)) {
            $matrixValues = array(array($matrixValues));
        }

        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = array($matrixRow);
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if ((is_string($matrixCell)) || ($matrixCell === null)) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $matrixData[$column][$row] = $matrixCell;
                ++$column;
            }
            if ($column > $maxColumn) {
                $maxColumn = $column;
            }
            ++$row;
        }
        if ($row != $maxColumn) {
            return PHPExcel_Calculation_Functions::VALUE();
        }

        try {
            $matrix = new PHPExcel_Shared_JAMA_Matrix($matrixData);
            return $matrix->inverse()->getArray();
        } catch (PHPExcel_Exception $ex) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
    }


    /**
     * MMULT
     *
     * @param    array    $matrixData1    A matrix of values
     * @param    array    $matrixData2    A matrix of values
     * @return    array
     */
    public static function MMULT($matrixData1, $matrixData2)
    {
        $matrixAData = $matrixBData = array();
        if (!is_array($matrixData1)) {
            $matrixData1 = array(array($matrixData1));
        }
        if (!is_array($matrixData2)) {
            $matrixData2 = array(array($matrixData2));
        }

        try {
            $rowA = 0;
            foreach ($matrixData1 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = array($matrixRow);
                }
                $columnA = 0;
                foreach ($matrixRow as $matrixCell) {
                    if ((!is_numeric($matrixCell)) || ($matrixCell === null)) {
                        return PHPExcel_Calculation_Functions::VALUE();
                    }
                    $matrixAData[$rowA][$columnA] = $matrixCell;
                    ++$columnA;
                }
                ++$rowA;
            }
            $matrixA = new PHPExcel_Shared_JAMA_Matrix($matrixAData);
            $rowB = 0;
            foreach ($matrixData2 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = array($matrixRow);
                }
                $columnB = 0;
                foreach ($matrixRow as $matrixCell) {
                    if ((!is_numeric($matrixCell)) || ($matrixCell === null)) {
                        return PHPExcel_Calculation_Functions::VALUE();
                    }
                    $matrixBData[$rowB][$columnB] = $matrixCell;
                    ++$columnB;
                }
                ++$rowB;
            }
            $matrixB = new PHPExcel_Shared_JAMA_Matrix($matrixBData);

            if ($columnA != $rowB) {
                return PHPExcel_Calculation_Functions::VALUE();
            }

            return $matrixA->times($matrixB)->getArray();
        } catch (PHPExcel_Exception $ex) {
            var_dump($ex->getMessage());
            return PHPExcel_Calculation_Functions::VALUE();
        }
    }


    /**
     * MOD
     *
     * @param    int        $a        Dividend
     * @param    int        $b        Divisor
     * @return    int        Remainder
     */
    public static function MOD($a = 1, $b = 1)
    {
        $a = PHPExcel_Calculation_Functions::flattenSingleValue($a);
        $b = PHPExcel_Calculation_Functions::flattenSingleValue($b);

        if ($b == 0.0) {
            return PHPExcel_Calculation_Functions::DIV0();
        } elseif (($a < 0.0) && ($b > 0.0)) {
            return $b - fmod(abs($a), $b);
        } elseif (($a > 0.0) && ($b < 0.0)) {
            return $b + fmod($a, abs($b));
        }

        return fmod($a, $b);
    }


    /**
     * MROUND
     *
     * Rounds a number to the nearest multiple of a specified value
     *
     * @param    float    $number            Number to round
     * @param    int        $multiple        Multiple to which you want to round $number
     * @return    float    Rounded Number
     */
    public static function MROUND($number, $multiple)
    {
        $number   = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $multiple = PHPExcel_Calculation_Functions::flattenSingleValue($multiple);

        if ((is_numeric($number)) && (is_numeric($multiple))) {
            if ($multiple == 0) {
                return 0;
            }
            if ((self::SIGN($number)) == (self::SIGN($multiple))) {
                $multiplier = 1 / $multiple;
                return round($number * $multiplier) / $multiplier;
            }
            return PHPExcel_Calculation_Functions::NaN();
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * MULTINOMIAL
     *
     * Returns the ratio of the factorial of a sum of values to the product of factorials.
     *
     * @param    array of mixed        Data Series
     * @return    float
     */
    public static function MULTINOMIAL()
    {
        $summer = 0;
        $divisor = 1;
        // Loop through arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                if ($arg < 1) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
                $summer += floor($arg);
                $divisor *= self::FACT($arg);
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }

        // Return
        if ($summer > 0) {
            $summer = self::FACT($summer);
            return $summer / $divisor;
        }
        return 0;
    }


    /**
     * ODD
     *
     * Returns number rounded up to the nearest odd integer.
     *
     * @param    float    $number            Number to round
     * @return    int        Rounded Number
     */
    public static function ODD($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);

        if (is_null($number)) {
            return 1;
        } elseif (is_bool($number)) {
            return 1;
        } elseif (is_numeric($number)) {
            $significance = self::SIGN($number);
            if ($significance == 0) {
                return 1;
            }

            $result = self::CEILING($number, $significance);
            if ($result == self::EVEN($result)) {
                $result += $significance;
            }

            return (int) $result;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * POWER
     *
     * Computes x raised to the power y.
     *
     * @param    float        $x
     * @param    float        $y
     * @return    float
     */
    public static function POWER($x = 0, $y = 2)
    {
        $x    = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $y    = PHPExcel_Calculation_Functions::flattenSingleValue($y);

        // Validate parameters
        if ($x == 0.0 && $y == 0.0) {
            return PHPExcel_Calculation_Functions::NaN();
        } elseif ($x == 0.0 && $y < 0.0) {
            return PHPExcel_Calculation_Functions::DIV0();
        }

        // Return
        $result = pow($x, $y);
        return (!is_nan($result) && !is_infinite($result)) ? $result : PHPExcel_Calculation_Functions::NaN();
    }


    /**
     * PRODUCT
     *
     * PRODUCT returns the product of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed        $arg,...        Data values
     * @return    float
     */
    public static function PRODUCT()
    {
        // Return value
        $returnValue = null;

        // Loop through arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if (is_null($returnValue)) {
                    $returnValue = $arg;
                } else {
                    $returnValue *= $arg;
                }
            }
        }

        // Return
        if (is_null($returnValue)) {
            return 0;
        }
        return $returnValue;
    }


    /**
     * QUOTIENT
     *
     * QUOTIENT function returns the integer portion of a division. Numerator is the divided number
     *        and denominator is the divisor.
     *
     * Excel Function:
     *        QUOTIENT(value1[,value2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed        $arg,...        Data values
     * @return    float
     */
    public static function QUOTIENT()
    {
        // Return value
        $returnValue = null;

        // Loop through arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if (is_null($returnValue)) {
                    $returnValue = ($arg == 0) ? 0 : $arg;
                } else {
                    if (($returnValue == 0) || ($arg == 0)) {
                        $returnValue = 0;
                    } else {
                        $returnValue /= $arg;
                    }
                }
            }
        }

        // Return
        return intval($returnValue);
    }


    /**
     * RAND
     *
     * @param    int        $min    Minimal value
     * @param    int        $max    Maximal value
     * @return    int        Random number
     */
    public static function RAND($min = 0, $max = 0)
    {
        $min = PHPExcel_Calculation_Functions::flattenSingleValue($min);
        $max = PHPExcel_Calculation_Functions::flattenSingleValue($max);

        if ($min == 0 && $max == 0) {
            return (mt_rand(0, 10000000)) / 10000000;
        } else {
            return mt_rand($min, $max);
        }
    }


    public static function ROMAN($aValue, $style = 0)
    {
        $aValue    = PHPExcel_Calculation_Functions::flattenSingleValue($aValue);
        $style    = (is_null($style))    ? 0 :    (integer) PHPExcel_Calculation_Functions::flattenSingleValue($style);
        if ((!is_numeric($aValue)) || ($aValue < 0) || ($aValue >= 4000)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $aValue = (integer) $aValue;
        if ($aValue == 0) {
            return '';
        }

        $mill = array('', 'M', 'MM', 'MMM', 'MMMM', 'MMMMM');
        $cent = array('', 'C', 'CC', 'CCC', 'CD', 'D', 'DC', 'DCC', 'DCCC', 'CM');
        $tens = array('', 'X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC');
        $ones = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX');

        $roman = '';
        while ($aValue > 5999) {
            $roman .= 'M';
            $aValue -= 1000;
        }
        $m = self::romanCut($aValue, 1000);
        $aValue %= 1000;
        $c = self::romanCut($aValue, 100);
        $aValue %= 100;
        $t = self::romanCut($aValue, 10);
        $aValue %= 10;

        return $roman.$mill[$m].$cent[$c].$tens[$t].$ones[$aValue];
    }


    /**
     * ROUNDUP
     *
     * Rounds a number up to a specified number of decimal places
     *
     * @param    float    $number            Number to round
     * @param    int        $digits            Number of digits to which you want to round $number
     * @return    float    Rounded Number
     */
    public static function ROUNDUP($number, $digits)
    {
        $number    = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $digits    = PHPExcel_Calculation_Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            $significance = pow(10, (int) $digits);
            if ($number < 0.0) {
                return floor($number * $significance) / $significance;
            } else {
                return ceil($number * $significance) / $significance;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * ROUNDDOWN
     *
     * Rounds a number down to a specified number of decimal places
     *
     * @param    float    $number            Number to round
     * @param    int        $digits            Number of digits to which you want to round $number
     * @return    float    Rounded Number
     */
    public static function ROUNDDOWN($number, $digits)
    {
        $number    = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $digits    = PHPExcel_Calculation_Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            $significance = pow(10, (int) $digits);
            if ($number < 0.0) {
                return ceil($number * $significance) / $significance;
            } else {
                return floor($number * $significance) / $significance;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * SERIESSUM
     *
     * Returns the sum of a power series
     *
     * @param    float            $x    Input value to the power series
     * @param    float            $n    Initial power to which you want to raise $x
     * @param    float            $m    Step by which to increase $n for each term in the series
     * @param    array of mixed        Data Series
     * @return    float
     */
    public static function SERIESSUM()
    {
        $returnValue = 0;

        // Loop through arguments
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

        $x = array_shift($aArgs);
        $n = array_shift($aArgs);
        $m = array_shift($aArgs);

        if ((is_numeric($x)) && (is_numeric($n)) && (is_numeric($m))) {
            // Calculate
            $i = 0;
            foreach ($aArgs as $arg) {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $returnValue += $arg * pow($x, $n + ($m * $i++));
                } else {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
            return $returnValue;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * SIGN
     *
     * Determines the sign of a number. Returns 1 if the number is positive, zero (0)
     *        if the number is 0, and -1 if the number is negative.
     *
     * @param    float    $number            Number to round
     * @return    int        sign value
     */
    public static function SIGN($number)
    {
        $number    = PHPExcel_Calculation_Functions::flattenSingleValue($number);

        if (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            if ($number == 0.0) {
                return 0;
            }
            return $number / abs($number);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * SQRTPI
     *
     * Returns the square root of (number * pi).
     *
     * @param    float    $number        Number
     * @return    float    Square Root of Number * Pi
     */
    public static function SQRTPI($number)
    {
        $number    = PHPExcel_Calculation_Functions::flattenSingleValue($number);

        if (is_numeric($number)) {
            if ($number < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return sqrt($number * M_PI) ;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * SUBTOTAL
     *
     * Returns a subtotal in a list or database.
     *
     * @param    int        the number 1 to 11 that specifies which function to
     *                    use in calculating subtotals within a list.
     * @param    array of mixed        Data Series
     * @return    float
     */
    public static function SUBTOTAL()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

        // Calculate
        $subtotal = array_shift($aArgs);

        if ((is_numeric($subtotal)) && (!is_string($subtotal))) {
            switch ($subtotal) {
                case 1:
                    return PHPExcel_Calculation_Statistical::AVERAGE($aArgs);
                case 2:
                    return PHPExcel_Calculation_Statistical::COUNT($aArgs);
                case 3:
                    return PHPExcel_Calculation_Statistical::COUNTA($aArgs);
                case 4:
                    return PHPExcel_Calculation_Statistical::MAX($aArgs);
                case 5:
                    return PHPExcel_Calculation_Statistical::MIN($aArgs);
                case 6:
                    return self::PRODUCT($aArgs);
                case 7:
                    return PHPExcel_Calculation_Statistical::STDEV($aArgs);
                case 8:
                    return PHPExcel_Calculation_Statistical::STDEVP($aArgs);
                case 9:
                    return self::SUM($aArgs);
                case 10:
                    return PHPExcel_Calculation_Statistical::VARFunc($aArgs);
                case 11:
                    return PHPExcel_Calculation_Statistical::VARP($aArgs);
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }


    /**
     * SUM
     *
     * SUM computes the sum of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        SUM(value1[,value2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed        $arg,...        Data values
     * @return    float
     */
    public static function SUM()
    {
        $returnValue = 0;

        // Loop through the arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += $arg;
            }
        }

        return $returnValue;
    }


    /**
     * SUMIF
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        SUMIF(value1[,value2[, ...]],condition)
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed        $arg,...        Data values
     * @param    string        $condition        The criteria that defines which cells will be summed.
     * @return    float
     */
    public static function SUMIF($aArgs, $condition, $sumArgs = array())
    {
        $returnValue = 0;

        $aArgs = PHPExcel_Calculation_Functions::flattenArray($aArgs);
        $sumArgs = PHPExcel_Calculation_Functions::flattenArray($sumArgs);
        if (empty($sumArgs)) {
            $sumArgs = $aArgs;
        }
        $condition = PHPExcel_Calculation_Functions::ifCondition($condition);
        // Loop through arguments
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = str_replace('"', '""', $arg);
                $arg = PHPExcel_Calculation::wrapResult(strtoupper($arg));
            }

            $testCondition = '='.$arg.$condition;
            if (PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                // Is it a value within our criteria
                $returnValue += $sumArgs[$key];
            }
        }

        return $returnValue;
    }


 	/**
	 *	SUMIFS
	 *
	 *	Counts the number of cells that contain numbers within the list of arguments
	 *
	 *	Excel Function:
	 *		SUMIFS(value1[,value2[, ...]],condition)
	 *
	 *	@access	public
	 *	@category Mathematical and Trigonometric Functions
	 *	@param	mixed		$arg,...		Data values
	 *	@param	string		$condition		The criteria that defines which cells will be summed.
	 *	@return	float
	 */
	public static function SUMIFS() {
		$arrayList = func_get_args();

		$sumArgs = PHPExcel_Calculation_Functions::flattenArray(array_shift($arrayList));

        while (count($arrayList) > 0) {
            $aArgsArray[] = PHPExcel_Calculation_Functions::flattenArray(array_shift($arrayList));
            $conditions[] = PHPExcel_Calculation_Functions::ifCondition(array_shift($arrayList));
        }

        // Loop through each set of arguments and conditions
        foreach ($conditions as $index => $condition) {
            $aArgs = $aArgsArray[$index];
            $wildcard = false;
            if ((strpos($condition, '*') !== false) || (strpos($condition, '?') !== false)) {
                // * and ? are wildcard characters.
                // Use ~* and ~? for literal star and question mark
                // Code logic doesn't yet handle escaping
                $condition = trim(ltrim($condition, '=<>'), '"');
                $wildcard = true;
            }
            // Loop through arguments
            foreach ($aArgs as $key => $arg) {
                if ($wildcard) {
                    if (!fnmatch($condition, $arg, FNM_CASEFOLD)) {
                        // Is it a value within our criteria
                        $sumArgs[$key] = 0.0;
                    }
                } else {
                    if (!is_numeric($arg)) {
                        $arg = PHPExcel_Calculation::wrapResult(strtoupper($arg));
                    }
                    $testCondition = '='.$arg.$condition;
                    if (!PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                        // Is it a value within our criteria
                        $sumArgs[$key] = 0.0;
                    }
                }
            }
        }

		// Return
		return array_sum($sumArgs);
	}


    /**
     * SUMPRODUCT
     *
     * Excel Function:
     *        SUMPRODUCT(value1[,value2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed        $arg,...        Data values
     * @return    float
     */
    public static function SUMPRODUCT()
    {
        $arrayList = func_get_args();

        $wrkArray = PHPExcel_Calculation_Functions::flattenArray(array_shift($arrayList));
        $wrkCellCount = count($wrkArray);

        for ($i=0; $i< $wrkCellCount; ++$i) {
            if ((!is_numeric($wrkArray[$i])) || (is_string($wrkArray[$i]))) {
                $wrkArray[$i] = 0;
            }
        }

        foreach ($arrayList as $matrixData) {
            $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData);
            $count = count($array2);
            if ($wrkCellCount != $count) {
                return PHPExcel_Calculation_Functions::VALUE();
            }

            foreach ($array2 as $i => $val) {
                if ((!is_numeric($val)) || (is_string($val))) {
                    $val = 0;
                }
                $wrkArray[$i] *= $val;
            }
        }

        return array_sum($wrkArray);
    }


    /**
     * SUMSQ
     *
     * SUMSQ returns the sum of the squares of the arguments
     *
     * Excel Function:
     *        SUMSQ(value1[,value2[, ...]])
     *
     * @access    public
     * @category Mathematical and Trigonometric Functions
     * @param    mixed        $arg,...        Data values
     * @return    float
     */
    public static function SUMSQ()
    {
        $returnValue = 0;

        // Loop through arguments
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += ($arg * $arg);
            }
        }

        return $returnValue;
    }


    /**
     * SUMX2MY2
     *
     * @param    mixed[]    $matrixData1    Matrix #1
     * @param    mixed[]    $matrixData2    Matrix #2
     * @return    float
     */
    public static function SUMX2MY2($matrixData1, $matrixData2)
    {
        $array1 = PHPExcel_Calculation_Functions::flattenArray($matrixData1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))) {
                $result += ($array1[$i] * $array1[$i]) - ($array2[$i] * $array2[$i]);
            }
        }

        return $result;
    }


    /**
     * SUMX2PY2
     *
     * @param    mixed[]    $matrixData1    Matrix #1
     * @param    mixed[]    $matrixData2    Matrix #2
     * @return    float
     */
    public static function SUMX2PY2($matrixData1, $matrixData2)
    {
        $array1 = PHPExcel_Calculation_Functions::flattenArray($matrixData1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))) {
                $result += ($array1[$i] * $array1[$i]) + ($array2[$i] * $array2[$i]);
            }
        }

        return $result;
    }


    /**
     * SUMXMY2
     *
     * @param    mixed[]    $matrixData1    Matrix #1
     * @param    mixed[]    $matrixData2    Matrix #2
     * @return    float
     */
    public static function SUMXMY2($matrixData1, $matrixData2)
    {
        $array1 = PHPExcel_Calculation_Functions::flattenArray($matrixData1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))) {
                $result += ($array1[$i] - $array2[$i]) * ($array1[$i] - $array2[$i]);
            }
        }

        return $result;
    }


    /**
     * TRUNC
     *
     * Truncates value to the number of fractional digits by number_digits.
     *
     * @param    float        $value
     * @param    int            $digits
     * @return    float        Truncated value
     */
    public static function TRUNC($value = 0, $digits = 0)
    {
        $value    = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $digits    = PHPExcel_Calculation_Functions::flattenSingleValue($digits);

        // Validate parameters
        if ((!is_numeric($value)) || (!is_numeric($digits))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $digits = floor($digits);

        // Truncate
        $adjust = pow(10, $digits);

        if (($digits > 0) && (rtrim(intval((abs($value) - abs(intval($value))) * $adjust), '0') < $adjust/10)) {
            return $value;
        }

        return (intval($value * $adjust)) / $adjust;
    }
}
