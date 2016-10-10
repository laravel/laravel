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


/** FINANCIAL_MAX_ITERATIONS */
define('FINANCIAL_MAX_ITERATIONS', 128);

/** FINANCIAL_PRECISION */
define('FINANCIAL_PRECISION', 1.0e-08);


/**
 * PHPExcel_Calculation_Financial
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Financial {

	/**
	 * _lastDayOfMonth
	 *
	 * Returns a boolean TRUE/FALSE indicating if this date is the last date of the month
	 *
	 * @param	DateTime	$testDate	The date for testing
	 * @return	boolean
	 */
	private static function _lastDayOfMonth($testDate)
	{
		return ($testDate->format('d') == $testDate->format('t'));
	}	//	function _lastDayOfMonth()


	/**
	 * _firstDayOfMonth
	 *
	 * Returns a boolean TRUE/FALSE indicating if this date is the first date of the month
	 *
	 * @param	DateTime	$testDate	The date for testing
	 * @return	boolean
	 */
	private static function _firstDayOfMonth($testDate)
	{
		return ($testDate->format('d') == 1);
	}	//	function _firstDayOfMonth()


	private static function _coupFirstPeriodDate($settlement, $maturity, $frequency, $next)
	{
		$months = 12 / $frequency;

		$result = PHPExcel_Shared_Date::ExcelToPHPObject($maturity);
		$eom = self::_lastDayOfMonth($result);

		while ($settlement < PHPExcel_Shared_Date::PHPToExcel($result)) {
			$result->modify('-'.$months.' months');
		}
		if ($next) {
			$result->modify('+'.$months.' months');
		}

		if ($eom) {
			$result->modify('-1 day');
		}

		return PHPExcel_Shared_Date::PHPToExcel($result);
	}	//	function _coupFirstPeriodDate()


	private static function _validFrequency($frequency)
	{
		if (($frequency == 1) || ($frequency == 2) || ($frequency == 4)) {
			return true;
		}
		if ((PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) &&
			(($frequency == 6) || ($frequency == 12))) {
			return true;
		}
		return false;
	}	//	function _validFrequency()


	/**
	 * _daysPerYear
	 *
	 * Returns the number of days in a specified year, as defined by the "basis" value
	 *
	 * @param	integer		$year	The year against which we're testing
	 * @param   integer		$basis	The type of day count:
	 *									0 or omitted US (NASD)	360
	 *									1						Actual (365 or 366 in a leap year)
	 *									2						360
	 *									3						365
	 *									4						European 360
	 * @return	integer
	 */
	private static function _daysPerYear($year, $basis=0)
	{
		switch ($basis) {
			case 0 :
			case 2 :
			case 4 :
				$daysPerYear = 360;
				break;
			case 3 :
				$daysPerYear = 365;
				break;
			case 1 :
				$daysPerYear = (PHPExcel_Calculation_DateTime::_isLeapYear($year)) ? 366 : 365;
				break;
			default	:
				return PHPExcel_Calculation_Functions::NaN();
		}
		return $daysPerYear;
	}	//	function _daysPerYear()


	private static function _interestAndPrincipal($rate=0, $per=0, $nper=0, $pv=0, $fv=0, $type=0)
	{
		$pmt = self::PMT($rate, $nper, $pv, $fv, $type);
		$capital = $pv;
		for ($i = 1; $i<= $per; ++$i) {
			$interest = ($type && $i == 1) ? 0 : -$capital * $rate;
			$principal = $pmt - $interest;
			$capital += $principal;
		}
		return array($interest, $principal);
	}	//	function _interestAndPrincipal()


	/**
	 * ACCRINT
	 *
	 * Returns the accrued interest for a security that pays periodic interest.
	 *
	 * Excel Function:
	 *		ACCRINT(issue,firstinterest,settlement,rate,par,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	$issue			The security's issue date.
	 * @param	mixed	$firstinterest	The security's first interest date.
	 * @param	mixed	$settlement		The security's settlement date.
	 *									The security settlement date is the date after the issue date
	 *									when the security is traded to the buyer.
	 * @param	float	$rate			The security's annual coupon rate.
	 * @param	float	$par			The security's par value.
	 *									If you omit par, ACCRINT uses $1,000.
	 * @param	integer	$frequency		the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer	$basis			The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function ACCRINT($issue, $firstinterest, $settlement, $rate, $par=1000, $frequency=1, $basis=0)
	{
		$issue		= PHPExcel_Calculation_Functions::flattenSingleValue($issue);
		$firstinterest	= PHPExcel_Calculation_Functions::flattenSingleValue($firstinterest);
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$rate		= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$par		= (is_null($par))		? 1000 :	PHPExcel_Calculation_Functions::flattenSingleValue($par);
		$frequency	= (is_null($frequency))	? 1	: 		PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))		? 0	:		PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if ((is_numeric($rate)) && (is_numeric($par))) {
			$rate	= (float) $rate;
			$par	= (float) $par;
			if (($rate <= 0) || ($par <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
			if (!is_numeric($daysBetweenIssueAndSettlement)) {
				//	return date error
				return $daysBetweenIssueAndSettlement;
			}

			return $par * $rate * $daysBetweenIssueAndSettlement;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function ACCRINT()


	/**
	 * ACCRINTM
	 *
	 * Returns the accrued interest for a security that pays interest at maturity.
	 *
	 * Excel Function:
	 *		ACCRINTM(issue,settlement,rate[,par[,basis]])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	issue		The security's issue date.
	 * @param	mixed	settlement	The security's settlement (or maturity) date.
	 * @param	float	rate		The security's annual coupon rate.
	 * @param	float	par			The security's par value.
	 *									If you omit par, ACCRINT uses $1,000.
	 * @param	integer	basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function ACCRINTM($issue, $settlement, $rate, $par=1000, $basis=0) {
		$issue		= PHPExcel_Calculation_Functions::flattenSingleValue($issue);
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$rate		= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$par		= (is_null($par))	? 1000 :	PHPExcel_Calculation_Functions::flattenSingleValue($par);
		$basis		= (is_null($basis))	? 0 :		PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if ((is_numeric($rate)) && (is_numeric($par))) {
			$rate	= (float) $rate;
			$par	= (float) $par;
			if (($rate <= 0) || ($par <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
			if (!is_numeric($daysBetweenIssueAndSettlement)) {
				//	return date error
				return $daysBetweenIssueAndSettlement;
			}
			return $par * $rate * $daysBetweenIssueAndSettlement;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function ACCRINTM()


	/**
	 * AMORDEGRC
	 *
	 * Returns the depreciation for each accounting period.
	 * This function is provided for the French accounting system. If an asset is purchased in
	 * the middle of the accounting period, the prorated depreciation is taken into account.
	 * The function is similar to AMORLINC, except that a depreciation coefficient is applied in
	 * the calculation depending on the life of the assets.
	 * This function will return the depreciation until the last period of the life of the assets
	 * or until the cumulated value of depreciation is greater than the cost of the assets minus
	 * the salvage value.
	 *
	 * Excel Function:
	 *		AMORDEGRC(cost,purchased,firstPeriod,salvage,period,rate[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	cost		The cost of the asset.
	 * @param	mixed	purchased	Date of the purchase of the asset.
	 * @param	mixed	firstPeriod	Date of the end of the first period.
	 * @param	mixed	salvage		The salvage value at the end of the life of the asset.
	 * @param	float	period		The period.
	 * @param	float	rate		Rate of depreciation.
	 * @param	integer	basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function AMORDEGRC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis=0) {
		$cost			= PHPExcel_Calculation_Functions::flattenSingleValue($cost);
		$purchased		= PHPExcel_Calculation_Functions::flattenSingleValue($purchased);
		$firstPeriod	= PHPExcel_Calculation_Functions::flattenSingleValue($firstPeriod);
		$salvage		= PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
		$period			= floor(PHPExcel_Calculation_Functions::flattenSingleValue($period));
		$rate			= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$basis			= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	The depreciation coefficients are:
		//	Life of assets (1/rate)		Depreciation coefficient
		//	Less than 3 years			1
		//	Between 3 and 4 years		1.5
		//	Between 5 and 6 years		2
		//	More than 6 years			2.5
		$fUsePer = 1.0 / $rate;
		if ($fUsePer < 3.0) {
			$amortiseCoeff = 1.0;
		} elseif ($fUsePer < 5.0) {
			$amortiseCoeff = 1.5;
		} elseif ($fUsePer <= 6.0) {
			$amortiseCoeff = 2.0;
		} else {
			$amortiseCoeff = 2.5;
		}

		$rate *= $amortiseCoeff;
		$fNRate = round(PHPExcel_Calculation_DateTime::YEARFRAC($purchased, $firstPeriod, $basis) * $rate * $cost,0);
		$cost -= $fNRate;
		$fRest = $cost - $salvage;

		for ($n = 0; $n < $period; ++$n) {
			$fNRate = round($rate * $cost,0);
			$fRest -= $fNRate;

			if ($fRest < 0.0) {
				switch ($period - $n) {
					case 0	:
					case 1	: return round($cost * 0.5, 0);
							  break;
					default	: return 0.0;
							  break;
				}
			}
			$cost -= $fNRate;
		}
		return $fNRate;
	}	//	function AMORDEGRC()


	/**
	 * AMORLINC
	 *
	 * Returns the depreciation for each accounting period.
	 * This function is provided for the French accounting system. If an asset is purchased in
	 * the middle of the accounting period, the prorated depreciation is taken into account.
	 *
	 * Excel Function:
	 *		AMORLINC(cost,purchased,firstPeriod,salvage,period,rate[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	cost		The cost of the asset.
	 * @param	mixed	purchased	Date of the purchase of the asset.
	 * @param	mixed	firstPeriod	Date of the end of the first period.
	 * @param	mixed	salvage		The salvage value at the end of the life of the asset.
	 * @param	float	period		The period.
	 * @param	float	rate		Rate of depreciation.
	 * @param	integer	basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function AMORLINC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis=0) {
		$cost			= PHPExcel_Calculation_Functions::flattenSingleValue($cost);
		$purchased		= PHPExcel_Calculation_Functions::flattenSingleValue($purchased);
		$firstPeriod	= PHPExcel_Calculation_Functions::flattenSingleValue($firstPeriod);
		$salvage		= PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
		$period			= PHPExcel_Calculation_Functions::flattenSingleValue($period);
		$rate			= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$basis			= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		$fOneRate = $cost * $rate;
		$fCostDelta = $cost - $salvage;
		//	Note, quirky variation for leap years on the YEARFRAC for this function
		$purchasedYear = PHPExcel_Calculation_DateTime::YEAR($purchased);
		$yearFrac = PHPExcel_Calculation_DateTime::YEARFRAC($purchased, $firstPeriod, $basis);

		if (($basis == 1) && ($yearFrac < 1) && (PHPExcel_Calculation_DateTime::_isLeapYear($purchasedYear))) {
			$yearFrac *= 365 / 366;
		}

		$f0Rate = $yearFrac * $rate * $cost;
		$nNumOfFullPeriods = intval(($cost - $salvage - $f0Rate) / $fOneRate);

		if ($period == 0) {
			return $f0Rate;
		} elseif ($period <= $nNumOfFullPeriods) {
			return $fOneRate;
		} elseif ($period == ($nNumOfFullPeriods + 1)) {
            return ($fCostDelta - $fOneRate * $nNumOfFullPeriods - $f0Rate);
		} else {
			return 0.0;
		}
	}	//	function AMORLINC()


	/**
	 * COUPDAYBS
	 *
	 * Returns the number of days from the beginning of the coupon period to the settlement date.
	 *
	 * Excel Function:
	 *		COUPDAYBS(settlement,maturity,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	frequency	the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function COUPDAYBS($settlement, $maturity, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		$daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement),$basis);
		$prev = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, False);

		return PHPExcel_Calculation_DateTime::YEARFRAC($prev, $settlement, $basis) * $daysPerYear;
	}	//	function COUPDAYBS()


	/**
	 * COUPDAYS
	 *
	 * Returns the number of days in the coupon period that contains the settlement date.
	 *
	 * Excel Function:
	 *		COUPDAYS(settlement,maturity,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	frequency	the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function COUPDAYS($settlement, $maturity, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		switch ($basis) {
			case 3: // Actual/365
					return 365 / $frequency;
			case 1: // Actual/actual
					if ($frequency == 1) {
						$daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($maturity),$basis);
						return ($daysPerYear / $frequency);
					} else {
						$prev = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, False);
						$next = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, True);
						return ($next - $prev);
					}
			default: // US (NASD) 30/360, Actual/360 or European 30/360
					return 360 / $frequency;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function COUPDAYS()


	/**
	 * COUPDAYSNC
	 *
	 * Returns the number of days from the settlement date to the next coupon date.
	 *
	 * Excel Function:
	 *		COUPDAYSNC(settlement,maturity,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	frequency	the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function COUPDAYSNC($settlement, $maturity, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		$daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement),$basis);
		$next = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, True);

		return PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $next, $basis) * $daysPerYear;
	}	//	function COUPDAYSNC()


	/**
	 * COUPNCD
	 *
	 * Returns the next coupon date after the settlement date.
	 *
	 * Excel Function:
	 *		COUPNCD(settlement,maturity,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	frequency	the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function COUPNCD($settlement, $maturity, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		return self::_coupFirstPeriodDate($settlement, $maturity, $frequency, True);
	}	//	function COUPNCD()


	/**
	 * COUPNUM
	 *
	 * Returns the number of coupons payable between the settlement date and maturity date,
	 * rounded up to the nearest whole coupon.
	 *
	 * Excel Function:
	 *		COUPNUM(settlement,maturity,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	frequency	the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	integer
	 */
	public static function COUPNUM($settlement, $maturity, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		$settlement = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, True);
		$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis) * 365;

		switch ($frequency) {
			case 1: // annual payments
					return ceil($daysBetweenSettlementAndMaturity / 360);
			case 2: // half-yearly
					return ceil($daysBetweenSettlementAndMaturity / 180);
			case 4: // quarterly
					return ceil($daysBetweenSettlementAndMaturity / 90);
			case 6: // bimonthly
					return ceil($daysBetweenSettlementAndMaturity / 60);
			case 12: // monthly
					return ceil($daysBetweenSettlementAndMaturity / 30);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function COUPNUM()


	/**
	 * COUPPCD
	 *
	 * Returns the previous coupon date before the settlement date.
	 *
	 * Excel Function:
	 *		COUPPCD(settlement,maturity,frequency[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	frequency	the number of coupon payments per year.
	 *									Valid frequency values are:
	 *										1	Annual
	 *										2	Semi-Annual
	 *										4	Quarterly
	 *									If working in Gnumeric Mode, the following frequency options are
	 *									also available
	 *										6	Bimonthly
	 *										12	Monthly
	 * @param	integer		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function COUPPCD($settlement, $maturity, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		return self::_coupFirstPeriodDate($settlement, $maturity, $frequency, False);
	}	//	function COUPPCD()


	/**
	 * CUMIPMT
	 *
	 * Returns the cumulative interest paid on a loan between the start and end periods.
	 *
	 * Excel Function:
	 *		CUMIPMT(rate,nper,pv,start,end[,type])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	$rate	The Interest rate
	 * @param	integer	$nper	The total number of payment periods
	 * @param	float	$pv		Present Value
	 * @param	integer	$start	The first period in the calculation.
	 *							Payment periods are numbered beginning with 1.
	 * @param	integer	$end	The last period in the calculation.
	 * @param	integer	$type	A number 0 or 1 and indicates when payments are due:
	 *								0 or omitted	At the end of the period.
	 *								1				At the beginning of the period.
	 * @return	float
	 */
	public static function CUMIPMT($rate, $nper, $pv, $start, $end, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$nper	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$start	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($start);
		$end	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($end);
		$type	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ($start < 1 || $start > $end) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Calculate
		$interest = 0;
		for ($per = $start; $per <= $end; ++$per) {
			$interest += self::IPMT($rate, $per, $nper, $pv, 0, $type);
		}

		return $interest;
	}	//	function CUMIPMT()


	/**
	 * CUMPRINC
	 *
	 * Returns the cumulative principal paid on a loan between the start and end periods.
	 *
	 * Excel Function:
	 *		CUMPRINC(rate,nper,pv,start,end[,type])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	$rate	The Interest rate
	 * @param	integer	$nper	The total number of payment periods
	 * @param	float	$pv		Present Value
	 * @param	integer	$start	The first period in the calculation.
	 *							Payment periods are numbered beginning with 1.
	 * @param	integer	$end	The last period in the calculation.
	 * @param	integer	$type	A number 0 or 1 and indicates when payments are due:
	 *								0 or omitted	At the end of the period.
	 *								1				At the beginning of the period.
	 * @return	float
	 */
	public static function CUMPRINC($rate, $nper, $pv, $start, $end, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$nper	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$start	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($start);
		$end	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($end);
		$type	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ($start < 1 || $start > $end) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Calculate
		$principal = 0;
		for ($per = $start; $per <= $end; ++$per) {
			$principal += self::PPMT($rate, $per, $nper, $pv, 0, $type);
		}

		return $principal;
	}	//	function CUMPRINC()


	/**
	 * DB
	 *
	 * Returns the depreciation of an asset for a specified period using the
	 * fixed-declining balance method.
	 * This form of depreciation is used if you want to get a higher depreciation value
	 * at the beginning of the depreciation (as opposed to linear depreciation). The
	 * depreciation value is reduced with every depreciation period by the depreciation
	 * already deducted from the initial cost.
	 *
	 * Excel Function:
	 *		DB(cost,salvage,life,period[,month])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	cost		Initial cost of the asset.
	 * @param	float	salvage		Value at the end of the depreciation.
	 *								(Sometimes called the salvage value of the asset)
	 * @param	integer	life		Number of periods over which the asset is depreciated.
	 *								(Sometimes called the useful life of the asset)
	 * @param	integer	period		The period for which you want to calculate the
	 *								depreciation. Period must use the same units as life.
	 * @param	integer	month		Number of months in the first year. If month is omitted,
	 *								it defaults to 12.
	 * @return	float
	 */
	public static function DB($cost, $salvage, $life, $period, $month=12) {
		$cost		= PHPExcel_Calculation_Functions::flattenSingleValue($cost);
		$salvage	= PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
		$life		= PHPExcel_Calculation_Functions::flattenSingleValue($life);
		$period		= PHPExcel_Calculation_Functions::flattenSingleValue($period);
		$month		= PHPExcel_Calculation_Functions::flattenSingleValue($month);

		//	Validate
		if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period)) && (is_numeric($month))) {
			$cost		= (float) $cost;
			$salvage	= (float) $salvage;
			$life		= (int) $life;
			$period		= (int) $period;
			$month		= (int) $month;
			if ($cost == 0) {
				return 0.0;
			} elseif (($cost < 0) || (($salvage / $cost) < 0) || ($life <= 0) || ($period < 1) || ($month < 1)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			//	Set Fixed Depreciation Rate
			$fixedDepreciationRate = 1 - pow(($salvage / $cost), (1 / $life));
			$fixedDepreciationRate = round($fixedDepreciationRate, 3);

			//	Loop through each period calculating the depreciation
			$previousDepreciation = 0;
			for ($per = 1; $per <= $period; ++$per) {
				if ($per == 1) {
					$depreciation = $cost * $fixedDepreciationRate * $month / 12;
				} elseif ($per == ($life + 1)) {
					$depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate * (12 - $month) / 12;
				} else {
					$depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate;
				}
				$previousDepreciation += $depreciation;
			}
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$depreciation = round($depreciation,2);
			}
			return $depreciation;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function DB()


	/**
	 * DDB
	 *
	 * Returns the depreciation of an asset for a specified period using the
	 * double-declining balance method or some other method you specify.
	 *
	 * Excel Function:
	 *		DDB(cost,salvage,life,period[,factor])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	cost		Initial cost of the asset.
	 * @param	float	salvage		Value at the end of the depreciation.
	 *								(Sometimes called the salvage value of the asset)
	 * @param	integer	life		Number of periods over which the asset is depreciated.
	 *								(Sometimes called the useful life of the asset)
	 * @param	integer	period		The period for which you want to calculate the
	 *								depreciation. Period must use the same units as life.
	 * @param	float	factor		The rate at which the balance declines.
	 *								If factor is omitted, it is assumed to be 2 (the
	 *								double-declining balance method).
	 * @return	float
	 */
	public static function DDB($cost, $salvage, $life, $period, $factor=2.0) {
		$cost		= PHPExcel_Calculation_Functions::flattenSingleValue($cost);
		$salvage	= PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
		$life		= PHPExcel_Calculation_Functions::flattenSingleValue($life);
		$period		= PHPExcel_Calculation_Functions::flattenSingleValue($period);
		$factor		= PHPExcel_Calculation_Functions::flattenSingleValue($factor);

		//	Validate
		if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period)) && (is_numeric($factor))) {
			$cost		= (float) $cost;
			$salvage	= (float) $salvage;
			$life		= (int) $life;
			$period		= (int) $period;
			$factor		= (float) $factor;
			if (($cost <= 0) || (($salvage / $cost) < 0) || ($life <= 0) || ($period < 1) || ($factor <= 0.0) || ($period > $life)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			//	Set Fixed Depreciation Rate
			$fixedDepreciationRate = 1 - pow(($salvage / $cost), (1 / $life));
			$fixedDepreciationRate = round($fixedDepreciationRate, 3);

			//	Loop through each period calculating the depreciation
			$previousDepreciation = 0;
			for ($per = 1; $per <= $period; ++$per) {
				$depreciation = min( ($cost - $previousDepreciation) * ($factor / $life), ($cost - $salvage - $previousDepreciation) );
				$previousDepreciation += $depreciation;
			}
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$depreciation = round($depreciation,2);
			}
			return $depreciation;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function DDB()


	/**
	 * DISC
	 *
	 * Returns the discount rate for a security.
	 *
	 * Excel Function:
	 *		DISC(settlement,maturity,price,redemption[,basis])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue
	 *								date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	integer	price		The security's price per $100 face value.
	 * @param	integer	redemption	The security's redemption value per $100 face value.
	 * @param	integer	basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function DISC($settlement, $maturity, $price, $redemption, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$price		= PHPExcel_Calculation_Functions::flattenSingleValue($price);
		$redemption	= PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
		$basis		= PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if ((is_numeric($price)) && (is_numeric($redemption)) && (is_numeric($basis))) {
			$price		= (float) $price;
			$redemption	= (float) $redemption;
			$basis		= (int) $basis;
			if (($price <= 0) || ($redemption <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}

			return ((1 - $price / $redemption) / $daysBetweenSettlementAndMaturity);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function DISC()


	/**
	 * DOLLARDE
	 *
	 * Converts a dollar price expressed as an integer part and a fraction
	 *		part into a dollar price expressed as a decimal number.
	 * Fractional dollar numbers are sometimes used for security prices.
	 *
	 * Excel Function:
	 *		DOLLARDE(fractional_dollar,fraction)
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	$fractional_dollar	Fractional Dollar
	 * @param	integer	$fraction			Fraction
	 * @return	float
	 */
	public static function DOLLARDE($fractional_dollar = Null, $fraction = 0) {
		$fractional_dollar	= PHPExcel_Calculation_Functions::flattenSingleValue($fractional_dollar);
		$fraction			= (int)PHPExcel_Calculation_Functions::flattenSingleValue($fraction);

		// Validate parameters
		if (is_null($fractional_dollar) || $fraction < 0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ($fraction == 0) {
			return PHPExcel_Calculation_Functions::DIV0();
		}

		$dollars = floor($fractional_dollar);
		$cents = fmod($fractional_dollar,1);
		$cents /= $fraction;
		$cents *= pow(10,ceil(log10($fraction)));
		return $dollars + $cents;
	}	//	function DOLLARDE()


	/**
	 * DOLLARFR
	 *
	 * Converts a dollar price expressed as a decimal number into a dollar price
	 *		expressed as a fraction.
	 * Fractional dollar numbers are sometimes used for security prices.
	 *
	 * Excel Function:
	 *		DOLLARFR(decimal_dollar,fraction)
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	$decimal_dollar		Decimal Dollar
	 * @param	integer	$fraction			Fraction
	 * @return	float
	 */
	public static function DOLLARFR($decimal_dollar = Null, $fraction = 0) {
		$decimal_dollar	= PHPExcel_Calculation_Functions::flattenSingleValue($decimal_dollar);
		$fraction		= (int)PHPExcel_Calculation_Functions::flattenSingleValue($fraction);

		// Validate parameters
		if (is_null($decimal_dollar) || $fraction < 0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ($fraction == 0) {
			return PHPExcel_Calculation_Functions::DIV0();
		}

		$dollars = floor($decimal_dollar);
		$cents = fmod($decimal_dollar,1);
		$cents *= $fraction;
		$cents *= pow(10,-ceil(log10($fraction)));
		return $dollars + $cents;
	}	//	function DOLLARFR()


	/**
	 * EFFECT
	 *
	 * Returns the effective interest rate given the nominal rate and the number of
	 *		compounding payments per year.
	 *
	 * Excel Function:
	 *		EFFECT(nominal_rate,npery)
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	$nominal_rate		Nominal interest rate
	 * @param	integer	$npery				Number of compounding payments per year
	 * @return	float
	 */
	public static function EFFECT($nominal_rate = 0, $npery = 0) {
		$nominal_rate	= PHPExcel_Calculation_Functions::flattenSingleValue($nominal_rate);
		$npery			= (int)PHPExcel_Calculation_Functions::flattenSingleValue($npery);

		// Validate parameters
		if ($nominal_rate <= 0 || $npery < 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		return pow((1 + $nominal_rate / $npery), $npery) - 1;
	}	//	function EFFECT()


	/**
	 * FV
	 *
	 * Returns the Future Value of a cash flow with constant payments and interest rate (annuities).
	 *
	 * Excel Function:
	 *		FV(rate,nper,pmt[,pv[,type]])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	$rate	The interest rate per period
	 * @param	int		$nper	Total number of payment periods in an annuity
	 * @param	float	$pmt	The payment made each period: it cannot change over the
	 *							life of the annuity. Typically, pmt contains principal
	 *							and interest but no other fees or taxes.
	 * @param	float	$pv		Present Value, or the lump-sum amount that a series of
	 *							future payments is worth right now.
	 * @param	integer	$type	A number 0 or 1 and indicates when payments are due:
	 *								0 or omitted	At the end of the period.
	 *								1				At the beginning of the period.
	 * @return	float
	 */
	public static function FV($rate = 0, $nper = 0, $pmt = 0, $pv = 0, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$nper	= PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pmt	= PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$type	= PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Calculate
		if (!is_null($rate) && $rate != 0) {
			return -$pv * pow(1 + $rate, $nper) - $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate;
		} else {
			return -$pv - $pmt * $nper;
		}
	}	//	function FV()


	/**
	 * FVSCHEDULE
	 *
	 * Returns the future value of an initial principal after applying a series of compound interest rates.
	 * Use FVSCHEDULE to calculate the future value of an investment with a variable or adjustable rate.
	 *
	 * Excel Function:
	 *		FVSCHEDULE(principal,schedule)
	 *
	 * @param	float	$principal	The present value.
	 * @param	float[]	$schedule	An array of interest rates to apply.
	 * @return	float
	 */
	public static function FVSCHEDULE($principal, $schedule) {
		$principal	= PHPExcel_Calculation_Functions::flattenSingleValue($principal);
		$schedule	= PHPExcel_Calculation_Functions::flattenArray($schedule);

		foreach($schedule as $rate) {
			$principal *= 1 + $rate;
		}

		return $principal;
	}	//	function FVSCHEDULE()


	/**
	 * INTRATE
	 *
	 * Returns the interest rate for a fully invested security.
	 *
	 * Excel Function:
	 *		INTRATE(settlement,maturity,investment,redemption[,basis])
	 *
	 * @param	mixed	$settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue date when the security is traded to the buyer.
	 * @param	mixed	$maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	integer	$investment	The amount invested in the security.
	 * @param	integer	$redemption	The amount to be received at maturity.
	 * @param	integer	$basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function INTRATE($settlement, $maturity, $investment, $redemption, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$investment	= PHPExcel_Calculation_Functions::flattenSingleValue($investment);
		$redemption	= PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
		$basis		= PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if ((is_numeric($investment)) && (is_numeric($redemption)) && (is_numeric($basis))) {
			$investment	= (float) $investment;
			$redemption	= (float) $redemption;
			$basis		= (int) $basis;
			if (($investment <= 0) || ($redemption <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}

			return (($redemption / $investment) - 1) / ($daysBetweenSettlementAndMaturity);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function INTRATE()


	/**
	 * IPMT
	 *
	 * Returns the interest payment for a given period for an investment based on periodic, constant payments and a constant interest rate.
	 *
	 * Excel Function:
	 *		IPMT(rate,per,nper,pv[,fv][,type])
	 *
	 * @param	float	$rate	Interest rate per period
	 * @param	int		$per	Period for which we want to find the interest
	 * @param	int		$nper	Number of periods
	 * @param	float	$pv		Present Value
	 * @param	float	$fv		Future Value
	 * @param	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
	 * @return	float
	 */
	public static function IPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$per	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($per);
		$nper	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$fv		= PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ($per <= 0 || $per > $nper) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Calculate
		$interestAndPrincipal = self::_interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);
		return $interestAndPrincipal[0];
	}	//	function IPMT()

	/**
	 * IRR
	 *
	 * Returns the internal rate of return for a series of cash flows represented by the numbers in values. 
	 * These cash flows do not have to be even, as they would be for an annuity. However, the cash flows must occur 
	 * at regular intervals, such as monthly or annually. The internal rate of return is the interest rate received
	 * for an investment consisting of payments (negative values) and income (positive values) that occur at regular 
	 * periods.
	 *
	 * Excel Function:
	 *		IRR(values[,guess])
	 *
	 * @param	float[]	$values		An array or a reference to cells that contain numbers for which you want
	 *									to calculate the internal rate of return.
	 *								Values must contain at least one positive value and one negative value to 
	 *									calculate the internal rate of return.
	 * @param	float	$guess		A number that you guess is close to the result of IRR
	 * @return	float
	 */
	public static function IRR($values, $guess = 0.1) {
		if (!is_array($values)) return PHPExcel_Calculation_Functions::VALUE();
		$values = PHPExcel_Calculation_Functions::flattenArray($values);
		$guess = PHPExcel_Calculation_Functions::flattenSingleValue($guess);

		// create an initial range, with a root somewhere between 0 and guess
		$x1 = 0.0;
		$x2 = $guess;
		$f1 = self::NPV($x1, $values);
		$f2 = self::NPV($x2, $values);
		for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
			if (($f1 * $f2) < 0.0) break;
			if (abs($f1) < abs($f2)) {
				$f1 = self::NPV($x1 += 1.6 * ($x1 - $x2), $values);
			} else {
				$f2 = self::NPV($x2 += 1.6 * ($x2 - $x1), $values);
			}
		}
		if (($f1 * $f2) > 0.0) return PHPExcel_Calculation_Functions::VALUE();

		$f = self::NPV($x1, $values);
		if ($f < 0.0) {
			$rtb = $x1;
			$dx = $x2 - $x1;
		} else {
			$rtb = $x2;
			$dx = $x1 - $x2;
		}

		for ($i = 0;  $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
			$dx *= 0.5;
			$x_mid = $rtb + $dx;
			$f_mid = self::NPV($x_mid, $values);
			if ($f_mid <= 0.0) 
				$rtb = $x_mid;
			if ((abs($f_mid) < FINANCIAL_PRECISION) || (abs($dx) < FINANCIAL_PRECISION)) 
				return $x_mid;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function IRR()


	/**
	 * ISPMT
	 *
	 * Returns the interest payment for an investment based on an interest rate and a constant payment schedule.
	 *
	 * Excel Function:
	 *     =ISPMT(interest_rate, period, number_payments, PV)
	 *
	 * interest_rate is the interest rate for the investment
	 *
	 * period is the period to calculate the interest rate.  It must be betweeen 1 and number_payments.
	 *
	 * number_payments is the number of payments for the annuity
	 *
	 * PV is the loan amount or present value of the payments
	 */
	public static function ISPMT() {
		// Return value
		$returnValue = 0;

		// Get the parameters
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$interestRate = array_shift($aArgs);
		$period = array_shift($aArgs);
		$numberPeriods = array_shift($aArgs);
		$principleRemaining = array_shift($aArgs);

		// Calculate
		$principlePayment = ($principleRemaining * 1.0) / ($numberPeriods * 1.0);
		for($i=0; $i <= $period; ++$i) {
			$returnValue = $interestRate * $principleRemaining * -1;
			$principleRemaining -= $principlePayment;
			// principle needs to be 0 after the last payment, don't let floating point screw it up
			if($i == $numberPeriods) {
				$returnValue = 0;
			}
		}
		return($returnValue);
	}	//	function ISPMT()


	/**
	 * MIRR
	 *
	 * Returns the modified internal rate of return for a series of periodic cash flows. MIRR considers both 
	 *		the cost of the investment and the interest received on reinvestment of cash.
	 *
	 * Excel Function:
	 *		MIRR(values,finance_rate, reinvestment_rate)
	 *
	 * @param	float[]	$values				An array or a reference to cells that contain a series of payments and
	 *											income occurring at regular intervals.
	 *										Payments are negative value, income is positive values.
	 * @param	float	$finance_rate		The interest rate you pay on the money used in the cash flows
	 * @param	float	$reinvestment_rate	The interest rate you receive on the cash flows as you reinvest them
	 * @return	float
	 */
	public static function MIRR($values, $finance_rate, $reinvestment_rate) {
		if (!is_array($values)) return PHPExcel_Calculation_Functions::VALUE();
		$values				= PHPExcel_Calculation_Functions::flattenArray($values);
		$finance_rate		= PHPExcel_Calculation_Functions::flattenSingleValue($finance_rate);
		$reinvestment_rate	= PHPExcel_Calculation_Functions::flattenSingleValue($reinvestment_rate);
		$n = count($values);

		$rr = 1.0 + $reinvestment_rate;
		$fr = 1.0 + $finance_rate;

		$npv_pos = $npv_neg = 0.0;
		foreach($values as $i => $v) {
			if ($v >= 0) {
				$npv_pos += $v / pow($rr, $i);
			} else {
				$npv_neg += $v / pow($fr, $i);
			}
		}

		if (($npv_neg == 0) || ($npv_pos == 0) || ($reinvestment_rate <= -1)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		$mirr = pow((-$npv_pos * pow($rr, $n))
				/ ($npv_neg * ($rr)), (1.0 / ($n - 1))) - 1.0;

		return (is_finite($mirr) ? $mirr : PHPExcel_Calculation_Functions::VALUE());
	}	//	function MIRR()


	/**
	 * NOMINAL
	 *
	 * Returns the nominal interest rate given the effective rate and the number of compounding payments per year.
	 *
	 * @param	float	$effect_rate	Effective interest rate
	 * @param	int		$npery			Number of compounding payments per year
	 * @return	float
	 */
	public static function NOMINAL($effect_rate = 0, $npery = 0) {
		$effect_rate	= PHPExcel_Calculation_Functions::flattenSingleValue($effect_rate);
		$npery			= (int)PHPExcel_Calculation_Functions::flattenSingleValue($npery);

		// Validate parameters
		if ($effect_rate <= 0 || $npery < 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Calculate
		return $npery * (pow($effect_rate + 1, 1 / $npery) - 1);
	}	//	function NOMINAL()


	/**
	 * NPER
	 *
	 * Returns the number of periods for a cash flow with constant periodic payments (annuities), and interest rate.
	 *
	 * @param	float	$rate	Interest rate per period
	 * @param	int		$pmt	Periodic payment (annuity)
	 * @param	float	$pv		Present Value
	 * @param	float	$fv		Future Value
	 * @param	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
	 * @return	float
	 */
	public static function NPER($rate = 0, $pmt = 0, $pv = 0, $fv = 0, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$pmt	= PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$fv		= PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Calculate
		if (!is_null($rate) && $rate != 0) {
			if ($pmt == 0 && $pv == 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			return log(($pmt * (1 + $rate * $type) / $rate - $fv) / ($pv + $pmt * (1 + $rate * $type) / $rate)) / log(1 + $rate);
		} else {
			if ($pmt == 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			return (-$pv -$fv) / $pmt;
		}
	}	//	function NPER()

	/**
	 * NPV
	 *
	 * Returns the Net Present Value of a cash flow series given a discount rate.
	 *
	 * @return	float
	 */
	public static function NPV() {
		// Return value
		$returnValue = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

		// Calculate
		$rate = array_shift($aArgs);
		for ($i = 1; $i <= count($aArgs); ++$i) {
			// Is it a numeric value?
			if (is_numeric($aArgs[$i - 1])) {
				$returnValue += $aArgs[$i - 1] / pow(1 + $rate, $i);
			}
		}

		// Return
		return $returnValue;
	}	//	function NPV()

	/**
	 * PMT
	 *
	 * Returns the constant payment (annuity) for a cash flow with a constant interest rate.
	 *
	 * @param	float	$rate	Interest rate per period
	 * @param	int		$nper	Number of periods
	 * @param	float	$pv		Present Value
	 * @param	float	$fv		Future Value
	 * @param	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
	 * @return	float
	 */
	public static function PMT($rate = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$nper	= PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$fv		= PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Calculate
		if (!is_null($rate) && $rate != 0) {
			return (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $type) / ((pow(1 + $rate, $nper) - 1) / $rate);
		} else {
			return (-$pv - $fv) / $nper;
		}
	}	//	function PMT()


	/**
	 * PPMT
	 *
	 * Returns the interest payment for a given period for an investment based on periodic, constant payments and a constant interest rate.
	 *
	 * @param	float	$rate	Interest rate per period
	 * @param	int		$per	Period for which we want to find the interest
	 * @param	int		$nper	Number of periods
	 * @param	float	$pv		Present Value
	 * @param	float	$fv		Future Value
	 * @param	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
	 * @return	float
	 */
	public static function PPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$per	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($per);
		$nper	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$fv		= PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ($per <= 0 || $per > $nper) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Calculate
		$interestAndPrincipal = self::_interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);
		return $interestAndPrincipal[1];
	}	//	function PPMT()


	public static function PRICE($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$rate		= (float) PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$yield		= (float) PHPExcel_Calculation_Functions::flattenSingleValue($yield);
		$redemption	= (float) PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
		$frequency	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
		$basis		= (is_null($basis))	? 0 :	(int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (($settlement > $maturity) ||
			(!self::_validFrequency($frequency)) ||
			(($basis < 0) || ($basis > 4))) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		$dsc = self::COUPDAYSNC($settlement, $maturity, $frequency, $basis);
		$e = self::COUPDAYS($settlement, $maturity, $frequency, $basis);
		$n = self::COUPNUM($settlement, $maturity, $frequency, $basis);
		$a = self::COUPDAYBS($settlement, $maturity, $frequency, $basis);

		$baseYF	= 1.0 + ($yield / $frequency);
		$rfp	= 100 * ($rate / $frequency);
		$de	= $dsc / $e;

		$result = $redemption / pow($baseYF, (--$n + $de));
		for($k = 0; $k <= $n; ++$k) {
			$result += $rfp / (pow($baseYF, ($k + $de)));
		}
		$result -= $rfp * ($a / $e);

		return $result;
	}	//	function PRICE()


	/**
	 * PRICEDISC
	 *
	 * Returns the price per $100 face value of a discounted security.
	 *
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	int		discount	The security's discount rate.
	 * @param	int		redemption	The security's redemption value per $100 face value.
	 * @param	int		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function PRICEDISC($settlement, $maturity, $discount, $redemption, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$discount	= (float) PHPExcel_Calculation_Functions::flattenSingleValue($discount);
		$redemption	= (float) PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
		$basis		= (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if ((is_numeric($discount)) && (is_numeric($redemption)) && (is_numeric($basis))) {
			if (($discount <= 0) || ($redemption <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}

			return $redemption * (1 - $discount * $daysBetweenSettlementAndMaturity);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function PRICEDISC()


	/**
	 * PRICEMAT
	 *
	 * Returns the price per $100 face value of a security that pays interest at maturity.
	 *
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security's settlement date is the date after the issue date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	issue		The security's issue date.
	 * @param	int		rate		The security's interest rate at date of issue.
	 * @param	int		yield		The security's annual yield.
	 * @param	int		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function PRICEMAT($settlement, $maturity, $issue, $rate, $yield, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$issue		= PHPExcel_Calculation_Functions::flattenSingleValue($issue);
		$rate		= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$yield		= PHPExcel_Calculation_Functions::flattenSingleValue($yield);
		$basis		= (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if (is_numeric($rate) && is_numeric($yield)) {
			if (($rate <= 0) || ($yield <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement),$basis);
			if (!is_numeric($daysPerYear)) {
				return $daysPerYear;
			}
			$daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
			if (!is_numeric($daysBetweenIssueAndSettlement)) {
				//	return date error
				return $daysBetweenIssueAndSettlement;
			}
			$daysBetweenIssueAndSettlement *= $daysPerYear;
			$daysBetweenIssueAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $maturity, $basis);
			if (!is_numeric($daysBetweenIssueAndMaturity)) {
				//	return date error
				return $daysBetweenIssueAndMaturity;
			}
			$daysBetweenIssueAndMaturity *= $daysPerYear;
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}
			$daysBetweenSettlementAndMaturity *= $daysPerYear;

			return ((100 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate * 100)) /
				   (1 + (($daysBetweenSettlementAndMaturity / $daysPerYear) * $yield)) -
				   (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate * 100));
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function PRICEMAT()


	/**
	 * PV
	 *
	 * Returns the Present Value of a cash flow with constant payments and interest rate (annuities).
	 *
	 * @param	float	$rate	Interest rate per period
	 * @param	int		$nper	Number of periods
	 * @param	float	$pmt	Periodic payment (annuity)
	 * @param	float	$fv		Future Value
	 * @param	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
	 * @return	float
	 */
	public static function PV($rate = 0, $nper = 0, $pmt = 0, $fv = 0, $type = 0) {
		$rate	= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$nper	= PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pmt	= PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
		$fv		= PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= PHPExcel_Calculation_Functions::flattenSingleValue($type);

		// Validate parameters
		if ($type != 0 && $type != 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Calculate
		if (!is_null($rate) && $rate != 0) {
			return (-$pmt * (1 + $rate * $type) * ((pow(1 + $rate, $nper) - 1) / $rate) - $fv) / pow(1 + $rate, $nper);
		} else {
			return -$fv - $pmt * $nper;
		}
	}	//	function PV()


	/**
	 * RATE
	 *
	 * Returns the interest rate per period of an annuity.
	 * RATE is calculated by iteration and can have zero or more solutions.
	 * If the successive results of RATE do not converge to within 0.0000001 after 20 iterations,
	 * RATE returns the #NUM! error value.
	 *
	 * Excel Function:
	 *		RATE(nper,pmt,pv[,fv[,type[,guess]]])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	nper		The total number of payment periods in an annuity.
	 * @param	float	pmt			The payment made each period and cannot change over the life
	 *									of the annuity.
	 *								Typically, pmt includes principal and interest but no other
	 *									fees or taxes.
	 * @param	float	pv			The present value - the total amount that a series of future
	 *									payments is worth now.
	 * @param	float	fv			The future value, or a cash balance you want to attain after
	 *									the last payment is made. If fv is omitted, it is assumed
	 *									to be 0 (the future value of a loan, for example, is 0).
	 * @param	integer	type		A number 0 or 1 and indicates when payments are due:
	 *										0 or omitted	At the end of the period.
	 *										1				At the beginning of the period.
	 * @param	float	guess		Your guess for what the rate will be.
	 *									If you omit guess, it is assumed to be 10 percent.
	 * @return	float
	 **/
	public static function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
		$nper	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pmt	= PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$fv		= (is_null($fv))	? 0.0	:	PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= (is_null($type))	? 0		:	(int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
		$guess	= (is_null($guess))	? 0.1	:	PHPExcel_Calculation_Functions::flattenSingleValue($guess);

		$rate = $guess;
		if (abs($rate) < FINANCIAL_PRECISION) {
			$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
		} else {
			$f = exp($nper * log(1 + $rate));
			$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
		}
		$y0 = $pv + $pmt * $nper + $fv;
		$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;

		// find root by secant method
		$i  = $x0 = 0.0;
		$x1 = $rate;
		while ((abs($y0 - $y1) > FINANCIAL_PRECISION) && ($i < FINANCIAL_MAX_ITERATIONS)) {
			$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
			$x0 = $x1;
			$x1 = $rate;
			if (($nper * abs($pmt)) > ($pv - $fv))
				$x1 = abs($x1);

			if (abs($rate) < FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}

			$y0 = $y1;
			$y1 = $y;
			++$i;
		}
		return $rate;
	}	//	function RATE()


	/**
	 * RECEIVED
	 *
	 * Returns the price per $100 face value of a discounted security.
	 *
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security settlement date is the date after the issue date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	int		investment	The amount invested in the security.
	 * @param	int		discount	The security's discount rate.
	 * @param	int		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function RECEIVED($settlement, $maturity, $investment, $discount, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$investment	= (float) PHPExcel_Calculation_Functions::flattenSingleValue($investment);
		$discount	= (float) PHPExcel_Calculation_Functions::flattenSingleValue($discount);
		$basis		= (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if ((is_numeric($investment)) && (is_numeric($discount)) && (is_numeric($basis))) {
			if (($investment <= 0) || ($discount <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}

			return $investment / ( 1 - ($discount * $daysBetweenSettlementAndMaturity));
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function RECEIVED()


	/**
	 * SLN
	 *
	 * Returns the straight-line depreciation of an asset for one period
	 *
	 * @param	cost		Initial cost of the asset
	 * @param	salvage		Value at the end of the depreciation
	 * @param	life		Number of periods over which the asset is depreciated
	 * @return	float
	 */
	public static function SLN($cost, $salvage, $life) {
		$cost		= PHPExcel_Calculation_Functions::flattenSingleValue($cost);
		$salvage	= PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
		$life		= PHPExcel_Calculation_Functions::flattenSingleValue($life);

		// Calculate
		if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life))) {
			if ($life < 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			return ($cost - $salvage) / $life;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function SLN()


	/**
	 * SYD
	 *
	 * Returns the sum-of-years' digits depreciation of an asset for a specified period.
	 *
	 * @param	cost		Initial cost of the asset
	 * @param	salvage		Value at the end of the depreciation
	 * @param	life		Number of periods over which the asset is depreciated
	 * @param	period		Period
	 * @return	float
	 */
	public static function SYD($cost, $salvage, $life, $period) {
		$cost		= PHPExcel_Calculation_Functions::flattenSingleValue($cost);
		$salvage	= PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
		$life		= PHPExcel_Calculation_Functions::flattenSingleValue($life);
		$period		= PHPExcel_Calculation_Functions::flattenSingleValue($period);

		// Calculate
		if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period))) {
			if (($life < 1) || ($period > $life)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			return (($cost - $salvage) * ($life - $period + 1) * 2) / ($life * ($life + 1));
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function SYD()


	/**
	 * TBILLEQ
	 *
	 * Returns the bond-equivalent yield for a Treasury bill.
	 *
	 * @param	mixed	settlement	The Treasury bill's settlement date.
	 *								The Treasury bill's settlement date is the date after the issue date when the Treasury bill is traded to the buyer.
	 * @param	mixed	maturity	The Treasury bill's maturity date.
	 *								The maturity date is the date when the Treasury bill expires.
	 * @param	int		discount	The Treasury bill's discount rate.
	 * @return	float
	 */
	public static function TBILLEQ($settlement, $maturity, $discount) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$discount	= PHPExcel_Calculation_Functions::flattenSingleValue($discount);

		//	Use TBILLPRICE for validation
		$testValue = self::TBILLPRICE($settlement, $maturity, $discount);
		if (is_string($testValue)) {
			return $testValue;
		}

		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
			++$maturity;
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity) * 360;
		} else {
			$daysBetweenSettlementAndMaturity = (PHPExcel_Calculation_DateTime::_getDateValue($maturity) - PHPExcel_Calculation_DateTime::_getDateValue($settlement));
		}

		return (365 * $discount) / (360 - $discount * $daysBetweenSettlementAndMaturity);
	}	//	function TBILLEQ()


	/**
	 * TBILLPRICE
	 *
	 * Returns the yield for a Treasury bill.
	 *
	 * @param	mixed	settlement	The Treasury bill's settlement date.
	 *								The Treasury bill's settlement date is the date after the issue date when the Treasury bill is traded to the buyer.
	 * @param	mixed	maturity	The Treasury bill's maturity date.
	 *								The maturity date is the date when the Treasury bill expires.
	 * @param	int		discount	The Treasury bill's discount rate.
	 * @return	float
	 */
	public static function TBILLPRICE($settlement, $maturity, $discount) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$discount	= PHPExcel_Calculation_Functions::flattenSingleValue($discount);

		if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		//	Validate
		if (is_numeric($discount)) {
			if ($discount <= 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				++$maturity;
				$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity) * 360;
				if (!is_numeric($daysBetweenSettlementAndMaturity)) {
					//	return date error
					return $daysBetweenSettlementAndMaturity;
				}
			} else {
				$daysBetweenSettlementAndMaturity = (PHPExcel_Calculation_DateTime::_getDateValue($maturity) - PHPExcel_Calculation_DateTime::_getDateValue($settlement));
			}

			if ($daysBetweenSettlementAndMaturity > 360) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			$price = 100 * (1 - (($discount * $daysBetweenSettlementAndMaturity) / 360));
			if ($price <= 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			return $price;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function TBILLPRICE()


	/**
	 * TBILLYIELD
	 *
	 * Returns the yield for a Treasury bill.
	 *
	 * @param	mixed	settlement	The Treasury bill's settlement date.
	 *								The Treasury bill's settlement date is the date after the issue date when the Treasury bill is traded to the buyer.
	 * @param	mixed	maturity	The Treasury bill's maturity date.
	 *								The maturity date is the date when the Treasury bill expires.
	 * @param	int		price		The Treasury bill's price per $100 face value.
	 * @return	float
	 */
	public static function TBILLYIELD($settlement, $maturity, $price) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$price		= PHPExcel_Calculation_Functions::flattenSingleValue($price);

		//	Validate
		if (is_numeric($price)) {
			if ($price <= 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				++$maturity;
				$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity) * 360;
				if (!is_numeric($daysBetweenSettlementAndMaturity)) {
					//	return date error
					return $daysBetweenSettlementAndMaturity;
				}
			} else {
				$daysBetweenSettlementAndMaturity = (PHPExcel_Calculation_DateTime::_getDateValue($maturity) - PHPExcel_Calculation_DateTime::_getDateValue($settlement));
			}

			if ($daysBetweenSettlementAndMaturity > 360) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			return ((100 - $price) / $price) * (360 / $daysBetweenSettlementAndMaturity);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function TBILLYIELD()


	public static function XIRR($values, $dates, $guess = 0.1) {
		if ((!is_array($values)) && (!is_array($dates))) return PHPExcel_Calculation_Functions::VALUE();
		$values	= PHPExcel_Calculation_Functions::flattenArray($values);
		$dates	= PHPExcel_Calculation_Functions::flattenArray($dates);
		$guess = PHPExcel_Calculation_Functions::flattenSingleValue($guess);
		if (count($values) != count($dates)) return PHPExcel_Calculation_Functions::NaN();

		// create an initial range, with a root somewhere between 0 and guess
		$x1 = 0.0;
		$x2 = $guess;
		$f1 = self::XNPV($x1, $values, $dates);
		$f2 = self::XNPV($x2, $values, $dates);
		for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
			if (($f1 * $f2) < 0.0) break;
			if (abs($f1) < abs($f2)) {
				$f1 = self::XNPV($x1 += 1.6 * ($x1 - $x2), $values, $dates);
			} else {
				$f2 = self::XNPV($x2 += 1.6 * ($x2 - $x1), $values, $dates);
			}
		}
		if (($f1 * $f2) > 0.0) return PHPExcel_Calculation_Functions::VALUE();

		$f = self::XNPV($x1, $values, $dates);
		if ($f < 0.0) {
			$rtb = $x1;
			$dx = $x2 - $x1;
		} else {
			$rtb = $x2;
			$dx = $x1 - $x2;
		}

		for ($i = 0;  $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
			$dx *= 0.5;
			$x_mid = $rtb + $dx;
			$f_mid = self::XNPV($x_mid, $values, $dates);
			if ($f_mid <= 0.0) $rtb = $x_mid;
			if ((abs($f_mid) < FINANCIAL_PRECISION) || (abs($dx) < FINANCIAL_PRECISION)) return $x_mid;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}


	/**
	 * XNPV
	 *
	 * Returns the net present value for a schedule of cash flows that is not necessarily periodic.
	 * To calculate the net present value for a series of cash flows that is periodic, use the NPV function.
	 *
	 * Excel Function:
	 *		=XNPV(rate,values,dates)
	 *
	 * @param	float			$rate		The discount rate to apply to the cash flows.
	 * @param	array of float	$values		A series of cash flows that corresponds to a schedule of payments in dates. The first payment is optional and corresponds to a cost or payment that occurs at the beginning of the investment. If the first value is a cost or payment, it must be a negative value. All succeeding payments are discounted based on a 365-day year. The series of values must contain at least one positive value and one negative value.
	 * @param	array of mixed	$dates		A schedule of payment dates that corresponds to the cash flow payments. The first payment date indicates the beginning of the schedule of payments. All other dates must be later than this date, but they may occur in any order.
	 * @return	float
	 */
	public static function XNPV($rate, $values, $dates) {
		$rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		if (!is_numeric($rate)) return PHPExcel_Calculation_Functions::VALUE();
		if ((!is_array($values)) || (!is_array($dates))) return PHPExcel_Calculation_Functions::VALUE();
		$values	= PHPExcel_Calculation_Functions::flattenArray($values);
		$dates	= PHPExcel_Calculation_Functions::flattenArray($dates);
		$valCount = count($values);
		if ($valCount != count($dates)) return PHPExcel_Calculation_Functions::NaN();
		if ((min($values) > 0) || (max($values) < 0)) return PHPExcel_Calculation_Functions::VALUE();

		$xnpv = 0.0;
		for ($i = 0; $i < $valCount; ++$i) {
			if (!is_numeric($values[$i])) return PHPExcel_Calculation_Functions::VALUE();
			$xnpv += $values[$i] / pow(1 + $rate, PHPExcel_Calculation_DateTime::DATEDIF($dates[0],$dates[$i],'d') / 365);
		}
		return (is_finite($xnpv)) ? $xnpv : PHPExcel_Calculation_Functions::VALUE();
	}	//	function XNPV()


	/**
	 * YIELDDISC
	 *
	 * Returns the annual yield of a security that pays interest at maturity.
	 *
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security's settlement date is the date after the issue date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	int		price		The security's price per $100 face value.
	 * @param	int		redemption	The security's redemption value per $100 face value.
	 * @param	int		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function YIELDDISC($settlement, $maturity, $price, $redemption, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$price		= PHPExcel_Calculation_Functions::flattenSingleValue($price);
		$redemption	= PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
		$basis		= (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if (is_numeric($price) && is_numeric($redemption)) {
			if (($price <= 0) || ($redemption <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement),$basis);
			if (!is_numeric($daysPerYear)) {
				return $daysPerYear;
			}
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity,$basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}
			$daysBetweenSettlementAndMaturity *= $daysPerYear;

			return (($redemption - $price) / $price) * ($daysPerYear / $daysBetweenSettlementAndMaturity);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function YIELDDISC()


	/**
	 * YIELDMAT
	 *
	 * Returns the annual yield of a security that pays interest at maturity.
	 *
	 * @param	mixed	settlement	The security's settlement date.
	 *								The security's settlement date is the date after the issue date when the security is traded to the buyer.
	 * @param	mixed	maturity	The security's maturity date.
	 *								The maturity date is the date when the security expires.
	 * @param	mixed	issue		The security's issue date.
	 * @param	int		rate		The security's interest rate at date of issue.
	 * @param	int		price		The security's price per $100 face value.
	 * @param	int		basis		The type of day count to use.
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 * @return	float
	 */
	public static function YIELDMAT($settlement, $maturity, $issue, $rate, $price, $basis=0) {
		$settlement	= PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
		$maturity	= PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
		$issue		= PHPExcel_Calculation_Functions::flattenSingleValue($issue);
		$rate		= PHPExcel_Calculation_Functions::flattenSingleValue($rate);
		$price		= PHPExcel_Calculation_Functions::flattenSingleValue($price);
		$basis		= (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);

		//	Validate
		if (is_numeric($rate) && is_numeric($price)) {
			if (($rate <= 0) || ($price <= 0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement),$basis);
			if (!is_numeric($daysPerYear)) {
				return $daysPerYear;
			}
			$daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
			if (!is_numeric($daysBetweenIssueAndSettlement)) {
				//	return date error
				return $daysBetweenIssueAndSettlement;
			}
			$daysBetweenIssueAndSettlement *= $daysPerYear;
			$daysBetweenIssueAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $maturity, $basis);
			if (!is_numeric($daysBetweenIssueAndMaturity)) {
				//	return date error
				return $daysBetweenIssueAndMaturity;
			}
			$daysBetweenIssueAndMaturity *= $daysPerYear;
			$daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
			if (!is_numeric($daysBetweenSettlementAndMaturity)) {
				//	return date error
				return $daysBetweenSettlementAndMaturity;
			}
			$daysBetweenSettlementAndMaturity *= $daysPerYear;

			return ((1 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate) - (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate))) /
				   (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate))) *
				   ($daysPerYear / $daysBetweenSettlementAndMaturity);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function YIELDMAT()

}	//	class PHPExcel_Calculation_Financial
