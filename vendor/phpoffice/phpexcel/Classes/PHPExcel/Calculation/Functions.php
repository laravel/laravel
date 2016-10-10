<?php

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/** MAX_VALUE */
define('MAX_VALUE', 1.2e308);

/** 2 / PI */
define('M_2DIVPI', 0.63661977236758134307553505349006);

/** MAX_ITERATIONS */
define('MAX_ITERATIONS', 256);

/** PRECISION */
define('PRECISION', 8.88E-016);


/**
 * PHPExcel_Calculation_Functions
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
class PHPExcel_Calculation_Functions
{

    /** constants */
    const COMPATIBILITY_EXCEL      = 'Excel';
    const COMPATIBILITY_GNUMERIC   = 'Gnumeric';
    const COMPATIBILITY_OPENOFFICE = 'OpenOfficeCalc';

    const RETURNDATE_PHP_NUMERIC = 'P';
    const RETURNDATE_PHP_OBJECT  = 'O';
    const RETURNDATE_EXCEL       = 'E';


    /**
     * Compatibility mode to use for error checking and responses
     *
     * @access    private
     * @var string
     */
    protected static $compatibilityMode = self::COMPATIBILITY_EXCEL;

    /**
     * Data Type to use when returning date values
     *
     * @access    private
     * @var string
     */
    protected static $returnDateType = self::RETURNDATE_EXCEL;

    /**
     * List of error codes
     *
     * @access    private
     * @var array
     */
    protected static $errorCodes = array(
        'null'           => '#NULL!',
        'divisionbyzero' => '#DIV/0!',
        'value'          => '#VALUE!',
        'reference'      => '#REF!',
        'name'           => '#NAME?',
        'num'            => '#NUM!',
        'na'             => '#N/A',
        'gettingdata'    => '#GETTING_DATA'
    );


    /**
     * Set the Compatibility Mode
     *
     * @access    public
     * @category Function Configuration
     * @param     string        $compatibilityMode        Compatibility Mode
     *                                                Permitted values are:
     *                                                    PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL            'Excel'
     *                                                    PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC        'Gnumeric'
     *                                                    PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE    'OpenOfficeCalc'
     * @return     boolean    (Success or Failure)
     */
    public static function setCompatibilityMode($compatibilityMode)
    {
        if (($compatibilityMode == self::COMPATIBILITY_EXCEL) ||
            ($compatibilityMode == self::COMPATIBILITY_GNUMERIC) ||
            ($compatibilityMode == self::COMPATIBILITY_OPENOFFICE)) {
            self::$compatibilityMode = $compatibilityMode;
            return true;
        }
        return false;
    }


    /**
     * Return the current Compatibility Mode
     *
     * @access    public
     * @category Function Configuration
     * @return     string        Compatibility Mode
     *                            Possible Return values are:
     *                                PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL            'Excel'
     *                                PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC        'Gnumeric'
     *                                PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE    'OpenOfficeCalc'
     */
    public static function getCompatibilityMode()
    {
        return self::$compatibilityMode;
    }


    /**
     * Set the Return Date Format used by functions that return a date/time (Excel, PHP Serialized Numeric or PHP Object)
     *
     * @access    public
     * @category Function Configuration
     * @param     string    $returnDateType            Return Date Format
     *                                                Permitted values are:
     *                                                    PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC        'P'
     *                                                    PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT        'O'
     *                                                    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL            'E'
     * @return     boolean                            Success or failure
     */
    public static function setReturnDateType($returnDateType)
    {
        if (($returnDateType == self::RETURNDATE_PHP_NUMERIC) ||
            ($returnDateType == self::RETURNDATE_PHP_OBJECT) ||
            ($returnDateType == self::RETURNDATE_EXCEL)) {
            self::$returnDateType = $returnDateType;
            return true;
        }
        return false;
    }


    /**
     * Return the current Return Date Format for functions that return a date/time (Excel, PHP Serialized Numeric or PHP Object)
     *
     * @access    public
     * @category Function Configuration
     * @return     string        Return Date Format
     *                            Possible Return values are:
     *                                PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC        'P'
     *                                PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT        'O'
     *                                PHPExcel_Calculation_Functions::RETURNDATE_EXCEL            'E'
     */
    public static function getReturnDateType()
    {
        return self::$returnDateType;
    }


    /**
     * DUMMY
     *
     * @access    public
     * @category Error Returns
     * @return    string    #Not Yet Implemented
     */
    public static function DUMMY()
    {
        return '#Not Yet Implemented';
    }


    /**
     * DIV0
     *
     * @access    public
     * @category Error Returns
     * @return    string    #Not Yet Implemented
     */
    public static function DIV0()
    {
        return self::$errorCodes['divisionbyzero'];
    }


    /**
     * NA
     *
     * Excel Function:
     *        =NA()
     *
     * Returns the error value #N/A
     *        #N/A is the error value that means "no value is available."
     *
     * @access    public
     * @category Logical Functions
     * @return    string    #N/A!
     */
    public static function NA()
    {
        return self::$errorCodes['na'];
    }


    /**
     * NaN
     *
     * Returns the error value #NUM!
     *
     * @access    public
     * @category Error Returns
     * @return    string    #NUM!
     */
    public static function NaN()
    {
        return self::$errorCodes['num'];
    }


    /**
     * NAME
     *
     * Returns the error value #NAME?
     *
     * @access    public
     * @category Error Returns
     * @return    string    #NAME?
     */
    public static function NAME()
    {
        return self::$errorCodes['name'];
    }


    /**
     * REF
     *
     * Returns the error value #REF!
     *
     * @access    public
     * @category Error Returns
     * @return    string    #REF!
     */
    public static function REF()
    {
        return self::$errorCodes['reference'];
    }


    /**
     * NULL
     *
     * Returns the error value #NULL!
     *
     * @access    public
     * @category Error Returns
     * @return    string    #NULL!
     */
    public static function NULL()
    {
        return self::$errorCodes['null'];
    }


    /**
     * VALUE
     *
     * Returns the error value #VALUE!
     *
     * @access    public
     * @category Error Returns
     * @return    string    #VALUE!
     */
    public static function VALUE()
    {
        return self::$errorCodes['value'];
    }


    public static function isMatrixValue($idx)
    {
        return ((substr_count($idx, '.') <= 1) || (preg_match('/\.[A-Z]/', $idx) > 0));
    }


    public static function isValue($idx)
    {
        return (substr_count($idx, '.') == 0);
    }


    public static function isCellValue($idx)
    {
        return (substr_count($idx, '.') > 1);
    }


    public static function ifCondition($condition)
    {
        $condition    = PHPExcel_Calculation_Functions::flattenSingleValue($condition);
        if (!isset($condition{0})) {
            $condition = '=""';
        }
        if (!in_array($condition{0}, array('>', '<', '='))) {
            if (!is_numeric($condition)) {
                $condition = PHPExcel_Calculation::wrapResult(strtoupper($condition));
            }
            return '=' . $condition;
        } else {
            preg_match('/([<>=]+)(.*)/', $condition, $matches);
            list(, $operator, $operand) = $matches;

            if (!is_numeric($operand)) {
                $operand = str_replace('"', '""', $operand);
                $operand = PHPExcel_Calculation::wrapResult(strtoupper($operand));
            }

            return $operator.$operand;
        }
    }

    /**
     * ERROR_TYPE
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function ERROR_TYPE($value = '')
    {
        $value = self::flattenSingleValue($value);

        $i = 1;
        foreach (self::$errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }
        return self::NA();
    }


    /**
     * IS_BLANK
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function IS_BLANK($value = null)
    {
        if (!is_null($value)) {
            $value    = self::flattenSingleValue($value);
        }

        return is_null($value);
    }


    /**
     * IS_ERR
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function IS_ERR($value = '')
    {
        $value = self::flattenSingleValue($value);

        return self::IS_ERROR($value) && (!self::IS_NA($value));
    }


    /**
     * IS_ERROR
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function IS_ERROR($value = '')
    {
        $value = self::flattenSingleValue($value);

        if (!is_string($value)) {
            return false;
        }
        return in_array($value, array_values(self::$errorCodes));
    }


    /**
     * IS_NA
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function IS_NA($value = '')
    {
        $value = self::flattenSingleValue($value);

        return ($value === self::NA());
    }


    /**
     * IS_EVEN
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function IS_EVEN($value = null)
    {
        $value = self::flattenSingleValue($value);

        if ($value === null) {
            return self::NAME();
        } elseif ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return self::VALUE();
        }

        return ($value % 2 == 0);
    }


    /**
     * IS_ODD
     *
     * @param    mixed    $value    Value to check
     * @return    boolean
     */
    public static function IS_ODD($value = null)
    {
        $value = self::flattenSingleValue($value);

        if ($value === null) {
            return self::NAME();
        } elseif ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return self::VALUE();
        }

        return (abs($value) % 2 == 1);
    }


    /**
     * IS_NUMBER
     *
     * @param    mixed    $value        Value to check
     * @return    boolean
     */
    public static function IS_NUMBER($value = null)
    {
        $value = self::flattenSingleValue($value);

        if (is_string($value)) {
            return false;
        }
        return is_numeric($value);
    }


    /**
     * IS_LOGICAL
     *
     * @param    mixed    $value        Value to check
     * @return    boolean
     */
    public static function IS_LOGICAL($value = null)
    {
        $value = self::flattenSingleValue($value);

        return is_bool($value);
    }


    /**
     * IS_TEXT
     *
     * @param    mixed    $value        Value to check
     * @return    boolean
     */
    public static function IS_TEXT($value = null)
    {
        $value = self::flattenSingleValue($value);

        return (is_string($value) && !self::IS_ERROR($value));
    }


    /**
     * IS_NONTEXT
     *
     * @param    mixed    $value        Value to check
     * @return    boolean
     */
    public static function IS_NONTEXT($value = null)
    {
        return !self::IS_TEXT($value);
    }


    /**
     * VERSION
     *
     * @return    string    Version information
     */
    public static function VERSION()
    {
        return 'PHPExcel ##VERSION##, ##DATE##';
    }


    /**
     * N
     *
     * Returns a value converted to a number
     *
     * @param    value        The value you want converted
     * @return    number        N converts values listed in the following table
     *        If value is or refers to N returns
     *        A number            That number
     *        A date                The serial number of that date
     *        TRUE                1
     *        FALSE                0
     *        An error value        The error value
     *        Anything else        0
     */
    public static function N($value = null)
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }

        switch (gettype($value)) {
            case 'double':
            case 'float':
            case 'integer':
                return $value;
            case 'boolean':
                return (integer) $value;
            case 'string':
                //    Errors
                if ((strlen($value) > 0) && ($value{0} == '#')) {
                    return $value;
                }
                break;
        }
        return 0;
    }


    /**
     * TYPE
     *
     * Returns a number that identifies the type of a value
     *
     * @param    value        The value you want tested
     * @return    number        N converts values listed in the following table
     *        If value is or refers to N returns
     *        A number            1
     *        Text                2
     *        Logical Value        4
     *        An error value        16
     *        Array or Matrix        64
     */
    public static function TYPE($value = null)
    {
        $value = self::flattenArrayIndexed($value);
        if (is_array($value) && (count($value) > 1)) {
            end($value);
            $a = key($value);
            //    Range of cells is an error
            if (self::isCellValue($a)) {
                return 16;
            //    Test for Matrix
            } elseif (self::isMatrixValue($a)) {
                return 64;
            }
        } elseif (empty($value)) {
            //    Empty Cell
            return 1;
        }
        $value = self::flattenSingleValue($value);

        if (($value === null) || (is_float($value)) || (is_int($value))) {
                return 1;
        } elseif (is_bool($value)) {
                return 4;
        } elseif (is_array($value)) {
                return 64;
        } elseif (is_string($value)) {
            //    Errors
            if ((strlen($value) > 0) && ($value{0} == '#')) {
                return 16;
            }
            return 2;
        }
        return 0;
    }


    /**
     * Convert a multi-dimensional array to a simple 1-dimensional array
     *
     * @param    array    $array    Array to be flattened
     * @return    array    Flattened array
     */
    public static function flattenArray($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $arrayValues[] = $v;
                        }
                    } else {
                        $arrayValues[] = $val;
                    }
                }
            } else {
                $arrayValues[] = $value;
            }
        }

        return $arrayValues;
    }


    /**
     * Convert a multi-dimensional array to a simple 1-dimensional array, but retain an element of indexing
     *
     * @param    array    $array    Array to be flattened
     * @return    array    Flattened array
     */
    public static function flattenArrayIndexed($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = array();
        foreach ($array as $k1 => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k3 => $v) {
                            $arrayValues[$k1.'.'.$k2.'.'.$k3] = $v;
                        }
                    } else {
                        $arrayValues[$k1.'.'.$k2] = $val;
                    }
                }
            } else {
                $arrayValues[$k1] = $value;
            }
        }

        return $arrayValues;
    }


    /**
     * Convert an array to a single scalar value by extracting the first element
     *
     * @param    mixed        $value        Array or scalar value
     * @return    mixed
     */
    public static function flattenSingleValue($value = '')
    {
        while (is_array($value)) {
            $value = array_pop($value);
        }

        return $value;
    }
}


//
//    There are a few mathematical functions that aren't available on all versions of PHP for all platforms
//    These functions aren't available in Windows implementations of PHP prior to version 5.3.0
//    So we test if they do exist for this version of PHP/operating platform; and if not we create them
//
if (!function_exists('acosh')) {
    function acosh($x)
    {
        return 2 * log(sqrt(($x + 1) / 2) + sqrt(($x - 1) / 2));
    }    //    function acosh()
}

if (!function_exists('asinh')) {
    function asinh($x)
    {
        return log($x + sqrt(1 + $x * $x));
    }    //    function asinh()
}

if (!function_exists('atanh')) {
    function atanh($x)
    {
        return (log(1 + $x) - log(1 - $x)) / 2;
    }    //    function atanh()
}


//
//    Strangely, PHP doesn't have a mb_str_replace multibyte function
//    As we'll only ever use this function with UTF-8 characters, we can simply "hard-code" the character set
//
if ((!function_exists('mb_str_replace')) &&
    (function_exists('mb_substr')) && (function_exists('mb_strlen')) && (function_exists('mb_strpos'))) {
    function mb_str_replace($search, $replace, $subject)
    {
        if (is_array($subject)) {
            $ret = array();
            foreach ($subject as $key => $val) {
                $ret[$key] = mb_str_replace($search, $replace, $val);
            }
            return $ret;
        }

        foreach ((array) $search as $key => $s) {
            if ($s == '' && $s !== 0) {
                continue;
            }
            $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
            $pos = mb_strpos($subject, $s, 0, 'UTF-8');
            while ($pos !== false) {
                $subject = mb_substr($subject, 0, $pos, 'UTF-8') . $r . mb_substr($subject, $pos + mb_strlen($s, 'UTF-8'), 65535, 'UTF-8');
                $pos = mb_strpos($subject, $s, $pos + mb_strlen($r, 'UTF-8'), 'UTF-8');
            }
        }
        return $subject;
    }
}
