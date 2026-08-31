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
 * - Serhan Apaydın
 * - Dominika
 */
return [
    'year' => '{1}wa’ DIS|:count DIS',
    'month' => '{1}wa’ jar|:count jar',
    'week' => '{1}wa’ hogh|:count hogh',
    'day' => '{1}wa’ jaj|:count jaj',
    'hour' => '{1}wa’ rep|:count rep',
    'minute' => '{1}wa’ tup|:count tup',
    'second' => '{1}puS lup|:count lup',
    'ago' => static function ($time) {
        $output = strtr($time, [
            'jaj' => 'Hu’',
            'jar' => 'wen',
            'DIS' => 'ben',
        ]);

        return $output === $time ? "$time ret" : $output;
    },
    'from_now' => static function ($time) {
        $output = strtr($time, [
            'jaj' => 'leS',
            'jar' => 'waQ',
            'DIS' => 'nem',
        ]);

        return $output === $time ? "$time pIq" : $output;
    },
    'diff_yesterday' => 'wa’Hu’',
    'diff_today' => 'DaHjaj',
    'diff_tomorrow' => 'wa’leS',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[DaHjaj] LT',
        'nextDay' => '[wa’leS] LT',
        'nextWeek' => 'LLL',
        'lastDay' => '[wa’Hu’] LT',
        'lastWeek' => 'LLL',
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['tera’ jar wa’', 'tera’ jar cha’', 'tera’ jar wej', 'tera’ jar loS', 'tera’ jar vagh', 'tera’ jar jav', 'tera’ jar Soch', 'tera’ jar chorgh', 'tera’ jar Hut', 'tera’ jar wa’maH', 'tera’ jar wa’maH wa’', 'tera’ jar wa’maH cha’'],
    'months_short' => ['jar wa’', 'jar cha’', 'jar wej', 'jar loS', 'jar vagh', 'jar jav', 'jar Soch', 'jar chorgh', 'jar Hut', 'jar wa’maH', 'jar wa’maH wa’', 'jar wa’maH cha’'],
    'weekdays' => ['lojmItjaj', 'DaSjaj', 'povjaj', 'ghItlhjaj', 'loghjaj', 'buqjaj', 'ghInjaj'],
    'weekdays_short' => ['lojmItjaj', 'DaSjaj', 'povjaj', 'ghItlhjaj', 'loghjaj', 'buqjaj', 'ghInjaj'],
    'weekdays_min' => ['lojmItjaj', 'DaSjaj', 'povjaj', 'ghItlhjaj', 'loghjaj', 'buqjaj', 'ghInjaj'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' ’ej '],
];
