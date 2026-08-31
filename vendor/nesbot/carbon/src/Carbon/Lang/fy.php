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
 * - Tim Fish
 * - JD Isaacks
 */
return [
    'year' => ':count jier|:count jierren',
    'a_year' => 'ien jier|:count jierren',
    'y' => ':count j',
    'month' => ':count moanne|:count moannen',
    'a_month' => 'ien moanne|:count moannen',
    'm' => ':count moa.',
    'week' => ':count wike|:count wiken',
    'a_week' => 'in wike|:count wiken',
    'a' => ':count w.',
    'day' => ':count dei|:count dagen',
    'a_day' => 'ien dei|:count dagen',
    'd' => ':count d.',
    'hour' => ':count oere|:count oeren',
    'a_hour' => 'ien oere|:count oeren',
    'h' => ':count o.',
    'minute' => ':count minút|:count minuten',
    'a_minute' => 'ien minút|:count minuten',
    'min' => ':count min.',
    'second' => ':count sekonde|:count sekonden',
    'a_second' => 'in pear sekonden|:count sekonden',
    's' => ':count s.',
    'ago' => ':time lyn',
    'from_now' => 'oer :time',
    'diff_yesterday' => 'juster',
    'diff_yesterday_regexp' => 'juster(?:\\s+om)?',
    'diff_today' => 'hjoed',
    'diff_today_regexp' => 'hjoed(?:\\s+om)?',
    'diff_tomorrow' => 'moarn',
    'diff_tomorrow_regexp' => 'moarn(?:\\s+om)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD-MM-YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[hjoed om] LT',
        'nextDay' => '[moarn om] LT',
        'nextWeek' => 'dddd [om] LT',
        'lastDay' => '[juster om] LT',
        'lastWeek' => '[ôfrûne] dddd [om] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number) {
        return $number.(($number === 1 || $number === 8 || $number >= 20) ? 'ste' : 'de');
    },
    'months' => ['jannewaris', 'febrewaris', 'maart', 'april', 'maaie', 'juny', 'july', 'augustus', 'septimber', 'oktober', 'novimber', 'desimber'],
    'months_short' => ['jan', 'feb', 'mrt', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'],
    'mmm_suffix' => '.',
    'weekdays' => ['snein', 'moandei', 'tiisdei', 'woansdei', 'tongersdei', 'freed', 'sneon'],
    'weekdays_short' => ['si.', 'mo.', 'ti.', 'wo.', 'to.', 'fr.', 'so.'],
    'weekdays_min' => ['Si', 'Mo', 'Ti', 'Wo', 'To', 'Fr', 'So'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' en '],
];
