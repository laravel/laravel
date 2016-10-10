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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.8.0, 2014-03-02
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/**
 * PHPExcel_Calculation_DateTime
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_DateTime {

	/**
	 * Identify if a year is a leap year or not
	 *
	 * @param	integer	$year	The year to test
	 * @return	boolean			TRUE if the year is a leap year, otherwise FALSE
	 */
	public static function _isLeapYear($year) {
		return ((($year % 4) == 0) && (($year % 100) != 0) || (($year % 400) == 0));
	}	//	function _isLeapYear()


	/**
	 * Return the number of days between two dates based on a 360 day calendar
	 *
	 * @param	integer	$startDay		Day of month of the start date
	 * @param	integer	$startMonth		Month of the start date
	 * @param	integer	$startYear		Year of the start date
	 * @param	integer	$endDay			Day of month of the start date
	 * @param	integer	$endMonth		Month of the start date
	 * @param	integer	$endYear		Year of the start date
	 * @param	boolean $methodUS		Whether to use the US method or the European method of calculation
	 * @return	integer	Number of days between the start date and the end date
	 */
	private static function _dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $methodUS) {
		if ($startDay == 31) {
			--$startDay;
		} elseif ($methodUS && ($startMonth == 2 && ($startDay == 29 || ($startDay == 28 && !self::_isLeapYear($startYear))))) {
			$startDay = 30;
		}
		if ($endDay == 31) {
			if ($methodUS && $startDay != 30) {
				$endDay = 1;
				if ($endMonth == 12) {
					++$endYear;
					$endMonth = 1;
				} else {
					++$endMonth;
				}
			} else {
				$endDay = 30;
			}
		}

		return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
	}	//	function _dateDiff360()


	/**
	 * _getDateValue
	 *
	 * @param	string	$dateValue
	 * @return	mixed	Excel date/time serial value, or string if error
	 */
	public static function _getDateValue($dateValue) {
		if (!is_numeric($dateValue)) {
			if ((is_string($dateValue)) &&
				(PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
			if ((is_object($dateValue)) && ($dateValue instanceof DateTime)) {
				$dateValue = PHPExcel_Shared_Date::PHPToExcel($dateValue);
			} else {
				$saveReturnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
				PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
				$dateValue = self::DATEVALUE($dateValue);
				PHPExcel_Calculation_Functions::setReturnDateType($saveReturnDateType);
			}
		}
		return $dateValue;
	}	//	function _getDateValue()


	/**
	 * _getTimeValue
	 *
	 * @param	string	$timeValue
	 * @return	mixed	Excel date/time serial value, or string if error
	 */
	private static function _getTimeValue($timeValue) {
		$saveReturnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$timeValue = self::TIMEVALUE($timeValue);
		PHPExcel_Calculation_Functions::setReturnDateType($saveReturnDateType);
		return $timeValue;
	}	//	function _getTimeValue()


	private static function _adjustDateByMonths($dateValue = 0, $adjustmentMonths = 0) {
		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
		$oMonth = (int) $PHPDateObject->format('m');
		$oYear = (int) $PHPDateObject->format('Y');

		$adjustmentMonthsString = (string) $adjustmentMonths;
		if ($adjustmentMonths > 0) {
			$adjustmentMonthsString = '+'.$adjustmentMonths;
		}
		if ($adjustmentMonths != 0) {
			$PHPDateObject->modify($adjustmentMonthsString.' months');
		}
		$nMonth = (int) $PHPDateObject->format('m');
		$nYear = (int) $PHPDateObject->format('Y');

		$monthDiff = ($nMonth - $oMonth) + (($nYear - $oYear) * 12);
		if ($monthDiff != $adjustmentMonths) {
			$adjustDays = (int) $PHPDateObject->format('d');
			$adjustDaysString = '-'.$adjustDays.' days';
			$PHPDateObject->modify($adjustDaysString);
		}
		return $PHPDateObject;
	}	//	function _adjustDateByMonths()


	/**
	 * DATETIMENOW
	 *
	 * Returns the current date and time.
	 * The NOW function is useful when you need to display the current date and time on a worksheet or
	 * calculate a value based on the current date and time, and have that value updated each time you
	 * open the worksheet.
	 *
	 * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
	 * and time format of your regional settings. PHPExcel does not change cell formatting in this way.
	 *
	 * Excel Function:
	 *		NOW()
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATETIMENOW() {
		$saveTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$retValue = False;
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					$retValue = (float) PHPExcel_Shared_Date::PHPToExcel(time());
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					$retValue = (integer) time();
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					$retValue = new DateTime();
					break;
		}
		date_default_timezone_set($saveTimeZone);

		return $retValue;
	}	//	function DATETIMENOW()


	/**
	 * DATENOW
	 *
	 * Returns the current date.
	 * The NOW function is useful when you need to display the current date and time on a worksheet or
	 * calculate a value based on the current date and time, and have that value updated each time you
	 * open the worksheet.
	 *
	 * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
	 * and time format of your regional settings. PHPExcel does not change cell formatting in this way.
	 *
	 * Excel Function:
	 *		TODAY()
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATENOW() {
		$saveTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$retValue = False;
		$excelDateTime = floor(PHPExcel_Shared_Date::PHPToExcel(time()));
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					$retValue = (float) $excelDateTime;
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					$retValue = (integer) PHPExcel_Shared_Date::ExcelToPHP($excelDateTime);
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					$retValue = PHPExcel_Shared_Date::ExcelToPHPObject($excelDateTime);
					break;
		}
		date_default_timezone_set($saveTimeZone);

		return $retValue;
	}	//	function DATENOW()


	/**
	 * DATE
	 *
	 * The DATE function returns a value that represents a particular date.
	 *
	 * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
	 * format of your regional settings. PHPExcel does not change cell formatting in this way.
	 *
	 * Excel Function:
	 *		DATE(year,month,day)
	 *
	 * PHPExcel is a lot more forgiving than MS Excel when passing non numeric values to this function.
	 * A Month name or abbreviation (English only at this point) such as 'January' or 'Jan' will still be accepted,
	 *     as will a day value with a suffix (e.g. '21st' rather than simply 21); again only English language.
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	integer		$year	The value of the year argument can include one to four digits.
	 *								Excel interprets the year argument according to the configured
	 *								date system: 1900 or 1904.
	 *								If year is between 0 (zero) and 1899 (inclusive), Excel adds that
	 *								value to 1900 to calculate the year. For example, DATE(108,1,2)
	 *								returns January 2, 2008 (1900+108).
	 *								If year is between 1900 and 9999 (inclusive), Excel uses that
	 *								value as the year. For example, DATE(2008,1,2) returns January 2,
	 *								2008.
	 *								If year is less than 0 or is 10000 or greater, Excel returns the
	 *								#NUM! error value.
	 * @param	integer		$month	A positive or negative integer representing the month of the year
	 *								from 1 to 12 (January to December).
	 *								If month is greater than 12, month adds that number of months to
	 *								the first month in the year specified. For example, DATE(2008,14,2)
	 *								returns the serial number representing February 2, 2009.
	 *								If month is less than 1, month subtracts the magnitude of that
	 *								number of months, plus 1, from the first month in the year
	 *								specified. For example, DATE(2008,-3,2) returns the serial number
	 *								representing September 2, 2007.
	 * @param	integer		$day	A positive or negative integer representing the day of the month
	 *								from 1 to 31.
	 *								If day is greater than the number of days in the month specified,
	 *								day adds that number of days to the first day in the month. For
	 *								example, DATE(2008,1,35) returns the serial number representing
	 *								February 4, 2008.
	 *								If day is less than 1, day subtracts the magnitude that number of
	 *								days, plus one, from the first day of the month specified. For
	 *								example, DATE(2008,1,-15) returns the serial number representing
	 *								December 16, 2007.
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATE($year = 0, $month = 1, $day = 1) {
		$year	= PHPExcel_Calculation_Functions::flattenSingleValue($year);
		$month	= PHPExcel_Calculation_Functions::flattenSingleValue($month);
		$day	= PHPExcel_Calculation_Functions::flattenSingleValue($day);

		if (($month !== NULL) && (!is_numeric($month))) {
            $month = PHPExcel_Shared_Date::monthStringToNumber($month);
		}

		if (($day !== NULL) && (!is_numeric($day))) {
            $day = PHPExcel_Shared_Date::dayStringToNumber($day);
		}

		$year	= ($year !== NULL)	? PHPExcel_Shared_String::testStringAsNumeric($year) : 0;
		$month	= ($month !== NULL)	? PHPExcel_Shared_String::testStringAsNumeric($month) : 0;
		$day	= ($day !== NULL)	? PHPExcel_Shared_String::testStringAsNumeric($day) : 0;
		if ((!is_numeric($year)) ||
			(!is_numeric($month)) ||
			(!is_numeric($day))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$year	= (integer) $year;
		$month	= (integer) $month;
		$day	= (integer) $day;

		$baseYear = PHPExcel_Shared_Date::getExcelCalendar();
		// Validate parameters
		if ($year < ($baseYear-1900)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ((($baseYear-1900) != 0) && ($year < $baseYear) && ($year >= 1900)) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		if (($year < $baseYear) && ($year >= ($baseYear-1900))) {
			$year += 1900;
		}

		if ($month < 1) {
			//	Handle year/month adjustment if month < 1
			--$month;
			$year += ceil($month / 12) - 1;
			$month = 13 - abs($month % 12);
		} elseif ($month > 12) {
			//	Handle year/month adjustment if month > 12
			$year += floor($month / 12);
			$month = ($month % 12);
		}

		// Re-validate the year parameter after adjustments
		if (($year < $baseYear) || ($year >= 10000)) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year, $month, $day);
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) $excelDateValue;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP($excelDateValue);
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return PHPExcel_Shared_Date::ExcelToPHPObject($excelDateValue);
		}
	}	//	function DATE()


	/**
	 * TIME
	 *
	 * The TIME function returns a value that represents a particular time.
	 *
	 * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the time
	 * format of your regional settings. PHPExcel does not change cell formatting in this way.
	 *
	 * Excel Function:
	 *		TIME(hour,minute,second)
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	integer		$hour		A number from 0 (zero) to 32767 representing the hour.
	 *									Any value greater than 23 will be divided by 24 and the remainder
	 *									will be treated as the hour value. For example, TIME(27,0,0) =
	 *									TIME(3,0,0) = .125 or 3:00 AM.
	 * @param	integer		$minute		A number from 0 to 32767 representing the minute.
	 *									Any value greater than 59 will be converted to hours and minutes.
	 *									For example, TIME(0,750,0) = TIME(12,30,0) = .520833 or 12:30 PM.
	 * @param	integer		$second		A number from 0 to 32767 representing the second.
	 *									Any value greater than 59 will be converted to hours, minutes,
	 *									and seconds. For example, TIME(0,0,2000) = TIME(0,33,22) = .023148
	 *									or 12:33:20 AM
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function TIME($hour = 0, $minute = 0, $second = 0) {
		$hour	= PHPExcel_Calculation_Functions::flattenSingleValue($hour);
		$minute	= PHPExcel_Calculation_Functions::flattenSingleValue($minute);
		$second	= PHPExcel_Calculation_Functions::flattenSingleValue($second);

		if ($hour == '') { $hour = 0; }
		if ($minute == '') { $minute = 0; }
		if ($second == '') { $second = 0; }

		if ((!is_numeric($hour)) || (!is_numeric($minute)) || (!is_numeric($second))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$hour	= (integer) $hour;
		$minute	= (integer) $minute;
		$second	= (integer) $second;

		if ($second < 0) {
			$minute += floor($second / 60);
			$second = 60 - abs($second % 60);
			if ($second == 60) { $second = 0; }
		} elseif ($second >= 60) {
			$minute += floor($second / 60);
			$second = $second % 60;
		}
		if ($minute < 0) {
			$hour += floor($minute / 60);
			$minute = 60 - abs($minute % 60);
			if ($minute == 60) { $minute = 0; }
		} elseif ($minute >= 60) {
			$hour += floor($minute / 60);
			$minute = $minute % 60;
		}

		if ($hour > 23) {
			$hour = $hour % 24;
		} elseif ($hour < 0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					$date = 0;
					$calendar = PHPExcel_Shared_Date::getExcelCalendar();
					if ($calendar != PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900) {
						$date = 1;
					}
					return (float) PHPExcel_Shared_Date::FormattedPHPToExcel($calendar, 1, $date, $hour, $minute, $second);
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::FormattedPHPToExcel(1970, 1, 1, $hour, $minute, $second));	// -2147468400; //	-2147472000 + 3600
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					$dayAdjust = 0;
					if ($hour < 0) {
						$dayAdjust = floor($hour / 24);
						$hour = 24 - abs($hour % 24);
						if ($hour == 24) { $hour = 0; }
					} elseif ($hour >= 24) {
						$dayAdjust = floor($hour / 24);
						$hour = $hour % 24;
					}
					$phpDateObject = new DateTime('1900-01-01 '.$hour.':'.$minute.':'.$second);
					if ($dayAdjust != 0) {
						$phpDateObject->modify($dayAdjust.' days');
					}
					return $phpDateObject;
		}
	}	//	function TIME()


	/**
	 * DATEVALUE
	 *
	 * Returns a value that represents a particular date.
	 * Use DATEVALUE to convert a date represented by a text string to an Excel or PHP date/time stamp
	 * value.
	 *
	 * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
	 * format of your regional settings. PHPExcel does not change cell formatting in this way.
	 *
	 * Excel Function:
	 *		DATEVALUE(dateValue)
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	string	$dateValue		Text that represents a date in a Microsoft Excel date format.
	 *									For example, "1/30/2008" or "30-Jan-2008" are text strings within
	 *									quotation marks that represent dates. Using the default date
	 *									system in Excel for Windows, date_text must represent a date from
	 *									January 1, 1900, to December 31, 9999. Using the default date
	 *									system in Excel for the Macintosh, date_text must represent a date
	 *									from January 1, 1904, to December 31, 9999. DATEVALUE returns the
	 *									#VALUE! error value if date_text is out of this range.
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATEVALUE($dateValue = 1) {
		$dateValue = trim(PHPExcel_Calculation_Functions::flattenSingleValue($dateValue),'"');
		//	Strip any ordinals because they're allowed in Excel (English only)
		$dateValue = preg_replace('/(\d)(st|nd|rd|th)([ -\/])/Ui','$1$3',$dateValue);
		//	Convert separators (/ . or space) to hyphens (should also handle dot used for ordinals in some countries, e.g. Denmark, Germany)
		$dateValue	= str_replace(array('/','.','-','  '),array(' ',' ',' ',' '),$dateValue);

		$yearFound = false;
		$t1 = explode(' ',$dateValue);
		foreach($t1 as &$t) {
			if ((is_numeric($t)) && ($t > 31)) {
				if ($yearFound) {
					return PHPExcel_Calculation_Functions::VALUE();
				} else {
					if ($t < 100) { $t += 1900; }
					$yearFound = true;
				}
			}
		}
		if ((count($t1) == 1) && (strpos($t,':') != false)) {
			//	We've been fed a time value without any date
			return 0.0;
		} elseif (count($t1) == 2) {
			//	We only have two parts of the date: either day/month or month/year
			if ($yearFound) {
				array_unshift($t1,1);
			} else {
				array_push($t1,date('Y'));
			}
		}
		unset($t);
		$dateValue = implode(' ',$t1);

		$PHPDateArray = date_parse($dateValue);
		if (($PHPDateArray === False) || ($PHPDateArray['error_count'] > 0)) {
			$testVal1 = strtok($dateValue,'- ');
			if ($testVal1 !== False) {
				$testVal2 = strtok('- ');
				if ($testVal2 !== False) {
					$testVal3 = strtok('- ');
					if ($testVal3 === False) {
						$testVal3 = strftime('%Y');
					}
				} else {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
			$PHPDateArray = date_parse($testVal1.'-'.$testVal2.'-'.$testVal3);
			if (($PHPDateArray === False) || ($PHPDateArray['error_count'] > 0)) {
				$PHPDateArray = date_parse($testVal2.'-'.$testVal1.'-'.$testVal3);
				if (($PHPDateArray === False) || ($PHPDateArray['error_count'] > 0)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
		}

		if (($PHPDateArray !== False) && ($PHPDateArray['error_count'] == 0)) {
			// Execute function
			if ($PHPDateArray['year'] == '')	{ $PHPDateArray['year'] = strftime('%Y'); }
			if ($PHPDateArray['year'] < 1900)
				return PHPExcel_Calculation_Functions::VALUE();
			if ($PHPDateArray['month'] == '')	{ $PHPDateArray['month'] = strftime('%m'); }
			if ($PHPDateArray['day'] == '')		{ $PHPDateArray['day'] = strftime('%d'); }
			$excelDateValue = floor(PHPExcel_Shared_Date::FormattedPHPToExcel($PHPDateArray['year'],$PHPDateArray['month'],$PHPDateArray['day'],$PHPDateArray['hour'],$PHPDateArray['minute'],$PHPDateArray['second']));

			switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
				case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
						return (float) $excelDateValue;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
						return (integer) PHPExcel_Shared_Date::ExcelToPHP($excelDateValue);
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
						return new DateTime($PHPDateArray['year'].'-'.$PHPDateArray['month'].'-'.$PHPDateArray['day'].' 00:00:00');
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function DATEVALUE()


	/**
	 * TIMEVALUE
	 *
	 * Returns a value that represents a particular time.
	 * Use TIMEVALUE to convert a time represented by a text string to an Excel or PHP date/time stamp
	 * value.
	 *
	 * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the time
	 * format of your regional settings. PHPExcel does not change cell formatting in this way.
	 *
	 * Excel Function:
	 *		TIMEVALUE(timeValue)
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	string	$timeValue		A text string that represents a time in any one of the Microsoft
	 *									Excel time formats; for example, "6:45 PM" and "18:45" text strings
	 *									within quotation marks that represent time.
	 *									Date information in time_text is ignored.
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function TIMEVALUE($timeValue) {
		$timeValue = trim(PHPExcel_Calculation_Functions::flattenSingleValue($timeValue),'"');
		$timeValue	= str_replace(array('/','.'),array('-','-'),$timeValue);

		$PHPDateArray = date_parse($timeValue);
		if (($PHPDateArray !== False) && ($PHPDateArray['error_count'] == 0)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel($PHPDateArray['year'],$PHPDateArray['month'],$PHPDateArray['day'],$PHPDateArray['hour'],$PHPDateArray['minute'],$PHPDateArray['second']);
			} else {
				$excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel(1900,1,1,$PHPDateArray['hour'],$PHPDateArray['minute'],$PHPDateArray['second']) - 1;
			}

			switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
				case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
						return (float) $excelDateValue;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
						return (integer) $phpDateValue = PHPExcel_Shared_Date::ExcelToPHP($excelDateValue+25569) - 3600;;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
						return new DateTime('1900-01-01 '.$PHPDateArray['hour'].':'.$PHPDateArray['minute'].':'.$PHPDateArray['second']);
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function TIMEVALUE()


	/**
	 * DATEDIF
	 *
	 * @param	mixed	$startDate		Excel date serial value, PHP date/time stamp, PHP DateTime object
	 *									or a standard date string
	 * @param	mixed	$endDate		Excel date serial value, PHP date/time stamp, PHP DateTime object
	 *									or a standard date string
	 * @param	string	$unit
	 * @return	integer	Interval between the dates
	 */
	public static function DATEDIF($startDate = 0, $endDate = 0, $unit = 'D') {
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
		$unit		= strtoupper(PHPExcel_Calculation_Functions::flattenSingleValue($unit));

		if (is_string($startDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($endDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Validate parameters
		if ($startDate >= $endDate) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$difference = $endDate - $startDate;

		$PHPStartDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($startDate);
		$startDays = $PHPStartDateObject->format('j');
		$startMonths = $PHPStartDateObject->format('n');
		$startYears = $PHPStartDateObject->format('Y');

		$PHPEndDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
		$endDays = $PHPEndDateObject->format('j');
		$endMonths = $PHPEndDateObject->format('n');
		$endYears = $PHPEndDateObject->format('Y');

		$retVal = PHPExcel_Calculation_Functions::NaN();
		switch ($unit) {
			case 'D':
				$retVal = intval($difference);
				break;
			case 'M':
				$retVal = intval($endMonths - $startMonths) + (intval($endYears - $startYears) * 12);
				//	We're only interested in full months
				if ($endDays < $startDays) {
					--$retVal;
				}
				break;
			case 'Y':
				$retVal = intval($endYears - $startYears);
				//	We're only interested in full months
				if ($endMonths < $startMonths) {
					--$retVal;
				} elseif (($endMonths == $startMonths) && ($endDays < $startDays)) {
					--$retVal;
				}
				break;
			case 'MD':
				if ($endDays < $startDays) {
					$retVal = $endDays;
					$PHPEndDateObject->modify('-'.$endDays.' days');
					$adjustDays = $PHPEndDateObject->format('j');
					if ($adjustDays > $startDays) {
						$retVal += ($adjustDays - $startDays);
					}
				} else {
					$retVal = $endDays - $startDays;
				}
				break;
			case 'YM':
				$retVal = intval($endMonths - $startMonths);
				if ($retVal < 0) $retVal = 12 + $retVal;
				//	We're only interested in full months
				if ($endDays < $startDays) {
					--$retVal;
				}
				break;
			case 'YD':
				$retVal = intval($difference);
				if ($endYears > $startYears) {
					while ($endYears > $startYears) {
						$PHPEndDateObject->modify('-1 year');
						$endYears = $PHPEndDateObject->format('Y');
					}
					$retVal = $PHPEndDateObject->format('z') - $PHPStartDateObject->format('z');
					if ($retVal < 0) { $retVal += 365; }
				}
				break;
			default:
				$retVal = PHPExcel_Calculation_Functions::NaN();
		}
		return $retVal;
	}	//	function DATEDIF()


	/**
	 * DAYS360
	 *
	 * Returns the number of days between two dates based on a 360-day year (twelve 30-day months),
	 * which is used in some accounting calculations. Use this function to help compute payments if
	 * your accounting system is based on twelve 30-day months.
	 *
	 * Excel Function:
	 *		DAYS360(startDate,endDate[,method])
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	mixed		$startDate		Excel date serial value (float), PHP date timestamp (integer),
	 *										PHP DateTime object, or a standard date string
	 * @param	mixed		$endDate		Excel date serial value (float), PHP date timestamp (integer),
	 *										PHP DateTime object, or a standard date string
	 * @param	boolean		$method			US or European Method
	 *										FALSE or omitted: U.S. (NASD) method. If the starting date is
	 *										the last day of a month, it becomes equal to the 30th of the
	 *										same month. If the ending date is the last day of a month and
	 *										the starting date is earlier than the 30th of a month, the
	 *										ending date becomes equal to the 1st of the next month;
	 *										otherwise the ending date becomes equal to the 30th of the
	 *										same month.
	 *										TRUE: European method. Starting dates and ending dates that
	 *										occur on the 31st of a month become equal to the 30th of the
	 *										same month.
	 * @return	integer		Number of days between start date and end date
	 */
	public static function DAYS360($startDate = 0, $endDate = 0, $method = false) {
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);

		if (is_string($startDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($endDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (!is_bool($method)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Execute function
		$PHPStartDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($startDate);
		$startDay = $PHPStartDateObject->format('j');
		$startMonth = $PHPStartDateObject->format('n');
		$startYear = $PHPStartDateObject->format('Y');

		$PHPEndDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
		$endDay = $PHPEndDateObject->format('j');
		$endMonth = $PHPEndDateObject->format('n');
		$endYear = $PHPEndDateObject->format('Y');

		return self::_dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, !$method);
	}	//	function DAYS360()


	/**
	 * YEARFRAC
	 *
	 * Calculates the fraction of the year represented by the number of whole days between two dates
	 * (the start_date and the end_date).
	 * Use the YEARFRAC worksheet function to identify the proportion of a whole year's benefits or
	 * obligations to assign to a specific term.
	 *
	 * Excel Function:
	 *		YEARFRAC(startDate,endDate[,method])
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	mixed	$startDate		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @param	mixed	$endDate		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @param	integer	$method			Method used for the calculation
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float	fraction of the year
	 */
	public static function YEARFRAC($startDate = 0, $endDate = 0, $method = 0) {
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
		$method		= PHPExcel_Calculation_Functions::flattenSingleValue($method);

		if (is_string($startDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($endDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (((is_numeric($method)) && (!is_string($method))) || ($method == '')) {
			switch($method) {
				case 0	:
					return self::DAYS360($startDate,$endDate) / 360;
				case 1	:
					$days = self::DATEDIF($startDate,$endDate);
					$startYear = self::YEAR($startDate);
					$endYear = self::YEAR($endDate);
					$years = $endYear - $startYear + 1;
					$leapDays = 0;
					if ($years == 1) {
						if (self::_isLeapYear($endYear)) {
							$startMonth = self::MONTHOFYEAR($startDate);
							$endMonth = self::MONTHOFYEAR($endDate);
							$endDay = self::DAYOFMONTH($endDate);
							if (($startMonth < 3) ||
								(($endMonth * 100 + $endDay) >= (2 * 100 + 29))) {
				     			$leapDays += 1;
							}
						}
					} else {
						for($year = $startYear; $year <= $endYear; ++$year) {
							if ($year == $startYear) {
								$startMonth = self::MONTHOFYEAR($startDate);
								$startDay = self::DAYOFMONTH($startDate);
								if ($startMonth < 3) {
									$leapDays += (self::_isLeapYear($year)) ? 1 : 0;
								}
							} elseif($year == $endYear) {
								$endMonth = self::MONTHOFYEAR($endDate);
								$endDay = self::DAYOFMONTH($endDate);
								if (($endMonth * 100 + $endDay) >= (2 * 100 + 29)) {
									$leapDays += (self::_isLeapYear($year)) ? 1 : 0;
								}
							} else {
								$leapDays += (self::_isLeapYear($year)) ? 1 : 0;
							}
						}
						if ($years == 2) {
							if (($leapDays == 0) && (self::_isLeapYear($startYear)) && ($days > 365)) {
								$leapDays = 1;
							} elseif ($days < 366) {
								$years = 1;
							}
						}
						$leapDays /= $years;
					}
					return $days / (365 + $leapDays);
				case 2	:
					return self::DATEDIF($startDate,$endDate) / 360;
				case 3	:
					return self::DATEDIF($startDate,$endDate) / 365;
				case 4	:
					return self::DAYS360($startDate,$endDate,True) / 360;
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function YEARFRAC()


	/**
	 * NETWORKDAYS
	 *
	 * Returns the number of whole working days between start_date and end_date. Working days
	 * exclude weekends and any dates identified in holidays.
	 * Use NETWORKDAYS to calculate employee benefits that accrue based on the number of days
	 * worked during a specific term.
	 *
	 * Excel Function:
	 *		NETWORKDAYS(startDate,endDate[,holidays[,holiday[,...]]])
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	mixed			$startDate		Excel date serial value (float), PHP date timestamp (integer),
	 *											PHP DateTime object, or a standard date string
	 * @param	mixed			$endDate		Excel date serial value (float), PHP date timestamp (integer),
	 *											PHP DateTime object, or a standard date string
	 * @param	mixed			$holidays,...	Optional series of Excel date serial value (float), PHP date
	 *											timestamp (integer), PHP DateTime object, or a standard date
	 *											strings that will be excluded from the working calendar, such
	 *											as state and federal holidays and floating holidays.
	 * @return	integer			Interval between the dates
	 */
	public static function NETWORKDAYS($startDate,$endDate) {
		//	Retrieve the mandatory start and end date that are referenced in the function definition
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
		//	Flush the mandatory start and end date that are referenced in the function definition, and get the optional days
		$dateArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		array_shift($dateArgs);
		array_shift($dateArgs);

		//	Validate the start and end dates
		if (is_string($startDate = $sDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$startDate = (float) floor($startDate);
		if (is_string($endDate = $eDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$endDate = (float) floor($endDate);

		if ($sDate > $eDate) {
			$startDate = $eDate;
			$endDate = $sDate;
		}

		// Execute function
		$startDoW = 6 - self::DAYOFWEEK($startDate,2);
		if ($startDoW < 0) { $startDoW = 0; }
		$endDoW = self::DAYOFWEEK($endDate,2);
		if ($endDoW >= 6) { $endDoW = 0; }

		$wholeWeekDays = floor(($endDate - $startDate) / 7) * 5;
		$partWeekDays = $endDoW + $startDoW;
		if ($partWeekDays > 5) {
			$partWeekDays -= 5;
		}

		//	Test any extra holiday parameters
		$holidayCountedArray = array();
		foreach ($dateArgs as $holidayDate) {
			if (is_string($holidayDate = self::_getDateValue($holidayDate))) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
			if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
				if ((self::DAYOFWEEK($holidayDate,2) < 6) && (!in_array($holidayDate,$holidayCountedArray))) {
					--$partWeekDays;
					$holidayCountedArray[] = $holidayDate;
				}
			}
		}

		if ($sDate > $eDate) {
			return 0 - ($wholeWeekDays + $partWeekDays);
		}
		return $wholeWeekDays + $partWeekDays;
	}	//	function NETWORKDAYS()


	/**
	 * WORKDAY
	 *
	 * Returns the date that is the indicated number of working days before or after a date (the
	 * starting date). Working days exclude weekends and any dates identified as holidays.
	 * Use WORKDAY to exclude weekends or holidays when you calculate invoice due dates, expected
	 * delivery times, or the number of days of work performed.
	 *
	 * Excel Function:
	 *		WORKDAY(startDate,endDays[,holidays[,holiday[,...]]])
	 *
	 * @access	public
	 * @category Date/Time Functions
	 * @param	mixed		$startDate		Excel date serial value (float), PHP date timestamp (integer),
	 *										PHP DateTime object, or a standard date string
	 * @param	integer		$endDays		The number of nonweekend and nonholiday days before or after
	 *										startDate. A positive value for days yields a future date; a
	 *										negative value yields a past date.
	 * @param	mixed		$holidays,...	Optional series of Excel date serial value (float), PHP date
	 *										timestamp (integer), PHP DateTime object, or a standard date
	 *										strings that will be excluded from the working calendar, such
	 *										as state and federal holidays and floating holidays.
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function WORKDAY($startDate,$endDays) {
		//	Retrieve the mandatory start date and days that are referenced in the function definition
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDays	= PHPExcel_Calculation_Functions::flattenSingleValue($endDays);
		//	Flush the mandatory start date and days that are referenced in the function definition, and get the optional days
		$dateArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		array_shift($dateArgs);
		array_shift($dateArgs);

		if ((is_string($startDate = self::_getDateValue($startDate))) || (!is_numeric($endDays))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$startDate = (float) floor($startDate);
		$endDays = (int) floor($endDays);
		//	If endDays is 0, we always return startDate
		if ($endDays == 0) { return $startDate; }

		$decrementing = ($endDays < 0) ? True : False;

		//	Adjust the start date if it falls over a weekend

		$startDoW = self::DAYOFWEEK($startDate,3);
		if (self::DAYOFWEEK($startDate,3) >= 5) {
			$startDate += ($decrementing) ? -$startDoW + 4: 7 - $startDoW;
			($decrementing) ? $endDays++ : $endDays--;
		}

		//	Add endDays
		$endDate = (float) $startDate + (intval($endDays / 5) * 7) + ($endDays % 5);

		//	Adjust the calculated end date if it falls over a weekend
		$endDoW = self::DAYOFWEEK($endDate,3);
		if ($endDoW >= 5) {
			$endDate += ($decrementing) ? -$endDoW + 4: 7 - $endDoW;
		}

		//	Test any extra holiday parameters
		if (!empty($dateArgs)) {
			$holidayCountedArray = $holidayDates = array();
			foreach ($dateArgs as $holidayDate) {
				if (($holidayDate !== NULL) && (trim($holidayDate) > '')) {
					if (is_string($holidayDate = self::_getDateValue($holidayDate))) {
						return PHPExcel_Calculation_Functions::VALUE();
					}
					if (self::DAYOFWEEK($holidayDate,3) < 5) {
						$holidayDates[] = $holidayDate;
					}
				}
			}
			if ($decrementing) {
				rsort($holidayDates, SORT_NUMERIC);
			} else {
				sort($holidayDates, SORT_NUMERIC);
			}
			foreach ($holidayDates as $holidayDate) {
				if ($decrementing) {
					if (($holidayDate <= $startDate) && ($holidayDate >= $endDate)) {
						if (!in_array($holidayDate,$holidayCountedArray)) {
							--$endDate;
							$holidayCountedArray[] = $holidayDate;
						}
					}
				} else {
					if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
						if (!in_array($holidayDate,$holidayCountedArray)) {
							++$endDate;
							$holidayCountedArray[] = $holidayDate;
						}
					}
				}
				//	Adjust the calculated end date if it falls over a weekend
				$endDoW = self::DAYOFWEEK($endDate,3);
				if ($endDoW >= 5) {
					$endDate += ($decrementing) ? -$endDoW + 4: 7 - $endDoW;
				}

			}
		}

		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) $endDate;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP($endDate);
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
		}
	}	//	function WORKDAY()


	/**
	 * DAYOFMONTH
	 *
	 * Returns the day of the month, for a specified date. The day is given as an integer
	 * ranging from 1 to 31.
	 *
	 * Excel Function:
	 *		DAY(dateValue)
	 *
	 * @param	mixed	$dateValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @return	int		Day of the month
	 */
	public static function DAYOFMONTH($dateValue = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue == 0.0) {
			return 0;
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);

		return (int) $PHPDateObject->format('j');
	}	//	function DAYOFMONTH()


	/**
	 * DAYOFWEEK
	 *
	 * Returns the day of the week for a specified date. The day is given as an integer
	 * ranging from 0 to 7 (dependent on the requested style).
	 *
	 * Excel Function:
	 *		WEEKDAY(dateValue[,style])
	 *
	 * @param	mixed	$dateValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @param	int		$style			A number that determines the type of return value
	 *										1 or omitted	Numbers 1 (Sunday) through 7 (Saturday).
	 *										2				Numbers 1 (Monday) through 7 (Sunday).
	 *										3				Numbers 0 (Monday) through 6 (Sunday).
	 * @return	int		Day of the week value
	 */
	public static function DAYOFWEEK($dateValue = 1, $style = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$style		= PHPExcel_Calculation_Functions::flattenSingleValue($style);

		if (!is_numeric($style)) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif (($style < 1) || ($style > 3)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$style = floor($style);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
		$DoW = $PHPDateObject->format('w');

		$firstDay = 1;
		switch ($style) {
			case 1: ++$DoW;
					break;
			case 2: if ($DoW == 0) { $DoW = 7; }
					break;
			case 3: if ($DoW == 0) { $DoW = 7; }
					$firstDay = 0;
					--$DoW;
					break;
		}
		if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL) {
			//	Test for Excel's 1900 leap year, and introduce the error as required
			if (($PHPDateObject->format('Y') == 1900) && ($PHPDateObject->format('n') <= 2)) {
				--$DoW;
				if ($DoW < $firstDay) {
					$DoW += 7;
				}
			}
		}

		return (int) $DoW;
	}	//	function DAYOFWEEK()


	/**
	 * WEEKOFYEAR
	 *
	 * Returns the week of the year for a specified date.
	 * The WEEKNUM function considers the week containing January 1 to be the first week of the year.
	 * However, there is a European standard that defines the first week as the one with the majority
	 * of days (four or more) falling in the new year. This means that for years in which there are
	 * three days or less in the first week of January, the WEEKNUM function returns week numbers
	 * that are incorrect according to the European standard.
	 *
	 * Excel Function:
	 *		WEEKNUM(dateValue[,style])
	 *
	 * @param	mixed	$dateValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @param	boolean	$method			Week begins on Sunday or Monday
	 *										1 or omitted	Week begins on Sunday.
	 *										2				Week begins on Monday.
	 * @return	int		Week Number
	 */
	public static function WEEKOFYEAR($dateValue = 1, $method = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$method		= PHPExcel_Calculation_Functions::flattenSingleValue($method);

		if (!is_numeric($method)) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif (($method < 1) || ($method > 2)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$method = floor($method);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
		$dayOfYear = $PHPDateObject->format('z');
		$dow = $PHPDateObject->format('w');
		$PHPDateObject->modify('-'.$dayOfYear.' days');
		$dow = $PHPDateObject->format('w');
		$daysInFirstWeek = 7 - (($dow + (2 - $method)) % 7);
		$dayOfYear -= $daysInFirstWeek;
		$weekOfYear = ceil($dayOfYear / 7) + 1;

		return (int) $weekOfYear;
	}	//	function WEEKOFYEAR()


	/**
	 * MONTHOFYEAR
	 *
	 * Returns the month of a date represented by a serial number.
	 * The month is given as an integer, ranging from 1 (January) to 12 (December).
	 *
	 * Excel Function:
	 *		MONTH(dateValue)
	 *
	 * @param	mixed	$dateValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @return	int		Month of the year
	 */
	public static function MONTHOFYEAR($dateValue = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);

		return (int) $PHPDateObject->format('n');
	}	//	function MONTHOFYEAR()


	/**
	 * YEAR
	 *
	 * Returns the year corresponding to a date.
	 * The year is returned as an integer in the range 1900-9999.
	 *
	 * Excel Function:
	 *		YEAR(dateValue)
	 *
	 * @param	mixed	$dateValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard date string
	 * @return	int		Year
	 */
	public static function YEAR($dateValue = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);

		return (int) $PHPDateObject->format('Y');
	}	//	function YEAR()


	/**
	 * HOUROFDAY
	 *
	 * Returns the hour of a time value.
	 * The hour is given as an integer, ranging from 0 (12:00 A.M.) to 23 (11:00 P.M.).
	 *
	 * Excel Function:
	 *		HOUR(timeValue)
	 *
	 * @param	mixed	$timeValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard time string
	 * @return	int		Hour
	 */
	public static function HOUROFDAY($timeValue = 0) {
		$timeValue	= PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);

		if (!is_numeric($timeValue)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$testVal = strtok($timeValue,'/-: ');
				if (strlen($testVal) < strlen($timeValue)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
			$timeValue = self::_getTimeValue($timeValue);
			if (is_string($timeValue)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		// Execute function
		if ($timeValue >= 1) {
			$timeValue = fmod($timeValue,1);
		} elseif ($timeValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);

		return (int) gmdate('G',$timeValue);
	}	//	function HOUROFDAY()


	/**
	 * MINUTEOFHOUR
	 *
	 * Returns the minutes of a time value.
	 * The minute is given as an integer, ranging from 0 to 59.
	 *
	 * Excel Function:
	 *		MINUTE(timeValue)
	 *
	 * @param	mixed	$timeValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard time string
	 * @return	int		Minute
	 */
	public static function MINUTEOFHOUR($timeValue = 0) {
		$timeValue = $timeTester	= PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);

		if (!is_numeric($timeValue)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$testVal = strtok($timeValue,'/-: ');
				if (strlen($testVal) < strlen($timeValue)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
			$timeValue = self::_getTimeValue($timeValue);
			if (is_string($timeValue)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		// Execute function
		if ($timeValue >= 1) {
			$timeValue = fmod($timeValue,1);
		} elseif ($timeValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);

		return (int) gmdate('i',$timeValue);
	}	//	function MINUTEOFHOUR()


	/**
	 * SECONDOFMINUTE
	 *
	 * Returns the seconds of a time value.
	 * The second is given as an integer in the range 0 (zero) to 59.
	 *
	 * Excel Function:
	 *		SECOND(timeValue)
	 *
	 * @param	mixed	$timeValue		Excel date serial value (float), PHP date timestamp (integer),
	 *									PHP DateTime object, or a standard time string
	 * @return	int		Second
	 */
	public static function SECONDOFMINUTE($timeValue = 0) {
		$timeValue	= PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);

		if (!is_numeric($timeValue)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$testVal = strtok($timeValue,'/-: ');
				if (strlen($testVal) < strlen($timeValue)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
			$timeValue = self::_getTimeValue($timeValue);
			if (is_string($timeValue)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		// Execute function
		if ($timeValue >= 1) {
			$timeValue = fmod($timeValue,1);
		} elseif ($timeValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);

		return (int) gmdate('s',$timeValue);
	}	//	function SECONDOFMINUTE()


	/**
	 * EDATE
	 *
	 * Returns the serial number that represents the date that is the indicated number of months
	 * before or after a specified date (the start_date).
	 * Use EDATE to calculate maturity dates or due dates that fall on the same day of the month
	 * as the date of issue.
	 *
	 * Excel Function:
	 *		EDATE(dateValue,adjustmentMonths)
	 *
	 * @param	mixed	$dateValue			Excel date serial value (float), PHP date timestamp (integer),
	 *										PHP DateTime object, or a standard date string
	 * @param	int		$adjustmentMonths	The number of months before or after start_date.
	 *										A positive value for months yields a future date;
	 *										a negative value yields a past date.
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function EDATE($dateValue = 1, $adjustmentMonths = 0) {
		$dateValue			= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$adjustmentMonths	= PHPExcel_Calculation_Functions::flattenSingleValue($adjustmentMonths);

		if (!is_numeric($adjustmentMonths)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$adjustmentMonths = floor($adjustmentMonths);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Execute function
		$PHPDateObject = self::_adjustDateByMonths($dateValue,$adjustmentMonths);

		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) PHPExcel_Shared_Date::PHPToExcel($PHPDateObject);
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::PHPToExcel($PHPDateObject));
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return $PHPDateObject;
		}
	}	//	function EDATE()


	/**
	 * EOMONTH
	 *
	 * Returns the date value for the last day of the month that is the indicated number of months
	 * before or after start_date.
	 * Use EOMONTH to calculate maturity dates or due dates that fall on the last day of the month.
	 *
	 * Excel Function:
	 *		EOMONTH(dateValue,adjustmentMonths)
	 *
	 * @param	mixed	$dateValue			Excel date serial value (float), PHP date timestamp (integer),
	 *										PHP DateTime object, or a standard date string
	 * @param	int		$adjustmentMonths	The number of months before or after start_date.
	 *										A positive value for months yields a future date;
	 *										a negative value yields a past date.
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function EOMONTH($dateValue = 1, $adjustmentMonths = 0) {
		$dateValue			= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$adjustmentMonths	= PHPExcel_Calculation_Functions::flattenSingleValue($adjustmentMonths);

		if (!is_numeric($adjustmentMonths)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$adjustmentMonths = floor($adjustmentMonths);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Execute function
		$PHPDateObject = self::_adjustDateByMonths($dateValue,$adjustmentMonths+1);
		$adjustDays = (int) $PHPDateObject->format('d');
		$adjustDaysString = '-'.$adjustDays.' days';
		$PHPDateObject->modify($adjustDaysString);

		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) PHPExcel_Shared_Date::PHPToExcel($PHPDateObject);
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::PHPToExcel($PHPDateObject));
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return $PHPDateObject;
		}
	}	//	function EOMONTH()

}	//	class PHPExcel_Calculation_DateTime

