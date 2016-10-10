<?php

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

if (!defined('CALCULATION_REGEXP_CELLREF')) {
    //    Test for support of \P (multibyte options) in PCRE
    if (defined('PREG_BAD_UTF8_ERROR')) {
        //    Cell reference (cell or range of cells, with or without a sheet reference)
        define('CALCULATION_REGEXP_CELLREF', '((([^\s,!&%^\/\*\+<>=-]*)|(\'[^\']*\')|(\"[^\"]*\"))!)?\$?([a-z]{1,3})\$?(\d{1,7})');
        //    Named Range of cells
        define('CALCULATION_REGEXP_NAMEDRANGE', '((([^\s,!&%^\/\*\+<>=-]*)|(\'[^\']*\')|(\"[^\"]*\"))!)?([_A-Z][_A-Z0-9\.]*)');
    } else {
        //    Cell reference (cell or range of cells, with or without a sheet reference)
        define('CALCULATION_REGEXP_CELLREF', '(((\w*)|(\'[^\']*\')|(\"[^\"]*\"))!)?\$?([a-z]{1,3})\$?(\d+)');
        //    Named Range of cells
        define('CALCULATION_REGEXP_NAMEDRANGE', '(((\w*)|(\'.*\')|(\".*\"))!)?([_A-Z][_A-Z0-9\.]*)');
    }
}

/**
 * PHPExcel_Calculation (Multiton)
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Calculation
{
    /** Constants                */
    /** Regular Expressions        */
    //    Numeric operand
    const CALCULATION_REGEXP_NUMBER        = '[-+]?\d*\.?\d+(e[-+]?\d+)?';
    //    String operand
    const CALCULATION_REGEXP_STRING        = '"(?:[^"]|"")*"';
    //    Opening bracket
    const CALCULATION_REGEXP_OPENBRACE    = '\(';
    //    Function (allow for the old @ symbol that could be used to prefix a function, but we'll ignore it)
    const CALCULATION_REGEXP_FUNCTION    = '@?([A-Z][A-Z0-9\.]*)[\s]*\(';
    //    Cell reference (cell or range of cells, with or without a sheet reference)
    const CALCULATION_REGEXP_CELLREF    = CALCULATION_REGEXP_CELLREF;
    //    Named Range of cells
    const CALCULATION_REGEXP_NAMEDRANGE    = CALCULATION_REGEXP_NAMEDRANGE;
    //    Error
    const CALCULATION_REGEXP_ERROR        = '\#[A-Z][A-Z0_\/]*[!\?]?';


    /** constants */
    const RETURN_ARRAY_AS_ERROR = 'error';
    const RETURN_ARRAY_AS_VALUE = 'value';
    const RETURN_ARRAY_AS_ARRAY = 'array';

    private static $returnArrayAsType = self::RETURN_ARRAY_AS_VALUE;


    /**
     * Instance of this class
     *
     * @access    private
     * @var PHPExcel_Calculation
     */
    private static $instance;


    /**
     * Instance of the workbook this Calculation Engine is using
     *
     * @access    private
     * @var PHPExcel
     */
    private $workbook;

    /**
     * List of instances of the calculation engine that we've instantiated for individual workbooks
     *
     * @access    private
     * @var PHPExcel_Calculation[]
     */
    private static $workbookSets;

    /**
     * Calculation cache
     *
     * @access    private
     * @var array
     */
    private $calculationCache = array ();


    /**
     * Calculation cache enabled
     *
     * @access    private
     * @var boolean
     */
    private $calculationCacheEnabled = true;


    /**
     * List of operators that can be used within formulae
     * The true/false value indicates whether it is a binary operator or a unary operator
     *
     * @access    private
     * @var array
     */
    private static $operators = array(
        '+' => true,    '-' => true,    '*' => true,    '/' => true,
        '^' => true,    '&' => true,    '%' => false,    '~' => false,
        '>' => true,    '<' => true,    '=' => true,    '>=' => true,
        '<=' => true,    '<>' => true,    '|' => true,    ':' => true
    );

    /**
     * List of binary operators (those that expect two operands)
     *
     * @access    private
     * @var array
     */
    private static $binaryOperators = array(
        '+' => true,    '-' => true,    '*' => true,    '/' => true,
        '^' => true,    '&' => true,    '>' => true,    '<' => true,
        '=' => true,    '>=' => true,    '<=' => true,    '<>' => true,
        '|' => true,    ':' => true
    );

    /**
     * The debug log generated by the calculation engine
     *
     * @access    private
     * @var PHPExcel_CalcEngine_Logger
     *
     */
    private $debugLog;

    /**
     * Flag to determine how formula errors should be handled
     *        If true, then a user error will be triggered
     *        If false, then an exception will be thrown
     *
     * @access    public
     * @var boolean
     *
     */
    public $suppressFormulaErrors = false;

    /**
     * Error message for any error that was raised/thrown by the calculation engine
     *
     * @access    public
     * @var string
     *
     */
    public $formulaError = null;

    /**
     * An array of the nested cell references accessed by the calculation engine, used for the debug log
     *
     * @access    private
     * @var array of string
     *
     */
    private $cyclicReferenceStack;

    private $cellStack = array();

    /**
     * Current iteration counter for cyclic formulae
     * If the value is 0 (or less) then cyclic formulae will throw an exception,
     *    otherwise they will iterate to the limit defined here before returning a result
     *
     * @var integer
     *
     */
    private $cyclicFormulaCounter = 1;

    private $cyclicFormulaCell = '';

    /**
     * Number of iterations for cyclic formulae
     *
     * @var integer
     *
     */
    public $cyclicFormulaCount = 1;

    /**
     * Epsilon Precision used for comparisons in calculations
     *
     * @var float
     *
     */
    private $delta    = 0.1e-12;


    /**
     * The current locale setting
     *
     * @var string
     *
     */
    private static $localeLanguage = 'en_us';                    //    US English    (default locale)

    /**
     * List of available locale settings
     * Note that this is read for the locale subdirectory only when requested
     *
     * @var string[]
     *
     */
    private static $validLocaleLanguages = array(
        'en'        //    English        (default language)
    );

    /**
     * Locale-specific argument separator for function arguments
     *
     * @var string
     *
     */
    private static $localeArgumentSeparator = ',';
    private static $localeFunctions = array();

    /**
     * Locale-specific translations for Excel constants (True, False and Null)
     *
     * @var string[]
     *
     */
    public static $localeBoolean = array(
        'TRUE'  => 'TRUE',
        'FALSE' => 'FALSE',
        'NULL'  => 'NULL'
    );

    /**
     * Excel constant string translations to their PHP equivalents
     * Constant conversion from text name/value to actual (datatyped) value
     *
     * @var string[]
     *
     */
    private static $excelConstants = array(
        'TRUE'  => true,
        'FALSE' => false,
        'NULL'  => null
    );

     //    PHPExcel functions
    private static $PHPExcelFunctions = array(
        'ABS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'abs',
            'argumentCount' => '1'
        ),
        'ACCRINT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::ACCRINT',
            'argumentCount' => '4-7'
        ),
        'ACCRINTM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::ACCRINTM',
            'argumentCount' => '3-5'
        ),
        'ACOS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'acos',
            'argumentCount' => '1'
        ),
        'ACOSH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'acosh',
            'argumentCount' => '1'
        ),
        'ADDRESS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::CELL_ADDRESS',
            'argumentCount' => '2-5'
        ),
        'AMORDEGRC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::AMORDEGRC',
            'argumentCount' => '6,7'
        ),
        'AMORLINC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::AMORLINC',
            'argumentCount' => '6,7'
        ),
        'AND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::LOGICAL_AND',
            'argumentCount' => '1+'
        ),
        'AREAS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'ASC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'ASIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'asin',
            'argumentCount' => '1'
        ),
        'ASINH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'asinh',
            'argumentCount' => '1'
        ),
        'ATAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'atan',
            'argumentCount' => '1'
        ),
        'ATAN2' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::ATAN2',
            'argumentCount' => '2'
        ),
        'ATANH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'atanh',
            'argumentCount' => '1'
        ),
        'AVEDEV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::AVEDEV',
            'argumentCount' => '1+'
        ),
        'AVERAGE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::AVERAGE',
            'argumentCount' => '1+'
        ),
        'AVERAGEA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::AVERAGEA',
            'argumentCount' => '1+'
        ),
        'AVERAGEIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::AVERAGEIF',
            'argumentCount' => '2,3'
        ),
        'AVERAGEIFS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '3+'
        ),
        'BAHTTEXT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'BESSELI' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BESSELI',
            'argumentCount' => '2'
        ),
        'BESSELJ' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BESSELJ',
            'argumentCount' => '2'
        ),
        'BESSELK' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BESSELK',
            'argumentCount' => '2'
        ),
        'BESSELY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BESSELY',
            'argumentCount' => '2'
        ),
        'BETADIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::BETADIST',
            'argumentCount' => '3-5'
        ),
        'BETAINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::BETAINV',
            'argumentCount' => '3-5'
        ),
        'BIN2DEC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BINTODEC',
            'argumentCount' => '1'
        ),
        'BIN2HEX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BINTOHEX',
            'argumentCount' => '1,2'
        ),
        'BIN2OCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::BINTOOCT',
            'argumentCount' => '1,2'
        ),
        'BINOMDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::BINOMDIST',
            'argumentCount' => '4'
        ),
        'CEILING' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::CEILING',
            'argumentCount' => '2'
        ),
        'CELL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1,2'
        ),
        'CHAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::CHARACTER',
            'argumentCount' => '1'
        ),
        'CHIDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::CHIDIST',
            'argumentCount' => '2'
        ),
        'CHIINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::CHIINV',
            'argumentCount' => '2'
        ),
        'CHITEST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2'
        ),
        'CHOOSE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::CHOOSE',
            'argumentCount' => '2+'
        ),
        'CLEAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::TRIMNONPRINTABLE',
            'argumentCount' => '1'
        ),
        'CODE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::ASCIICODE',
            'argumentCount' => '1'
        ),
        'COLUMN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::COLUMN',
            'argumentCount' => '-1',
            'passByReference' => array(true)
        ),
        'COLUMNS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::COLUMNS',
            'argumentCount' => '1'
        ),
        'COMBIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::COMBIN',
            'argumentCount' => '2'
        ),
        'COMPLEX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::COMPLEX',
            'argumentCount' => '2,3'
        ),
        'CONCATENATE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::CONCATENATE',
            'argumentCount' => '1+'
        ),
        'CONFIDENCE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::CONFIDENCE',
            'argumentCount' => '3'
        ),
        'CONVERT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::CONVERTUOM',
            'argumentCount' => '3'
        ),
        'CORREL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::CORREL',
            'argumentCount' => '2'
        ),
        'COS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'cos',
            'argumentCount' => '1'
        ),
        'COSH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'cosh',
            'argumentCount' => '1'
        ),
        'COUNT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::COUNT',
            'argumentCount' => '1+'
        ),
        'COUNTA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::COUNTA',
            'argumentCount' => '1+'
        ),
        'COUNTBLANK' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::COUNTBLANK',
            'argumentCount' => '1'
        ),
        'COUNTIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::COUNTIF',
            'argumentCount' => '2'
        ),
        'COUNTIFS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2'
        ),
        'COUPDAYBS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::COUPDAYBS',
            'argumentCount' => '3,4'
        ),
        'COUPDAYS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::COUPDAYS',
            'argumentCount' => '3,4'
        ),
        'COUPDAYSNC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::COUPDAYSNC',
            'argumentCount' => '3,4'
        ),
        'COUPNCD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::COUPNCD',
            'argumentCount' => '3,4'
        ),
        'COUPNUM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::COUPNUM',
            'argumentCount' => '3,4'
        ),
        'COUPPCD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::COUPPCD',
            'argumentCount' => '3,4'
        ),
        'COVAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::COVAR',
            'argumentCount' => '2'
        ),
        'CRITBINOM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::CRITBINOM',
            'argumentCount' => '3'
        ),
        'CUBEKPIMEMBER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUBEMEMBER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUBEMEMBERPROPERTY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUBERANKEDMEMBER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUBESET' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUBESETCOUNT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUBEVALUE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_CUBE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '?'
        ),
        'CUMIPMT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::CUMIPMT',
            'argumentCount' => '6'
        ),
        'CUMPRINC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::CUMPRINC',
            'argumentCount' => '6'
        ),
        'DATE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DATE',
            'argumentCount' => '3'
        ),
        'DATEDIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DATEDIF',
            'argumentCount' => '2,3'
        ),
        'DATEVALUE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DATEVALUE',
            'argumentCount' => '1'
        ),
        'DAVERAGE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DAVERAGE',
            'argumentCount' => '3'
        ),
        'DAY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DAYOFMONTH',
            'argumentCount' => '1'
        ),
        'DAYS360' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DAYS360',
            'argumentCount' => '2,3'
        ),
        'DB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::DB',
            'argumentCount' => '4,5'
        ),
        'DCOUNT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DCOUNT',
            'argumentCount' => '3'
        ),
        'DCOUNTA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DCOUNTA',
            'argumentCount' => '3'
        ),
        'DDB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::DDB',
            'argumentCount' => '4,5'
        ),
        'DEC2BIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::DECTOBIN',
            'argumentCount' => '1,2'
        ),
        'DEC2HEX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::DECTOHEX',
            'argumentCount' => '1,2'
        ),
        'DEC2OCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::DECTOOCT',
            'argumentCount' => '1,2'
        ),
        'DEGREES' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'rad2deg',
            'argumentCount' => '1'
        ),
        'DELTA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::DELTA',
            'argumentCount' => '1,2'
        ),
        'DEVSQ' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::DEVSQ',
            'argumentCount' => '1+'
        ),
        'DGET' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DGET',
            'argumentCount' => '3'
        ),
        'DISC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::DISC',
            'argumentCount' => '4,5'
        ),
        'DMAX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DMAX',
            'argumentCount' => '3'
        ),
        'DMIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DMIN',
            'argumentCount' => '3'
        ),
        'DOLLAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::DOLLAR',
            'argumentCount' => '1,2'
        ),
        'DOLLARDE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::DOLLARDE',
            'argumentCount' => '2'
        ),
        'DOLLARFR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::DOLLARFR',
            'argumentCount' => '2'
        ),
        'DPRODUCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DPRODUCT',
            'argumentCount' => '3'
        ),
        'DSTDEV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DSTDEV',
            'argumentCount' => '3'
        ),
        'DSTDEVP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DSTDEVP',
            'argumentCount' => '3'
        ),
        'DSUM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DSUM',
            'argumentCount' => '3'
        ),
        'DURATION' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '5,6'
        ),
        'DVAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DVAR',
            'argumentCount' => '3'
        ),
        'DVARP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATABASE,
            'functionCall' => 'PHPExcel_Calculation_Database::DVARP',
            'argumentCount' => '3'
        ),
        'EDATE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::EDATE',
            'argumentCount' => '2'
        ),
        'EFFECT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::EFFECT',
            'argumentCount' => '2'
        ),
        'EOMONTH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::EOMONTH',
            'argumentCount' => '2'
        ),
        'ERF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::ERF',
            'argumentCount' => '1,2'
        ),
        'ERFC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::ERFC',
            'argumentCount' => '1'
        ),
        'ERROR.TYPE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::ERROR_TYPE',
            'argumentCount' => '1'
        ),
        'EVEN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::EVEN',
            'argumentCount' => '1'
        ),
        'EXACT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2'
        ),
        'EXP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'exp',
            'argumentCount' => '1'
        ),
        'EXPONDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::EXPONDIST',
            'argumentCount' => '3'
        ),
        'FACT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::FACT',
            'argumentCount' => '1'
        ),
        'FACTDOUBLE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::FACTDOUBLE',
            'argumentCount' => '1'
        ),
        'FALSE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::FALSE',
            'argumentCount' => '0'
        ),
        'FDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '3'
        ),
        'FIND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::SEARCHSENSITIVE',
            'argumentCount' => '2,3'
        ),
        'FINDB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::SEARCHSENSITIVE',
            'argumentCount' => '2,3'
        ),
        'FINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '3'
        ),
        'FISHER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::FISHER',
            'argumentCount' => '1'
        ),
        'FISHERINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::FISHERINV',
            'argumentCount' => '1'
        ),
        'FIXED' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::FIXEDFORMAT',
            'argumentCount' => '1-3'
        ),
        'FLOOR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::FLOOR',
            'argumentCount' => '2'
        ),
        'FORECAST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::FORECAST',
            'argumentCount' => '3'
        ),
        'FREQUENCY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2'
        ),
        'FTEST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2'
        ),
        'FV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::FV',
            'argumentCount' => '3-5'
        ),
        'FVSCHEDULE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::FVSCHEDULE',
            'argumentCount' => '2'
        ),
        'GAMMADIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::GAMMADIST',
            'argumentCount' => '4'
        ),
        'GAMMAINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::GAMMAINV',
            'argumentCount' => '3'
        ),
        'GAMMALN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::GAMMALN',
            'argumentCount' => '1'
        ),
        'GCD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::GCD',
            'argumentCount' => '1+'
        ),
        'GEOMEAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::GEOMEAN',
            'argumentCount' => '1+'
        ),
        'GESTEP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::GESTEP',
            'argumentCount' => '1,2'
        ),
        'GETPIVOTDATA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2+'
        ),
        'GROWTH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::GROWTH',
            'argumentCount' => '1-4'
        ),
        'HARMEAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::HARMEAN',
            'argumentCount' => '1+'
        ),
        'HEX2BIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::HEXTOBIN',
            'argumentCount' => '1,2'
        ),
        'HEX2DEC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::HEXTODEC',
            'argumentCount' => '1'
        ),
        'HEX2OCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::HEXTOOCT',
            'argumentCount' => '1,2'
        ),
        'HLOOKUP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::HLOOKUP',
            'argumentCount' => '3,4'
        ),
        'HOUR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::HOUROFDAY',
            'argumentCount' => '1'
        ),
        'HYPERLINK' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::HYPERLINK',
            'argumentCount' => '1,2',
            'passCellReference' => true
        ),
        'HYPGEOMDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::HYPGEOMDIST',
            'argumentCount' => '4'
        ),
        'IF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::STATEMENT_IF',
            'argumentCount' => '1-3'
        ),
        'IFERROR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::IFERROR',
            'argumentCount' => '2'
        ),
        'IMABS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMABS',
            'argumentCount' => '1'
        ),
        'IMAGINARY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMAGINARY',
            'argumentCount' => '1'
        ),
        'IMARGUMENT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMARGUMENT',
            'argumentCount' => '1'
        ),
        'IMCONJUGATE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMCONJUGATE',
            'argumentCount' => '1'
        ),
        'IMCOS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMCOS',
            'argumentCount' => '1'
        ),
        'IMDIV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMDIV',
            'argumentCount' => '2'
        ),
        'IMEXP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMEXP',
            'argumentCount' => '1'
        ),
        'IMLN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMLN',
            'argumentCount' => '1'
        ),
        'IMLOG10' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMLOG10',
            'argumentCount' => '1'
        ),
        'IMLOG2' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMLOG2',
            'argumentCount' => '1'
        ),
        'IMPOWER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMPOWER',
            'argumentCount' => '2'
        ),
        'IMPRODUCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMPRODUCT',
            'argumentCount' => '1+'
        ),
        'IMREAL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMREAL',
            'argumentCount' => '1'
        ),
        'IMSIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMSIN',
            'argumentCount' => '1'
        ),
        'IMSQRT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMSQRT',
            'argumentCount' => '1'
        ),
        'IMSUB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMSUB',
            'argumentCount' => '2'
        ),
        'IMSUM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::IMSUM',
            'argumentCount' => '1+'
        ),
        'INDEX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::INDEX',
            'argumentCount' => '1-4'
        ),
        'INDIRECT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::INDIRECT',
            'argumentCount' => '1,2',
            'passCellReference' => true
        ),
        'INFO' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'INT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::INT',
            'argumentCount' => '1'
        ),
        'INTERCEPT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::INTERCEPT',
            'argumentCount' => '2'
        ),
        'INTRATE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::INTRATE',
            'argumentCount' => '4,5'
        ),
        'IPMT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::IPMT',
            'argumentCount' => '4-6'
        ),
        'IRR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::IRR',
            'argumentCount' => '1,2'
        ),
        'ISBLANK' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_BLANK',
            'argumentCount' => '1'
        ),
        'ISERR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_ERR',
            'argumentCount' => '1'
        ),
        'ISERROR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_ERROR',
            'argumentCount' => '1'
        ),
        'ISEVEN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_EVEN',
            'argumentCount' => '1'
        ),
        'ISLOGICAL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_LOGICAL',
            'argumentCount' => '1'
        ),
        'ISNA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_NA',
            'argumentCount' => '1'
        ),
        'ISNONTEXT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_NONTEXT',
            'argumentCount' => '1'
        ),
        'ISNUMBER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_NUMBER',
            'argumentCount' => '1'
        ),
        'ISODD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_ODD',
            'argumentCount' => '1'
        ),
        'ISPMT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::ISPMT',
            'argumentCount' => '4'
        ),
        'ISREF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'ISTEXT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::IS_TEXT',
            'argumentCount' => '1'
        ),
        'JIS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'KURT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::KURT',
            'argumentCount' => '1+'
        ),
        'LARGE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::LARGE',
            'argumentCount' => '2'
        ),
        'LCM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::LCM',
            'argumentCount' => '1+'
        ),
        'LEFT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::LEFT',
            'argumentCount' => '1,2'
        ),
        'LEFTB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::LEFT',
            'argumentCount' => '1,2'
        ),
        'LEN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::STRINGLENGTH',
            'argumentCount' => '1'
        ),
        'LENB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::STRINGLENGTH',
            'argumentCount' => '1'
        ),
        'LINEST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::LINEST',
            'argumentCount' => '1-4'
        ),
        'LN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'log',
            'argumentCount' => '1'
        ),
        'LOG' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::LOG_BASE',
            'argumentCount' => '1,2'
        ),
        'LOG10' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'log10',
            'argumentCount' => '1'
        ),
        'LOGEST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::LOGEST',
            'argumentCount' => '1-4'
        ),
        'LOGINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::LOGINV',
            'argumentCount' => '3'
        ),
        'LOGNORMDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::LOGNORMDIST',
            'argumentCount' => '3'
        ),
        'LOOKUP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::LOOKUP',
            'argumentCount' => '2,3'
        ),
        'LOWER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::LOWERCASE',
            'argumentCount' => '1'
        ),
        'MATCH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::MATCH',
            'argumentCount' => '2,3'
        ),
        'MAX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MAX',
            'argumentCount' => '1+'
        ),
        'MAXA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MAXA',
            'argumentCount' => '1+'
        ),
        'MAXIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MAXIF',
            'argumentCount' => '2+'
        ),
        'MDETERM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::MDETERM',
            'argumentCount' => '1'
        ),
        'MDURATION' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '5,6'
        ),
        'MEDIAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MEDIAN',
            'argumentCount' => '1+'
        ),
        'MEDIANIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2+'
        ),
        'MID' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::MID',
            'argumentCount' => '3'
        ),
        'MIDB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::MID',
            'argumentCount' => '3'
        ),
        'MIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MIN',
            'argumentCount' => '1+'
        ),
        'MINA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MINA',
            'argumentCount' => '1+'
        ),
        'MINIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MINIF',
            'argumentCount' => '2+'
        ),
        'MINUTE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::MINUTEOFHOUR',
            'argumentCount' => '1'
        ),
        'MINVERSE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::MINVERSE',
            'argumentCount' => '1'
        ),
        'MIRR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::MIRR',
            'argumentCount' => '3'
        ),
        'MMULT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::MMULT',
            'argumentCount' => '2'
        ),
        'MOD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::MOD',
            'argumentCount' => '2'
        ),
        'MODE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::MODE',
            'argumentCount' => '1+'
        ),
        'MONTH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::MONTHOFYEAR',
            'argumentCount' => '1'
        ),
        'MROUND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::MROUND',
            'argumentCount' => '2'
        ),
        'MULTINOMIAL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::MULTINOMIAL',
            'argumentCount' => '1+'
        ),
        'N' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::N',
            'argumentCount' => '1'
        ),
        'NA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::NA',
            'argumentCount' => '0'
        ),
        'NEGBINOMDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::NEGBINOMDIST',
            'argumentCount' => '3'
        ),
        'NETWORKDAYS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::NETWORKDAYS',
            'argumentCount' => '2+'
        ),
        'NOMINAL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::NOMINAL',
            'argumentCount' => '2'
        ),
        'NORMDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::NORMDIST',
            'argumentCount' => '4'
        ),
        'NORMINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::NORMINV',
            'argumentCount' => '3'
        ),
        'NORMSDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::NORMSDIST',
            'argumentCount' => '1'
        ),
        'NORMSINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::NORMSINV',
            'argumentCount' => '1'
        ),
        'NOT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::NOT',
            'argumentCount' => '1'
        ),
        'NOW' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DATETIMENOW',
            'argumentCount' => '0'
        ),
        'NPER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::NPER',
            'argumentCount' => '3-5'
        ),
        'NPV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::NPV',
            'argumentCount' => '2+'
        ),
        'OCT2BIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::OCTTOBIN',
            'argumentCount' => '1,2'
        ),
        'OCT2DEC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::OCTTODEC',
            'argumentCount' => '1'
        ),
        'OCT2HEX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_ENGINEERING,
            'functionCall' => 'PHPExcel_Calculation_Engineering::OCTTOHEX',
            'argumentCount' => '1,2'
        ),
        'ODD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::ODD',
            'argumentCount' => '1'
        ),
        'ODDFPRICE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '8,9'
        ),
        'ODDFYIELD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '8,9'
        ),
        'ODDLPRICE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '7,8'
        ),
        'ODDLYIELD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '7,8'
        ),
        'OFFSET' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::OFFSET',
            'argumentCount' => '3-5',
            'passCellReference' => true,
            'passByReference' => array(true)
        ),
        'OR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::LOGICAL_OR',
            'argumentCount' => '1+'
        ),
        'PEARSON' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::CORREL',
            'argumentCount' => '2'
        ),
        'PERCENTILE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::PERCENTILE',
            'argumentCount' => '2'
        ),
        'PERCENTRANK' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::PERCENTRANK',
            'argumentCount' => '2,3'
        ),
        'PERMUT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::PERMUT',
            'argumentCount' => '2'
        ),
        'PHONETIC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1'
        ),
        'PI' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'pi',
            'argumentCount' => '0'
        ),
        'PMT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::PMT',
            'argumentCount' => '3-5'
        ),
        'POISSON' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::POISSON',
            'argumentCount' => '3'
        ),
        'POWER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::POWER',
            'argumentCount' => '2'
        ),
        'PPMT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::PPMT',
            'argumentCount' => '4-6'
        ),
        'PRICE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::PRICE',
            'argumentCount' => '6,7'
        ),
        'PRICEDISC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::PRICEDISC',
            'argumentCount' => '4,5'
        ),
        'PRICEMAT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::PRICEMAT',
            'argumentCount' => '5,6'
        ),
        'PROB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '3,4'
        ),
        'PRODUCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::PRODUCT',
            'argumentCount' => '1+'
        ),
        'PROPER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::PROPERCASE',
            'argumentCount' => '1'
        ),
        'PV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::PV',
            'argumentCount' => '3-5'
        ),
        'QUARTILE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::QUARTILE',
            'argumentCount' => '2'
        ),
        'QUOTIENT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::QUOTIENT',
            'argumentCount' => '2'
        ),
        'RADIANS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'deg2rad',
            'argumentCount' => '1'
        ),
        'RAND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::RAND',
            'argumentCount' => '0'
        ),
        'RANDBETWEEN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::RAND',
            'argumentCount' => '2'
        ),
        'RANK' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::RANK',
            'argumentCount' => '2,3'
        ),
        'RATE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::RATE',
            'argumentCount' => '3-6'
        ),
        'RECEIVED' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::RECEIVED',
            'argumentCount' => '4-5'
        ),
        'REPLACE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::REPLACE',
            'argumentCount' => '4'
        ),
        'REPLACEB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::REPLACE',
            'argumentCount' => '4'
        ),
        'REPT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'str_repeat',
            'argumentCount' => '2'
        ),
        'RIGHT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::RIGHT',
            'argumentCount' => '1,2'
        ),
        'RIGHTB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::RIGHT',
            'argumentCount' => '1,2'
        ),
        'ROMAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::ROMAN',
            'argumentCount' => '1,2'
        ),
        'ROUND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'round',
            'argumentCount' => '2'
        ),
        'ROUNDDOWN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::ROUNDDOWN',
            'argumentCount' => '2'
        ),
        'ROUNDUP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::ROUNDUP',
            'argumentCount' => '2'
        ),
        'ROW' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::ROW',
            'argumentCount' => '-1',
            'passByReference' => array(true)
        ),
        'ROWS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::ROWS',
            'argumentCount' => '1'
        ),
        'RSQ' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::RSQ',
            'argumentCount' => '2'
        ),
        'RTD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '1+'
        ),
        'SEARCH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::SEARCHINSENSITIVE',
            'argumentCount' => '2,3'
        ),
        'SEARCHB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::SEARCHINSENSITIVE',
            'argumentCount' => '2,3'
        ),
        'SECOND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::SECONDOFMINUTE',
            'argumentCount' => '1'
        ),
        'SERIESSUM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SERIESSUM',
            'argumentCount' => '4'
        ),
        'SIGN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SIGN',
            'argumentCount' => '1'
        ),
        'SIN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'sin',
            'argumentCount' => '1'
        ),
        'SINH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'sinh',
            'argumentCount' => '1'
        ),
        'SKEW' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::SKEW',
            'argumentCount' => '1+'
        ),
        'SLN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::SLN',
            'argumentCount' => '3'
        ),
        'SLOPE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::SLOPE',
            'argumentCount' => '2'
        ),
        'SMALL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::SMALL',
            'argumentCount' => '2'
        ),
        'SQRT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'sqrt',
            'argumentCount' => '1'
        ),
        'SQRTPI' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SQRTPI',
            'argumentCount' => '1'
        ),
        'STANDARDIZE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::STANDARDIZE',
            'argumentCount' => '3'
        ),
        'STDEV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::STDEV',
            'argumentCount' => '1+'
        ),
        'STDEVA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::STDEVA',
            'argumentCount' => '1+'
        ),
        'STDEVP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::STDEVP',
            'argumentCount' => '1+'
        ),
        'STDEVPA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::STDEVPA',
            'argumentCount' => '1+'
        ),
        'STEYX' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::STEYX',
            'argumentCount' => '2'
        ),
        'SUBSTITUTE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::SUBSTITUTE',
            'argumentCount' => '3,4'
        ),
        'SUBTOTAL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUBTOTAL',
            'argumentCount' => '2+'
        ),
        'SUM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUM',
            'argumentCount' => '1+'
        ),
        'SUMIF' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMIF',
            'argumentCount' => '2,3'
        ),
        'SUMIFS' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMIFS',
            'argumentCount' => '3+'
        ),
        'SUMPRODUCT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMPRODUCT',
            'argumentCount' => '1+'
        ),
        'SUMSQ' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMSQ',
            'argumentCount' => '1+'
        ),
        'SUMX2MY2' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMX2MY2',
            'argumentCount' => '2'
        ),
        'SUMX2PY2' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMX2PY2',
            'argumentCount' => '2'
        ),
        'SUMXMY2' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::SUMXMY2',
            'argumentCount' => '2'
        ),
        'SYD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::SYD',
            'argumentCount' => '4'
        ),
        'T' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::RETURNSTRING',
            'argumentCount' => '1'
        ),
        'TAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'tan',
            'argumentCount' => '1'
        ),
        'TANH' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'tanh',
            'argumentCount' => '1'
        ),
        'TBILLEQ' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::TBILLEQ',
            'argumentCount' => '3'
        ),
        'TBILLPRICE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::TBILLPRICE',
            'argumentCount' => '3'
        ),
        'TBILLYIELD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::TBILLYIELD',
            'argumentCount' => '3'
        ),
        'TDIST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::TDIST',
            'argumentCount' => '3'
        ),
        'TEXT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::TEXTFORMAT',
            'argumentCount' => '2'
        ),
        'TIME' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::TIME',
            'argumentCount' => '3'
        ),
        'TIMEVALUE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::TIMEVALUE',
            'argumentCount' => '1'
        ),
        'TINV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::TINV',
            'argumentCount' => '2'
        ),
        'TODAY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DATENOW',
            'argumentCount' => '0'
        ),
        'TRANSPOSE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::TRANSPOSE',
            'argumentCount' => '1'
        ),
        'TREND' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::TREND',
            'argumentCount' => '1-4'
        ),
        'TRIM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::TRIMSPACES',
            'argumentCount' => '1'
        ),
        'TRIMMEAN' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::TRIMMEAN',
            'argumentCount' => '2'
        ),
        'TRUE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOGICAL,
            'functionCall' => 'PHPExcel_Calculation_Logical::TRUE',
            'argumentCount' => '0'
        ),
        'TRUNC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'PHPExcel_Calculation_MathTrig::TRUNC',
            'argumentCount' => '1,2'
        ),
        'TTEST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '4'
        ),
        'TYPE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::TYPE',
            'argumentCount' => '1'
        ),
        'UPPER' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::UPPERCASE',
            'argumentCount' => '1'
        ),
        'USDOLLAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '2'
        ),
        'VALUE' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'PHPExcel_Calculation_TextData::VALUE',
            'argumentCount' => '1'
        ),
        'VAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::VARFunc',
            'argumentCount' => '1+'
        ),
        'VARA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::VARA',
            'argumentCount' => '1+'
        ),
        'VARP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::VARP',
            'argumentCount' => '1+'
        ),
        'VARPA' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::VARPA',
            'argumentCount' => '1+'
        ),
        'VDB' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '5-7'
        ),
        'VERSION' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_INFORMATION,
            'functionCall' => 'PHPExcel_Calculation_Functions::VERSION',
            'argumentCount' => '0'
        ),
        'VLOOKUP' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => 'PHPExcel_Calculation_LookupRef::VLOOKUP',
            'argumentCount' => '3,4'
        ),
        'WEEKDAY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::DAYOFWEEK',
            'argumentCount' => '1,2'
        ),
        'WEEKNUM' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::WEEKOFYEAR',
            'argumentCount' => '1,2'
        ),
        'WEIBULL' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::WEIBULL',
            'argumentCount' => '4'
        ),
        'WORKDAY' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::WORKDAY',
            'argumentCount' => '2+'
        ),
        'XIRR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::XIRR',
            'argumentCount' => '2,3'
        ),
        'XNPV' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::XNPV',
            'argumentCount' => '3'
        ),
        'YEAR' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::YEAR',
            'argumentCount' => '1'
        ),
        'YEARFRAC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME,
            'functionCall' => 'PHPExcel_Calculation_DateTime::YEARFRAC',
            'argumentCount' => '2,3'
        ),
        'YIELD' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Functions::DUMMY',
            'argumentCount' => '6,7'
        ),
        'YIELDDISC' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::YIELDDISC',
            'argumentCount' => '4,5'
        ),
        'YIELDMAT' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_FINANCIAL,
            'functionCall' => 'PHPExcel_Calculation_Financial::YIELDMAT',
            'argumentCount' => '5,6'
        ),
        'ZTEST' => array(
            'category' => PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,
            'functionCall' => 'PHPExcel_Calculation_Statistical::ZTEST',
            'argumentCount' => '2-3'
        )
    );

    //    Internal functions used for special control purposes
    private static $controlFunctions = array(
        'MKMATRIX' => array(
            'argumentCount' => '*',
            'functionCall' => 'self::mkMatrix'
        )
    );


    public function __construct(PHPExcel $workbook = null)
    {
        $this->delta = 1 * pow(10, 0 - ini_get('precision'));

        $this->workbook = $workbook;
        $this->cyclicReferenceStack = new PHPExcel_CalcEngine_CyclicReferenceStack();
        $this->_debugLog = new PHPExcel_CalcEngine_Logger($this->cyclicReferenceStack);
    }


    private static function loadLocales()
    {
        $localeFileDirectory = PHPEXCEL_ROOT.'PHPExcel/locale/';
        foreach (glob($localeFileDirectory.'/*', GLOB_ONLYDIR) as $filename) {
            $filename = substr($filename, strlen($localeFileDirectory)+1);
            if ($filename != 'en') {
                self::$validLocaleLanguages[] = $filename;
            }
        }
    }

    /**
     * Get an instance of this class
     *
     * @access    public
     * @param   PHPExcel $workbook  Injected workbook for working with a PHPExcel object,
     *                                    or NULL to create a standalone claculation engine
     * @return PHPExcel_Calculation
     */
    public static function getInstance(PHPExcel $workbook = null)
    {
        if ($workbook !== null) {
            $instance = $workbook->getCalculationEngine();
            if (isset($instance)) {
                return $instance;  
            }
        }

        if (!isset(self::$instance) || (self::$instance === null)) {
            self::$instance = new PHPExcel_Calculation();
        }
        return self::$instance;
    }

    /**
     * Unset an instance of this class
     *
     * @access    public
     */
    public function __destruct()
    {
        $this->workbook = null;
    }

    /**
     * Flush the calculation cache for any existing instance of this class
     *        but only if a PHPExcel_Calculation instance exists
     *
     * @access    public
     * @return null
     */
    public function flushInstance()
    {
        $this->clearCalculationCache();
    }


    /**
     * Get the debuglog for this claculation engine instance
     *
     * @access    public
     * @return PHPExcel_CalcEngine_Logger
     */
    public function getDebugLog()
    {
        return $this->_debugLog;
    }

    /**
     * __clone implementation. Cloning should not be allowed in a Singleton!
     *
     * @access    public
     * @throws    PHPExcel_Calculation_Exception
     */
    final public function __clone()
    {
        throw new PHPExcel_Calculation_Exception('Cloning the calculation engine is not allowed!');
    }


    /**
     * Return the locale-specific translation of TRUE
     *
     * @access    public
     * @return     string        locale-specific translation of TRUE
     */
    public static function getTRUE()
    {
        return self::$localeBoolean['TRUE'];
    }

    /**
     * Return the locale-specific translation of FALSE
     *
     * @access    public
     * @return     string        locale-specific translation of FALSE
     */
    public static function getFALSE()
    {
        return self::$localeBoolean['FALSE'];
    }

    /**
     * Set the Array Return Type (Array or Value of first element in the array)
     *
     * @access    public
     * @param     string    $returnType            Array return type
     * @return     boolean                    Success or failure
     */
    public static function setArrayReturnType($returnType)
    {
        if (($returnType == self::RETURN_ARRAY_AS_VALUE) ||
            ($returnType == self::RETURN_ARRAY_AS_ERROR) ||
            ($returnType == self::RETURN_ARRAY_AS_ARRAY)) {
            self::$returnArrayAsType = $returnType;
            return true;
        }
        return false;
    }


    /**
     * Return the Array Return Type (Array or Value of first element in the array)
     *
     * @access    public
     * @return     string        $returnType            Array return type
     */
    public static function getArrayReturnType()
    {
        return self::$returnArrayAsType;
    }


    /**
     * Is calculation caching enabled?
     *
     * @access    public
     * @return boolean
     */
    public function getCalculationCacheEnabled()
    {
        return $this->calculationCacheEnabled;
    }

    /**
     * Enable/disable calculation cache
     *
     * @access    public
     * @param boolean $pValue
     */
    public function setCalculationCacheEnabled($pValue = true)
    {
        $this->calculationCacheEnabled = $pValue;
        $this->clearCalculationCache();
    }


    /**
     * Enable calculation cache
     */
    public function enableCalculationCache()
    {
        $this->setCalculationCacheEnabled(true);
    }


    /**
     * Disable calculation cache
     */
    public function disableCalculationCache()
    {
        $this->setCalculationCacheEnabled(false);
    }


    /**
     * Clear calculation cache
     */
    public function clearCalculationCache()
    {
        $this->calculationCache = array();
    }

    /**
     * Clear calculation cache for a specified worksheet
     *
     * @param string $worksheetName
     */
    public function clearCalculationCacheForWorksheet($worksheetName)
    {
        if (isset($this->calculationCache[$worksheetName])) {
            unset($this->calculationCache[$worksheetName]);
        }
    }

    /**
     * Rename calculation cache for a specified worksheet
     *
     * @param string $fromWorksheetName
     * @param string $toWorksheetName
     */
    public function renameCalculationCacheForWorksheet($fromWorksheetName, $toWorksheetName)
    {
        if (isset($this->calculationCache[$fromWorksheetName])) {
            $this->calculationCache[$toWorksheetName] = &$this->calculationCache[$fromWorksheetName];
            unset($this->calculationCache[$fromWorksheetName]);
        }
    }


    /**
     * Get the currently defined locale code
     *
     * @return string
     */
    public function getLocale()
    {
        return self::$localeLanguage;
    }


    /**
     * Set the locale code
     *
     * @param string $locale  The locale to use for formula translation
     * @return boolean
     */
    public function setLocale($locale = 'en_us')
    {
        //    Identify our locale and language
        $language = $locale = strtolower($locale);
        if (strpos($locale, '_') !== false) {
            list($language) = explode('_', $locale);
        }

        if (count(self::$validLocaleLanguages) == 1) {
            self::loadLocales();
        }
        //    Test whether we have any language data for this language (any locale)
        if (in_array($language, self::$validLocaleLanguages)) {
            //    initialise language/locale settings
            self::$localeFunctions = array();
            self::$localeArgumentSeparator = ',';
            self::$localeBoolean = array('TRUE' => 'TRUE', 'FALSE' => 'FALSE', 'NULL' => 'NULL');
            //    Default is English, if user isn't requesting english, then read the necessary data from the locale files
            if ($locale != 'en_us') {
                //    Search for a file with a list of function names for locale
                $functionNamesFile = PHPEXCEL_ROOT . 'PHPExcel'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $locale).DIRECTORY_SEPARATOR.'functions';
                if (!file_exists($functionNamesFile)) {
                    //    If there isn't a locale specific function file, look for a language specific function file
                    $functionNamesFile = PHPEXCEL_ROOT . 'PHPExcel'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.'functions';
                    if (!file_exists($functionNamesFile)) {
                        return false;
                    }
                }
                //    Retrieve the list of locale or language specific function names
                $localeFunctions = file($functionNamesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($localeFunctions as $localeFunction) {
                    list($localeFunction) = explode('##', $localeFunction);    //    Strip out comments
                    if (strpos($localeFunction, '=') !== false) {
                        list($fName, $lfName) = explode('=', $localeFunction);
                        $fName = trim($fName);
                        $lfName = trim($lfName);
                        if ((isset(self::$PHPExcelFunctions[$fName])) && ($lfName != '') && ($fName != $lfName)) {
                            self::$localeFunctions[$fName] = $lfName;
                        }
                    }
                }
                //    Default the TRUE and FALSE constants to the locale names of the TRUE() and FALSE() functions
                if (isset(self::$localeFunctions['TRUE'])) {
                    self::$localeBoolean['TRUE'] = self::$localeFunctions['TRUE'];
                }
                if (isset(self::$localeFunctions['FALSE'])) {
                    self::$localeBoolean['FALSE'] = self::$localeFunctions['FALSE'];
                }

                $configFile = PHPEXCEL_ROOT . 'PHPExcel'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $locale).DIRECTORY_SEPARATOR.'config';
                if (!file_exists($configFile)) {
                    $configFile = PHPEXCEL_ROOT . 'PHPExcel'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.'config';
                }
                if (file_exists($configFile)) {
                    $localeSettings = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($localeSettings as $localeSetting) {
                        list($localeSetting) = explode('##', $localeSetting);    //    Strip out comments
                        if (strpos($localeSetting, '=') !== false) {
                            list($settingName, $settingValue) = explode('=', $localeSetting);
                            $settingName = strtoupper(trim($settingName));
                            switch ($settingName) {
                                case 'ARGUMENTSEPARATOR':
                                    self::$localeArgumentSeparator = trim($settingValue);
                                    break;
                            }
                        }
                    }
                }
            }

            self::$functionReplaceFromExcel = self::$functionReplaceToExcel =
            self::$functionReplaceFromLocale = self::$functionReplaceToLocale = null;
            self::$localeLanguage = $locale;
            return true;
        }
        return false;
    }



    public static function translateSeparator($fromSeparator, $toSeparator, $formula, &$inBraces)
    {
        $strlen = mb_strlen($formula);
        for ($i = 0; $i < $strlen; ++$i) {
            $chr = mb_substr($formula, $i, 1);
            switch ($chr) {
                case '{':
                    $inBraces = true;
                    break;
                case '}':
                    $inBraces = false;
                    break;
                case $fromSeparator:
                    if (!$inBraces) {
                        $formula = mb_substr($formula, 0, $i).$toSeparator.mb_substr($formula, $i+1);
                    }
            }
        }
        return $formula;
    }

    private static function translateFormula($from, $to, $formula, $fromSeparator, $toSeparator)
    {
        //    Convert any Excel function names to the required language
        if (self::$localeLanguage !== 'en_us') {
            $inBraces = false;
            //    If there is the possibility of braces within a quoted string, then we don't treat those as matrix indicators
            if (strpos($formula, '"') !== false) {
                //    So instead we skip replacing in any quoted strings by only replacing in every other array element after we've exploded
                //        the formula
                $temp = explode('"', $formula);
                $i = false;
                foreach ($temp as &$value) {
                    //    Only count/replace in alternating array entries
                    if ($i = !$i) {
                        $value = preg_replace($from, $to, $value);
                        $value = self::translateSeparator($fromSeparator, $toSeparator, $value, $inBraces);
                    }
                }
                unset($value);
                //    Then rebuild the formula string
                $formula = implode('"', $temp);
            } else {
                //    If there's no quoted strings, then we do a simple count/replace
                $formula = preg_replace($from, $to, $formula);
                $formula = self::translateSeparator($fromSeparator, $toSeparator, $formula, $inBraces);
            }
        }

        return $formula;
    }

    private static $functionReplaceFromExcel = null;
    private static $functionReplaceToLocale  = null;

    public function _translateFormulaToLocale($formula)
    {
        if (self::$functionReplaceFromExcel === null) {
            self::$functionReplaceFromExcel = array();
            foreach (array_keys(self::$localeFunctions) as $excelFunctionName) {
                self::$functionReplaceFromExcel[] = '/(@?[^\w\.])'.preg_quote($excelFunctionName).'([\s]*\()/Ui';
            }
            foreach (array_keys(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceFromExcel[] = '/(@?[^\w\.])'.preg_quote($excelBoolean).'([^\w\.])/Ui';
            }

        }

        if (self::$functionReplaceToLocale === null) {
            self::$functionReplaceToLocale = array();
            foreach (array_values(self::$localeFunctions) as $localeFunctionName) {
                self::$functionReplaceToLocale[] = '$1'.trim($localeFunctionName).'$2';
            }
            foreach (array_values(self::$localeBoolean) as $localeBoolean) {
                self::$functionReplaceToLocale[] = '$1'.trim($localeBoolean).'$2';
            }
        }

        return self::translateFormula(self::$functionReplaceFromExcel, self::$functionReplaceToLocale, $formula, ',', self::$localeArgumentSeparator);
    }


    private static $functionReplaceFromLocale = null;
    private static $functionReplaceToExcel    = null;

    public function _translateFormulaToEnglish($formula)
    {
        if (self::$functionReplaceFromLocale === null) {
            self::$functionReplaceFromLocale = array();
            foreach (array_values(self::$localeFunctions) as $localeFunctionName) {
                self::$functionReplaceFromLocale[] = '/(@?[^\w\.])'.preg_quote($localeFunctionName).'([\s]*\()/Ui';
            }
            foreach (array_values(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceFromLocale[] = '/(@?[^\w\.])'.preg_quote($excelBoolean).'([^\w\.])/Ui';
            }
        }

        if (self::$functionReplaceToExcel === null) {
            self::$functionReplaceToExcel = array();
            foreach (array_keys(self::$localeFunctions) as $excelFunctionName) {
                self::$functionReplaceToExcel[] = '$1'.trim($excelFunctionName).'$2';
            }
            foreach (array_keys(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceToExcel[] = '$1'.trim($excelBoolean).'$2';
            }
        }

        return self::translateFormula(self::$functionReplaceFromLocale, self::$functionReplaceToExcel, $formula, self::$localeArgumentSeparator, ',');
    }


    public static function localeFunc($function)
    {
        if (self::$localeLanguage !== 'en_us') {
            $functionName = trim($function, '(');
            if (isset(self::$localeFunctions[$functionName])) {
                $brace = ($functionName != $function);
                $function = self::$localeFunctions[$functionName];
                if ($brace) {
                    $function .= '(';
                }
            }
        }
        return $function;
    }




    /**
     * Wrap string values in quotes
     *
     * @param mixed $value
     * @return mixed
     */
    public static function wrapResult($value)
    {
        if (is_string($value)) {
            //    Error values cannot be "wrapped"
            if (preg_match('/^'.self::CALCULATION_REGEXP_ERROR.'$/i', $value, $match)) {
                //    Return Excel errors "as is"
                return $value;
            }
            //    Return strings wrapped in quotes
            return '"'.$value.'"';
        //    Convert numeric errors to NaN error
        } elseif ((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) {
            return PHPExcel_Calculation_Functions::NaN();
        }

        return $value;
    }


    /**
     * Remove quotes used as a wrapper to identify string values
     *
     * @param mixed $value
     * @return mixed
     */
    public static function unwrapResult($value)
    {
        if (is_string($value)) {
            if ((isset($value{0})) && ($value{0} == '"') && (substr($value, -1) == '"')) {
                return substr($value, 1, -1);
            }
        //    Convert numeric errors to NaN error
        } elseif ((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return $value;
    }




    /**
     * Calculate cell value (using formula from a cell ID)
     * Retained for backward compatibility
     *
     * @access    public
     * @param    PHPExcel_Cell    $pCell    Cell to calculate
     * @return    mixed
     * @throws    PHPExcel_Calculation_Exception
     */
    public function calculate(PHPExcel_Cell $pCell = null)
    {
        try {
            return $this->calculateCellValue($pCell);
        } catch (PHPExcel_Exception $e) {
            throw new PHPExcel_Calculation_Exception($e->getMessage());
        }
    }


    /**
     * Calculate the value of a cell formula
     *
     * @access    public
     * @param    PHPExcel_Cell    $pCell        Cell to calculate
     * @param    Boolean            $resetLog    Flag indicating whether the debug log should be reset or not
     * @return    mixed
     * @throws    PHPExcel_Calculation_Exception
     */
    public function calculateCellValue(PHPExcel_Cell $pCell = null, $resetLog = true)
    {
        if ($pCell === null) {
            return null;
        }

        $returnArrayAsType = self::$returnArrayAsType;
        if ($resetLog) {
            //    Initialise the logging settings if requested
            $this->formulaError = null;
            $this->_debugLog->clearLog();
            $this->cyclicReferenceStack->clear();
            $this->cyclicFormulaCounter = 1;

            self::$returnArrayAsType = self::RETURN_ARRAY_AS_ARRAY;
        }

        //    Execute the calculation for the cell formula
        $this->cellStack[] = array(
            'sheet' => $pCell->getWorksheet()->getTitle(),
            'cell' => $pCell->getCoordinate(),
        );
        try {
            $result = self::unwrapResult($this->_calculateFormulaValue($pCell->getValue(), $pCell->getCoordinate(), $pCell));
            $cellAddress = array_pop($this->cellStack);
            $this->workbook->getSheetByName($cellAddress['sheet'])->getCell($cellAddress['cell']);
        } catch (PHPExcel_Exception $e) {
            $cellAddress = array_pop($this->cellStack);
            $this->workbook->getSheetByName($cellAddress['sheet'])->getCell($cellAddress['cell']);
            throw new PHPExcel_Calculation_Exception($e->getMessage());
        }

        if ((is_array($result)) && (self::$returnArrayAsType != self::RETURN_ARRAY_AS_ARRAY)) {
            self::$returnArrayAsType = $returnArrayAsType;
            $testResult = PHPExcel_Calculation_Functions::flattenArray($result);
            if (self::$returnArrayAsType == self::RETURN_ARRAY_AS_ERROR) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            //    If there's only a single cell in the array, then we allow it
            if (count($testResult) != 1) {
                //    If keys are numeric, then it's a matrix result rather than a cell range result, so we permit it
                $r = array_keys($result);
                $r = array_shift($r);
                if (!is_numeric($r)) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                if (is_array($result[$r])) {
                    $c = array_keys($result[$r]);
                    $c = array_shift($c);
                    if (!is_numeric($c)) {
                        return PHPExcel_Calculation_Functions::VALUE();
                    }
                }
            }
            $result = array_shift($testResult);
        }
        self::$returnArrayAsType = $returnArrayAsType;


        if ($result === null) {
            return 0;
        } elseif ((is_float($result)) && ((is_nan($result)) || (is_infinite($result)))) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return $result;
    }


    /**
     * Validate and parse a formula string
     *
     * @param    string        $formula        Formula to parse
     * @return    array
     * @throws    PHPExcel_Calculation_Exception
     */
    public function parseFormula($formula)
    {
        //    Basic validation that this is indeed a formula
        //    We return an empty array if not
        $formula = trim($formula);
        if ((!isset($formula{0})) || ($formula{0} != '=')) {
            return array();
        }
        $formula = ltrim(substr($formula, 1));
        if (!isset($formula{0})) {
            return array();
        }

        //    Parse the formula and return the token stack
        return $this->_parseFormula($formula);
    }


    /**
     * Calculate the value of a formula
     *
     * @param    string            $formula    Formula to parse
     * @param    string            $cellID        Address of the cell to calculate
     * @param    PHPExcel_Cell    $pCell        Cell to calculate
     * @return    mixed
     * @throws    PHPExcel_Calculation_Exception
     */
    public function calculateFormula($formula, $cellID = null, PHPExcel_Cell $pCell = null)
    {
        //    Initialise the logging settings
        $this->formulaError = null;
        $this->_debugLog->clearLog();
        $this->cyclicReferenceStack->clear();

        if ($this->workbook !== null && $cellID === null && $pCell === null) {
            $cellID = 'A1';
            $pCell = $this->workbook->getActiveSheet()->getCell($cellID);
        } else {
            //    Disable calculation cacheing because it only applies to cell calculations, not straight formulae
            //    But don't actually flush any cache
            $resetCache = $this->getCalculationCacheEnabled();
            $this->calculationCacheEnabled = false;
        }

        //    Execute the calculation
        try {
            $result = self::unwrapResult($this->_calculateFormulaValue($formula, $cellID, $pCell));
        } catch (PHPExcel_Exception $e) {
            throw new PHPExcel_Calculation_Exception($e->getMessage());
        }

        if ($this->workbook === null) {
            //    Reset calculation cacheing to its previous state
            $this->calculationCacheEnabled = $resetCache;
        }

        return $result;
    }


    public function getValueFromCache($cellReference, &$cellValue)
    {
        // Is calculation cacheing enabled?
        // Is the value present in calculation cache?
        $this->_debugLog->writeDebugLog('Testing cache value for cell ', $cellReference);
        if (($this->calculationCacheEnabled) && (isset($this->calculationCache[$cellReference]))) {
            $this->_debugLog->writeDebugLog('Retrieving value for cell ', $cellReference, ' from cache');
            // Return the cached result
            $cellValue = $this->calculationCache[$cellReference];
            return true;
        }
        return false;
    }

    public function saveValueToCache($cellReference, $cellValue)
    {
        if ($this->calculationCacheEnabled) {
            $this->calculationCache[$cellReference] = $cellValue;
        }
    }

    /**
     * Parse a cell formula and calculate its value
     *
     * @param    string            $formula    The formula to parse and calculate
     * @param    string            $cellID        The ID (e.g. A3) of the cell that we are calculating
     * @param    PHPExcel_Cell    $pCell        Cell to calculate
     * @return    mixed
     * @throws    PHPExcel_Calculation_Exception
     */
    public function _calculateFormulaValue($formula, $cellID = null, PHPExcel_Cell $pCell = null)
    {
        $cellValue = null;

        //    Basic validation that this is indeed a formula
        //    We simply return the cell value if not
        $formula = trim($formula);
        if ($formula{0} != '=') {
            return self::wrapResult($formula);
        }
        $formula = ltrim(substr($formula, 1));
        if (!isset($formula{0})) {
            return self::wrapResult($formula);
        }

        $pCellParent = ($pCell !== null) ? $pCell->getWorksheet() : null;
        $wsTitle = ($pCellParent !== null) ? $pCellParent->getTitle() : "\x00Wrk";
        $wsCellReference = $wsTitle . '!' . $cellID;

        if (($cellID !== null) && ($this->getValueFromCache($wsCellReference, $cellValue))) {
            return $cellValue;
        }

        if (($wsTitle{0} !== "\x00") && ($this->cyclicReferenceStack->onStack($wsCellReference))) {
            if ($this->cyclicFormulaCount <= 0) {
                $this->cyclicFormulaCell = '';
                return $this->raiseFormulaError('Cyclic Reference in Formula');
            } elseif ($this->cyclicFormulaCell === $wsCellReference) {
                ++$this->cyclicFormulaCounter;
                if ($this->cyclicFormulaCounter >= $this->cyclicFormulaCount) {
                    $this->cyclicFormulaCell = '';
                    return $cellValue;
                }
            } elseif ($this->cyclicFormulaCell == '') {
                if ($this->cyclicFormulaCounter >= $this->cyclicFormulaCount) {
                    return $cellValue;
                }
                $this->cyclicFormulaCell = $wsCellReference;
            }
        }

        //    Parse the formula onto the token stack and calculate the value
        $this->cyclicReferenceStack->push($wsCellReference);
        $cellValue = $this->processTokenStack($this->_parseFormula($formula, $pCell), $cellID, $pCell);
        $this->cyclicReferenceStack->pop();

        // Save to calculation cache
        if ($cellID !== null) {
            $this->saveValueToCache($wsCellReference, $cellValue);
        }

        //    Return the calculated value
        return $cellValue;
    }


    /**
     * Ensure that paired matrix operands are both matrices and of the same size
     *
     * @param    mixed        &$operand1    First matrix operand
     * @param    mixed        &$operand2    Second matrix operand
     * @param    integer        $resize        Flag indicating whether the matrices should be resized to match
     *                                        and (if so), whether the smaller dimension should grow or the
     *                                        larger should shrink.
     *                                            0 = no resize
     *                                            1 = shrink to fit
     *                                            2 = extend to fit
     */
    private static function checkMatrixOperands(&$operand1, &$operand2, $resize = 1)
    {
        //    Examine each of the two operands, and turn them into an array if they aren't one already
        //    Note that this function should only be called if one or both of the operand is already an array
        if (!is_array($operand1)) {
            list($matrixRows, $matrixColumns) = self::getMatrixDimensions($operand2);
            $operand1 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand1));
            $resize = 0;
        } elseif (!is_array($operand2)) {
            list($matrixRows, $matrixColumns) = self::getMatrixDimensions($operand1);
            $operand2 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand2));
            $resize = 0;
        }

        list($matrix1Rows, $matrix1Columns) = self::getMatrixDimensions($operand1);
        list($matrix2Rows, $matrix2Columns) = self::getMatrixDimensions($operand2);
        if (($matrix1Rows == $matrix2Columns) && ($matrix2Rows == $matrix1Columns)) {
            $resize = 1;
        }

        if ($resize == 2) {
            //    Given two matrices of (potentially) unequal size, convert the smaller in each dimension to match the larger
            self::resizeMatricesExtend($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        } elseif ($resize == 1) {
            //    Given two matrices of (potentially) unequal size, convert the larger in each dimension to match the smaller
            self::resizeMatricesShrink($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        }
        return array( $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
    }


    /**
     * Read the dimensions of a matrix, and re-index it with straight numeric keys starting from row 0, column 0
     *
     * @param    mixed        &$matrix        matrix operand
     * @return    array        An array comprising the number of rows, and number of columns
     */
    private static function getMatrixDimensions(&$matrix)
    {
        $matrixRows = count($matrix);
        $matrixColumns = 0;
        foreach ($matrix as $rowKey => $rowValue) {
            $matrixColumns = max(count($rowValue), $matrixColumns);
            if (!is_array($rowValue)) {
                $matrix[$rowKey] = array($rowValue);
            } else {
                $matrix[$rowKey] = array_values($rowValue);
            }
        }
        $matrix = array_values($matrix);
        return array($matrixRows, $matrixColumns);
    }


    /**
     * Ensure that paired matrix operands are both matrices of the same size
     *
     * @param    mixed        &$matrix1        First matrix operand
     * @param    mixed        &$matrix2        Second matrix operand
     * @param    integer        $matrix1Rows    Row size of first matrix operand
     * @param    integer        $matrix1Columns    Column size of first matrix operand
     * @param    integer        $matrix2Rows    Row size of second matrix operand
     * @param    integer        $matrix2Columns    Column size of second matrix operand
     */
    private static function resizeMatricesShrink(&$matrix1, &$matrix2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns)
    {
        if (($matrix2Columns < $matrix1Columns) || ($matrix2Rows < $matrix1Rows)) {
            if ($matrix2Rows < $matrix1Rows) {
                for ($i = $matrix2Rows; $i < $matrix1Rows; ++$i) {
                    unset($matrix1[$i]);
                }
            }
            if ($matrix2Columns < $matrix1Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        unset($matrix1[$i][$j]);
                    }
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Rows < $matrix2Rows) {
                for ($i = $matrix1Rows; $i < $matrix2Rows; ++$i) {
                    unset($matrix2[$i]);
                }
            }
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        unset($matrix2[$i][$j]);
                    }
                }
            }
        }
    }


    /**
     * Ensure that paired matrix operands are both matrices of the same size
     *
     * @param    mixed        &$matrix1    First matrix operand
     * @param    mixed        &$matrix2    Second matrix operand
     * @param    integer        $matrix1Rows    Row size of first matrix operand
     * @param    integer        $matrix1Columns    Column size of first matrix operand
     * @param    integer        $matrix2Rows    Row size of second matrix operand
     * @param    integer        $matrix2Columns    Column size of second matrix operand
     */
    private static function resizeMatricesExtend(&$matrix1, &$matrix2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns)
    {
        if (($matrix2Columns < $matrix1Columns) || ($matrix2Rows < $matrix1Rows)) {
            if ($matrix2Columns < $matrix1Columns) {
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    $x = $matrix2[$i][$matrix2Columns-1];
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        $matrix2[$i][$j] = $x;
                    }
                }
            }
            if ($matrix2Rows < $matrix1Rows) {
                $x = $matrix2[$matrix2Rows-1];
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    $matrix2[$i] = $x;
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    $x = $matrix1[$i][$matrix1Columns-1];
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        $matrix1[$i][$j] = $x;
                    }
                }
            }
            if ($matrix1Rows < $matrix2Rows) {
                $x = $matrix1[$matrix1Rows-1];
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    $matrix1[$i] = $x;
                }
            }
        }
    }


    /**
     * Format details of an operand for display in the log (based on operand type)
     *
     * @param    mixed        $value    First matrix operand
     * @return    mixed
     */
    private function showValue($value)
    {
        if ($this->_debugLog->getWriteDebugLog()) {
            $testArray = PHPExcel_Calculation_Functions::flattenArray($value);
            if (count($testArray) == 1) {
                $value = array_pop($testArray);
            }

            if (is_array($value)) {
                $returnMatrix = array();
                $pad = $rpad = ', ';
                foreach ($value as $row) {
                    if (is_array($row)) {
                        $returnMatrix[] = implode($pad, array_map(array($this, 'showValue'), $row));
                        $rpad = '; ';
                    } else {
                        $returnMatrix[] = $this->showValue($row);
                    }
                }
                return '{ '.implode($rpad, $returnMatrix).' }';
            } elseif (is_string($value) && (trim($value, '"') == $value)) {
                return '"'.$value.'"';
            } elseif (is_bool($value)) {
                return ($value) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
            }
        }
        return PHPExcel_Calculation_Functions::flattenSingleValue($value);
    }


    /**
     * Format type and details of an operand for display in the log (based on operand type)
     *
     * @param    mixed        $value    First matrix operand
     * @return    mixed
     */
    private function showTypeDetails($value)
    {
        if ($this->_debugLog->getWriteDebugLog()) {
            $testArray = PHPExcel_Calculation_Functions::flattenArray($value);
            if (count($testArray) == 1) {
                $value = array_pop($testArray);
            }

            if ($value === null) {
                return 'a NULL value';
            } elseif (is_float($value)) {
                $typeString = 'a floating point number';
            } elseif (is_int($value)) {
                $typeString = 'an integer number';
            } elseif (is_bool($value)) {
                $typeString = 'a boolean';
            } elseif (is_array($value)) {
                $typeString = 'a matrix';
            } else {
                if ($value == '') {
                    return 'an empty string';
                } elseif ($value{0} == '#') {
                    return 'a '.$value.' error';
                } else {
                    $typeString = 'a string';
                }
            }
            return $typeString.' with a value of '.$this->showValue($value);
        }
    }


    private function convertMatrixReferences($formula)
    {
        static $matrixReplaceFrom = array('{', ';', '}');
        static $matrixReplaceTo = array('MKMATRIX(MKMATRIX(', '),MKMATRIX(', '))');

        //    Convert any Excel matrix references to the MKMATRIX() function
        if (strpos($formula, '{') !== false) {
            //    If there is the possibility of braces within a quoted string, then we don't treat those as matrix indicators
            if (strpos($formula, '"') !== false) {
                //    So instead we skip replacing in any quoted strings by only replacing in every other array element after we've exploded
                //        the formula
                $temp = explode('"', $formula);
                //    Open and Closed counts used for trapping mismatched braces in the formula
                $openCount = $closeCount = 0;
                $i = false;
                foreach ($temp as &$value) {
                    //    Only count/replace in alternating array entries
                    if ($i = !$i) {
                        $openCount += substr_count($value, '{');
                        $closeCount += substr_count($value, '}');
                        $value = str_replace($matrixReplaceFrom, $matrixReplaceTo, $value);
                    }
                }
                unset($value);
                //    Then rebuild the formula string
                $formula = implode('"', $temp);
            } else {
                //    If there's no quoted strings, then we do a simple count/replace
                $openCount = substr_count($formula, '{');
                $closeCount = substr_count($formula, '}');
                $formula = str_replace($matrixReplaceFrom, $matrixReplaceTo, $formula);
            }
            //    Trap for mismatched braces and trigger an appropriate error
            if ($openCount < $closeCount) {
                if ($openCount > 0) {
                    return $this->raiseFormulaError("Formula Error: Mismatched matrix braces '}'");
                } else {
                    return $this->raiseFormulaError("Formula Error: Unexpected '}' encountered");
                }
            } elseif ($openCount > $closeCount) {
                if ($closeCount > 0) {
                    return $this->raiseFormulaError("Formula Error: Mismatched matrix braces '{'");
                } else {
                    return $this->raiseFormulaError("Formula Error: Unexpected '{' encountered");
                }
            }
        }

        return $formula;
    }


    private static function mkMatrix()
    {
        return func_get_args();
    }


    //    Binary Operators
    //    These operators always work on two values
    //    Array key is the operator, the value indicates whether this is a left or right associative operator
    private static $operatorAssociativity    = array(
        '^' => 0,                                                            //    Exponentiation
        '*' => 0, '/' => 0,                                                 //    Multiplication and Division
        '+' => 0, '-' => 0,                                                    //    Addition and Subtraction
        '&' => 0,                                                            //    Concatenation
        '|' => 0, ':' => 0,                                                    //    Intersect and Range
        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0        //    Comparison
    );

    //    Comparison (Boolean) Operators
    //    These operators work on two values, but always return a boolean result
    private static $comparisonOperators    = array('>' => true, '<' => true, '=' => true, '>=' => true, '<=' => true, '<>' => true);

    //    Operator Precedence
    //    This list includes all valid operators, whether binary (including boolean) or unary (such as %)
    //    Array key is the operator, the value is its precedence
    private static $operatorPrecedence    = array(
        ':' => 8,                                                                //    Range
        '|' => 7,                                                                //    Intersect
        '~' => 6,                                                                //    Negation
        '%' => 5,                                                                //    Percentage
        '^' => 4,                                                                //    Exponentiation
        '*' => 3, '/' => 3,                                                     //    Multiplication and Division
        '+' => 2, '-' => 2,                                                        //    Addition and Subtraction
        '&' => 1,                                                                //    Concatenation
        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0            //    Comparison
    );

    // Convert infix to postfix notation
    private function _parseFormula($formula, PHPExcel_Cell $pCell = null)
    {
        if (($formula = $this->convertMatrixReferences(trim($formula))) === false) {
            return false;
        }

        //    If we're using cell caching, then $pCell may well be flushed back to the cache (which detaches the parent worksheet),
        //        so we store the parent worksheet so that we can re-attach it when necessary
        $pCellParent = ($pCell !== null) ? $pCell->getWorksheet() : null;

        $regexpMatchString = '/^('.self::CALCULATION_REGEXP_FUNCTION.
                               '|'.self::CALCULATION_REGEXP_CELLREF.
                               '|'.self::CALCULATION_REGEXP_NUMBER.
                               '|'.self::CALCULATION_REGEXP_STRING.
                               '|'.self::CALCULATION_REGEXP_OPENBRACE.
                               '|'.self::CALCULATION_REGEXP_NAMEDRANGE.
                               '|'.self::CALCULATION_REGEXP_ERROR.
                             ')/si';

        //    Start with initialisation
        $index = 0;
        $stack = new PHPExcel_Calculation_Token_Stack;
        $output = array();
        $expectingOperator = false;                    //    We use this test in syntax-checking the expression to determine when a
                                                    //        - is a negation or + is a positive operator rather than an operation
        $expectingOperand = false;                    //    We use this test in syntax-checking the expression to determine whether an operand
                                                    //        should be null in a function call
        //    The guts of the lexical parser
        //    Loop through the formula extracting each operator and operand in turn
        while (true) {
//echo 'Assessing Expression '.substr($formula, $index), PHP_EOL;
            $opCharacter = $formula{$index};    //    Get the first character of the value at the current index position
//echo 'Initial character of expression block is '.$opCharacter, PHP_EOL;
            if ((isset(self::$comparisonOperators[$opCharacter])) && (strlen($formula) > $index) && (isset(self::$comparisonOperators[$formula{$index+1}]))) {
                $opCharacter .= $formula{++$index};
//echo 'Initial character of expression block is comparison operator '.$opCharacter.PHP_EOL;
            }

            //    Find out if we're currently at the beginning of a number, variable, cell reference, function, parenthesis or operand
            $isOperandOrFunction = preg_match($regexpMatchString, substr($formula, $index), $match);
//echo '$isOperandOrFunction is '.(($isOperandOrFunction) ? 'True' : 'False').PHP_EOL;
//var_dump($match);

            if ($opCharacter == '-' && !$expectingOperator) {                //    Is it a negation instead of a minus?
//echo 'Element is a Negation operator', PHP_EOL;
                $stack->push('Unary Operator', '~');                            //    Put a negation on the stack
                ++$index;                                                    //        and drop the negation symbol
            } elseif ($opCharacter == '%' && $expectingOperator) {
//echo 'Element is a Percentage operator', PHP_EOL;
                $stack->push('Unary Operator', '%');                            //    Put a percentage on the stack
                ++$index;
            } elseif ($opCharacter == '+' && !$expectingOperator) {            //    Positive (unary plus rather than binary operator plus) can be discarded?
//echo 'Element is a Positive number, not Plus operator', PHP_EOL;
                ++$index;                                                    //    Drop the redundant plus symbol
            } elseif ((($opCharacter == '~') || ($opCharacter == '|')) && (!$isOperandOrFunction)) {    //    We have to explicitly deny a tilde or pipe, because they are legal
                return $this->raiseFormulaError("Formula Error: Illegal character '~'");                //        on the stack but not in the input expression

            } elseif ((isset(self::$operators[$opCharacter]) or $isOperandOrFunction) && $expectingOperator) {    //    Are we putting an operator on the stack?
//echo 'Element with value '.$opCharacter.' is an Operator', PHP_EOL;
                while ($stack->count() > 0 &&
                    ($o2 = $stack->last()) &&
                    isset(self::$operators[$o2['value']]) &&
                    @(self::$operatorAssociativity[$opCharacter] ? self::$operatorPrecedence[$opCharacter] < self::$operatorPrecedence[$o2['value']] : self::$operatorPrecedence[$opCharacter] <= self::$operatorPrecedence[$o2['value']])) {
                    $output[] = $stack->pop();                                //    Swap operands and higher precedence operators from the stack to the output
                }
                $stack->push('Binary Operator', $opCharacter);    //    Finally put our current operator onto the stack
                ++$index;
                $expectingOperator = false;

            } elseif ($opCharacter == ')' && $expectingOperator) {            //    Are we expecting to close a parenthesis?
//echo 'Element is a Closing bracket', PHP_EOL;
                $expectingOperand = false;
                while (($o2 = $stack->pop()) && $o2['value'] != '(') {        //    Pop off the stack back to the last (
                    if ($o2 === null) {
                        return $this->raiseFormulaError('Formula Error: Unexpected closing brace ")"');
                    } else {
                        $output[] = $o2;
                    }
                }
                $d = $stack->last(2);
                if (preg_match('/^'.self::CALCULATION_REGEXP_FUNCTION.'$/i', $d['value'], $matches)) {    //    Did this parenthesis just close a function?
                    $functionName = $matches[1];                                        //    Get the function name
//echo 'Closed Function is '.$functionName, PHP_EOL;
                    $d = $stack->pop();
                    $argumentCount = $d['value'];        //    See how many arguments there were (argument count is the next value stored on the stack)
//if ($argumentCount == 0) {
//    echo 'With no arguments', PHP_EOL;
//} elseif ($argumentCount == 1) {
//    echo 'With 1 argument', PHP_EOL;
//} else {
//    echo 'With '.$argumentCount.' arguments', PHP_EOL;
//}
                    $output[] = $d;                        //    Dump the argument count on the output
                    $output[] = $stack->pop();            //    Pop the function and push onto the output
                    if (isset(self::$controlFunctions[$functionName])) {
//echo 'Built-in function '.$functionName, PHP_EOL;
                        $expectedArgumentCount = self::$controlFunctions[$functionName]['argumentCount'];
                        $functionCall = self::$controlFunctions[$functionName]['functionCall'];
                    } elseif (isset(self::$PHPExcelFunctions[$functionName])) {
//echo 'PHPExcel function '.$functionName, PHP_EOL;
                        $expectedArgumentCount = self::$PHPExcelFunctions[$functionName]['argumentCount'];
                        $functionCall = self::$PHPExcelFunctions[$functionName]['functionCall'];
                    } else {    // did we somehow push a non-function on the stack? this should never happen
                        return $this->raiseFormulaError("Formula Error: Internal error, non-function on stack");
                    }
                    //    Check the argument count
                    $argumentCountError = false;
                    if (is_numeric($expectedArgumentCount)) {
                        if ($expectedArgumentCount < 0) {
//echo '$expectedArgumentCount is between 0 and '.abs($expectedArgumentCount), PHP_EOL;
                            if ($argumentCount > abs($expectedArgumentCount)) {
                                $argumentCountError = true;
                                $expectedArgumentCountString = 'no more than '.abs($expectedArgumentCount);
                            }
                        } else {
//echo '$expectedArgumentCount is numeric '.$expectedArgumentCount, PHP_EOL;
                            if ($argumentCount != $expectedArgumentCount) {
                                $argumentCountError = true;
                                $expectedArgumentCountString = $expectedArgumentCount;
                            }
                        }
                    } elseif ($expectedArgumentCount != '*') {
                        $isOperandOrFunction = preg_match('/(\d*)([-+,])(\d*)/', $expectedArgumentCount, $argMatch);
//print_r($argMatch);
//echo PHP_EOL;
                        switch ($argMatch[2]) {
                            case '+':
                                if ($argumentCount < $argMatch[1]) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = $argMatch[1].' or more ';
                                }
                                break;
                            case '-':
                                if (($argumentCount < $argMatch[1]) || ($argumentCount > $argMatch[3])) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = 'between '.$argMatch[1].' and '.$argMatch[3];
                                }
                                break;
                            case ',':
                                if (($argumentCount != $argMatch[1]) && ($argumentCount != $argMatch[3])) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = 'either '.$argMatch[1].' or '.$argMatch[3];
                                }
                                break;
                        }
                    }
                    if ($argumentCountError) {
                        return $this->raiseFormulaError("Formula Error: Wrong number of arguments for $functionName() function: $argumentCount given, ".$expectedArgumentCountString." expected");
                    }
                }
                ++$index;

            } elseif ($opCharacter == ',') {            //    Is this the separator for function arguments?
//echo 'Element is a Function argument separator', PHP_EOL;
                while (($o2 = $stack->pop()) && $o2['value'] != '(') {        //    Pop off the stack back to the last (
                    if ($o2 === null) {
                        return $this->raiseFormulaError("Formula Error: Unexpected ,");
                    } else {
                        $output[] = $o2;    // pop the argument expression stuff and push onto the output
                    }
                }
                //    If we've a comma when we're expecting an operand, then what we actually have is a null operand;
                //        so push a null onto the stack
                if (($expectingOperand) || (!$expectingOperator)) {
                    $output[] = array('type' => 'NULL Value', 'value' => self::$excelConstants['NULL'], 'reference' => null);
                }
                // make sure there was a function
                $d = $stack->last(2);
                if (!preg_match('/^'.self::CALCULATION_REGEXP_FUNCTION.'$/i', $d['value'], $matches)) {
                    return $this->raiseFormulaError("Formula Error: Unexpected ,");
                }
                $d = $stack->pop();
                $stack->push($d['type'], ++$d['value'], $d['reference']);    // increment the argument count
                $stack->push('Brace', '(');    // put the ( back on, we'll need to pop back to it again
                $expectingOperator = false;
                $expectingOperand = true;
                ++$index;

            } elseif ($opCharacter == '(' && !$expectingOperator) {
//                echo 'Element is an Opening Bracket<br />';
                $stack->push('Brace', '(');
                ++$index;

            } elseif ($isOperandOrFunction && !$expectingOperator) {    // do we now have a function/variable/number?
                $expectingOperator = true;
                $expectingOperand = false;
                $val = $match[1];
                $length = strlen($val);
//                echo 'Element with value '.$val.' is an Operand, Variable, Constant, String, Number, Cell Reference or Function<br />';

                if (preg_match('/^'.self::CALCULATION_REGEXP_FUNCTION.'$/i', $val, $matches)) {
                    $val = preg_replace('/\s/u', '', $val);
//                    echo 'Element '.$val.' is a Function<br />';
                    if (isset(self::$PHPExcelFunctions[strtoupper($matches[1])]) || isset(self::$controlFunctions[strtoupper($matches[1])])) {    // it's a function
                        $stack->push('Function', strtoupper($val));
                        $ax = preg_match('/^\s*(\s*\))/ui', substr($formula, $index+$length), $amatch);
                        if ($ax) {
                            $stack->push('Operand Count for Function '.strtoupper($val).')', 0);
                            $expectingOperator = true;
                        } else {
                            $stack->push('Operand Count for Function '.strtoupper($val).')', 1);
                            $expectingOperator = false;
                        }
                        $stack->push('Brace', '(');
                    } else {    // it's a var w/ implicit multiplication
                        $output[] = array('type' => 'Value', 'value' => $matches[1], 'reference' => null);
                    }
                } elseif (preg_match('/^'.self::CALCULATION_REGEXP_CELLREF.'$/i', $val, $matches)) {
//                    echo 'Element '.$val.' is a Cell reference<br />';
                    //    Watch for this case-change when modifying to allow cell references in different worksheets...
                    //    Should only be applied to the actual cell column, not the worksheet name

                    //    If the last entry on the stack was a : operator, then we have a cell range reference
                    $testPrevOp = $stack->last(1);
                    if ($testPrevOp['value'] == ':') {
                        //    If we have a worksheet reference, then we're playing with a 3D reference
                        if ($matches[2] == '') {
                            //    Otherwise, we 'inherit' the worksheet reference from the start cell reference
                            //    The start of the cell range reference should be the last entry in $output
                            $startCellRef = $output[count($output)-1]['value'];
                            preg_match('/^'.self::CALCULATION_REGEXP_CELLREF.'$/i', $startCellRef, $startMatches);
                            if ($startMatches[2] > '') {
                                $val = $startMatches[2].'!'.$val;
                            }
                        } else {
                            return $this->raiseFormulaError("3D Range references are not yet supported");
                        }
                    }

                    $output[] = array('type' => 'Cell Reference', 'value' => $val, 'reference' => $val);
//                    $expectingOperator = FALSE;
                } else {    // it's a variable, constant, string, number or boolean
//                    echo 'Element is a Variable, Constant, String, Number or Boolean<br />';
                    //    If the last entry on the stack was a : operator, then we may have a row or column range reference
                    $testPrevOp = $stack->last(1);
                    if ($testPrevOp['value'] == ':') {
                        $startRowColRef = $output[count($output)-1]['value'];
                        $rangeWS1 = '';
                        if (strpos('!', $startRowColRef) !== false) {
                            list($rangeWS1, $startRowColRef) = explode('!', $startRowColRef);
                        }
                        if ($rangeWS1 != '') {
                            $rangeWS1 .= '!';
                        }
                        $rangeWS2 = $rangeWS1;
                        if (strpos('!', $val) !== false) {
                            list($rangeWS2, $val) = explode('!', $val);
                        }
                        if ($rangeWS2 != '') {
                            $rangeWS2 .= '!';
                        }
                        if ((is_integer($startRowColRef)) && (ctype_digit($val)) &&
                            ($startRowColRef <= 1048576) && ($val <= 1048576)) {
                            //    Row range
                            $endRowColRef = ($pCellParent !== null) ? $pCellParent->getHighestColumn() : 'XFD';    //    Max 16,384 columns for Excel2007
                            $output[count($output)-1]['value'] = $rangeWS1.'A'.$startRowColRef;
                            $val = $rangeWS2.$endRowColRef.$val;
                        } elseif ((ctype_alpha($startRowColRef)) && (ctype_alpha($val)) &&
                            (strlen($startRowColRef) <= 3) && (strlen($val) <= 3)) {
                            //    Column range
                            $endRowColRef = ($pCellParent !== null) ? $pCellParent->getHighestRow() : 1048576;        //    Max 1,048,576 rows for Excel2007
                            $output[count($output)-1]['value'] = $rangeWS1.strtoupper($startRowColRef).'1';
                            $val = $rangeWS2.$val.$endRowColRef;
                        }
                    }

                    $localeConstant = false;
                    if ($opCharacter == '"') {
//                        echo 'Element is a String<br />';
                        //    UnEscape any quotes within the string
                        $val = self::wrapResult(str_replace('""', '"', self::unwrapResult($val)));
                    } elseif (is_numeric($val)) {
//                        echo 'Element is a Number<br />';
                        if ((strpos($val, '.') !== false) || (stripos($val, 'e') !== false) || ($val > PHP_INT_MAX) || ($val < -PHP_INT_MAX)) {
//                            echo 'Casting '.$val.' to float<br />';
                            $val = (float) $val;
                        } else {
//                            echo 'Casting '.$val.' to integer<br />';
                            $val = (integer) $val;
                        }
                    } elseif (isset(self::$excelConstants[trim(strtoupper($val))])) {
                        $excelConstant = trim(strtoupper($val));
//                        echo 'Element '.$excelConstant.' is an Excel Constant<br />';
                        $val = self::$excelConstants[$excelConstant];
                    } elseif (($localeConstant = array_search(trim(strtoupper($val)), self::$localeBoolean)) !== false) {
//                        echo 'Element '.$localeConstant.' is an Excel Constant<br />';
                        $val = self::$excelConstants[$localeConstant];
                    }
                    $details = array('type' => 'Value', 'value' => $val, 'reference' => null);
                    if ($localeConstant) {
                        $details['localeValue'] = $localeConstant;
                    }
                    $output[] = $details;
                }
                $index += $length;

            } elseif ($opCharacter == '$') {    // absolute row or column range
                ++$index;
            } elseif ($opCharacter == ')') {    // miscellaneous error checking
                if ($expectingOperand) {
                    $output[] = array('type' => 'NULL Value', 'value' => self::$excelConstants['NULL'], 'reference' => null);
                    $expectingOperand = false;
                    $expectingOperator = true;
                } else {
                    return $this->raiseFormulaError("Formula Error: Unexpected ')'");
                }
            } elseif (isset(self::$operators[$opCharacter]) && !$expectingOperator) {
                return $this->raiseFormulaError("Formula Error: Unexpected operator '$opCharacter'");
            } else {    // I don't even want to know what you did to get here
                return $this->raiseFormulaError("Formula Error: An unexpected error occured");
            }
            //    Test for end of formula string
            if ($index == strlen($formula)) {
                //    Did we end with an operator?.
                //    Only valid for the % unary operator
                if ((isset(self::$operators[$opCharacter])) && ($opCharacter != '%')) {
                    return $this->raiseFormulaError("Formula Error: Operator '$opCharacter' has no operands");
                } else {
                    break;
                }
            }
            //    Ignore white space
            while (($formula{$index} == "\n") || ($formula{$index} == "\r")) {
                ++$index;
            }
            if ($formula{$index} == ' ') {
                while ($formula{$index} == ' ') {
                    ++$index;
                }
                //    If we're expecting an operator, but only have a space between the previous and next operands (and both are
                //        Cell References) then we have an INTERSECTION operator
//                echo 'Possible Intersect Operator<br />';
                if (($expectingOperator) && (preg_match('/^'.self::CALCULATION_REGEXP_CELLREF.'.*/Ui', substr($formula, $index), $match)) &&
                    ($output[count($output)-1]['type'] == 'Cell Reference')) {
//                    echo 'Element is an Intersect Operator<br />';
                    while ($stack->count() > 0 &&
                        ($o2 = $stack->last()) &&
                        isset(self::$operators[$o2['value']]) &&
                        @(self::$operatorAssociativity[$opCharacter] ? self::$operatorPrecedence[$opCharacter] < self::$operatorPrecedence[$o2['value']] : self::$operatorPrecedence[$opCharacter] <= self::$operatorPrecedence[$o2['value']])) {
                        $output[] = $stack->pop();                                //    Swap operands and higher precedence operators from the stack to the output
                    }
                    $stack->push('Binary Operator', '|');    //    Put an Intersect Operator on the stack
                    $expectingOperator = false;
                }
            }
        }

        while (($op = $stack->pop()) !== null) {    // pop everything off the stack and push onto output
            if ((is_array($op) && $op['value'] == '(') || ($op === '(')) {
                return $this->raiseFormulaError("Formula Error: Expecting ')'");    // if there are any opening braces on the stack, then braces were unbalanced
            }
            $output[] = $op;
        }
        return $output;
    }


    private static function dataTestReference(&$operandData)
    {
        $operand = $operandData['value'];
        if (($operandData['reference'] === null) && (is_array($operand))) {
            $rKeys = array_keys($operand);
            $rowKey = array_shift($rKeys);
            $cKeys = array_keys(array_keys($operand[$rowKey]));
            $colKey = array_shift($cKeys);
            if (ctype_upper($colKey)) {
                $operandData['reference'] = $colKey.$rowKey;
            }
        }
        return $operand;
    }

    // evaluate postfix notation
    private function processTokenStack($tokens, $cellID = null, PHPExcel_Cell $pCell = null)
    {
        if ($tokens == false) {
            return false;
        }

        //    If we're using cell caching, then $pCell may well be flushed back to the cache (which detaches the parent cell collection),
        //        so we store the parent cell collection so that we can re-attach it when necessary
        $pCellWorksheet = ($pCell !== null) ? $pCell->getWorksheet() : null;
        $pCellParent = ($pCell !== null) ? $pCell->getParent() : null;
        $stack = new PHPExcel_Calculation_Token_Stack;

        //    Loop through each token in turn
        foreach ($tokens as $tokenData) {
//            print_r($tokenData);
//            echo '<br />';
            $token = $tokenData['value'];
//            echo '<b>Token is '.$token.'</b><br />';
            // if the token is a binary operator, pop the top two values off the stack, do the operation, and push the result back on the stack
            if (isset(self::$binaryOperators[$token])) {
//                echo 'Token is a binary operator<br />';
                //    We must have two operands, error if we don't
                if (($operand2Data = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }
                if (($operand1Data = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }

                $operand1 = self::dataTestReference($operand1Data);
                $operand2 = self::dataTestReference($operand2Data);

                //    Log what we're doing
                if ($token == ':') {
                    $this->_debugLog->writeDebugLog('Evaluating Range ', $this->showValue($operand1Data['reference']), ' ', $token, ' ', $this->showValue($operand2Data['reference']));
                } else {
                    $this->_debugLog->writeDebugLog('Evaluating ', $this->showValue($operand1), ' ', $token, ' ', $this->showValue($operand2));
                }

                //    Process the operation in the appropriate manner
                switch ($token) {
                    //    Comparison (Boolean) Operators
                    case '>':            //    Greater than
                    case '<':            //    Less than
                    case '>=':            //    Greater than or Equal to
                    case '<=':            //    Less than or Equal to
                    case '=':            //    Equality
                    case '<>':            //    Inequality
                        $this->executeBinaryComparisonOperation($cellID, $operand1, $operand2, $token, $stack);
                        break;
                    //    Binary Operators
                    case ':':            //    Range
                        $sheet1 = $sheet2 = '';
                        if (strpos($operand1Data['reference'], '!') !== false) {
                            list($sheet1, $operand1Data['reference']) = explode('!', $operand1Data['reference']);
                        } else {
                            $sheet1 = ($pCellParent !== null) ? $pCellWorksheet->getTitle() : '';
                        }
                        if (strpos($operand2Data['reference'], '!') !== false) {
                            list($sheet2, $operand2Data['reference']) = explode('!', $operand2Data['reference']);
                        } else {
                            $sheet2 = $sheet1;
                        }
                        if ($sheet1 == $sheet2) {
                            if ($operand1Data['reference'] === null) {
                                if ((trim($operand1Data['value']) != '') && (is_numeric($operand1Data['value']))) {
                                    $operand1Data['reference'] = $pCell->getColumn().$operand1Data['value'];
                                } elseif (trim($operand1Data['reference']) == '') {
                                    $operand1Data['reference'] = $pCell->getCoordinate();
                                } else {
                                    $operand1Data['reference'] = $operand1Data['value'].$pCell->getRow();
                                }
                            }
                            if ($operand2Data['reference'] === null) {
                                if ((trim($operand2Data['value']) != '') && (is_numeric($operand2Data['value']))) {
                                    $operand2Data['reference'] = $pCell->getColumn().$operand2Data['value'];
                                } elseif (trim($operand2Data['reference']) == '') {
                                    $operand2Data['reference'] = $pCell->getCoordinate();
                                } else {
                                    $operand2Data['reference'] = $operand2Data['value'].$pCell->getRow();
                                }
                            }

                            $oData = array_merge(explode(':', $operand1Data['reference']), explode(':', $operand2Data['reference']));
                            $oCol = $oRow = array();
                            foreach ($oData as $oDatum) {
                                $oCR = PHPExcel_Cell::coordinateFromString($oDatum);
                                $oCol[] = PHPExcel_Cell::columnIndexFromString($oCR[0]) - 1;
                                $oRow[] = $oCR[1];
                            }
                            $cellRef = PHPExcel_Cell::stringFromColumnIndex(min($oCol)).min($oRow).':'.PHPExcel_Cell::stringFromColumnIndex(max($oCol)).max($oRow);
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $this->workbook->getSheetByName($sheet1), false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $stack->push('Cell Reference', $cellValue, $cellRef);
                        } else {
                            $stack->push('Error', PHPExcel_Calculation_Functions::REF(), null);
                        }
                        break;
                    case '+':            //    Addition
                        $this->executeNumericBinaryOperation($cellID, $operand1, $operand2, $token, 'plusEquals', $stack);
                        break;
                    case '-':            //    Subtraction
                        $this->executeNumericBinaryOperation($cellID, $operand1, $operand2, $token, 'minusEquals', $stack);
                        break;
                    case '*':            //    Multiplication
                        $this->executeNumericBinaryOperation($cellID, $operand1, $operand2, $token, 'arrayTimesEquals', $stack);
                        break;
                    case '/':            //    Division
                        $this->executeNumericBinaryOperation($cellID, $operand1, $operand2, $token, 'arrayRightDivide', $stack);
                        break;
                    case '^':            //    Exponential
                        $this->executeNumericBinaryOperation($cellID, $operand1, $operand2, $token, 'power', $stack);
                        break;
                    case '&':            //    Concatenation
                        //    If either of the operands is a matrix, we need to treat them both as matrices
                        //        (converting the other operand to a matrix if need be); then perform the required
                        //        matrix operation
                        if (is_bool($operand1)) {
                            $operand1 = ($operand1) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
                        }
                        if (is_bool($operand2)) {
                            $operand2 = ($operand2) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
                        }
                        if ((is_array($operand1)) || (is_array($operand2))) {
                            //    Ensure that both operands are arrays/matrices
                            self::checkMatrixOperands($operand1, $operand2, 2);
                            try {
                                //    Convert operand 1 from a PHP array to a matrix
                                $matrix = new PHPExcel_Shared_JAMA_Matrix($operand1);
                                //    Perform the required operation against the operand 1 matrix, passing in operand 2
                                $matrixResult = $matrix->concat($operand2);
                                $result = $matrixResult->getArray();
                            } catch (PHPExcel_Exception $ex) {
                                $this->_debugLog->writeDebugLog('JAMA Matrix Exception: ', $ex->getMessage());
                                $result = '#VALUE!';
                            }
                        } else {
                            $result = '"'.str_replace('""', '"', self::unwrapResult($operand1, '"').self::unwrapResult($operand2, '"')).'"';
                        }
                        $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
                        $stack->push('Value', $result);
                        break;
                    case '|':            //    Intersect
                        $rowIntersect = array_intersect_key($operand1, $operand2);
                        $cellIntersect = $oCol = $oRow = array();
                        foreach (array_keys($rowIntersect) as $row) {
                            $oRow[] = $row;
                            foreach ($rowIntersect[$row] as $col => $data) {
                                $oCol[] = PHPExcel_Cell::columnIndexFromString($col) - 1;
                                $cellIntersect[$row] = array_intersect_key($operand1[$row], $operand2[$row]);
                            }
                        }
                        $cellRef = PHPExcel_Cell::stringFromColumnIndex(min($oCol)).min($oRow).':'.PHPExcel_Cell::stringFromColumnIndex(max($oCol)).max($oRow);
                        $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($cellIntersect));
                        $stack->push('Value', $cellIntersect, $cellRef);
                        break;
                }

            // if the token is a unary operator, pop one value off the stack, do the operation, and push it back on
            } elseif (($token === '~') || ($token === '%')) {
//                echo 'Token is a unary operator<br />';
                if (($arg = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }
                $arg = $arg['value'];
                if ($token === '~') {
//                    echo 'Token is a negation operator<br />';
                    $this->_debugLog->writeDebugLog('Evaluating Negation of ', $this->showValue($arg));
                    $multiplier = -1;
                } else {
//                    echo 'Token is a percentile operator<br />';
                    $this->_debugLog->writeDebugLog('Evaluating Percentile of ', $this->showValue($arg));
                    $multiplier = 0.01;
                }
                if (is_array($arg)) {
                    self::checkMatrixOperands($arg, $multiplier, 2);
                    try {
                        $matrix1 = new PHPExcel_Shared_JAMA_Matrix($arg);
                        $matrixResult = $matrix1->arrayTimesEquals($multiplier);
                        $result = $matrixResult->getArray();
                    } catch (PHPExcel_Exception $ex) {
                        $this->_debugLog->writeDebugLog('JAMA Matrix Exception: ', $ex->getMessage());
                        $result = '#VALUE!';
                    }
                    $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
                    $stack->push('Value', $result);
                } else {
                    $this->executeNumericBinaryOperation($cellID, $multiplier, $arg, '*', 'arrayTimesEquals', $stack);
                }

            } elseif (preg_match('/^'.self::CALCULATION_REGEXP_CELLREF.'$/i', $token, $matches)) {
                $cellRef = null;
//                echo 'Element '.$token.' is a Cell reference<br />';
                if (isset($matches[8])) {
//                    echo 'Reference is a Range of cells<br />';
                    if ($pCell === null) {
//                        We can't access the range, so return a REF error
                        $cellValue = PHPExcel_Calculation_Functions::REF();
                    } else {
                        $cellRef = $matches[6].$matches[7].':'.$matches[9].$matches[10];
                        if ($matches[2] > '') {
                            $matches[2] = trim($matches[2], "\"'");
                            if ((strpos($matches[2], '[') !== false) || (strpos($matches[2], ']') !== false)) {
                                //    It's a Reference to an external workbook (not currently supported)
                                return $this->raiseFormulaError('Unable to access External Workbook');
                            }
                            $matches[2] = trim($matches[2], "\"'");
//                            echo '$cellRef='.$cellRef.' in worksheet '.$matches[2].'<br />';
                            $this->_debugLog->writeDebugLog('Evaluating Cell Range ', $cellRef, ' in worksheet ', $matches[2]);
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $this->workbook->getSheetByName($matches[2]), false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->_debugLog->writeDebugLog('Evaluation Result for cells ', $cellRef, ' in worksheet ', $matches[2], ' is ', $this->showTypeDetails($cellValue));
//                            $cellRef = $matches[2].'!'.$cellRef;
                        } else {
//                            echo '$cellRef='.$cellRef.' in current worksheet<br />';
                            $this->_debugLog->writeDebugLog('Evaluating Cell Range ', $cellRef, ' in current worksheet');
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $pCellWorksheet, false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->_debugLog->writeDebugLog('Evaluation Result for cells ', $cellRef, ' is ', $this->showTypeDetails($cellValue));
                        }
                    }
                } else {
//                    echo 'Reference is a single Cell<br />';
                    if ($pCell === null) {
//                        We can't access the cell, so return a REF error
                        $cellValue = PHPExcel_Calculation_Functions::REF();
                    } else {
                        $cellRef = $matches[6].$matches[7];
                        if ($matches[2] > '') {
                            $matches[2] = trim($matches[2], "\"'");
                            if ((strpos($matches[2], '[') !== false) || (strpos($matches[2], ']') !== false)) {
                                //    It's a Reference to an external workbook (not currently supported)
                                return $this->raiseFormulaError('Unable to access External Workbook');
                            }
//                            echo '$cellRef='.$cellRef.' in worksheet '.$matches[2].'<br />';
                            $this->_debugLog->writeDebugLog('Evaluating Cell ', $cellRef, ' in worksheet ', $matches[2]);
                            if ($pCellParent !== null) {
                                $cellSheet = $this->workbook->getSheetByName($matches[2]);
                                if ($cellSheet && $cellSheet->cellExists($cellRef)) {
                                    $cellValue = $this->extractCellRange($cellRef, $this->workbook->getSheetByName($matches[2]), false);
                                    $pCell->attach($pCellParent);
                                } else {
                                    $cellValue = null;
                                }
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->_debugLog->writeDebugLog('Evaluation Result for cell ', $cellRef, ' in worksheet ', $matches[2], ' is ', $this->showTypeDetails($cellValue));
//                            $cellRef = $matches[2].'!'.$cellRef;
                        } else {
//                            echo '$cellRef='.$cellRef.' in current worksheet<br />';
                            $this->_debugLog->writeDebugLog('Evaluating Cell ', $cellRef, ' in current worksheet');
                            if ($pCellParent->isDataSet($cellRef)) {
                                $cellValue = $this->extractCellRange($cellRef, $pCellWorksheet, false);
                                $pCell->attach($pCellParent);
                            } else {
                                $cellValue = null;
                            }
                            $this->_debugLog->writeDebugLog('Evaluation Result for cell ', $cellRef, ' is ', $this->showTypeDetails($cellValue));
                        }
                    }
                }
                $stack->push('Value', $cellValue, $cellRef);

            // if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on
            } elseif (preg_match('/^'.self::CALCULATION_REGEXP_FUNCTION.'$/i', $token, $matches)) {
//                echo 'Token is a function<br />';
                $functionName = $matches[1];
                $argCount = $stack->pop();
                $argCount = $argCount['value'];
                if ($functionName != 'MKMATRIX') {
                    $this->_debugLog->writeDebugLog('Evaluating Function ', self::localeFunc($functionName), '() with ', (($argCount == 0) ? 'no' : $argCount), ' argument', (($argCount == 1) ? '' : 's'));
                }
                if ((isset(self::$PHPExcelFunctions[$functionName])) || (isset(self::$controlFunctions[$functionName]))) {    // function
                    if (isset(self::$PHPExcelFunctions[$functionName])) {
                        $functionCall = self::$PHPExcelFunctions[$functionName]['functionCall'];
                        $passByReference = isset(self::$PHPExcelFunctions[$functionName]['passByReference']);
                        $passCellReference = isset(self::$PHPExcelFunctions[$functionName]['passCellReference']);
                    } elseif (isset(self::$controlFunctions[$functionName])) {
                        $functionCall = self::$controlFunctions[$functionName]['functionCall'];
                        $passByReference = isset(self::$controlFunctions[$functionName]['passByReference']);
                        $passCellReference = isset(self::$controlFunctions[$functionName]['passCellReference']);
                    }
                    // get the arguments for this function
//                    echo 'Function '.$functionName.' expects '.$argCount.' arguments<br />';
                    $args = $argArrayVals = array();
                    for ($i = 0; $i < $argCount; ++$i) {
                        $arg = $stack->pop();
                        $a = $argCount - $i - 1;
                        if (($passByReference) &&
                            (isset(self::$PHPExcelFunctions[$functionName]['passByReference'][$a])) &&
                            (self::$PHPExcelFunctions[$functionName]['passByReference'][$a])) {
                            if ($arg['reference'] === null) {
                                $args[] = $cellID;
                                if ($functionName != 'MKMATRIX') {
                                    $argArrayVals[] = $this->showValue($cellID);
                                }
                            } else {
                                $args[] = $arg['reference'];
                                if ($functionName != 'MKMATRIX') {
                                    $argArrayVals[] = $this->showValue($arg['reference']);
                                }
                            }
                        } else {
                            $args[] = self::unwrapResult($arg['value']);
                            if ($functionName != 'MKMATRIX') {
                                $argArrayVals[] = $this->showValue($arg['value']);
                            }
                        }
                    }
                    //    Reverse the order of the arguments
                    krsort($args);
                    if (($passByReference) && ($argCount == 0)) {
                        $args[] = $cellID;
                        $argArrayVals[] = $this->showValue($cellID);
                    }
//                    echo 'Arguments are: ';
//                    print_r($args);
//                    echo '<br />';
                    if ($functionName != 'MKMATRIX') {
                        if ($this->_debugLog->getWriteDebugLog()) {
                            krsort($argArrayVals);
                            $this->_debugLog->writeDebugLog('Evaluating ', self::localeFunc($functionName), '( ', implode(self::$localeArgumentSeparator.' ', PHPExcel_Calculation_Functions::flattenArray($argArrayVals)), ' )');
                        }
                    }
                    //    Process each argument in turn, building the return value as an array
//                    if (($argCount == 1) && (is_array($args[1])) && ($functionName != 'MKMATRIX')) {
//                        $operand1 = $args[1];
//                        $this->_debugLog->writeDebugLog('Argument is a matrix: ', $this->showValue($operand1));
//                        $result = array();
//                        $row = 0;
//                        foreach($operand1 as $args) {
//                            if (is_array($args)) {
//                                foreach($args as $arg) {
//                                    $this->_debugLog->writeDebugLog('Evaluating ', self::localeFunc($functionName), '( ', $this->showValue($arg), ' )');
//                                    $r = call_user_func_array($functionCall, $arg);
//                                    $this->_debugLog->writeDebugLog('Evaluation Result for ', self::localeFunc($functionName), '() function call is ', $this->showTypeDetails($r));
//                                    $result[$row][] = $r;
//                                }
//                                ++$row;
//                            } else {
//                                $this->_debugLog->writeDebugLog('Evaluating ', self::localeFunc($functionName), '( ', $this->showValue($args), ' )');
//                                $r = call_user_func_array($functionCall, $args);
//                                $this->_debugLog->writeDebugLog('Evaluation Result for ', self::localeFunc($functionName), '() function call is ', $this->showTypeDetails($r));
//                                $result[] = $r;
//                            }
//                        }
//                    } else {
                    //    Process the argument with the appropriate function call
                    if ($passCellReference) {
                        $args[] = $pCell;
                    }
                    if (strpos($functionCall, '::') !== false) {
                        $result = call_user_func_array(explode('::', $functionCall), $args);
                    } else {
                        foreach ($args as &$arg) {
                            $arg = PHPExcel_Calculation_Functions::flattenSingleValue($arg);
                        }
                        unset($arg);
                        $result = call_user_func_array($functionCall, $args);
                    }
                    if ($functionName != 'MKMATRIX') {
                        $this->_debugLog->writeDebugLog('Evaluation Result for ', self::localeFunc($functionName), '() function call is ', $this->showTypeDetails($result));
                    }
                    $stack->push('Value', self::wrapResult($result));
                }

            } else {
                // if the token is a number, boolean, string or an Excel error, push it onto the stack
                if (isset(self::$excelConstants[strtoupper($token)])) {
                    $excelConstant = strtoupper($token);
//                    echo 'Token is a PHPExcel constant: '.$excelConstant.'<br />';
                    $stack->push('Constant Value', self::$excelConstants[$excelConstant]);
                    $this->_debugLog->writeDebugLog('Evaluating Constant ', $excelConstant, ' as ', $this->showTypeDetails(self::$excelConstants[$excelConstant]));
                } elseif ((is_numeric($token)) || ($token === null) || (is_bool($token)) || ($token == '') || ($token{0} == '"') || ($token{0} == '#')) {
//                    echo 'Token is a number, boolean, string, null or an Excel error<br />';
                    $stack->push('Value', $token);
                // if the token is a named range, push the named range name onto the stack
                } elseif (preg_match('/^'.self::CALCULATION_REGEXP_NAMEDRANGE.'$/i', $token, $matches)) {
//                    echo 'Token is a named range<br />';
                    $namedRange = $matches[6];
//                    echo 'Named Range is '.$namedRange.'<br />';
                    $this->_debugLog->writeDebugLog('Evaluating Named Range ', $namedRange);
                    $cellValue = $this->extractNamedRange($namedRange, ((null !== $pCell) ? $pCellWorksheet : null), false);
                    $pCell->attach($pCellParent);
                    $this->_debugLog->writeDebugLog('Evaluation Result for named range ', $namedRange, ' is ', $this->showTypeDetails($cellValue));
                    $stack->push('Named Range', $cellValue, $namedRange);
                } else {
                    return $this->raiseFormulaError("undefined variable '$token'");
                }
            }
        }
        // when we're out of tokens, the stack should have a single element, the final result
        if ($stack->count() != 1) {
            return $this->raiseFormulaError("internal error");
        }
        $output = $stack->pop();
        $output = $output['value'];

//        if ((is_array($output)) && (self::$returnArrayAsType != self::RETURN_ARRAY_AS_ARRAY)) {
//            return array_shift(PHPExcel_Calculation_Functions::flattenArray($output));
//        }
        return $output;
    }


    private function validateBinaryOperand($cellID, &$operand, &$stack)
    {
        if (is_array($operand)) {
            if ((count($operand, COUNT_RECURSIVE) - count($operand)) == 1) {
                do {
                    $operand = array_pop($operand);
                } while (is_array($operand));
            }
        }
        //    Numbers, matrices and booleans can pass straight through, as they're already valid
        if (is_string($operand)) {
            //    We only need special validations for the operand if it is a string
            //    Start by stripping off the quotation marks we use to identify true excel string values internally
            if ($operand > '' && $operand{0} == '"') {
                $operand = self::unwrapResult($operand);
            }
            //    If the string is a numeric value, we treat it as a numeric, so no further testing
            if (!is_numeric($operand)) {
                //    If not a numeric, test to see if the value is an Excel error, and so can't be used in normal binary operations
                if ($operand > '' && $operand{0} == '#') {
                    $stack->push('Value', $operand);
                    $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($operand));
                    return false;
                } elseif (!PHPExcel_Shared_String::convertToNumberIfFraction($operand)) {
                    //    If not a numeric or a fraction, then it's a text string, and so can't be used in mathematical binary operations
                    $stack->push('Value', '#VALUE!');
                    $this->_debugLog->writeDebugLog('Evaluation Result is a ', $this->showTypeDetails('#VALUE!'));
                    return false;
                }
            }
        }

        //    return a true if the value of the operand is one that we can use in normal binary operations
        return true;
    }


    private function executeBinaryComparisonOperation($cellID, $operand1, $operand2, $operation, &$stack, $recursingArrays = false)
    {
        //    If we're dealing with matrix operations, we want a matrix result
        if ((is_array($operand1)) || (is_array($operand2))) {
            $result = array();
            if ((is_array($operand1)) && (!is_array($operand2))) {
                foreach ($operand1 as $x => $operandData) {
                    $this->_debugLog->writeDebugLog('Evaluating Comparison ', $this->showValue($operandData), ' ', $operation, ' ', $this->showValue($operand2));
                    $this->executeBinaryComparisonOperation($cellID, $operandData, $operand2, $operation, $stack);
                    $r = $stack->pop();
                    $result[$x] = $r['value'];
                }
            } elseif ((!is_array($operand1)) && (is_array($operand2))) {
                foreach ($operand2 as $x => $operandData) {
                    $this->_debugLog->writeDebugLog('Evaluating Comparison ', $this->showValue($operand1), ' ', $operation, ' ', $this->showValue($operandData));
                    $this->executeBinaryComparisonOperation($cellID, $operand1, $operandData, $operation, $stack);
                    $r = $stack->pop();
                    $result[$x] = $r['value'];
                }
            } else {
                if (!$recursingArrays) {
                    self::checkMatrixOperands($operand1, $operand2, 2);
                }
                foreach ($operand1 as $x => $operandData) {
                    $this->_debugLog->writeDebugLog('Evaluating Comparison ', $this->showValue($operandData), ' ', $operation, ' ', $this->showValue($operand2[$x]));
                    $this->executeBinaryComparisonOperation($cellID, $operandData, $operand2[$x], $operation, $stack, true);
                    $r = $stack->pop();
                    $result[$x] = $r['value'];
                }
            }
            //    Log the result details
            $this->_debugLog->writeDebugLog('Comparison Evaluation Result is ', $this->showTypeDetails($result));
            //    And push the result onto the stack
            $stack->push('Array', $result);
            return true;
        }

        //    Simple validate the two operands if they are string values
        if (is_string($operand1) && $operand1 > '' && $operand1{0} == '"') {
            $operand1 = self::unwrapResult($operand1);
        }
        if (is_string($operand2) && $operand2 > '' && $operand2{0} == '"') {
            $operand2 = self::unwrapResult($operand2);
        }

        // Use case insensitive comparaison if not OpenOffice mode
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() != PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
            if (is_string($operand1)) {
                $operand1 = strtoupper($operand1);
            }
            if (is_string($operand2)) {
                $operand2 = strtoupper($operand2);
            }
        }

        $useLowercaseFirstComparison = is_string($operand1) && is_string($operand2) && PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE;

        //    execute the necessary operation
        switch ($operation) {
            //    Greater than
            case '>':
                if ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) > 0;
                } else {
                    $result = ($operand1 > $operand2);
                }
                break;
            //    Less than
            case '<':
                if ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) < 0;
                } else {
                    $result = ($operand1 < $operand2);
                }
                break;
            //    Equality
            case '=':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = (abs($operand1 - $operand2) < $this->delta);
                } else {
                    $result = strcmp($operand1, $operand2) == 0;
                }
                break;
            //    Greater than or equal
            case '>=':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = ((abs($operand1 - $operand2) < $this->delta) || ($operand1 > $operand2));
                } elseif ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) >= 0;
                } else {
                    $result = strcmp($operand1, $operand2) >= 0;
                }
                break;
            //    Less than or equal
            case '<=':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = ((abs($operand1 - $operand2) < $this->delta) || ($operand1 < $operand2));
                } elseif ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) <= 0;
                } else {
                    $result = strcmp($operand1, $operand2) <= 0;
                }
                break;
            //    Inequality
            case '<>':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = (abs($operand1 - $operand2) > 1E-14);
                } else {
                    $result = strcmp($operand1, $operand2) != 0;
                }
                break;
        }

        //    Log the result details
        $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
        //    And push the result onto the stack
        $stack->push('Value', $result);
        return true;
    }

    /**
     * Compare two strings in the same way as strcmp() except that lowercase come before uppercase letters
     * @param    string    $str1    First string value for the comparison
     * @param    string    $str2    Second string value for the comparison
     * @return   integer
     */
    private function strcmpLowercaseFirst($str1, $str2)
    {
        $inversedStr1 = PHPExcel_Shared_String::StrCaseReverse($str1);
        $inversedStr2 = PHPExcel_Shared_String::StrCaseReverse($str2);

        return strcmp($inversedStr1, $inversedStr2);
    }

    private function executeNumericBinaryOperation($cellID, $operand1, $operand2, $operation, $matrixFunction, &$stack)
    {
        //    Validate the two operands
        if (!$this->validateBinaryOperand($cellID, $operand1, $stack)) {
            return false;
        }
        if (!$this->validateBinaryOperand($cellID, $operand2, $stack)) {
            return false;
        }

        //    If either of the operands is a matrix, we need to treat them both as matrices
        //        (converting the other operand to a matrix if need be); then perform the required
        //        matrix operation
        if ((is_array($operand1)) || (is_array($operand2))) {
            //    Ensure that both operands are arrays/matrices of the same size
            self::checkMatrixOperands($operand1, $operand2, 2);

            try {
                //    Convert operand 1 from a PHP array to a matrix
                $matrix = new PHPExcel_Shared_JAMA_Matrix($operand1);
                //    Perform the required operation against the operand 1 matrix, passing in operand 2
                $matrixResult = $matrix->$matrixFunction($operand2);
                $result = $matrixResult->getArray();
            } catch (PHPExcel_Exception $ex) {
                $this->_debugLog->writeDebugLog('JAMA Matrix Exception: ', $ex->getMessage());
                $result = '#VALUE!';
            }
        } else {
            if ((PHPExcel_Calculation_Functions::getCompatibilityMode() != PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) &&
                ((is_string($operand1) && !is_numeric($operand1) && strlen($operand1)>0) ||
                 (is_string($operand2) && !is_numeric($operand2) && strlen($operand2)>0))) {
                $result = PHPExcel_Calculation_Functions::VALUE();
            } else {
                //    If we're dealing with non-matrix operations, execute the necessary operation
                switch ($operation) {
                    //    Addition
                    case '+':
                        $result = $operand1 + $operand2;
                        break;
                    //    Subtraction
                    case '-':
                        $result = $operand1 - $operand2;
                        break;
                    //    Multiplication
                    case '*':
                        $result = $operand1 * $operand2;
                        break;
                    //    Division
                    case '/':
                        if ($operand2 == 0) {
                            //    Trap for Divide by Zero error
                            $stack->push('Value', '#DIV/0!');
                            $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails('#DIV/0!'));
                            return false;
                        } else {
                            $result = $operand1 / $operand2;
                        }
                        break;
                    //    Power
                    case '^':
                        $result = pow($operand1, $operand2);
                        break;
                }
            }
        }

        //    Log the result details
        $this->_debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
        //    And push the result onto the stack
        $stack->push('Value', $result);
        return true;
    }


    // trigger an error, but nicely, if need be
    protected function raiseFormulaError($errorMessage)
    {
        $this->formulaError = $errorMessage;
        $this->cyclicReferenceStack->clear();
        if (!$this->suppressFormulaErrors) {
            throw new PHPExcel_Calculation_Exception($errorMessage);
        }
        trigger_error($errorMessage, E_USER_ERROR);
    }


    /**
     * Extract range values
     *
     * @param    string                &$pRange    String based range representation
     * @param    PHPExcel_Worksheet    $pSheet        Worksheet
     * @param    boolean                $resetLog    Flag indicating whether calculation log should be reset or not
     * @return  mixed                Array of values in range if range contains more than one element. Otherwise, a single value is returned.
     * @throws    PHPExcel_Calculation_Exception
     */
    public function extractCellRange(&$pRange = 'A1', PHPExcel_Worksheet $pSheet = null, $resetLog = true)
    {
        // Return value
        $returnValue = array ();

//        echo 'extractCellRange('.$pRange.')', PHP_EOL;
        if ($pSheet !== null) {
            $pSheetName = $pSheet->getTitle();
//            echo 'Passed sheet name is '.$pSheetName.PHP_EOL;
//            echo 'Range reference is '.$pRange.PHP_EOL;
            if (strpos($pRange, '!') !== false) {
//                echo '$pRange reference includes sheet reference', PHP_EOL;
                list($pSheetName, $pRange) = PHPExcel_Worksheet::extractSheetTitle($pRange, true);
//                echo 'New sheet name is '.$pSheetName, PHP_EOL;
//                echo 'Adjusted Range reference is '.$pRange, PHP_EOL;
                $pSheet = $this->workbook->getSheetByName($pSheetName);
            }

            // Extract range
            $aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($pRange);
            $pRange = $pSheetName.'!'.$pRange;
            if (!isset($aReferences[1])) {
                //    Single cell in range
                sscanf($aReferences[0], '%[A-Z]%d', $currentCol, $currentRow);
                $cellValue = null;
                if ($pSheet->cellExists($aReferences[0])) {
                    $returnValue[$currentRow][$currentCol] = $pSheet->getCell($aReferences[0])->getCalculatedValue($resetLog);
                } else {
                    $returnValue[$currentRow][$currentCol] = null;
                }
            } else {
                // Extract cell data for all cells in the range
                foreach ($aReferences as $reference) {
                    // Extract range
                    sscanf($reference, '%[A-Z]%d', $currentCol, $currentRow);
                    $cellValue = null;
                    if ($pSheet->cellExists($reference)) {
                        $returnValue[$currentRow][$currentCol] = $pSheet->getCell($reference)->getCalculatedValue($resetLog);
                    } else {
                        $returnValue[$currentRow][$currentCol] = null;
                    }
                }
            }
        }

        return $returnValue;
    }


    /**
     * Extract range values
     *
     * @param    string                &$pRange    String based range representation
     * @param    PHPExcel_Worksheet    $pSheet        Worksheet
     * @return  mixed                Array of values in range if range contains more than one element. Otherwise, a single value is returned.
     * @param    boolean                $resetLog    Flag indicating whether calculation log should be reset or not
     * @throws    PHPExcel_Calculation_Exception
     */
    public function extractNamedRange(&$pRange = 'A1', PHPExcel_Worksheet $pSheet = null, $resetLog = true)
    {
        // Return value
        $returnValue = array ();

//        echo 'extractNamedRange('.$pRange.')<br />';
        if ($pSheet !== null) {
            $pSheetName = $pSheet->getTitle();
//            echo 'Current sheet name is '.$pSheetName.'<br />';
//            echo 'Range reference is '.$pRange.'<br />';
            if (strpos($pRange, '!') !== false) {
//                echo '$pRange reference includes sheet reference', PHP_EOL;
                list($pSheetName, $pRange) = PHPExcel_Worksheet::extractSheetTitle($pRange, true);
//                echo 'New sheet name is '.$pSheetName, PHP_EOL;
//                echo 'Adjusted Range reference is '.$pRange, PHP_EOL;
                $pSheet = $this->workbook->getSheetByName($pSheetName);
            }

            // Named range?
            $namedRange = PHPExcel_NamedRange::resolveRange($pRange, $pSheet);
            if ($namedRange !== null) {
                $pSheet = $namedRange->getWorksheet();
//                echo 'Named Range '.$pRange.' (';
                $pRange = $namedRange->getRange();
                $splitRange = PHPExcel_Cell::splitRange($pRange);
                //    Convert row and column references
                if (ctype_alpha($splitRange[0][0])) {
                    $pRange = $splitRange[0][0] . '1:' . $splitRange[0][1] . $namedRange->getWorksheet()->getHighestRow();
                } elseif (ctype_digit($splitRange[0][0])) {
                    $pRange = 'A' . $splitRange[0][0] . ':' . $namedRange->getWorksheet()->getHighestColumn() . $splitRange[0][1];
                }
//                echo $pRange.') is in sheet '.$namedRange->getWorksheet()->getTitle().'<br />';

//                if ($pSheet->getTitle() != $namedRange->getWorksheet()->getTitle()) {
//                    if (!$namedRange->getLocalOnly()) {
//                        $pSheet = $namedRange->getWorksheet();
//                    } else {
//                        return $returnValue;
//                    }
//                }
            } else {
                return PHPExcel_Calculation_Functions::REF();
            }

            // Extract range
            $aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($pRange);
//            var_dump($aReferences);
            if (!isset($aReferences[1])) {
                //    Single cell (or single column or row) in range
                list($currentCol, $currentRow) = PHPExcel_Cell::coordinateFromString($aReferences[0]);
                $cellValue = null;
                if ($pSheet->cellExists($aReferences[0])) {
                    $returnValue[$currentRow][$currentCol] = $pSheet->getCell($aReferences[0])->getCalculatedValue($resetLog);
                } else {
                    $returnValue[$currentRow][$currentCol] = null;
                }
            } else {
                // Extract cell data for all cells in the range
                foreach ($aReferences as $reference) {
                    // Extract range
                    list($currentCol, $currentRow) = PHPExcel_Cell::coordinateFromString($reference);
//                    echo 'NAMED RANGE: $currentCol='.$currentCol.' $currentRow='.$currentRow.'<br />';
                    $cellValue = null;
                    if ($pSheet->cellExists($reference)) {
                        $returnValue[$currentRow][$currentCol] = $pSheet->getCell($reference)->getCalculatedValue($resetLog);
                    } else {
                        $returnValue[$currentRow][$currentCol] = null;
                    }
                }
            }
//                print_r($returnValue);
//            echo '<br />';
        }

        return $returnValue;
    }


    /**
     * Is a specific function implemented?
     *
     * @param    string    $pFunction    Function Name
     * @return    boolean
     */
    public function isImplemented($pFunction = '')
    {
        $pFunction = strtoupper($pFunction);
        if (isset(self::$PHPExcelFunctions[$pFunction])) {
            return (self::$PHPExcelFunctions[$pFunction]['functionCall'] != 'PHPExcel_Calculation_Functions::DUMMY');
        } else {
            return false;
        }
    }


    /**
     * Get a list of all implemented functions as an array of function objects
     *
     * @return    array of PHPExcel_Calculation_Function
     */
    public function listFunctions()
    {
        $returnValue = array();

        foreach (self::$PHPExcelFunctions as $functionName => $function) {
            if ($function['functionCall'] != 'PHPExcel_Calculation_Functions::DUMMY') {
                $returnValue[$functionName] = new PHPExcel_Calculation_Function(
                    $function['category'],
                    $functionName,
                    $function['functionCall']
                );
            }
        }

        return $returnValue;
    }


    /**
     * Get a list of all Excel function names
     *
     * @return    array
     */
    public function listAllFunctionNames()
    {
        return array_keys(self::$PHPExcelFunctions);
    }

    /**
     * Get a list of implemented Excel function names
     *
     * @return    array
     */
    public function listFunctionNames()
    {
        $returnValue = array();
        foreach (self::$PHPExcelFunctions as $functionName => $function) {
            if ($function['functionCall'] != 'PHPExcel_Calculation_Functions::DUMMY') {
                $returnValue[] = $functionName;
            }
        }

        return $returnValue;
    }
}
