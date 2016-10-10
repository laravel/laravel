<?php

/**
 * PHPExcel_Writer_Excel5_Parser
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
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */

// Original file header of PEAR::Spreadsheet_Excel_Writer_Parser (used as the base for this class):
// -----------------------------------------------------------------------------------------
// *  Class for parsing Excel formulas
// *
// *  License Information:
// *
// *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
// *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
// *
// *    This library is free software; you can redistribute it and/or
// *    modify it under the terms of the GNU Lesser General Public
// *    License as published by the Free Software Foundation; either
// *    version 2.1 of the License, or (at your option) any later version.
// *
// *    This library is distributed in the hope that it will be useful,
// *    but WITHOUT ANY WARRANTY; without even the implied warranty of
// *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// *    Lesser General Public License for more details.
// *
// *    You should have received a copy of the GNU Lesser General Public
// *    License along with this library; if not, write to the Free Software
// *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// */
class PHPExcel_Writer_Excel5_Parser
{
    /**    Constants                */
    // Sheet title in unquoted form
    // Invalid sheet title characters cannot occur in the sheet title:
    //         *:/\?[]
    // Moreover, there are valid sheet title characters that cannot occur in unquoted form (there may be more?)
    // +-% '^&<>=,;#()"{}
    const REGEX_SHEET_TITLE_UNQUOTED = '[^\*\:\/\\\\\?\[\]\+\-\% \\\'\^\&\<\>\=\,\;\#\(\)\"\{\}]+';

    // Sheet title in quoted form (without surrounding quotes)
    // Invalid sheet title characters cannot occur in the sheet title:
    // *:/\?[]                    (usual invalid sheet title characters)
    // Single quote is represented as a pair ''
    const REGEX_SHEET_TITLE_QUOTED = '(([^\*\:\/\\\\\?\[\]\\\'])+|(\\\'\\\')+)+';

    /**
     * The index of the character we are currently looking at
     * @var integer
     */
    public $currentCharacter;

    /**
     * The token we are working on.
     * @var string
     */
    public $currentToken;

    /**
     * The formula to parse
     * @var string
     */
    private $formula;

    /**
     * The character ahead of the current char
     * @var string
     */
    public $lookAhead;

    /**
     * The parse tree to be generated
     * @var string
     */
    private $parseTree;

    /**
     * Array of external sheets
     * @var array
     */
    private $externalSheets;

    /**
     * Array of sheet references in the form of REF structures
     * @var array
     */
    public $references;

    /**
     * The class constructor
     *
     */
    public function __construct()
    {
        $this->currentCharacter  = 0;
        $this->currentToken = '';       // The token we are working on.
        $this->formula       = '';       // The formula to parse.
        $this->lookAhead     = '';       // The character ahead of the current char.
        $this->parseTree    = '';       // The parse tree to be generated.
        $this->initializeHashes();      // Initialize the hashes: ptg's and function's ptg's
        $this->externalSheets = array();
        $this->references = array();
    }

    /**
     * Initialize the ptg and function hashes.
     *
     * @access private
     */
    private function initializeHashes()
    {
        // The Excel ptg indices
        $this->ptg = array(
            'ptgExp'       => 0x01,
            'ptgTbl'       => 0x02,
            'ptgAdd'       => 0x03,
            'ptgSub'       => 0x04,
            'ptgMul'       => 0x05,
            'ptgDiv'       => 0x06,
            'ptgPower'     => 0x07,
            'ptgConcat'    => 0x08,
            'ptgLT'        => 0x09,
            'ptgLE'        => 0x0A,
            'ptgEQ'        => 0x0B,
            'ptgGE'        => 0x0C,
            'ptgGT'        => 0x0D,
            'ptgNE'        => 0x0E,
            'ptgIsect'     => 0x0F,
            'ptgUnion'     => 0x10,
            'ptgRange'     => 0x11,
            'ptgUplus'     => 0x12,
            'ptgUminus'    => 0x13,
            'ptgPercent'   => 0x14,
            'ptgParen'     => 0x15,
            'ptgMissArg'   => 0x16,
            'ptgStr'       => 0x17,
            'ptgAttr'      => 0x19,
            'ptgSheet'     => 0x1A,
            'ptgEndSheet'  => 0x1B,
            'ptgErr'       => 0x1C,
            'ptgBool'      => 0x1D,
            'ptgInt'       => 0x1E,
            'ptgNum'       => 0x1F,
            'ptgArray'     => 0x20,
            'ptgFunc'      => 0x21,
            'ptgFuncVar'   => 0x22,
            'ptgName'      => 0x23,
            'ptgRef'       => 0x24,
            'ptgArea'      => 0x25,
            'ptgMemArea'   => 0x26,
            'ptgMemErr'    => 0x27,
            'ptgMemNoMem'  => 0x28,
            'ptgMemFunc'   => 0x29,
            'ptgRefErr'    => 0x2A,
            'ptgAreaErr'   => 0x2B,
            'ptgRefN'      => 0x2C,
            'ptgAreaN'     => 0x2D,
            'ptgMemAreaN'  => 0x2E,
            'ptgMemNoMemN' => 0x2F,
            'ptgNameX'     => 0x39,
            'ptgRef3d'     => 0x3A,
            'ptgArea3d'    => 0x3B,
            'ptgRefErr3d'  => 0x3C,
            'ptgAreaErr3d' => 0x3D,
            'ptgArrayV'    => 0x40,
            'ptgFuncV'     => 0x41,
            'ptgFuncVarV'  => 0x42,
            'ptgNameV'     => 0x43,
            'ptgRefV'      => 0x44,
            'ptgAreaV'     => 0x45,
            'ptgMemAreaV'  => 0x46,
            'ptgMemErrV'   => 0x47,
            'ptgMemNoMemV' => 0x48,
            'ptgMemFuncV'  => 0x49,
            'ptgRefErrV'   => 0x4A,
            'ptgAreaErrV'  => 0x4B,
            'ptgRefNV'     => 0x4C,
            'ptgAreaNV'    => 0x4D,
            'ptgMemAreaNV' => 0x4E,
            'ptgMemNoMemN' => 0x4F,
            'ptgFuncCEV'   => 0x58,
            'ptgNameXV'    => 0x59,
            'ptgRef3dV'    => 0x5A,
            'ptgArea3dV'   => 0x5B,
            'ptgRefErr3dV' => 0x5C,
            'ptgAreaErr3d' => 0x5D,
            'ptgArrayA'    => 0x60,
            'ptgFuncA'     => 0x61,
            'ptgFuncVarA'  => 0x62,
            'ptgNameA'     => 0x63,
            'ptgRefA'      => 0x64,
            'ptgAreaA'     => 0x65,
            'ptgMemAreaA'  => 0x66,
            'ptgMemErrA'   => 0x67,
            'ptgMemNoMemA' => 0x68,
            'ptgMemFuncA'  => 0x69,
            'ptgRefErrA'   => 0x6A,
            'ptgAreaErrA'  => 0x6B,
            'ptgRefNA'     => 0x6C,
            'ptgAreaNA'    => 0x6D,
            'ptgMemAreaNA' => 0x6E,
            'ptgMemNoMemN' => 0x6F,
            'ptgFuncCEA'   => 0x78,
            'ptgNameXA'    => 0x79,
            'ptgRef3dA'    => 0x7A,
            'ptgArea3dA'   => 0x7B,
            'ptgRefErr3dA' => 0x7C,
            'ptgAreaErr3d' => 0x7D
        );

        // Thanks to Michael Meeks and Gnumeric for the initial arg values.
        //
        // The following hash was generated by "function_locale.pl" in the distro.
        // Refer to function_locale.pl for non-English function names.
        //
        // The array elements are as follow:
        // ptg:   The Excel function ptg code.
        // args:  The number of arguments that the function takes:
        //           >=0 is a fixed number of arguments.
        //           -1  is a variable  number of arguments.
        // class: The reference, value or array class of the function args.
        // vol:   The function is volatile.
        //
        $this->functions = array(
            // function                  ptg  args  class  vol
            'COUNT'           => array(   0,   -1,    0,    0 ),
            'IF'              => array(   1,   -1,    1,    0 ),
            'ISNA'            => array(   2,    1,    1,    0 ),
            'ISERROR'         => array(   3,    1,    1,    0 ),
            'SUM'             => array(   4,   -1,    0,    0 ),
            'AVERAGE'         => array(   5,   -1,    0,    0 ),
            'MIN'             => array(   6,   -1,    0,    0 ),
            'MAX'             => array(   7,   -1,    0,    0 ),
            'ROW'             => array(   8,   -1,    0,    0 ),
            'COLUMN'          => array(   9,   -1,    0,    0 ),
            'NA'              => array(  10,    0,    0,    0 ),
            'NPV'             => array(  11,   -1,    1,    0 ),
            'STDEV'           => array(  12,   -1,    0,    0 ),
            'DOLLAR'          => array(  13,   -1,    1,    0 ),
            'FIXED'           => array(  14,   -1,    1,    0 ),
            'SIN'             => array(  15,    1,    1,    0 ),
            'COS'             => array(  16,    1,    1,    0 ),
            'TAN'             => array(  17,    1,    1,    0 ),
            'ATAN'            => array(  18,    1,    1,    0 ),
            'PI'              => array(  19,    0,    1,    0 ),
            'SQRT'            => array(  20,    1,    1,    0 ),
            'EXP'             => array(  21,    1,    1,    0 ),
            'LN'              => array(  22,    1,    1,    0 ),
            'LOG10'           => array(  23,    1,    1,    0 ),
            'ABS'             => array(  24,    1,    1,    0 ),
            'INT'             => array(  25,    1,    1,    0 ),
            'SIGN'            => array(  26,    1,    1,    0 ),
            'ROUND'           => array(  27,    2,    1,    0 ),
            'LOOKUP'          => array(  28,   -1,    0,    0 ),
            'INDEX'           => array(  29,   -1,    0,    1 ),
            'REPT'            => array(  30,    2,    1,    0 ),
            'MID'             => array(  31,    3,    1,    0 ),
            'LEN'             => array(  32,    1,    1,    0 ),
            'VALUE'           => array(  33,    1,    1,    0 ),
            'TRUE'            => array(  34,    0,    1,    0 ),
            'FALSE'           => array(  35,    0,    1,    0 ),
            'AND'             => array(  36,   -1,    0,    0 ),
            'OR'              => array(  37,   -1,    0,    0 ),
            'NOT'             => array(  38,    1,    1,    0 ),
            'MOD'             => array(  39,    2,    1,    0 ),
            'DCOUNT'          => array(  40,    3,    0,    0 ),
            'DSUM'            => array(  41,    3,    0,    0 ),
            'DAVERAGE'        => array(  42,    3,    0,    0 ),
            'DMIN'            => array(  43,    3,    0,    0 ),
            'DMAX'            => array(  44,    3,    0,    0 ),
            'DSTDEV'          => array(  45,    3,    0,    0 ),
            'VAR'             => array(  46,   -1,    0,    0 ),
            'DVAR'            => array(  47,    3,    0,    0 ),
            'TEXT'            => array(  48,    2,    1,    0 ),
            'LINEST'          => array(  49,   -1,    0,    0 ),
            'TREND'           => array(  50,   -1,    0,    0 ),
            'LOGEST'          => array(  51,   -1,    0,    0 ),
            'GROWTH'          => array(  52,   -1,    0,    0 ),
            'PV'              => array(  56,   -1,    1,    0 ),
            'FV'              => array(  57,   -1,    1,    0 ),
            'NPER'            => array(  58,   -1,    1,    0 ),
            'PMT'             => array(  59,   -1,    1,    0 ),
            'RATE'            => array(  60,   -1,    1,    0 ),
            'MIRR'            => array(  61,    3,    0,    0 ),
            'IRR'             => array(  62,   -1,    0,    0 ),
            'RAND'            => array(  63,    0,    1,    1 ),
            'MATCH'           => array(  64,   -1,    0,    0 ),
            'DATE'            => array(  65,    3,    1,    0 ),
            'TIME'            => array(  66,    3,    1,    0 ),
            'DAY'             => array(  67,    1,    1,    0 ),
            'MONTH'           => array(  68,    1,    1,    0 ),
            'YEAR'            => array(  69,    1,    1,    0 ),
            'WEEKDAY'         => array(  70,   -1,    1,    0 ),
            'HOUR'            => array(  71,    1,    1,    0 ),
            'MINUTE'          => array(  72,    1,    1,    0 ),
            'SECOND'          => array(  73,    1,    1,    0 ),
            'NOW'             => array(  74,    0,    1,    1 ),
            'AREAS'           => array(  75,    1,    0,    1 ),
            'ROWS'            => array(  76,    1,    0,    1 ),
            'COLUMNS'         => array(  77,    1,    0,    1 ),
            'OFFSET'          => array(  78,   -1,    0,    1 ),
            'SEARCH'          => array(  82,   -1,    1,    0 ),
            'TRANSPOSE'       => array(  83,    1,    1,    0 ),
            'TYPE'            => array(  86,    1,    1,    0 ),
            'ATAN2'           => array(  97,    2,    1,    0 ),
            'ASIN'            => array(  98,    1,    1,    0 ),
            'ACOS'            => array(  99,    1,    1,    0 ),
            'CHOOSE'          => array( 100,   -1,    1,    0 ),
            'HLOOKUP'         => array( 101,   -1,    0,    0 ),
            'VLOOKUP'         => array( 102,   -1,    0,    0 ),
            'ISREF'           => array( 105,    1,    0,    0 ),
            'LOG'             => array( 109,   -1,    1,    0 ),
            'CHAR'            => array( 111,    1,    1,    0 ),
            'LOWER'           => array( 112,    1,    1,    0 ),
            'UPPER'           => array( 113,    1,    1,    0 ),
            'PROPER'          => array( 114,    1,    1,    0 ),
            'LEFT'            => array( 115,   -1,    1,    0 ),
            'RIGHT'           => array( 116,   -1,    1,    0 ),
            'EXACT'           => array( 117,    2,    1,    0 ),
            'TRIM'            => array( 118,    1,    1,    0 ),
            'REPLACE'         => array( 119,    4,    1,    0 ),
            'SUBSTITUTE'      => array( 120,   -1,    1,    0 ),
            'CODE'            => array( 121,    1,    1,    0 ),
            'FIND'            => array( 124,   -1,    1,    0 ),
            'CELL'            => array( 125,   -1,    0,    1 ),
            'ISERR'           => array( 126,    1,    1,    0 ),
            'ISTEXT'          => array( 127,    1,    1,    0 ),
            'ISNUMBER'        => array( 128,    1,    1,    0 ),
            'ISBLANK'         => array( 129,    1,    1,    0 ),
            'T'               => array( 130,    1,    0,    0 ),
            'N'               => array( 131,    1,    0,    0 ),
            'DATEVALUE'       => array( 140,    1,    1,    0 ),
            'TIMEVALUE'       => array( 141,    1,    1,    0 ),
            'SLN'             => array( 142,    3,    1,    0 ),
            'SYD'             => array( 143,    4,    1,    0 ),
            'DDB'             => array( 144,   -1,    1,    0 ),
            'INDIRECT'        => array( 148,   -1,    1,    1 ),
            'CALL'            => array( 150,   -1,    1,    0 ),
            'CLEAN'           => array( 162,    1,    1,    0 ),
            'MDETERM'         => array( 163,    1,    2,    0 ),
            'MINVERSE'        => array( 164,    1,    2,    0 ),
            'MMULT'           => array( 165,    2,    2,    0 ),
            'IPMT'            => array( 167,   -1,    1,    0 ),
            'PPMT'            => array( 168,   -1,    1,    0 ),
            'COUNTA'          => array( 169,   -1,    0,    0 ),
            'PRODUCT'         => array( 183,   -1,    0,    0 ),
            'FACT'            => array( 184,    1,    1,    0 ),
            'DPRODUCT'        => array( 189,    3,    0,    0 ),
            'ISNONTEXT'       => array( 190,    1,    1,    0 ),
            'STDEVP'          => array( 193,   -1,    0,    0 ),
            'VARP'            => array( 194,   -1,    0,    0 ),
            'DSTDEVP'         => array( 195,    3,    0,    0 ),
            'DVARP'           => array( 196,    3,    0,    0 ),
            'TRUNC'           => array( 197,   -1,    1,    0 ),
            'ISLOGICAL'       => array( 198,    1,    1,    0 ),
            'DCOUNTA'         => array( 199,    3,    0,    0 ),
            'USDOLLAR'        => array( 204,   -1,    1,    0 ),
            'FINDB'           => array( 205,   -1,    1,    0 ),
            'SEARCHB'         => array( 206,   -1,    1,    0 ),
            'REPLACEB'        => array( 207,    4,    1,    0 ),
            'LEFTB'           => array( 208,   -1,    1,    0 ),
            'RIGHTB'          => array( 209,   -1,    1,    0 ),
            'MIDB'            => array( 210,    3,    1,    0 ),
            'LENB'            => array( 211,    1,    1,    0 ),
            'ROUNDUP'         => array( 212,    2,    1,    0 ),
            'ROUNDDOWN'       => array( 213,    2,    1,    0 ),
            'ASC'             => array( 214,    1,    1,    0 ),
            'DBCS'            => array( 215,    1,    1,    0 ),
            'RANK'            => array( 216,   -1,    0,    0 ),
            'ADDRESS'         => array( 219,   -1,    1,    0 ),
            'DAYS360'         => array( 220,   -1,    1,    0 ),
            'TODAY'           => array( 221,    0,    1,    1 ),
            'VDB'             => array( 222,   -1,    1,    0 ),
            'MEDIAN'          => array( 227,   -1,    0,    0 ),
            'SUMPRODUCT'      => array( 228,   -1,    2,    0 ),
            'SINH'            => array( 229,    1,    1,    0 ),
            'COSH'            => array( 230,    1,    1,    0 ),
            'TANH'            => array( 231,    1,    1,    0 ),
            'ASINH'           => array( 232,    1,    1,    0 ),
            'ACOSH'           => array( 233,    1,    1,    0 ),
            'ATANH'           => array( 234,    1,    1,    0 ),
            'DGET'            => array( 235,    3,    0,    0 ),
            'INFO'            => array( 244,    1,    1,    1 ),
            'DB'              => array( 247,   -1,    1,    0 ),
            'FREQUENCY'       => array( 252,    2,    0,    0 ),
            'ERROR.TYPE'      => array( 261,    1,    1,    0 ),
            'REGISTER.ID'     => array( 267,   -1,    1,    0 ),
            'AVEDEV'          => array( 269,   -1,    0,    0 ),
            'BETADIST'        => array( 270,   -1,    1,    0 ),
            'GAMMALN'         => array( 271,    1,    1,    0 ),
            'BETAINV'         => array( 272,   -1,    1,    0 ),
            'BINOMDIST'       => array( 273,    4,    1,    0 ),
            'CHIDIST'         => array( 274,    2,    1,    0 ),
            'CHIINV'          => array( 275,    2,    1,    0 ),
            'COMBIN'          => array( 276,    2,    1,    0 ),
            'CONFIDENCE'      => array( 277,    3,    1,    0 ),
            'CRITBINOM'       => array( 278,    3,    1,    0 ),
            'EVEN'            => array( 279,    1,    1,    0 ),
            'EXPONDIST'       => array( 280,    3,    1,    0 ),
            'FDIST'           => array( 281,    3,    1,    0 ),
            'FINV'            => array( 282,    3,    1,    0 ),
            'FISHER'          => array( 283,    1,    1,    0 ),
            'FISHERINV'       => array( 284,    1,    1,    0 ),
            'FLOOR'           => array( 285,    2,    1,    0 ),
            'GAMMADIST'       => array( 286,    4,    1,    0 ),
            'GAMMAINV'        => array( 287,    3,    1,    0 ),
            'CEILING'         => array( 288,    2,    1,    0 ),
            'HYPGEOMDIST'     => array( 289,    4,    1,    0 ),
            'LOGNORMDIST'     => array( 290,    3,    1,    0 ),
            'LOGINV'          => array( 291,    3,    1,    0 ),
            'NEGBINOMDIST'    => array( 292,    3,    1,    0 ),
            'NORMDIST'        => array( 293,    4,    1,    0 ),
            'NORMSDIST'       => array( 294,    1,    1,    0 ),
            'NORMINV'         => array( 295,    3,    1,    0 ),
            'NORMSINV'        => array( 296,    1,    1,    0 ),
            'STANDARDIZE'     => array( 297,    3,    1,    0 ),
            'ODD'             => array( 298,    1,    1,    0 ),
            'PERMUT'          => array( 299,    2,    1,    0 ),
            'POISSON'         => array( 300,    3,    1,    0 ),
            'TDIST'           => array( 301,    3,    1,    0 ),
            'WEIBULL'         => array( 302,    4,    1,    0 ),
            'SUMXMY2'         => array( 303,    2,    2,    0 ),
            'SUMX2MY2'        => array( 304,    2,    2,    0 ),
            'SUMX2PY2'        => array( 305,    2,    2,    0 ),
            'CHITEST'         => array( 306,    2,    2,    0 ),
            'CORREL'          => array( 307,    2,    2,    0 ),
            'COVAR'           => array( 308,    2,    2,    0 ),
            'FORECAST'        => array( 309,    3,    2,    0 ),
            'FTEST'           => array( 310,    2,    2,    0 ),
            'INTERCEPT'       => array( 311,    2,    2,    0 ),
            'PEARSON'         => array( 312,    2,    2,    0 ),
            'RSQ'             => array( 313,    2,    2,    0 ),
            'STEYX'           => array( 314,    2,    2,    0 ),
            'SLOPE'           => array( 315,    2,    2,    0 ),
            'TTEST'           => array( 316,    4,    2,    0 ),
            'PROB'            => array( 317,   -1,    2,    0 ),
            'DEVSQ'           => array( 318,   -1,    0,    0 ),
            'GEOMEAN'         => array( 319,   -1,    0,    0 ),
            'HARMEAN'         => array( 320,   -1,    0,    0 ),
            'SUMSQ'           => array( 321,   -1,    0,    0 ),
            'KURT'            => array( 322,   -1,    0,    0 ),
            'SKEW'            => array( 323,   -1,    0,    0 ),
            'ZTEST'           => array( 324,   -1,    0,    0 ),
            'LARGE'           => array( 325,    2,    0,    0 ),
            'SMALL'           => array( 326,    2,    0,    0 ),
            'QUARTILE'        => array( 327,    2,    0,    0 ),
            'PERCENTILE'      => array( 328,    2,    0,    0 ),
            'PERCENTRANK'     => array( 329,   -1,    0,    0 ),
            'MODE'            => array( 330,   -1,    2,    0 ),
            'TRIMMEAN'        => array( 331,    2,    0,    0 ),
            'TINV'            => array( 332,    2,    1,    0 ),
            'CONCATENATE'     => array( 336,   -1,    1,    0 ),
            'POWER'           => array( 337,    2,    1,    0 ),
            'RADIANS'         => array( 342,    1,    1,    0 ),
            'DEGREES'         => array( 343,    1,    1,    0 ),
            'SUBTOTAL'        => array( 344,   -1,    0,    0 ),
            'SUMIF'           => array( 345,   -1,    0,    0 ),
            'COUNTIF'         => array( 346,    2,    0,    0 ),
            'COUNTBLANK'      => array( 347,    1,    0,    0 ),
            'ISPMT'           => array( 350,    4,    1,    0 ),
            'DATEDIF'         => array( 351,    3,    1,    0 ),
            'DATESTRING'      => array( 352,    1,    1,    0 ),
            'NUMBERSTRING'    => array( 353,    2,    1,    0 ),
            'ROMAN'           => array( 354,   -1,    1,    0 ),
            'GETPIVOTDATA'    => array( 358,   -1,    0,    0 ),
            'HYPERLINK'       => array( 359,   -1,    1,    0 ),
            'PHONETIC'        => array( 360,    1,    0,    0 ),
            'AVERAGEA'        => array( 361,   -1,    0,    0 ),
            'MAXA'            => array( 362,   -1,    0,    0 ),
            'MINA'            => array( 363,   -1,    0,    0 ),
            'STDEVPA'         => array( 364,   -1,    0,    0 ),
            'VARPA'           => array( 365,   -1,    0,    0 ),
            'STDEVA'          => array( 366,   -1,    0,    0 ),
            'VARA'            => array( 367,   -1,    0,    0 ),
            'BAHTTEXT'        => array( 368,    1,    0,    0 ),
        );
    }

    /**
     * Convert a token to the proper ptg value.
     *
     * @access private
     * @param mixed $token The token to convert.
     * @return mixed the converted token on success
     */
    private function convert($token)
    {
        if (preg_match("/\"([^\"]|\"\"){0,255}\"/", $token)) {
            return $this->convertString($token);

        } elseif (is_numeric($token)) {
            return $this->convertNumber($token);

        // match references like A1 or $A$1
        } elseif (preg_match('/^\$?([A-Ia-i]?[A-Za-z])\$?(\d+)$/', $token)) {
            return $this->convertRef2d($token);

        // match external references like Sheet1!A1 or Sheet1:Sheet2!A1 or Sheet1!$A$1 or Sheet1:Sheet2!$A$1
        } elseif (preg_match("/^" . self::REGEX_SHEET_TITLE_UNQUOTED . "(\:" . self::REGEX_SHEET_TITLE_UNQUOTED . ")?\!\\$?[A-Ia-i]?[A-Za-z]\\$?(\d+)$/u", $token)) {
            return $this->convertRef3d($token);

        // match external references like 'Sheet1'!A1 or 'Sheet1:Sheet2'!A1 or 'Sheet1'!$A$1 or 'Sheet1:Sheet2'!$A$1
        } elseif (preg_match("/^'" . self::REGEX_SHEET_TITLE_QUOTED . "(\:" . self::REGEX_SHEET_TITLE_QUOTED . ")?'\!\\$?[A-Ia-i]?[A-Za-z]\\$?(\d+)$/u", $token)) {
            return $this->convertRef3d($token);

        // match ranges like A1:B2 or $A$1:$B$2
        } elseif (preg_match('/^(\$)?[A-Ia-i]?[A-Za-z](\$)?(\d+)\:(\$)?[A-Ia-i]?[A-Za-z](\$)?(\d+)$/', $token)) {
            return $this->convertRange2d($token);

        // match external ranges like Sheet1!A1:B2 or Sheet1:Sheet2!A1:B2 or Sheet1!$A$1:$B$2 or Sheet1:Sheet2!$A$1:$B$2
        } elseif (preg_match("/^" . self::REGEX_SHEET_TITLE_UNQUOTED . "(\:" . self::REGEX_SHEET_TITLE_UNQUOTED . ")?\!\\$?([A-Ia-i]?[A-Za-z])?\\$?(\d+)\:\\$?([A-Ia-i]?[A-Za-z])?\\$?(\d+)$/u", $token)) {
            return $this->convertRange3d($token);

        // match external ranges like 'Sheet1'!A1:B2 or 'Sheet1:Sheet2'!A1:B2 or 'Sheet1'!$A$1:$B$2 or 'Sheet1:Sheet2'!$A$1:$B$2
        } elseif (preg_match("/^'" . self::REGEX_SHEET_TITLE_QUOTED . "(\:" . self::REGEX_SHEET_TITLE_QUOTED . ")?'\!\\$?([A-Ia-i]?[A-Za-z])?\\$?(\d+)\:\\$?([A-Ia-i]?[A-Za-z])?\\$?(\d+)$/u", $token)) {
            return $this->convertRange3d($token);

        // operators (including parentheses)
        } elseif (isset($this->ptg[$token])) {
            return pack("C", $this->ptg[$token]);

        // match error codes
        } elseif (preg_match("/^#[A-Z0\/]{3,5}[!?]{1}$/", $token) or $token == '#N/A') {
            return $this->convertError($token);

        // commented so argument number can be processed correctly. See toReversePolish().
        /*elseif (preg_match("/[A-Z0-9\xc0-\xdc\.]+/", $token))
        {
            return($this->convertFunction($token, $this->_func_args));
        }*/

        // if it's an argument, ignore the token (the argument remains)
        } elseif ($token == 'arg') {
            return '';
        }

        // TODO: use real error codes
        throw new PHPExcel_Writer_Exception("Unknown token $token");
    }

    /**
     * Convert a number token to ptgInt or ptgNum
     *
     * @access private
     * @param mixed $num an integer or double for conversion to its ptg value
     */
    private function convertNumber($num)
    {
        // Integer in the range 0..2**16-1
        if ((preg_match("/^\d+$/", $num)) and ($num <= 65535)) {
            return pack("Cv", $this->ptg['ptgInt'], $num);
        } else { // A float
            if (PHPExcel_Writer_Excel5_BIFFwriter::getByteOrder()) { // if it's Big Endian
                $num = strrev($num);
            }
            return pack("Cd", $this->ptg['ptgNum'], $num);
        }
    }

    /**
     * Convert a string token to ptgStr
     *
     * @access private
     * @param string $string A string for conversion to its ptg value.
     * @return mixed the converted token on success
     */
    private function convertString($string)
    {
        // chop away beggining and ending quotes
        $string = substr($string, 1, strlen($string) - 2);
        if (strlen($string) > 255) {
            throw new PHPExcel_Writer_Exception("String is too long");
        }

        return pack('C', $this->ptg['ptgStr']) . PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($string);
    }

    /**
     * Convert a function to a ptgFunc or ptgFuncVarV depending on the number of
     * args that it takes.
     *
     * @access private
     * @param string  $token    The name of the function for convertion to ptg value.
     * @param integer $num_args The number of arguments the function receives.
     * @return string The packed ptg for the function
     */
    private function convertFunction($token, $num_args)
    {
        $args     = $this->functions[$token][1];
//        $volatile = $this->functions[$token][3];

        // Fixed number of args eg. TIME($i, $j, $k).
        if ($args >= 0) {
            return pack("Cv", $this->ptg['ptgFuncV'], $this->functions[$token][0]);
        }
        // Variable number of args eg. SUM($i, $j, $k, ..).
        if ($args == -1) {
            return pack("CCv", $this->ptg['ptgFuncVarV'], $num_args, $this->functions[$token][0]);
        }
    }

    /**
     * Convert an Excel range such as A1:D4 to a ptgRefV.
     *
     * @access private
     * @param string    $range    An Excel range in the A1:A2
     * @param int        $class
     */
    private function convertRange2d($range, $class = 0)
    {

        // TODO: possible class value 0,1,2 check Formula.pm
        // Split the range into 2 cell refs
        if (preg_match('/^(\$)?([A-Ia-i]?[A-Za-z])(\$)?(\d+)\:(\$)?([A-Ia-i]?[A-Za-z])(\$)?(\d+)$/', $range)) {
            list($cell1, $cell2) = explode(':', $range);
        } else {
            // TODO: use real error codes
            throw new PHPExcel_Writer_Exception("Unknown range separator");
        }

        // Convert the cell references
        list($row1, $col1) = $this->cellToPackedRowcol($cell1);
        list($row2, $col2) = $this->cellToPackedRowcol($cell2);

        // The ptg value depends on the class of the ptg.
        if ($class == 0) {
            $ptgArea = pack("C", $this->ptg['ptgArea']);
        } elseif ($class == 1) {
            $ptgArea = pack("C", $this->ptg['ptgAreaV']);
        } elseif ($class == 2) {
            $ptgArea = pack("C", $this->ptg['ptgAreaA']);
        } else {
            // TODO: use real error codes
            throw new PHPExcel_Writer_Exception("Unknown class $class");
        }
        return $ptgArea . $row1 . $row2 . $col1. $col2;
    }

    /**
     * Convert an Excel 3d range such as "Sheet1!A1:D4" or "Sheet1:Sheet2!A1:D4" to
     * a ptgArea3d.
     *
     * @access private
     * @param string $token An Excel range in the Sheet1!A1:A2 format.
     * @return mixed The packed ptgArea3d token on success.
     */
    private function convertRange3d($token)
    {
//        $class = 0; // formulas like Sheet1!$A$1:$A$2 in list type data validation need this class (0x3B)

        // Split the ref at the ! symbol
        list($ext_ref, $range) = explode('!', $token);

        // Convert the external reference part (different for BIFF8)
        $ext_ref = $this->getRefIndex($ext_ref);

        // Split the range into 2 cell refs
        list($cell1, $cell2) = explode(':', $range);

        // Convert the cell references
        if (preg_match("/^(\\$)?[A-Ia-i]?[A-Za-z](\\$)?(\d+)$/", $cell1)) {
            list($row1, $col1) = $this->cellToPackedRowcol($cell1);
            list($row2, $col2) = $this->cellToPackedRowcol($cell2);
        } else { // It's a rows range (like 26:27)
             list($row1, $col1, $row2, $col2) = $this->rangeToPackedRange($cell1.':'.$cell2);
        }

        // The ptg value depends on the class of the ptg.
//        if ($class == 0) {
            $ptgArea = pack("C", $this->ptg['ptgArea3d']);
//        } elseif ($class == 1) {
//            $ptgArea = pack("C", $this->ptg['ptgArea3dV']);
//        } elseif ($class == 2) {
//            $ptgArea = pack("C", $this->ptg['ptgArea3dA']);
//        } else {
//            throw new PHPExcel_Writer_Exception("Unknown class $class");
//        }

        return $ptgArea . $ext_ref . $row1 . $row2 . $col1. $col2;
    }

    /**
     * Convert an Excel reference such as A1, $B2, C$3 or $D$4 to a ptgRefV.
     *
     * @access private
     * @param string $cell An Excel cell reference
     * @return string The cell in packed() format with the corresponding ptg
     */
    private function convertRef2d($cell)
    {
//        $class = 2; // as far as I know, this is magick.

        // Convert the cell reference
        $cell_array = $this->cellToPackedRowcol($cell);
        list($row, $col) = $cell_array;

        // The ptg value depends on the class of the ptg.
//        if ($class == 0) {
//            $ptgRef = pack("C", $this->ptg['ptgRef']);
//        } elseif ($class == 1) {
//            $ptgRef = pack("C", $this->ptg['ptgRefV']);
//        } elseif ($class == 2) {
            $ptgRef = pack("C", $this->ptg['ptgRefA']);
//        } else {
//            // TODO: use real error codes
//            throw new PHPExcel_Writer_Exception("Unknown class $class");
//        }
        return $ptgRef.$row.$col;
    }

    /**
     * Convert an Excel 3d reference such as "Sheet1!A1" or "Sheet1:Sheet2!A1" to a
     * ptgRef3d.
     *
     * @access private
     * @param string $cell An Excel cell reference
     * @return mixed The packed ptgRef3d token on success.
     */
    private function convertRef3d($cell)
    {
//        $class = 2; // as far as I know, this is magick.

        // Split the ref at the ! symbol
        list($ext_ref, $cell) = explode('!', $cell);

        // Convert the external reference part (different for BIFF8)
        $ext_ref = $this->getRefIndex($ext_ref);

        // Convert the cell reference part
        list($row, $col) = $this->cellToPackedRowcol($cell);

        // The ptg value depends on the class of the ptg.
//        if ($class == 0) {
//            $ptgRef = pack("C", $this->ptg['ptgRef3d']);
//        } elseif ($class == 1) {
//            $ptgRef = pack("C", $this->ptg['ptgRef3dV']);
//        } elseif ($class == 2) {
            $ptgRef = pack("C", $this->ptg['ptgRef3dA']);
//        } else {
//            throw new PHPExcel_Writer_Exception("Unknown class $class");
//        }

        return $ptgRef . $ext_ref. $row . $col;
    }

    /**
     * Convert an error code to a ptgErr
     *
     * @access    private
     * @param    string    $errorCode    The error code for conversion to its ptg value
     * @return    string                The error code ptgErr
     */
    private function convertError($errorCode)
    {
        switch ($errorCode) {
            case '#NULL!':
                return pack("C", 0x00);
            case '#DIV/0!':
                return pack("C", 0x07);
            case '#VALUE!':
                return pack("C", 0x0F);
            case '#REF!':
                return pack("C", 0x17);
            case '#NAME?':
                return pack("C", 0x1D);
            case '#NUM!':
                return pack("C", 0x24);
            case '#N/A':
                return pack("C", 0x2A);
        }
        return pack("C", 0xFF);
    }

    /**
     * Convert the sheet name part of an external reference, for example "Sheet1" or
     * "Sheet1:Sheet2", to a packed structure.
     *
     * @access    private
     * @param    string    $ext_ref    The name of the external reference
     * @return    string                The reference index in packed() format
     */
    private function packExtRef($ext_ref)
    {
        $ext_ref = preg_replace("/^'/", '', $ext_ref); // Remove leading  ' if any.
        $ext_ref = preg_replace("/'$/", '', $ext_ref); // Remove trailing ' if any.

        // Check if there is a sheet range eg., Sheet1:Sheet2.
        if (preg_match("/:/", $ext_ref)) {
            list($sheet_name1, $sheet_name2) = explode(':', $ext_ref);

            $sheet1 = $this->getSheetIndex($sheet_name1);
            if ($sheet1 == -1) {
                throw new PHPExcel_Writer_Exception("Unknown sheet name $sheet_name1 in formula");
            }
            $sheet2 = $this->getSheetIndex($sheet_name2);
            if ($sheet2 == -1) {
                throw new PHPExcel_Writer_Exception("Unknown sheet name $sheet_name2 in formula");
            }

            // Reverse max and min sheet numbers if necessary
            if ($sheet1 > $sheet2) {
                list($sheet1, $sheet2) = array($sheet2, $sheet1);
            }
        } else { // Single sheet name only.
            $sheet1 = $this->getSheetIndex($ext_ref);
            if ($sheet1 == -1) {
                throw new PHPExcel_Writer_Exception("Unknown sheet name $ext_ref in formula");
            }
            $sheet2 = $sheet1;
        }

        // References are stored relative to 0xFFFF.
        $offset = -1 - $sheet1;

        return pack('vdvv', $offset, 0x00, $sheet1, $sheet2);
    }

    /**
     * Look up the REF index that corresponds to an external sheet name
     * (or range). If it doesn't exist yet add it to the workbook's references
     * array. It assumes all sheet names given must exist.
     *
     * @access private
     * @param string $ext_ref The name of the external reference
     * @return mixed The reference index in packed() format on success
     */
    private function getRefIndex($ext_ref)
    {
        $ext_ref = preg_replace("/^'/", '', $ext_ref); // Remove leading  ' if any.
        $ext_ref = preg_replace("/'$/", '', $ext_ref); // Remove trailing ' if any.
        $ext_ref = str_replace('\'\'', '\'', $ext_ref); // Replace escaped '' with '

        // Check if there is a sheet range eg., Sheet1:Sheet2.
        if (preg_match("/:/", $ext_ref)) {
            list($sheet_name1, $sheet_name2) = explode(':', $ext_ref);

            $sheet1 = $this->getSheetIndex($sheet_name1);
            if ($sheet1 == -1) {
                throw new PHPExcel_Writer_Exception("Unknown sheet name $sheet_name1 in formula");
            }
            $sheet2 = $this->getSheetIndex($sheet_name2);
            if ($sheet2 == -1) {
                throw new PHPExcel_Writer_Exception("Unknown sheet name $sheet_name2 in formula");
            }

            // Reverse max and min sheet numbers if necessary
            if ($sheet1 > $sheet2) {
                list($sheet1, $sheet2) = array($sheet2, $sheet1);
            }
        } else { // Single sheet name only.
            $sheet1 = $this->getSheetIndex($ext_ref);
            if ($sheet1 == -1) {
                throw new PHPExcel_Writer_Exception("Unknown sheet name $ext_ref in formula");
            }
            $sheet2 = $sheet1;
        }

        // assume all references belong to this document
        $supbook_index = 0x00;
        $ref = pack('vvv', $supbook_index, $sheet1, $sheet2);
        $totalreferences = count($this->references);
        $index = -1;
        for ($i = 0; $i < $totalreferences; ++$i) {
            if ($ref == $this->references[$i]) {
                $index = $i;
                break;
            }
        }
        // if REF was not found add it to references array
        if ($index == -1) {
            $this->references[$totalreferences] = $ref;
            $index = $totalreferences;
        }

        return pack('v', $index);
    }

    /**
     * Look up the index that corresponds to an external sheet name. The hash of
     * sheet names is updated by the addworksheet() method of the
     * PHPExcel_Writer_Excel5_Workbook class.
     *
     * @access    private
     * @param    string    $sheet_name        Sheet name
     * @return    integer                    The sheet index, -1 if the sheet was not found
     */
    private function getSheetIndex($sheet_name)
    {
        if (!isset($this->externalSheets[$sheet_name])) {
            return -1;
        } else {
            return $this->externalSheets[$sheet_name];
        }
    }

    /**
     * This method is used to update the array of sheet names. It is
     * called by the addWorksheet() method of the
     * PHPExcel_Writer_Excel5_Workbook class.
     *
     * @access public
     * @see PHPExcel_Writer_Excel5_Workbook::addWorksheet()
     * @param string  $name  The name of the worksheet being added
     * @param integer $index The index of the worksheet being added
     */
    public function setExtSheet($name, $index)
    {
        $this->externalSheets[$name] = $index;
    }

    /**
     * pack() row and column into the required 3 or 4 byte format.
     *
     * @access private
     * @param string $cell The Excel cell reference to be packed
     * @return array Array containing the row and column in packed() format
     */
    private function cellToPackedRowcol($cell)
    {
        $cell = strtoupper($cell);
        list($row, $col, $row_rel, $col_rel) = $this->cellToRowcol($cell);
        if ($col >= 256) {
            throw new PHPExcel_Writer_Exception("Column in: $cell greater than 255");
        }
        if ($row >= 65536) {
            throw new PHPExcel_Writer_Exception("Row in: $cell greater than 65536 ");
        }

        // Set the high bits to indicate if row or col are relative.
        $col |= $col_rel << 14;
        $col |= $row_rel << 15;
        $col = pack('v', $col);

        $row = pack('v', $row);

        return array($row, $col);
    }

    /**
     * pack() row range into the required 3 or 4 byte format.
     * Just using maximum col/rows, which is probably not the correct solution
     *
     * @access private
     * @param string $range The Excel range to be packed
     * @return array Array containing (row1,col1,row2,col2) in packed() format
     */
    private function rangeToPackedRange($range)
    {
        preg_match('/(\$)?(\d+)\:(\$)?(\d+)/', $range, $match);
        // return absolute rows if there is a $ in the ref
        $row1_rel = empty($match[1]) ? 1 : 0;
        $row1     = $match[2];
        $row2_rel = empty($match[3]) ? 1 : 0;
        $row2     = $match[4];
        // Convert 1-index to zero-index
        --$row1;
        --$row2;
        // Trick poor inocent Excel
        $col1 = 0;
        $col2 = 65535; // FIXME: maximum possible value for Excel 5 (change this!!!)

        // FIXME: this changes for BIFF8
        if (($row1 >= 65536) or ($row2 >= 65536)) {
            throw new PHPExcel_Writer_Exception("Row in: $range greater than 65536 ");
        }

        // Set the high bits to indicate if rows are relative.
        $col1 |= $row1_rel << 15;
        $col2 |= $row2_rel << 15;
        $col1 = pack('v', $col1);
        $col2 = pack('v', $col2);

        $row1 = pack('v', $row1);
        $row2 = pack('v', $row2);

        return array($row1, $col1, $row2, $col2);
    }

    /**
     * Convert an Excel cell reference such as A1 or $B2 or C$3 or $D$4 to a zero
     * indexed row and column number. Also returns two (0,1) values to indicate
     * whether the row or column are relative references.
     *
     * @access private
     * @param string $cell The Excel cell reference in A1 format.
     * @return array
     */
    private function cellToRowcol($cell)
    {
        preg_match('/(\$)?([A-I]?[A-Z])(\$)?(\d+)/', $cell, $match);
        // return absolute column if there is a $ in the ref
        $col_rel = empty($match[1]) ? 1 : 0;
        $col_ref = $match[2];
        $row_rel = empty($match[3]) ? 1 : 0;
        $row     = $match[4];

        // Convert base26 column string to a number.
        $expn = strlen($col_ref) - 1;
        $col  = 0;
        $col_ref_length = strlen($col_ref);
        for ($i = 0; $i < $col_ref_length; ++$i) {
            $col += (ord($col_ref{$i}) - 64) * pow(26, $expn);
            --$expn;
        }

        // Convert 1-index to zero-index
        --$row;
        --$col;

        return array($row, $col, $row_rel, $col_rel);
    }

    /**
     * Advance to the next valid token.
     *
     * @access private
     */
    private function advance()
    {
        $i = $this->currentCharacter;
        $formula_length = strlen($this->formula);
        // eat up white spaces
        if ($i < $formula_length) {
            while ($this->formula{$i} == " ") {
                ++$i;
            }

            if ($i < ($formula_length - 1)) {
                $this->lookAhead = $this->formula{$i+1};
            }
            $token = '';
        }

        while ($i < $formula_length) {
            $token .= $this->formula{$i};

            if ($i < ($formula_length - 1)) {
                $this->lookAhead = $this->formula{$i+1};
            } else {
                $this->lookAhead = '';
            }

            if ($this->match($token) != '') {
                //if ($i < strlen($this->formula) - 1) {
                //    $this->lookAhead = $this->formula{$i+1};
                //}
                $this->currentCharacter = $i + 1;
                $this->currentToken = $token;
                return 1;
            }

            if ($i < ($formula_length - 2)) {
                $this->lookAhead = $this->formula{$i+2};
            } else { // if we run out of characters lookAhead becomes empty
                $this->lookAhead = '';
            }
            ++$i;
        }
        //die("Lexical error ".$this->currentCharacter);
    }

    /**
     * Checks if it's a valid token.
     *
     * @access private
     * @param mixed $token The token to check.
     * @return mixed       The checked token or false on failure
     */
    private function match($token)
    {
        switch ($token) {
            case "+":
            case "-":
            case "*":
            case "/":
            case "(":
            case ")":
            case ",":
            case ";":
            case ">=":
            case "<=":
            case "=":
            case "<>":
            case "^":
            case "&":
            case "%":
                return $token;
                break;
            case ">":
                if ($this->lookAhead == '=') { // it's a GE token
                    break;
                }
                return $token;
                break;
            case "<":
                // it's a LE or a NE token
                if (($this->lookAhead == '=') or ($this->lookAhead == '>')) {
                    break;
                }
                return $token;
                break;
            default:
                // if it's a reference A1 or $A$1 or $A1 or A$1
                if (preg_match('/^\$?[A-Ia-i]?[A-Za-z]\$?[0-9]+$/', $token) and !preg_match("/[0-9]/", $this->lookAhead) and ($this->lookAhead != ':') and ($this->lookAhead != '.') and ($this->lookAhead != '!')) {
                    return $token;
                } elseif (preg_match("/^" . self::REGEX_SHEET_TITLE_UNQUOTED . "(\:" . self::REGEX_SHEET_TITLE_UNQUOTED . ")?\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u", $token) and !preg_match("/[0-9]/", $this->lookAhead) and ($this->lookAhead != ':') and ($this->lookAhead != '.')) {
                    // If it's an external reference (Sheet1!A1 or Sheet1:Sheet2!A1 or Sheet1!$A$1 or Sheet1:Sheet2!$A$1)
                    return $token;
                } elseif (preg_match("/^'" . self::REGEX_SHEET_TITLE_QUOTED . "(\:" . self::REGEX_SHEET_TITLE_QUOTED . ")?'\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u", $token) and !preg_match("/[0-9]/", $this->lookAhead) and ($this->lookAhead != ':') and ($this->lookAhead != '.')) {
                    // If it's an external reference ('Sheet1'!A1 or 'Sheet1:Sheet2'!A1 or 'Sheet1'!$A$1 or 'Sheet1:Sheet2'!$A$1)
                    return $token;
                } elseif (preg_match('/^(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+:(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+$/', $token) && !preg_match("/[0-9]/", $this->lookAhead)) {
                    // if it's a range A1:A2 or $A$1:$A$2
                    return $token;
                } elseif (preg_match("/^" . self::REGEX_SHEET_TITLE_UNQUOTED . "(\:" . self::REGEX_SHEET_TITLE_UNQUOTED . ")?\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u", $token) and !preg_match("/[0-9]/", $this->lookAhead)) {
                    // If it's an external range like Sheet1!A1:B2 or Sheet1:Sheet2!A1:B2 or Sheet1!$A$1:$B$2 or Sheet1:Sheet2!$A$1:$B$2
                    return $token;
                } elseif (preg_match("/^'" . self::REGEX_SHEET_TITLE_QUOTED . "(\:" . self::REGEX_SHEET_TITLE_QUOTED . ")?'\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u", $token) and !preg_match("/[0-9]/", $this->lookAhead)) {
                    // If it's an external range like 'Sheet1'!A1:B2 or 'Sheet1:Sheet2'!A1:B2 or 'Sheet1'!$A$1:$B$2 or 'Sheet1:Sheet2'!$A$1:$B$2
                    return $token;
                } elseif (is_numeric($token) and (!is_numeric($token.$this->lookAhead) or ($this->lookAhead == '')) and ($this->lookAhead != '!') and ($this->lookAhead != ':')) {
                    // If it's a number (check that it's not a sheet name or range)
                    return $token;
                } elseif (preg_match("/\"([^\"]|\"\"){0,255}\"/", $token) and $this->lookAhead != '"' and (substr_count($token, '"')%2 == 0)) {
                    // If it's a string (of maximum 255 characters)
                    return $token;
                } elseif (preg_match("/^#[A-Z0\/]{3,5}[!?]{1}$/", $token) or $token == '#N/A') {
                    // If it's an error code
                    return $token;
                } elseif (preg_match("/^[A-Z0-9\xc0-\xdc\.]+$/i", $token) and ($this->lookAhead == "(")) {
                    // if it's a function call
                    return $token;
                } elseif (substr($token, -1) == ')') {
                    //    It's an argument of some description (e.g. a named range),
                    //        precise nature yet to be determined
                    return $token;
                }
                return '';
        }
    }

    /**
     * The parsing method. It parses a formula.
     *
     * @access public
     * @param string $formula The formula to parse, without the initial equal
     *                        sign (=).
     * @return mixed true on success
     */
    public function parse($formula)
    {
        $this->currentCharacter = 0;
        $this->formula      = $formula;
        $this->lookAhead    = isset($formula{1}) ? $formula{1} : '';
        $this->advance();
        $this->parseTree   = $this->condition();
        return true;
    }

    /**
     * It parses a condition. It assumes the following rule:
     * Cond -> Expr [(">" | "<") Expr]
     *
     * @access private
     * @return mixed The parsed ptg'd tree on success
     */
    private function condition()
    {
        $result = $this->expression();
        if ($this->currentToken == "<") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgLT', $result, $result2);
        } elseif ($this->currentToken == ">") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgGT', $result, $result2);
        } elseif ($this->currentToken == "<=") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgLE', $result, $result2);
        } elseif ($this->currentToken == ">=") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgGE', $result, $result2);
        } elseif ($this->currentToken == "=") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgEQ', $result, $result2);
        } elseif ($this->currentToken == "<>") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgNE', $result, $result2);
        } elseif ($this->currentToken == "&") {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgConcat', $result, $result2);
        }
        return $result;
    }

    /**
     * It parses a expression. It assumes the following rule:
     * Expr -> Term [("+" | "-") Term]
     *      -> "string"
     *      -> "-" Term : Negative value
     *      -> "+" Term : Positive value
     *      -> Error code
     *
     * @access private
     * @return mixed The parsed ptg'd tree on success
     */
    private function expression()
    {
        // If it's a string return a string node
        if (preg_match("/\"([^\"]|\"\"){0,255}\"/", $this->currentToken)) {
            $tmp = str_replace('""', '"', $this->currentToken);
            if (($tmp == '"') || ($tmp == '')) {
                //    Trap for "" that has been used for an empty string
                $tmp = '""';
            }
            $result = $this->createTree($tmp, '', '');
            $this->advance();
            return $result;
        // If it's an error code
        } elseif (preg_match("/^#[A-Z0\/]{3,5}[!?]{1}$/", $this->currentToken) or $this->currentToken == '#N/A') {
            $result = $this->createTree($this->currentToken, 'ptgErr', '');
            $this->advance();
            return $result;
        // If it's a negative value
        } elseif ($this->currentToken == "-") {
            // catch "-" Term
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgUminus', $result2, '');
            return $result;
        // If it's a positive value
        } elseif ($this->currentToken == "+") {
            // catch "+" Term
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgUplus', $result2, '');
            return $result;
        }
        $result = $this->term();
        while (($this->currentToken == "+") or
               ($this->currentToken == "-") or
               ($this->currentToken == "^")) {
        /**/
            if ($this->currentToken == "+") {
                $this->advance();
                $result2 = $this->term();
                $result = $this->createTree('ptgAdd', $result, $result2);
            } elseif ($this->currentToken == "-") {
                $this->advance();
                $result2 = $this->term();
                $result = $this->createTree('ptgSub', $result, $result2);
            } else {
                $this->advance();
                $result2 = $this->term();
                $result = $this->createTree('ptgPower', $result, $result2);
            }
        }
        return $result;
    }

    /**
     * This function just introduces a ptgParen element in the tree, so that Excel
     * doesn't get confused when working with a parenthesized formula afterwards.
     *
     * @access private
     * @see fact()
     * @return array The parsed ptg'd tree
     */
    private function parenthesizedExpression()
    {
        $result = $this->createTree('ptgParen', $this->expression(), '');
        return $result;
    }

    /**
     * It parses a term. It assumes the following rule:
     * Term -> Fact [("*" | "/") Fact]
     *
     * @access private
     * @return mixed The parsed ptg'd tree on success
     */
    private function term()
    {
        $result = $this->fact();
        while (($this->currentToken == "*") or
               ($this->currentToken == "/")) {
        /**/
            if ($this->currentToken == "*") {
                $this->advance();
                $result2 = $this->fact();
                $result = $this->createTree('ptgMul', $result, $result2);
            } else {
                $this->advance();
                $result2 = $this->fact();
                $result = $this->createTree('ptgDiv', $result, $result2);
            }
        }
        return $result;
    }

    /**
     * It parses a factor. It assumes the following rule:
     * Fact -> ( Expr )
     *       | CellRef
     *       | CellRange
     *       | Number
     *       | Function
     *
     * @access private
     * @return mixed The parsed ptg'd tree on success
     */
    private function fact()
    {
        if ($this->currentToken == "(") {
            $this->advance();         // eat the "("
            $result = $this->parenthesizedExpression();
            if ($this->currentToken != ")") {
                throw new PHPExcel_Writer_Exception("')' token expected.");
            }
            $this->advance();         // eat the ")"
            return $result;
        }
        // if it's a reference
        if (preg_match('/^\$?[A-Ia-i]?[A-Za-z]\$?[0-9]+$/', $this->currentToken)) {
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();
            return $result;
        } elseif (preg_match("/^" . self::REGEX_SHEET_TITLE_UNQUOTED . "(\:" . self::REGEX_SHEET_TITLE_UNQUOTED . ")?\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u", $this->currentToken)) {
            // If it's an external reference (Sheet1!A1 or Sheet1:Sheet2!A1 or Sheet1!$A$1 or Sheet1:Sheet2!$A$1)
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();
            return $result;
        } elseif (preg_match("/^'" . self::REGEX_SHEET_TITLE_QUOTED . "(\:" . self::REGEX_SHEET_TITLE_QUOTED . ")?'\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u", $this->currentToken)) {
            // If it's an external reference ('Sheet1'!A1 or 'Sheet1:Sheet2'!A1 or 'Sheet1'!$A$1 or 'Sheet1:Sheet2'!$A$1)
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();
            return $result;
        } elseif (preg_match('/^(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+:(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+$/', $this->currentToken) or
                preg_match('/^(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+\.\.(\$)?[A-Ia-i]?[A-Za-z](\$)?[0-9]+$/', $this->currentToken)) {
            // if it's a range A1:B2 or $A$1:$B$2
            // must be an error?
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();
            return $result;
        } elseif (preg_match("/^" . self::REGEX_SHEET_TITLE_UNQUOTED . "(\:" . self::REGEX_SHEET_TITLE_UNQUOTED . ")?\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u", $this->currentToken)) {
            // If it's an external range (Sheet1!A1:B2 or Sheet1:Sheet2!A1:B2 or Sheet1!$A$1:$B$2 or Sheet1:Sheet2!$A$1:$B$2)
            // must be an error?
            //$result = $this->currentToken;
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();
            return $result;
        } elseif (preg_match("/^'" . self::REGEX_SHEET_TITLE_QUOTED . "(\:" . self::REGEX_SHEET_TITLE_QUOTED . ")?'\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u", $this->currentToken)) {
            // If it's an external range ('Sheet1'!A1:B2 or 'Sheet1'!A1:B2 or 'Sheet1'!$A$1:$B$2 or 'Sheet1'!$A$1:$B$2)
            // must be an error?
            //$result = $this->currentToken;
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();
            return $result;
        } elseif (is_numeric($this->currentToken)) {
            // If it's a number or a percent
            if ($this->lookAhead == '%') {
                $result = $this->createTree('ptgPercent', $this->currentToken, '');
                $this->advance();  // Skip the percentage operator once we've pre-built that tree
            } else {
                $result = $this->createTree($this->currentToken, '', '');
            }
            $this->advance();
            return $result;
        } elseif (preg_match("/^[A-Z0-9\xc0-\xdc\.]+$/i", $this->currentToken)) {
            // if it's a function call
            $result = $this->func();
            return $result;
        }
        throw new PHPExcel_Writer_Exception("Syntax error: ".$this->currentToken.", lookahead: ".$this->lookAhead.", current char: ".$this->currentCharacter);
    }

    /**
     * It parses a function call. It assumes the following rule:
     * Func -> ( Expr [,Expr]* )
     *
     * @access private
     * @return mixed The parsed ptg'd tree on success
     */
    private function func()
    {
        $num_args = 0; // number of arguments received
        $function = strtoupper($this->currentToken);
        $result   = ''; // initialize result
        $this->advance();
        $this->advance();         // eat the "("
        while ($this->currentToken != ')') {
        /**/
            if ($num_args > 0) {
                if ($this->currentToken == "," || $this->currentToken == ";") {
                    $this->advance();  // eat the "," or ";"
                } else {
                    throw new PHPExcel_Writer_Exception("Syntax error: comma expected in function $function, arg #{$num_args}");
                }
                $result2 = $this->condition();
                $result = $this->createTree('arg', $result, $result2);
            } else { // first argument
                $result2 = $this->condition();
                $result = $this->createTree('arg', '', $result2);
            }
            ++$num_args;
        }
        if (!isset($this->functions[$function])) {
            throw new PHPExcel_Writer_Exception("Function $function() doesn't exist");
        }
        $args = $this->functions[$function][1];
        // If fixed number of args eg. TIME($i, $j, $k). Check that the number of args is valid.
        if (($args >= 0) and ($args != $num_args)) {
            throw new PHPExcel_Writer_Exception("Incorrect number of arguments in function $function() ");
        }

        $result = $this->createTree($function, $result, $num_args);
        $this->advance();         // eat the ")"
        return $result;
    }

    /**
     * Creates a tree. In fact an array which may have one or two arrays (sub-trees)
     * as elements.
     *
     * @access private
     * @param mixed $value The value of this node.
     * @param mixed $left  The left array (sub-tree) or a final node.
     * @param mixed $right The right array (sub-tree) or a final node.
     * @return array A tree
     */
    private function createTree($value, $left, $right)
    {
        return array('value' => $value, 'left' => $left, 'right' => $right);
    }

    /**
     * Builds a string containing the tree in reverse polish notation (What you
     * would use in a HP calculator stack).
     * The following tree:
     *
     *    +
     *   / \
     *  2   3
     *
     * produces: "23+"
     *
     * The following tree:
     *
     *    +
     *   / \
     *  3   *
     *     / \
     *    6   A1
     *
     * produces: "36A1*+"
     *
     * In fact all operands, functions, references, etc... are written as ptg's
     *
     * @access public
     * @param array $tree The optional tree to convert.
     * @return string The tree in reverse polish notation
     */
    public function toReversePolish($tree = array())
    {
        $polish = ""; // the string we are going to return
        if (empty($tree)) { // If it's the first call use parseTree
            $tree = $this->parseTree;
        }

        if (is_array($tree['left'])) {
            $converted_tree = $this->toReversePolish($tree['left']);
            $polish .= $converted_tree;
        } elseif ($tree['left'] != '') { // It's a final node
            $converted_tree = $this->convert($tree['left']);
            $polish .= $converted_tree;
        }
        if (is_array($tree['right'])) {
            $converted_tree = $this->toReversePolish($tree['right']);
            $polish .= $converted_tree;
        } elseif ($tree['right'] != '') { // It's a final node
            $converted_tree = $this->convert($tree['right']);
            $polish .= $converted_tree;
        }
        // if it's a function convert it here (so we can set it's arguments)
        if (preg_match("/^[A-Z0-9\xc0-\xdc\.]+$/", $tree['value']) and
            !preg_match('/^([A-Ia-i]?[A-Za-z])(\d+)$/', $tree['value']) and
            !preg_match("/^[A-Ia-i]?[A-Za-z](\d+)\.\.[A-Ia-i]?[A-Za-z](\d+)$/", $tree['value']) and
            !is_numeric($tree['value']) and
            !isset($this->ptg[$tree['value']])) {
            // left subtree for a function is always an array.
            if ($tree['left'] != '') {
                $left_tree = $this->toReversePolish($tree['left']);
            } else {
                $left_tree = '';
            }
            // add it's left subtree and return.
            return $left_tree.$this->convertFunction($tree['value'], $tree['right']);
        } else {
            $converted_tree = $this->convert($tree['value']);
        }
        $polish .= $converted_tree;
        return $polish;
    }
}
