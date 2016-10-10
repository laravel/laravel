<?php

/**
 * PHPExcel
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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Shared_TimeZone
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_TimeZone
{
    /*
     * Default Timezone used for date/time conversions
     *
     * @private
     * @var    string
     */
    protected static $timezone    = 'UTC';

    /**
     * Validate a Timezone name
     *
     * @param     string        $timezone            Time zone (e.g. 'Europe/London')
     * @return     boolean                        Success or failure
     */
    public static function _validateTimeZone($timezone)
    {
        if (in_array($timezone, DateTimeZone::listIdentifiers())) {
            return true;
        }
        return false;
    }

    /**
     * Set the Default Timezone used for date/time conversions
     *
     * @param     string        $timezone            Time zone (e.g. 'Europe/London')
     * @return     boolean                        Success or failure
     */
    public static function setTimeZone($timezone)
    {
        if (self::_validateTimezone($timezone)) {
            self::$timezone = $timezone;
            return true;
        }
        return false;
    }


    /**
     * Return the Default Timezone used for date/time conversions
     *
     * @return     string        Timezone (e.g. 'Europe/London')
     */
    public static function getTimeZone()
    {
        return self::$timezone;
    }


    /**
     *    Return the Timezone transition for the specified timezone and timestamp
     *
     *    @param        DateTimeZone         $objTimezone    The timezone for finding the transitions
     *    @param        integer                 $timestamp        PHP date/time value for finding the current transition
     *    @return         array                The current transition details
     */
    private static function getTimezoneTransitions($objTimezone, $timestamp)
    {
        $allTransitions = $objTimezone->getTransitions();
        $transitions = array();
        foreach ($allTransitions as $key => $transition) {
            if ($transition['ts'] > $timestamp) {
                $transitions[] = ($key > 0) ? $allTransitions[$key - 1] : $transition;
                break;
            }
            if (empty($transitions)) {
                $transitions[] = end($allTransitions);
            }
        }

        return $transitions;
    }

    /**
     *    Return the Timezone offset used for date/time conversions to/from UST
     *    This requires both the timezone and the calculated date/time to allow for local DST
     *
     *    @param        string                 $timezone        The timezone for finding the adjustment to UST
     *    @param        integer                 $timestamp        PHP date/time value
     *    @return         integer                Number of seconds for timezone adjustment
     *    @throws        PHPExcel_Exception
     */
    public static function getTimeZoneAdjustment($timezone, $timestamp)
    {
        if ($timezone !== null) {
            if (!self::_validateTimezone($timezone)) {
                throw new PHPExcel_Exception("Invalid timezone " . $timezone);
            }
        } else {
            $timezone = self::$timezone;
        }

        if ($timezone == 'UST') {
            return 0;
        }

        $objTimezone = new DateTimeZone($timezone);
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $transitions = $objTimezone->getTransitions($timestamp, $timestamp);
        } else {
            $transitions = self::getTimezoneTransitions($objTimezone, $timestamp);
        }

        return (count($transitions) > 0) ? $transitions[0]['offset'] : 0;
    }
}
