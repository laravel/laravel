<?php

/**
 * PHPExcel_Style_NumberFormat
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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Style_NumberFormat extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
    /* Pre-defined formats */
    const FORMAT_GENERAL                 = 'General';

    const FORMAT_TEXT                    = '@';

    const FORMAT_NUMBER                  = '0';
    const FORMAT_NUMBER_00               = '0.00';
    const FORMAT_NUMBER_COMMA_SEPARATED1 = '#,##0.00';
    const FORMAT_NUMBER_COMMA_SEPARATED2 = '#,##0.00_-';

    const FORMAT_PERCENTAGE              = '0%';
    const FORMAT_PERCENTAGE_00           = '0.00%';

    const FORMAT_DATE_YYYYMMDD2          = 'yyyy-mm-dd';
    const FORMAT_DATE_YYYYMMDD           = 'yy-mm-dd';
    const FORMAT_DATE_DDMMYYYY           = 'dd/mm/yy';
    const FORMAT_DATE_DMYSLASH           = 'd/m/y';
    const FORMAT_DATE_DMYMINUS           = 'd-m-y';
    const FORMAT_DATE_DMMINUS            = 'd-m';
    const FORMAT_DATE_MYMINUS            = 'm-y';
    const FORMAT_DATE_XLSX14             = 'mm-dd-yy';
    const FORMAT_DATE_XLSX15             = 'd-mmm-yy';
    const FORMAT_DATE_XLSX16             = 'd-mmm';
    const FORMAT_DATE_XLSX17             = 'mmm-yy';
    const FORMAT_DATE_XLSX22             = 'm/d/yy h:mm';
    const FORMAT_DATE_DATETIME           = 'd/m/y h:mm';
    const FORMAT_DATE_TIME1              = 'h:mm AM/PM';
    const FORMAT_DATE_TIME2              = 'h:mm:ss AM/PM';
    const FORMAT_DATE_TIME3              = 'h:mm';
    const FORMAT_DATE_TIME4              = 'h:mm:ss';
    const FORMAT_DATE_TIME5              = 'mm:ss';
    const FORMAT_DATE_TIME6              = 'h:mm:ss';
    const FORMAT_DATE_TIME7              = 'i:s.S';
    const FORMAT_DATE_TIME8              = 'h:mm:ss;@';
    const FORMAT_DATE_YYYYMMDDSLASH      = 'yy/mm/dd;@';

    const FORMAT_CURRENCY_USD_SIMPLE     = '"$"#,##0.00_-';
    const FORMAT_CURRENCY_USD            = '$#,##0_-';
    const FORMAT_CURRENCY_EUR_SIMPLE     = '[$EUR ]#,##0.00_-';

    /**
     * Excel built-in number formats
     *
     * @var array
     */
    protected static $builtInFormats;

    /**
     * Excel built-in number formats (flipped, for faster lookups)
     *
     * @var array
     */
    protected static $flippedBuiltInFormats;

    /**
     * Format Code
     *
     * @var string
     */
    protected $formatCode = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;

    /**
     * Built-in format Code
     *
     * @var string
     */
    protected $builtInFormatCode    = 0;

    /**
     * Create a new PHPExcel_Style_NumberFormat
     *
     * @param    boolean    $isSupervisor    Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param    boolean    $isConditional    Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        if ($isConditional) {
            $this->formatCode = null;
            $this->builtInFormatCode = false;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor
     *
     * @return PHPExcel_Style_NumberFormat
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getNumberFormat();
    }

    /**
     * Build style array from subcomponents
     *
     * @param array $array
     * @return array
     */
    public function getStyleArray($array)
    {
        return array('numberformat' => $array);
    }

    /**
     * Apply styles from array
     *
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->applyFromArray(
     *        array(
     *            'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
     *        )
     * );
     * </code>
     *
     * @param    array    $pStyles    Array containing style information
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Style_NumberFormat
     */
    public function applyFromArray($pStyles = null)
    {
        if (is_array($pStyles)) {
            if ($this->isSupervisor) {
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
            } else {
                if (array_key_exists('code', $pStyles)) {
                    $this->setFormatCode($pStyles['code']);
                }
            }
        } else {
            throw new PHPExcel_Exception("Invalid style array passed.");
        }
        return $this;
    }

    /**
     * Get Format Code
     *
     * @return string
     */
    public function getFormatCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getFormatCode();
        }
        if ($this->builtInFormatCode !== false) {
            return self::builtInFormatCode($this->builtInFormatCode);
        }
        return $this->formatCode;
    }

    /**
     * Set Format Code
     *
     * @param string $pValue
     * @return PHPExcel_Style_NumberFormat
     */
    public function setFormatCode($pValue = PHPExcel_Style_NumberFormat::FORMAT_GENERAL)
    {
        if ($pValue == '') {
            $pValue = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(array('code' => $pValue));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->formatCode = $pValue;
            $this->builtInFormatCode = self::builtInFormatCodeIndex($pValue);
        }
        return $this;
    }

    /**
     * Get Built-In Format Code
     *
     * @return int
     */
    public function getBuiltInFormatCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBuiltInFormatCode();
        }
        return $this->builtInFormatCode;
    }

    /**
     * Set Built-In Format Code
     *
     * @param int $pValue
     * @return PHPExcel_Style_NumberFormat
     */
    public function setBuiltInFormatCode($pValue = 0)
    {

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(array('code' => self::builtInFormatCode($pValue)));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->builtInFormatCode = $pValue;
            $this->formatCode = self::builtInFormatCode($pValue);
        }
        return $this;
    }

    /**
     * Fill built-in format codes
     */
    private static function fillBuiltInFormatCodes()
    {
        //  [MS-OI29500: Microsoft Office Implementation Information for ISO/IEC-29500 Standard Compliance]
        //  18.8.30. numFmt (Number Format)
        //
        //  The ECMA standard defines built-in format IDs
        //      14: "mm-dd-yy"
        //      22: "m/d/yy h:mm"
        //      37: "#,##0 ;(#,##0)"
        //      38: "#,##0 ;[Red](#,##0)"
        //      39: "#,##0.00;(#,##0.00)"
        //      40: "#,##0.00;[Red](#,##0.00)"
        //      47: "mmss.0"
        //      KOR fmt 55: "yyyy-mm-dd"
        //  Excel defines built-in format IDs
        //      14: "m/d/yyyy"
        //      22: "m/d/yyyy h:mm"
        //      37: "#,##0_);(#,##0)"
        //      38: "#,##0_);[Red](#,##0)"
        //      39: "#,##0.00_);(#,##0.00)"
        //      40: "#,##0.00_);[Red](#,##0.00)"
        //      47: "mm:ss.0"
        //      KOR fmt 55: "yyyy/mm/dd"
 
        // Built-in format codes
        if (is_null(self::$builtInFormats)) {
            self::$builtInFormats = array();

            // General
            self::$builtInFormats[0] = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
            self::$builtInFormats[1] = '0';
            self::$builtInFormats[2] = '0.00';
            self::$builtInFormats[3] = '#,##0';
            self::$builtInFormats[4] = '#,##0.00';

            self::$builtInFormats[9] = '0%';
            self::$builtInFormats[10] = '0.00%';
            self::$builtInFormats[11] = '0.00E+00';
            self::$builtInFormats[12] = '# ?/?';
            self::$builtInFormats[13] = '# ??/??';
            self::$builtInFormats[14] = 'm/d/yyyy';                     // Despite ECMA 'mm-dd-yy';
            self::$builtInFormats[15] = 'd-mmm-yy';
            self::$builtInFormats[16] = 'd-mmm';
            self::$builtInFormats[17] = 'mmm-yy';
            self::$builtInFormats[18] = 'h:mm AM/PM';
            self::$builtInFormats[19] = 'h:mm:ss AM/PM';
            self::$builtInFormats[20] = 'h:mm';
            self::$builtInFormats[21] = 'h:mm:ss';
            self::$builtInFormats[22] = 'm/d/yyyy h:mm';                // Despite ECMA 'm/d/yy h:mm';

            self::$builtInFormats[37] = '#,##0_);(#,##0)';              //  Despite ECMA '#,##0 ;(#,##0)';
            self::$builtInFormats[38] = '#,##0_);[Red](#,##0)';         //  Despite ECMA '#,##0 ;[Red](#,##0)';
            self::$builtInFormats[39] = '#,##0.00_);(#,##0.00)';        //  Despite ECMA '#,##0.00;(#,##0.00)';
            self::$builtInFormats[40] = '#,##0.00_);[Red](#,##0.00)';   //  Despite ECMA '#,##0.00;[Red](#,##0.00)';

            self::$builtInFormats[44] = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
            self::$builtInFormats[45] = 'mm:ss';
            self::$builtInFormats[46] = '[h]:mm:ss';
            self::$builtInFormats[47] = 'mm:ss.0';                      //  Despite ECMA 'mmss.0';
            self::$builtInFormats[48] = '##0.0E+0';
            self::$builtInFormats[49] = '@';

            // CHT
            self::$builtInFormats[27] = '[$-404]e/m/d';
            self::$builtInFormats[30] = 'm/d/yy';
            self::$builtInFormats[36] = '[$-404]e/m/d';
            self::$builtInFormats[50] = '[$-404]e/m/d';
            self::$builtInFormats[57] = '[$-404]e/m/d';

            // THA
            self::$builtInFormats[59] = 't0';
            self::$builtInFormats[60] = 't0.00';
            self::$builtInFormats[61] = 't#,##0';
            self::$builtInFormats[62] = 't#,##0.00';
            self::$builtInFormats[67] = 't0%';
            self::$builtInFormats[68] = 't0.00%';
            self::$builtInFormats[69] = 't# ?/?';
            self::$builtInFormats[70] = 't# ??/??';

            // Flip array (for faster lookups)
            self::$flippedBuiltInFormats = array_flip(self::$builtInFormats);
        }
    }

    /**
     * Get built-in format code
     *
     * @param    int        $pIndex
     * @return    string
     */
    public static function builtInFormatCode($pIndex)
    {
        // Clean parameter
        $pIndex = intval($pIndex);

        // Ensure built-in format codes are available
        self::fillBuiltInFormatCodes();
        // Lookup format code
        if (isset(self::$builtInFormats[$pIndex])) {
            return self::$builtInFormats[$pIndex];
        }

        return '';
    }

    /**
     * Get built-in format code index
     *
     * @param    string        $formatCode
     * @return    int|boolean
     */
    public static function builtInFormatCodeIndex($formatCode)
    {
        // Ensure built-in format codes are available
        self::fillBuiltInFormatCodes();

        // Lookup format code
        if (isset(self::$flippedBuiltInFormats[$formatCode])) {
            return self::$flippedBuiltInFormats[$formatCode];
        }

        return false;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }
        return md5(
            $this->formatCode .
            $this->builtInFormatCode .
            __CLASS__
        );
    }

    /**
     * Search/replace values to convert Excel date/time format masks to PHP format masks
     *
     * @var array
     */
    private static $dateFormatReplacements = array(
            // first remove escapes related to non-format characters
            '\\'    => '',
            //    12-hour suffix
            'am/pm' => 'A',
            //    4-digit year
            'e'     => 'Y',
            'yyyy'  => 'Y',
            //    2-digit year
            'yy'    => 'y',
            //    first letter of month - no php equivalent
            'mmmmm' => 'M',
            //    full month name
            'mmmm'  => 'F',
            //    short month name
            'mmm'   => 'M',
            //    mm is minutes if time, but can also be month w/leading zero
            //    so we try to identify times be the inclusion of a : separator in the mask
            //    It isn't perfect, but the best way I know how
            ':mm'   => ':i',
            'mm:'   => 'i:',
            //    month leading zero
            'mm'    => 'm',
            //    month no leading zero
            'm'     => 'n',
            //    full day of week name
            'dddd'  => 'l',
            //    short day of week name
            'ddd'   => 'D',
            //    days leading zero
            'dd'    => 'd',
            //    days no leading zero
            'd'     => 'j',
            //    seconds
            'ss'    => 's',
            //    fractional seconds - no php equivalent
            '.s'    => ''
        );
    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (24 hr clock)
     *
     * @var array
     */
    private static $dateFormatReplacements24 = array(
            'hh' => 'H',
            'h'  => 'G'
        );
    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (12 hr clock)
     *
     * @var array
     */
    private static $dateFormatReplacements12 = array(
            'hh' => 'h',
            'h'  => 'g'
        );

    private static function setLowercaseCallback($matches) {
        return mb_strtolower($matches[0]);
    }

    private static function escapeQuotesCallback($matches) {
        return '\\' . implode('\\', str_split($matches[1]));
    }

    private static function formatAsDate(&$value, &$format)
    {
        // strip off first part containing e.g. [$-F800] or [$USD-409]
        // general syntax: [$<Currency string>-<language info>]
        // language info is in hexadecimal
        $format = preg_replace('/^(\[\$[A-Z]*-[0-9A-F]*\])/i', '', $format);

        // OpenOffice.org uses upper-case number formats, e.g. 'YYYY', convert to lower-case;
        //    but we don't want to change any quoted strings
        $format = preg_replace_callback('/(?:^|")([^"]*)(?:$|")/', array('self', 'setLowercaseCallback'), $format);

        // Only process the non-quoted blocks for date format characters
        $blocks = explode('"', $format);
        foreach($blocks as $key => &$block) {
            if ($key % 2 == 0) {
                $block = strtr($block, self::$dateFormatReplacements);
                if (!strpos($block, 'A')) {
                    // 24-hour time format
                    $block = strtr($block, self::$dateFormatReplacements24);
                } else {
                    // 12-hour time format
                    $block = strtr($block, self::$dateFormatReplacements12);
                }
            }
        }
        $format = implode('"', $blocks);

        // escape any quoted characters so that DateTime format() will render them correctly
        $format = preg_replace_callback('/"(.*)"/U', array('self', 'escapeQuotesCallback'), $format);

        $dateObj = PHPExcel_Shared_Date::ExcelToPHPObject($value);
        $value = $dateObj->format($format);
    }

    private static function formatAsPercentage(&$value, &$format)
    {
        if ($format === self::FORMAT_PERCENTAGE) {
            $value = round((100 * $value), 0) . '%';
        } else {
            if (preg_match('/\.[#0]+/i', $format, $m)) {
                $s = substr($m[0], 0, 1) . (strlen($m[0]) - 1);
                $format = str_replace($m[0], $s, $format);
            }
            if (preg_match('/^[#0]+/', $format, $m)) {
                $format = str_replace($m[0], strlen($m[0]), $format);
            }
            $format = '%' . str_replace('%', 'f%%', $format);

            $value = sprintf($format, 100 * $value);
        }
    }

    private static function formatAsFraction(&$value, &$format)
    {
        $sign = ($value < 0) ? '-' : '';

        $integerPart = floor(abs($value));
        $decimalPart = trim(fmod(abs($value), 1), '0.');
        $decimalLength = strlen($decimalPart);
        $decimalDivisor = pow(10, $decimalLength);

        $GCD = PHPExcel_Calculation_MathTrig::GCD($decimalPart, $decimalDivisor);

        $adjustedDecimalPart = $decimalPart/$GCD;
        $adjustedDecimalDivisor = $decimalDivisor/$GCD;

        if ((strpos($format, '0') !== false) || (strpos($format, '#') !== false) || (substr($format, 0, 3) == '? ?')) {
            if ($integerPart == 0) {
                $integerPart = '';
            }
            $value = "$sign$integerPart $adjustedDecimalPart/$adjustedDecimalDivisor";
        } else {
            $adjustedDecimalPart += $integerPart * $adjustedDecimalDivisor;
            $value = "$sign$adjustedDecimalPart/$adjustedDecimalDivisor";
        }
    }

    private static function complexNumberFormatMask($number, $mask, $level = 0)
    {
        $sign = ($number < 0.0);
        $number = abs($number);
        if (strpos($mask, '.') !== false) {
            $numbers = explode('.', $number . '.0');
            $masks = explode('.', $mask . '.0');
            $result1 = self::complexNumberFormatMask($numbers[0], $masks[0], 1);
            $result2 = strrev(self::complexNumberFormatMask(strrev($numbers[1]), strrev($masks[1]), 1));
            return (($sign) ? '-' : '') . $result1 . '.' . $result2;
        }

        $r = preg_match_all('/0+/', $mask, $result, PREG_OFFSET_CAPTURE);
        if ($r > 1) {
            $result = array_reverse($result[0]);

            foreach ($result as $block) {
                $divisor = 1 . $block[0];
                $size = strlen($block[0]);
                $offset = $block[1];

                $blockValue = sprintf(
                    '%0' . $size . 'd',
                    fmod($number, $divisor)
                );
                $number = floor($number / $divisor);
                $mask = substr_replace($mask, $blockValue, $offset, $size);
            }
            if ($number > 0) {
                $mask = substr_replace($mask, $number, $offset, 0);
            }
            $result = $mask;
        } else {
            $result = $number;
        }

        return (($sign) ? '-' : '') . $result;
    }

    /**
     * Convert a value in a pre-defined format to a PHP string
     *
     * @param mixed    $value        Value to format
     * @param string    $format        Format code
     * @param array        $callBack    Callback function for additional formatting of string
     * @return string    Formatted string
     */
    public static function toFormattedString($value = '0', $format = PHPExcel_Style_NumberFormat::FORMAT_GENERAL, $callBack = null)
    {
        // For now we do not treat strings although section 4 of a format code affects strings
        if (!is_numeric($value)) {
            return $value;
        }

        // For 'General' format code, we just pass the value although this is not entirely the way Excel does it,
        // it seems to round numbers to a total of 10 digits.
        if (($format === PHPExcel_Style_NumberFormat::FORMAT_GENERAL) || ($format === PHPExcel_Style_NumberFormat::FORMAT_TEXT)) {
            return $value;
        }

        // Convert any other escaped characters to quoted strings, e.g. (\T to "T")
        $format = preg_replace('/(\\\(.))(?=(?:[^"]|"[^"]*")*$)/u', '"${2}"', $format);

        // Get the sections, there can be up to four sections, separated with a semi-colon (but only if not a quoted literal)
        $sections = preg_split('/(;)(?=(?:[^"]|"[^"]*")*$)/u', $format);

        // Extract the relevant section depending on whether number is positive, negative, or zero?
        // Text not supported yet.
        // Here is how the sections apply to various values in Excel:
        //   1 section:   [POSITIVE/NEGATIVE/ZERO/TEXT]
        //   2 sections:  [POSITIVE/ZERO/TEXT] [NEGATIVE]
        //   3 sections:  [POSITIVE/TEXT] [NEGATIVE] [ZERO]
        //   4 sections:  [POSITIVE] [NEGATIVE] [ZERO] [TEXT]
        switch (count($sections)) {
            case 1:
                $format = $sections[0];
                break;
            case 2:
                $format = ($value >= 0) ? $sections[0] : $sections[1];
                $value = abs($value); // Use the absolute value
                break;
            case 3:
                $format = ($value > 0) ?
                    $sections[0] : ( ($value < 0) ?
                        $sections[1] : $sections[2]);
                $value = abs($value); // Use the absolute value
                break;
            case 4:
                $format = ($value > 0) ?
                    $sections[0] : ( ($value < 0) ?
                        $sections[1] : $sections[2]);
                $value = abs($value); // Use the absolute value
                break;
            default:
                // something is wrong, just use first section
                $format = $sections[0];
                break;
        }

        // In Excel formats, "_" is used to add spacing,
        //    The following character indicates the size of the spacing, which we can't do in HTML, so we just use a standard space
        $format = preg_replace('/_./', ' ', $format);

        // Save format with color information for later use below
        $formatColor = $format;

        // Strip color information
        $color_regex = '/^\\[[a-zA-Z]+\\]/';
        $format = preg_replace($color_regex, '', $format);

        // Let's begin inspecting the format and converting the value to a formatted string

        //  Check for date/time characters (not inside quotes)
        if (preg_match('/(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy](?=(?:[^"]|"[^"]*")*$)/miu', $format, $matches)) {
            // datetime format
            self::formatAsDate($value, $format);
        } elseif (preg_match('/%$/', $format)) {
            // % number format
            self::formatAsPercentage($value, $format);
        } else {
            if ($format === self::FORMAT_CURRENCY_EUR_SIMPLE) {
                $value = 'EUR ' . sprintf('%1.2f', $value);
            } else {
                // Some non-number strings are quoted, so we'll get rid of the quotes, likewise any positional * symbols
                $format = str_replace(array('"', '*'), '', $format);

                // Find out if we need thousands separator
                // This is indicated by a comma enclosed by a digit placeholder:
                //        #,#   or   0,0
                $useThousands = preg_match('/(#,#|0,0)/', $format);
                if ($useThousands) {
                    $format = preg_replace('/0,0/', '00', $format);
                    $format = preg_replace('/#,#/', '##', $format);
                }

                // Scale thousands, millions,...
                // This is indicated by a number of commas after a digit placeholder:
                //        #,   or    0.0,,
                $scale = 1; // same as no scale
                $matches = array();
                if (preg_match('/(#|0)(,+)/', $format, $matches)) {
                    $scale = pow(1000, strlen($matches[2]));

                    // strip the commas
                    $format = preg_replace('/0,+/', '0', $format);
                    $format = preg_replace('/#,+/', '#', $format);
                }

                if (preg_match('/#?.*\?\/\?/', $format, $m)) {
                    //echo 'Format mask is fractional '.$format.' <br />';
                    if ($value != (int)$value) {
                        self::formatAsFraction($value, $format);
                    }

                } else {
                    // Handle the number itself

                    // scale number
                    $value = $value / $scale;

                    // Strip #
                    $format = preg_replace('/\\#/', '0', $format);

                    $n = "/\[[^\]]+\]/";
                    $m = preg_replace($n, '', $format);
                    $number_regex = "/(0+)(\.?)(0*)/";
                    if (preg_match($number_regex, $m, $matches)) {
                        $left = $matches[1];
                        $dec = $matches[2];
                        $right = $matches[3];

                        // minimun width of formatted number (including dot)
                        $minWidth = strlen($left) + strlen($dec) + strlen($right);
                        if ($useThousands) {
                            $value = number_format(
                                $value,
                                strlen($right),
                                PHPExcel_Shared_String::getDecimalSeparator(),
                                PHPExcel_Shared_String::getThousandsSeparator()
                            );
                            $value = preg_replace($number_regex, $value, $format);
                        } else {
                            if (preg_match('/[0#]E[+-]0/i', $format)) {
                                //    Scientific format
                                $value = sprintf('%5.2E', $value);
                            } elseif (preg_match('/0([^\d\.]+)0/', $format)) {
                                $value = self::complexNumberFormatMask($value, $format);
                            } else {
                                $sprintf_pattern = "%0$minWidth." . strlen($right) . "f";
                                $value = sprintf($sprintf_pattern, $value);
                                $value = preg_replace($number_regex, $value, $format);
                            }
                        }
                    }
                }
                if (preg_match('/\[\$(.*)\]/u', $format, $m)) {
                    //  Currency or Accounting
                    $currencyFormat = $m[0];
                    $currencyCode = $m[1];
                    list($currencyCode) = explode('-', $currencyCode);
                    if ($currencyCode == '') {
                        $currencyCode = PHPExcel_Shared_String::getCurrencyCode();
                    }
                    $value = preg_replace('/\[\$([^\]]*)\]/u', $currencyCode, $value);
                }
            }
        }

        // Escape any escaped slashes to a single slash
        $format = preg_replace("/\\\\/u", '\\', $format);

        // Additional formatting provided by callback function
        if ($callBack !== null) {
            list($writerInstance, $function) = $callBack;
            $value = $writerInstance->$function($value, $formatColor);
        }

        return $value;
    }
}
