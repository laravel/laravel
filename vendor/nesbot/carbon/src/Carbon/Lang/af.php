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
 * - Pierre du Plessis
 */
return [
    'year' => ':count jaar',
    'a_year' => '\'n jaar|:count jaar',
    'y' => ':count j.',
    'month' => ':count maand|:count maande',
    'a_month' => '\'n maand|:count maande',
    'm' => ':count maa.',
    'week' => ':count week|:count weke',
    'a_week' => '\'n week|:count weke',
    'w' => ':count w.',
    'day' => ':count dag|:count dae',
    'a_day' => '\'n dag|:count dae',
    'd' => ':count d.',
    'hour' => ':count uur',
    'a_hour' => '\'n uur|:count uur',
    'h' => ':count u.',
    'minute' => ':count minuut|:count minute',
    'a_minute' => '\'n minuut|:count minute',
    'min' => ':count min.',
    'second' => ':count sekond|:count sekondes',
    'a_second' => '\'n paar sekondes|:count sekondes',
    's' => ':count s.',
    'ago' => ':time gelede',
    'from_now' => 'oor :time',
    'after' => ':time na',
    'before' => ':time voor',
    'diff_now' => 'Nou',
    'diff_today' => 'Vandag',
    'diff_today_regexp' => 'Vandag(?:\\s+om)?',
    'diff_yesterday' => 'Gister',
    'diff_yesterday_regexp' => 'Gister(?:\\s+om)?',
    'diff_tomorrow' => 'Môre',
    'diff_tomorrow_regexp' => 'Môre(?:\\s+om)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Vandag om] LT',
        'nextDay' => '[Môre om] LT',
        'nextWeek' => 'dddd [om] LT',
        'lastDay' => '[Gister om] LT',
        'lastWeek' => '[Laas] dddd [om] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static fn ($number) => $number.(($number === 1 || $number === 8 || $number >= 20) ? 'ste' : 'de'),
    'meridiem' => ['VM', 'NM'],
    'months' => ['Januarie', 'Februarie', 'Maart', 'April', 'Mei', 'Junie', 'Julie', 'Augustus', 'September', 'Oktober', 'November', 'Desember'],
    'months_short' => ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
    'weekdays' => ['Sondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrydag', 'Saterdag'],
    'weekdays_short' => ['Son', 'Maa', 'Din', 'Woe', 'Don', 'Vry', 'Sat'],
    'weekdays_min' => ['So', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Sa'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' en '],
];
