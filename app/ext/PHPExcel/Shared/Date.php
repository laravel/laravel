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
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Shared_Date
 *
 * @category   PHPExcel
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Date
{
	/** constants */
	const CALENDAR_WINDOWS_1900 = 1900;		//	Base date of 1st Jan 1900 = 1.0
	const CALENDAR_MAC_1904 = 1904;			//	Base date of 2nd Jan 1904 = 1.0

	/*
	 * Names of the months of the year, indexed by shortname
	 * Planned usage for locale settings
	 *
	 * @public
	 * @var	string[]
	 */
	public static $_monthNames = array(	'Jan' => 'January',
										'Feb' => 'February',
										'Mar' => 'March',
										'Apr' => 'April',
										'May' => 'May',
										'Jun' => 'June',
										'Jul' => 'July',
										'Aug' => 'August',
										'Sep' => 'September',
										'Oct' => 'October',
										'Nov' => 'November',
										'Dec' => 'December',
									  );

	/*
	 * Names of the months of the year, indexed by shortname
	 * Planned usage for locale settings
	 *
	 * @public
	 * @var	string[]
	 */
	public static $_numberSuffixes = array(	'st',
											'nd',
											'rd',
											'th',
										  );

	/*
	 * Base calendar year to use for calculations
	 *
	 * @private
	 * @var	int
	 */
	protected static $_excelBaseDate	= self::CALENDAR_WINDOWS_1900;

	/**
	 * Set the Excel calendar (Windows 1900 or Mac 1904)
	 *
	 * @param	 integer	$baseDate			Excel base date (1900 or 1904)
	 * @return	 boolean						Success or failure
	 */
	public static function setExcelCalendar($baseDate) {
		if (($baseDate == self::CALENDAR_WINDOWS_1900) ||
			($baseDate == self::CALENDAR_MAC_1904)) {
			self::$_excelBaseDate = $baseDate;
			return TRUE;
		}
		return FALSE;
	}	//	function setExcelCalendar()


	/**
	 * Return the Excel calendar (Windows 1900 or Mac 1904)
	 *
	 * @return	 integer	Excel base date (1900 or 1904)
	 */
	public static function getExcelCalendar() {
		return self::$_excelBaseDate;
	}	//	function getExcelCalendar()


	/**
	 *	Convert a date from Excel to PHP
	 *
	 *	@param		long		$dateValue			Excel date/time value
	 *	@param		boolean		$adjustToTimezone	Flag indicating whether $dateValue should be treated as
	 *													a UST timestamp, or adjusted to UST
	 *	@param		string	 	$timezone			The timezone for finding the adjustment from UST
	 *	@return		long		PHP serialized date/time
	 */
	public static function ExcelToPHP($dateValue = 0, $adjustToTimezone = FALSE, $timezone = NULL) {
		if (self::$_excelBaseDate == self::CALENDAR_WINDOWS_1900) {
			$my_excelBaseDate = 25569;
			//	Adjust for the spurious 29-Feb-1900 (Day 60)
			if ($dateValue < 60) {
				--$my_excelBaseDate;
			}
		} else {
			$my_excelBaseDate = 24107;
		}

		// Perform conversion
		if ($dateValue >= 1) {
			$utcDays = $dateValue - $my_excelBaseDate;
			$returnValue = round($utcDays * 86400);
			if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
				$returnValue = (integer) $returnValue;
			}
		} else {
			$hours = round($dateValue * 24);
			$mins = round($dateValue * 1440) - round($hours * 60);
			$secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
			$returnValue = (integer) gmmktime($hours, $mins, $secs);
		}

		$timezoneAdjustment = ($adjustToTimezone) ?
		    PHPExcel_Shared_TimeZone::getTimezoneAdjustment($timezone, $returnValue) :
		    0;

		// Return
		return $returnValue + $timezoneAdjustment;
	}	//	function ExcelToPHP()


	/**
	 * Convert a date from Excel to a PHP Date/Time object
	 *
	 * @param	integer		$dateValue		Excel date/time value
	 * @return	integer						PHP date/time object
	 */
	public static function ExcelToPHPObject($dateValue = 0) {
		$dateTime = self::ExcelToPHP($dateValue);
		$days = floor($dateTime / 86400);
		$time = round((($dateTime / 86400) - $days) * 86400);
		$hours = round($time / 3600);
		$minutes = round($time / 60) - ($hours * 60);
		$seconds = round($time) - ($hours * 3600) - ($minutes * 60);

		$dateObj = date_create('1-Jan-1970+'.$days.' days');
		$dateObj->setTime($hours,$minutes,$seconds);

		return $dateObj;
	}	//	function ExcelToPHPObject()


	/**
	 *	Convert a date from PHP to Excel
	 *
	 *	@param	mixed		$dateValue			PHP serialized date/time or date object
	 *	@param	boolean		$adjustToTimezone	Flag indicating whether $dateValue should be treated as
	 *													a UST timestamp, or adjusted to UST
	 *	@param	string	 	$timezone			The timezone for finding the adjustment from UST
	 *	@return	mixed		Excel date/time value
	 *							or boolean FALSE on failure
	 */
	public static function PHPToExcel($dateValue = 0, $adjustToTimezone = FALSE, $timezone = NULL) {
		$saveTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$retValue = FALSE;
		if ((is_object($dateValue)) && ($dateValue instanceof DateTime)) {
			$retValue = self::FormattedPHPToExcel( $dateValue->format('Y'), $dateValue->format('m'), $dateValue->format('d'),
												   $dateValue->format('H'), $dateValue->format('i'), $dateValue->format('s')
												 );
		} elseif (is_numeric($dateValue)) {
			$retValue = self::FormattedPHPToExcel( date('Y',$dateValue), date('m',$dateValue), date('d',$dateValue),
												   date('H',$dateValue), date('i',$dateValue), date('s',$dateValue)
												 );
		}
		date_default_timezone_set($saveTimeZone);

		return $retValue;
	}	//	function PHPToExcel()


	/**
	 * FormattedPHPToExcel
	 *
	 * @param	long	$year
	 * @param	long	$month
	 * @param	long	$day
	 * @param	long	$hours
	 * @param	long	$minutes
	 * @param	long	$seconds
	 * @return  long				Excel date/time value
	 */
	public static function FormattedPHPToExcel($year, $month, $day, $hours=0, $minutes=0, $seconds=0) {
		if (self::$_excelBaseDate == self::CALENDAR_WINDOWS_1900) {
			//
			//	Fudge factor for the erroneous fact that the year 1900 is treated as a Leap Year in MS Excel
			//	This affects every date following 28th February 1900
			//
			$excel1900isLeapYear = TRUE;
			if (($year == 1900) && ($month <= 2)) { $excel1900isLeapYear = FALSE; }
			$my_excelBaseDate = 2415020;
		} else {
			$my_excelBaseDate = 2416481;
			$excel1900isLeapYear = FALSE;
		}

		//	Julian base date Adjustment
		if ($month > 2) {
			$month -= 3;
		} else {
			$month += 9;
			--$year;
		}

		//	Calculate the Julian Date, then subtract the Excel base date (JD 2415020 = 31-Dec-1899 Giving Excel Date of 0)
		$century = substr($year,0,2);
		$decade = substr($year,2,2);
		$excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $my_excelBaseDate + $excel1900isLeapYear;

		$excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

		return (float) $excelDate + $excelTime;
	}	//	function FormattedPHPToExcel()


	/**
	 * Is a given cell a date/time?
	 *
	 * @param	 PHPExcel_Cell	$pCell
	 * @return	 boolean
	 */
	public static function isDateTime(PHPExcel_Cell $pCell) {
		return self::isDateTimeFormat(
			$pCell->getWorksheet()->getStyle(
				$pCell->getCoordinate()
			)->getNumberFormat()
		);
	}	//	function isDateTime()


	/**
	 * Is a given number format a date/time?
	 *
	 * @param	 PHPExcel_Style_NumberFormat	$pFormat
	 * @return	 boolean
	 */
	public static function isDateTimeFormat(PHPExcel_Style_NumberFormat $pFormat) {
		return self::isDateTimeFormatCode($pFormat->getFormatCode());
	}	//	function isDateTimeFormat()


	private static	$possibleDateFormatCharacters = 'eymdHs';

	/**
	 * Is a given number format code a date/time?
	 *
	 * @param	 string	$pFormatCode
	 * @return	 boolean
	 */
	public static function isDateTimeFormatCode($pFormatCode = '') {
		if (strtolower($pFormatCode) === strtolower(PHPExcel_Style_NumberFormat::FORMAT_GENERAL))
            //	"General" contains an epoch letter 'e', so we trap for it explicitly here (case-insensitive check)
			return FALSE;
        if (preg_match('/[0#]E[+-]0/i', $pFormatCode))
			//	Scientific format
			return FALSE;
		// Switch on formatcode
		switch ($pFormatCode) {
			//	Explicitly defined date formats
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_MYMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME1:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME6:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME7:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME8:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX16:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX22:
				return TRUE;
		}

		//	Typically number, currency or accounting (or occasionally fraction) formats
		if ((substr($pFormatCode,0,1) == '_') || (substr($pFormatCode,0,2) == '0 ')) {
			return FALSE;
		}
		// Try checking for any of the date formatting characters that don't appear within square braces
		if (preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i',$pFormatCode)) {
			//	We might also have a format mask containing quoted strings...
			//		we don't want to test for any of our characters within the quoted blocks
			if (strpos($pFormatCode,'"') !== FALSE) {
				$segMatcher = FALSE;
				foreach(explode('"',$pFormatCode) as $subVal) {
					//	Only test in alternate array entries (the non-quoted blocks)
					if (($segMatcher = !$segMatcher) &&
						(preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i',$subVal))) {
						return TRUE;
					}
				}
				return FALSE;
			}
			return TRUE;
		}

		// No date...
		return FALSE;
	}	//	function isDateTimeFormatCode()


	/**
	 * Convert a date/time string to Excel time
	 *
	 * @param	string	$dateValue		Examples: '2009-12-31', '2009-12-31 15:59', '2009-12-31 15:59:10'
	 * @return	float|FALSE		Excel date/time serial value
	 */
	public static function stringToExcel($dateValue = '') {
		if (strlen($dateValue) < 2)
			return FALSE;
		if (!preg_match('/^(\d{1,4}[ \.\/\-][A-Z]{3,9}([ \.\/\-]\d{1,4})?|[A-Z]{3,9}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?|\d{1,4}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?)( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu', $dateValue))
			return FALSE;

		$dateValueNew = PHPExcel_Calculation_DateTime::DATEVALUE($dateValue);

		if ($dateValueNew === PHPExcel_Calculation_Functions::VALUE()) {
			return FALSE;
		} else {
			if (strpos($dateValue, ':') !== FALSE) {
				$timeValue = PHPExcel_Calculation_DateTime::TIMEVALUE($dateValue);
				if ($timeValue === PHPExcel_Calculation_Functions::VALUE()) {
					return FALSE;
				}
				$dateValueNew += $timeValue;
			}
			return $dateValueNew;
		}


	}

    public static function monthStringToNumber($month) {
        $monthIndex = 1;
        foreach(self::$_monthNames as $shortMonthName => $longMonthName) {
            if (($month === $longMonthName) || ($month === $shortMonthName)) {
                return $monthIndex;
            }
            ++$monthIndex;
        }
        return $month;
    }

    public static function dayStringToNumber($day) {
		$strippedDayValue = (str_replace(self::$_numberSuffixes,'',$day));
		if (is_numeric($strippedDayValue)) {
		    return $strippedDayValue;
		}
		return $day;
    }

}
