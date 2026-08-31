<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Authors:
 * - François B
 * - JD Isaacks
 * - Daniel Monaghan
 */
return [
    'year' => '{1}:count flwyddyn|[-Inf,Inf]:count flynedd',
    'a_year' => '{1}blwyddyn|[-Inf,Inf]:count flynedd',
    'y' => ':countbl',
    'month' => ':count mis',
    'a_month' => '{1}mis|[-Inf,Inf]:count mis',
    'm' => ':countmi',
    'week' => ':count wythnos',
    'a_week' => '{1}wythnos|[-Inf,Inf]:count wythnos',
    'w' => ':countw',
    'day' => ':count diwrnod',
    'a_day' => '{1}diwrnod|[-Inf,Inf]:count diwrnod',
    'd' => ':countd',
    'hour' => ':count awr',
    'a_hour' => '{1}awr|[-Inf,Inf]:count awr',
    'h' => ':counth',
    'minute' => ':count munud',
    'a_minute' => '{1}munud|[-Inf,Inf]:count munud',
    'min' => ':countm',
    'second' => ':count eiliad',
    'a_second' => '{0,1}ychydig eiliadau|[-Inf,Inf]:count eiliad',
    's' => ':counts',
    'ago' => ':time yn ôl',
    'from_now' => 'mewn :time',
    'after' => ':time ar ôl',
    'before' => ':time o\'r blaen',
    'diff_now' => 'nawr',
    'diff_today' => 'Heddiw',
    'diff_today_regexp' => 'Heddiw(?:\\s+am)?',
    'diff_yesterday' => 'ddoe',
    'diff_yesterday_regexp' => 'Ddoe(?:\\s+am)?',
    'diff_tomorrow' => 'yfory',
    'diff_tomorrow_regexp' => 'Yfory(?:\\s+am)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Heddiw am] LT',
        'nextDay' => '[Yfory am] LT',
        'nextWeek' => 'dddd [am] LT',
        'lastDay' => '[Ddoe am] LT',
        'lastWeek' => 'dddd [diwethaf am] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number) {
        return $number.(
            $number > 20
                ? (\in_array((int) $number, [40, 50, 60, 80, 100], true) ? 'fed' : 'ain')
                : ([
                    '', 'af', 'il', 'ydd', 'ydd', 'ed', 'ed', 'ed', 'fed', 'fed', 'fed', // 1af to 10fed
                    'eg', 'fed', 'eg', 'eg', 'fed', 'eg', 'eg', 'fed', 'eg', 'fed', // 11eg to 20fed
                ])[$number] ?? ''
        );
    },
    'months' => ['Ionawr', 'Chwefror', 'Mawrth', 'Ebrill', 'Mai', 'Mehefin', 'Gorffennaf', 'Awst', 'Medi', 'Hydref', 'Tachwedd', 'Rhagfyr'],
    'months_short' => ['Ion', 'Chwe', 'Maw', 'Ebr', 'Mai', 'Meh', 'Gor', 'Aws', 'Med', 'Hyd', 'Tach', 'Rhag'],
    'weekdays' => ['Dydd Sul', 'Dydd Llun', 'Dydd Mawrth', 'Dydd Mercher', 'Dydd Iau', 'Dydd Gwener', 'Dydd Sadwrn'],
    'weekdays_short' => ['Sul', 'Llun', 'Maw', 'Mer', 'Iau', 'Gwe', 'Sad'],
    'weekdays_min' => ['Su', 'Ll', 'Ma', 'Me', 'Ia', 'Gw', 'Sa'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' a '],
    'meridiem' => ['yb', 'yh'],
];
