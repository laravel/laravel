<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
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
 * @package	PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Style_NumberFormat
 *
 * @category   PHPExcel
 * @package	PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_NumberFormat extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
	/* Pre-defined formats */
	const FORMAT_GENERAL					= 'General';

	const FORMAT_TEXT						= '@';

	const FORMAT_NUMBER						= '0';
	const FORMAT_NUMBER_00					= '0.00';
	const FORMAT_NUMBER_COMMA_SEPARATED1	= '#,##0.00';
	const FORMAT_NUMBER_COMMA_SEPARATED2	= '#,##0.00_-';

	const FORMAT_PERCENTAGE					= '0%';
	const FORMAT_PERCENTAGE_00				= '0.00%';

	const FORMAT_DATE_YYYYMMDD2				= 'yyyy-mm-dd';
	const FORMAT_DATE_YYYYMMDD				= 'yy-mm-dd';
	const FORMAT_DATE_DDMMYYYY				= 'dd/mm/yy';
	const FORMAT_DATE_DMYSLASH				= 'd/m/y';
	const FORMAT_DATE_DMYMINUS				= 'd-m-y';
	const FORMAT_DATE_DMMINUS				= 'd-m';
	const FORMAT_DATE_MYMINUS				= 'm-y';
	const FORMAT_DATE_XLSX14				= 'mm-dd-yy';
	const FORMAT_DATE_XLSX15				= 'd-mmm-yy';
	const FORMAT_DATE_XLSX16				= 'd-mmm';
	const FORMAT_DATE_XLSX17				= 'mmm-yy';
	const FORMAT_DATE_XLSX22				= 'm/d/yy h:mm';
	const FORMAT_DATE_DATETIME				= 'd/m/y h:mm';
	const FORMAT_DATE_TIME1					= 'h:mm AM/PM';
	const FORMAT_DATE_TIME2					= 'h:mm:ss AM/PM';
	const FORMAT_DATE_TIME3					= 'h:mm';
	const FORMAT_DATE_TIME4					= 'h:mm:ss';
	const FORMAT_DATE_TIME5					= 'mm:ss';
	const FORMAT_DATE_TIME6					= 'h:mm:ss';
	const FORMAT_DATE_TIME7					= 'i:s.S';
	const FORMAT_DATE_TIME8					= 'h:mm:ss;@';
	const FORMAT_DATE_YYYYMMDDSLASH			= 'yy/mm/dd;@';

	const FORMAT_CURRENCY_USD_SIMPLE		= '"$"#,##0.00_-';
	const FORMAT_CURRENCY_USD				= '$#,##0_-';
	const FORMAT_CURRENCY_EUR_SIMPLE		= '[$EUR ]#,##0.00_-';

	/**
	 * Excel built-in number formats
	 *
	 * @var array
	 */
	protected static $_builtInFormats;

	/**
	 * Excel built-in number formats (flipped, for faster lookups)
	 *
	 * @var array
	 */
	protected static $_flippedBuiltInFormats;

	/**
	 * Format Code
	 *
	 * @var string
	 */
	protected $_formatCode	=	PHPExcel_Style_NumberFormat::FORMAT_GENERAL;

	/**
	 * Built-in format Code
	 *
	 * @var string
	 */
	protected $_builtInFormatCode	= 0;

	/**
	 * Create a new PHPExcel_Style_NumberFormat
	 *
	 * @param	boolean	$isSupervisor	Flag indicating if this is a supervisor or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
	 * @param	boolean	$isConditional	Flag indicating if this is a conditional style or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
	 */
	public function __construct($isSupervisor = FALSE, $isConditional = FALSE)
	{
		// Supervisor?
		parent::__construct($isSupervisor);

		if ($isConditional) {
			$this->_formatCode = NULL;
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
		return $this->_parent->getSharedComponent()->getNumberFormat();
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
	 *		array(
	 *			'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
	 *		)
	 * );
	 * </code>
	 *
	 * @param	array	$pStyles	Array containing style information
	 * @throws	PHPExcel_Exception
	 * @return PHPExcel_Style_NumberFormat
	 */
	public function applyFromArray($pStyles = null)
	{
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
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
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getFormatCode();
		}
		if ($this->_builtInFormatCode !== false)
		{
			return self::builtInFormatCode($this->_builtInFormatCode);
		}
		return $this->_formatCode;
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
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('code' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_formatCode = $pValue;
			$this->_builtInFormatCode = self::builtInFormatCodeIndex($pValue);
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
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getBuiltInFormatCode();
		}
		return $this->_builtInFormatCode;
	}

	/**
	 * Set Built-In Format Code
	 *
	 * @param int $pValue
	 * @return PHPExcel_Style_NumberFormat
	 */
	public function setBuiltInFormatCode($pValue = 0)
	{

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('code' => self::builtInFormatCode($pValue)));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_builtInFormatCode = $pValue;
			$this->_formatCode = self::builtInFormatCode($pValue);
		}
		return $this;
	}

	/**
	 * Fill built-in format codes
	 */
	private static function fillBuiltInFormatCodes()
	{
		// Built-in format codes
		if (is_null(self::$_builtInFormats)) {
			self::$_builtInFormats = array();

			// General
			self::$_builtInFormats[0] = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
			self::$_builtInFormats[1] = '0';
			self::$_builtInFormats[2] = '0.00';
			self::$_builtInFormats[3] = '#,##0';
			self::$_builtInFormats[4] = '#,##0.00';

			self::$_builtInFormats[9] = '0%';
			self::$_builtInFormats[10] = '0.00%';
			self::$_builtInFormats[11] = '0.00E+00';
			self::$_builtInFormats[12] = '# ?/?';
			self::$_builtInFormats[13] = '# ??/??';
			self::$_builtInFormats[14] = 'mm-dd-yy';
			self::$_builtInFormats[15] = 'd-mmm-yy';
			self::$_builtInFormats[16] = 'd-mmm';
			self::$_builtInFormats[17] = 'mmm-yy';
			self::$_builtInFormats[18] = 'h:mm AM/PM';
			self::$_builtInFormats[19] = 'h:mm:ss AM/PM';
			self::$_builtInFormats[20] = 'h:mm';
			self::$_builtInFormats[21] = 'h:mm:ss';
			self::$_builtInFormats[22] = 'm/d/yy h:mm';

			self::$_builtInFormats[37] = '#,##0 ;(#,##0)';
			self::$_builtInFormats[38] = '#,##0 ;[Red](#,##0)';
			self::$_builtInFormats[39] = '#,##0.00;(#,##0.00)';
			self::$_builtInFormats[40] = '#,##0.00;[Red](#,##0.00)';

			self::$_builtInFormats[44] = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
			self::$_builtInFormats[45] = 'mm:ss';
			self::$_builtInFormats[46] = '[h]:mm:ss';
			self::$_builtInFormats[47] = 'mmss.0';
			self::$_builtInFormats[48] = '##0.0E+0';
			self::$_builtInFormats[49] = '@';

			// CHT
			self::$_builtInFormats[27] = '[$-404]e/m/d';
			self::$_builtInFormats[30] = 'm/d/yy';
			self::$_builtInFormats[36] = '[$-404]e/m/d';
			self::$_builtInFormats[50] = '[$-404]e/m/d';
			self::$_builtInFormats[57] = '[$-404]e/m/d';

			// THA
			self::$_builtInFormats[59] = 't0';
			self::$_builtInFormats[60] = 't0.00';
			self::$_builtInFormats[61] = 't#,##0';
			self::$_builtInFormats[62] = 't#,##0.00';
			self::$_builtInFormats[67] = 't0%';
			self::$_builtInFormats[68] = 't0.00%';
			self::$_builtInFormats[69] = 't# ?/?';
			self::$_builtInFormats[70] = 't# ??/??';

			// Flip array (for faster lookups)
			self::$_flippedBuiltInFormats = array_flip(self::$_builtInFormats);
		}
	}

	/**
	 * Get built-in format code
	 *
	 * @param	int		$pIndex
	 * @return	string
	 */
	public static function builtInFormatCode($pIndex)
	{
		// Clean parameter
		$pIndex = intval($pIndex);

		// Ensure built-in format codes are available
		self::fillBuiltInFormatCodes();

		// Lookup format code
		if (isset(self::$_builtInFormats[$pIndex])) {
			return self::$_builtInFormats[$pIndex];
		}

		return '';
	}

	/**
	 * Get built-in format code index
	 *
	 * @param	string		$formatCode
	 * @return	int|boolean
	 */
	public static function builtInFormatCodeIndex($formatCode)
	{
		// Ensure built-in format codes are available
		self::fillBuiltInFormatCodes();

		// Lookup format code
		if (isset(self::$_flippedBuiltInFormats[$formatCode])) {
			return self::$_flippedBuiltInFormats[$formatCode];
		}

		return false;
	}

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}
		return md5(
			  $this->_formatCode
			. $this->_builtInFormatCode
			. __CLASS__
		);
	}

	/**
	 * Search/replace values to convert Excel date/time format masks to PHP format masks
	 *
	 * @var array
	 */
	private static $_dateFormatReplacements = array(
			// first remove escapes related to non-format characters
			'\\'	=> '',
			//	12-hour suffix
			'am/pm'	=> 'A',
			//	4-digit year
			'e'	=> 'Y',
			'yyyy'	=> 'Y',
			//	2-digit year
			'yy'	=> 'y',
			//	first letter of month - no php equivalent
			'mmmmm'	=> 'M',
			//	full month name
			'mmmm'	=> 'F',
			//	short month name
			'mmm'	=> 'M',
			//	mm is minutes if time, but can also be month w/leading zero
			//	so we try to identify times be the inclusion of a : separator in the mask
			//	It isn't perfect, but the best way I know how
			':mm'	=> ':i',
			'mm:'	=> 'i:',
			//	month leading zero
			'mm'	=> 'm',
			//	month no leading zero
			'm'		=> 'n',
			//	full day of week name
			'dddd'	=> 'l',
			//	short day of week name
			'ddd'	=> 'D',
			//	days leading zero
			'dd'	=> 'd',
			//	days no leading zero
			'd'		=> 'j',
			//	seconds
			'ss'	=> 's',
			//	fractional seconds - no php equivalent
			'.s'	=> ''
		);
	/**
	 * Search/replace values to convert Excel date/time format masks hours to PHP format masks (24 hr clock)
	 *
	 * @var array
	 */
	private static $_dateFormatReplacements24 = array(
			'hh'	=> 'H',
			'h'		=> 'G'
		);
	/**
	 * Search/replace values to convert Excel date/time format masks hours to PHP format masks (12 hr clock)
	 *
	 * @var array
	 */
	private static $_dateFormatReplacements12 = array(
			'hh'	=> 'h',
			'h'		=> 'g'
		);

	private static function _formatAsDate(&$value, &$format)
	{
		// dvc: convert Excel formats to PHP date formats

		// strip off first part containing e.g. [$-F800] or [$USD-409]
		// general syntax: [$<Currency string>-<language info>]
		// language info is in hexadecimal
		$format = preg_replace('/^(\[\$[A-Z]*-[0-9A-F]*\])/i', '', $format);

		// OpenOffice.org uses upper-case number formats, e.g. 'YYYY', convert to lower-case
		$format = strtolower($format);

		$format = strtr($format,self::$_dateFormatReplacements);
		if (!strpos($format,'A')) {	// 24-hour time format
			$format = strtr($format,self::$_dateFormatReplacements24);
		} else {					// 12-hour time format
			$format = strtr($format,self::$_dateFormatReplacements12);
		}

		$dateObj = PHPExcel_Shared_Date::ExcelToPHPObject($value);
		$value = $dateObj->format($format);
	}

	private static function _formatAsPercentage(&$value, &$format)
	{
		if ($format === self::FORMAT_PERCENTAGE) {
			$value = round( (100 * $value), 0) . '%';
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

	private static function _formatAsFraction(&$value, &$format)
	{
		$sign = ($value < 0) ? '-' : '';

		$integerPart = floor(abs($value));
		$decimalPart = trim(fmod(abs($value),1),'0.');
		$decimalLength = strlen($decimalPart);
		$decimalDivisor = pow(10,$decimalLength);

		$GCD = PHPExcel_Calculation_MathTrig::GCD($decimalPart,$decimalDivisor);

		$adjustedDecimalPart = $decimalPart/$GCD;
		$adjustedDecimalDivisor = $decimalDivisor/$GCD;

		if ((strpos($format,'0') !== false) || (strpos($format,'#') !== false) || (substr($format,0,3) == '? ?')) {
			if ($integerPart == 0) {
				$integerPart = '';
			}
			$value = "$sign$integerPart $adjustedDecimalPart/$adjustedDecimalDivisor";
		} else {
			$adjustedDecimalPart += $integerPart * $adjustedDecimalDivisor;
			$value = "$sign$adjustedDecimalPart/$adjustedDecimalDivisor";
		}
	}

	private static function _complexNumberFormatMask($number, $mask) {
		if (strpos($mask,'.') !== false) {
			$numbers = explode('.', $number . '.0');
			$masks = explode('.', $mask . '.0');
			$result1 = self::_complexNumberFormatMask($numbers[0], $masks[0]);
			$result2 = strrev(self::_complexNumberFormatMask(strrev($numbers[1]), strrev($masks[1])));
			return $result1 . '.' . $result2;
		}

		$r = preg_match_all('/0+/', $mask, $result, PREG_OFFSET_CAPTURE);
		if ($r > 1) {
			$result = array_reverse($result[0]);

			foreach($result as $block) {
				$divisor = 1 . $block[0];
				$size = strlen($block[0]);
				$offset = $block[1];

				$blockValue = sprintf(
					'%0' . $size . 'd',
					fmod($number, $divisor)
				);
				$number = floor($number / $divisor);
				$mask = substr_replace($mask,$blockValue, $offset, $size);
			}
			if ($number > 0) {
				$mask = substr_replace($mask, $number, $offset, 0);
			}
			$result = $mask;
		} else {
			$result = $number;
		}

		return $result;
	}

	/**
	 * Convert a value in a pre-defined format to a PHP string
	 *
	 * @param mixed	$value		Value to format
	 * @param string	$format		Format code
	 * @param array		$callBack	Callback function for additional formatting of string
	 * @return string	Formatted string
	 */
	public static function toFormattedString($value = '0', $format = PHPExcel_Style_NumberFormat::FORMAT_GENERAL, $callBack = null)
	{
		// For now we do not treat strings although section 4 of a format code affects strings
		if (!is_numeric($value)) return $value;

		// For 'General' format code, we just pass the value although this is not entirely the way Excel does it,
		// it seems to round numbers to a total of 10 digits.
		if (($format === PHPExcel_Style_NumberFormat::FORMAT_GENERAL) || ($format === PHPExcel_Style_NumberFormat::FORMAT_TEXT)) {
			return $value;
		}

		// Get the sections, there can be up to four sections
		$sections = explode(';', $format);

		// Fetch the relevant section depending on whether number is positive, negative, or zero?
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

		// Save format with color information for later use below
		$formatColor = $format;

		// Strip color information
		$color_regex = '/^\\[[a-zA-Z]+\\]/';
		$format = preg_replace($color_regex, '', $format);

		// Let's begin inspecting the format and converting the value to a formatted string
		if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $format)) { // datetime format
			self::_formatAsDate($value, $format);
		} else if (preg_match('/%$/', $format)) { // % number format
			self::_formatAsPercentage($value, $format);
		} else {
			if ($format === self::FORMAT_CURRENCY_EUR_SIMPLE) {
				$value = 'EUR ' . sprintf('%1.2f', $value);
			} else {
				// In Excel formats, "_" is used to add spacing, which we can't do in HTML
				$format = preg_replace('/_./', '', $format);

				// Some non-number characters are escaped with \, which we don't need
				$format = preg_replace("/\\\\/", '', $format);

				// Some non-number strings are quoted, so we'll get rid of the quotes, likewise any positional * symbols
				$format = str_replace(array('"','*'), '', $format);

				// Find out if we need thousands separator
				// This is indicated by a comma enclosed by a digit placeholder:
				//		#,#   or   0,0
				$useThousands = preg_match('/(#,#|0,0)/', $format);
				if ($useThousands) {
					$format = preg_replace('/0,0/', '00', $format);
					$format = preg_replace('/#,#/', '##', $format);
				}

				// Scale thousands, millions,...
				// This is indicated by a number of commas after a digit placeholder:
				//		#,   or	0.0,,
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
						self::_formatAsFraction($value, $format);
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
										$value
										, strlen($right)
										, PHPExcel_Shared_String::getDecimalSeparator()
										, PHPExcel_Shared_String::getThousandsSeparator()
									);
							$value = preg_replace($number_regex, $value, $format);
						} else {
							if (preg_match('/[0#]E[+-]0/i', $format)) {
								//	Scientific format
								$value = sprintf('%5.2E', $value);
							} elseif (preg_match('/0([^\d\.]+)0/', $format)) {
								$value = self::_complexNumberFormatMask($value, $format);
							} else {
								$sprintf_pattern = "%0$minWidth." . strlen($right) . "f";
								$value = sprintf($sprintf_pattern, $value);
								$value = preg_replace($number_regex, $value, $format);
							}
						}
					}
				}
				if (preg_match('/\[\$(.*)\]/u', $format, $m)) {
					//	Currency or Accounting
					$currencyFormat = $m[0];
					$currencyCode = $m[1];
					list($currencyCode) = explode('-',$currencyCode);
					if ($currencyCode == '') {
						$currencyCode = PHPExcel_Shared_String::getCurrencyCode();
					}
					$value = preg_replace('/\[\$([^\]]*)\]/u',$currencyCode,$value);
				}
			}
		}

		// Additional formatting provided by callback function
		if ($callBack !== null) {
			list($writerInstance, $function) = $callBack;
			$value = $writerInstance->$function($value, $formatColor);
		}

		return $value;
	}

}
