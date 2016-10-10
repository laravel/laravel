<?php

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/linearBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/logarithmicBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/exponentialBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/powerBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/polynomialBestFitClass.php';

/**
 * PHPExcel_trendClass
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
 * @package    PHPExcel_Shared_Trend
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class trendClass
{
    const TREND_LINEAR            = 'Linear';
    const TREND_LOGARITHMIC       = 'Logarithmic';
    const TREND_EXPONENTIAL       = 'Exponential';
    const TREND_POWER             = 'Power';
    const TREND_POLYNOMIAL_2      = 'Polynomial_2';
    const TREND_POLYNOMIAL_3      = 'Polynomial_3';
    const TREND_POLYNOMIAL_4      = 'Polynomial_4';
    const TREND_POLYNOMIAL_5      = 'Polynomial_5';
    const TREND_POLYNOMIAL_6      = 'Polynomial_6';
    const TREND_BEST_FIT          = 'Bestfit';
    const TREND_BEST_FIT_NO_POLY  = 'Bestfit_no_Polynomials';

    /**
     * Names of the best-fit trend analysis methods
     *
     * @var string[]
     **/
    private static $trendTypes = array(
        self::TREND_LINEAR,
        self::TREND_LOGARITHMIC,
        self::TREND_EXPONENTIAL,
        self::TREND_POWER
    );

    /**
     * Names of the best-fit trend polynomial orders
     *
     * @var string[]
     **/
    private static $trendTypePolynomialOrders = array(
        self::TREND_POLYNOMIAL_2,
        self::TREND_POLYNOMIAL_3,
        self::TREND_POLYNOMIAL_4,
        self::TREND_POLYNOMIAL_5,
        self::TREND_POLYNOMIAL_6
    );

    /**
     * Cached results for each method when trying to identify which provides the best fit
     *
     * @var PHPExcel_Best_Fit[]
     **/
    private static $trendCache = array();


    public static function calculate($trendType = self::TREND_BEST_FIT, $yValues, $xValues = array(), $const = true)
    {
        //    Calculate number of points in each dataset
        $nY = count($yValues);
        $nX = count($xValues);

        //    Define X Values if necessary
        if ($nX == 0) {
            $xValues = range(1, $nY);
            $nX = $nY;
        } elseif ($nY != $nX) {
            //    Ensure both arrays of points are the same size
            trigger_error("trend(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
        }

        $key = md5($trendType.$const.serialize($yValues).serialize($xValues));
        //    Determine which trend method has been requested
        switch ($trendType) {
            //    Instantiate and return the class for the requested trend method
            case self::TREND_LINEAR:
            case self::TREND_LOGARITHMIC:
            case self::TREND_EXPONENTIAL:
            case self::TREND_POWER:
                if (!isset(self::$trendCache[$key])) {
                    $className = 'PHPExcel_'.$trendType.'_Best_Fit';
                    self::$trendCache[$key] = new $className($yValues, $xValues, $const);
                }
                return self::$trendCache[$key];
            case self::TREND_POLYNOMIAL_2:
            case self::TREND_POLYNOMIAL_3:
            case self::TREND_POLYNOMIAL_4:
            case self::TREND_POLYNOMIAL_5:
            case self::TREND_POLYNOMIAL_6:
                if (!isset(self::$trendCache[$key])) {
                    $order = substr($trendType, -1);
                    self::$trendCache[$key] = new PHPExcel_Polynomial_Best_Fit($order, $yValues, $xValues, $const);
                }
                return self::$trendCache[$key];
            case self::TREND_BEST_FIT:
            case self::TREND_BEST_FIT_NO_POLY:
                //    If the request is to determine the best fit regression, then we test each trend line in turn
                //    Start by generating an instance of each available trend method
                foreach (self::$trendTypes as $trendMethod) {
                    $className = 'PHPExcel_'.$trendMethod.'BestFit';
                    $bestFit[$trendMethod] = new $className($yValues, $xValues, $const);
                    $bestFitValue[$trendMethod] = $bestFit[$trendMethod]->getGoodnessOfFit();
                }
                if ($trendType != self::TREND_BEST_FIT_NO_POLY) {
                    foreach (self::$trendTypePolynomialOrders as $trendMethod) {
                        $order = substr($trendMethod, -1);
                        $bestFit[$trendMethod] = new PHPExcel_Polynomial_Best_Fit($order, $yValues, $xValues, $const);
                        if ($bestFit[$trendMethod]->getError()) {
                            unset($bestFit[$trendMethod]);
                        } else {
                            $bestFitValue[$trendMethod] = $bestFit[$trendMethod]->getGoodnessOfFit();
                        }
                    }
                }
                //    Determine which of our trend lines is the best fit, and then we return the instance of that trend class
                arsort($bestFitValue);
                $bestFitType = key($bestFitValue);
                return $bestFit[$bestFitType];
            default:
                return false;
        }
    }
}
