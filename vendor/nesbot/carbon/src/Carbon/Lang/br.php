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
 * - JD Isaacks
 */
return [
    'year' => '{1}:count bloaz|{3,4,5,9}:count bloaz|[0,Inf[:count vloaz',
    'a_year' => '{1}ur bloaz|{3,4,5,9}:count bloaz|[0,Inf[:count vloaz',
    'month' => '{1}:count miz|{2}:count viz|[0,Inf[:count miz',
    'a_month' => '{1}ur miz|{2}:count viz|[0,Inf[:count miz',
    'week' => ':count sizhun',
    'a_week' => '{1}ur sizhun|:count sizhun',
    'day' => '{1}:count devezh|{2}:count zevezh|[0,Inf[:count devezh',
    'a_day' => '{1}un devezh|{2}:count zevezh|[0,Inf[:count devezh',
    'hour' => ':count eur',
    'a_hour' => '{1}un eur|:count eur',
    'minute' => '{1}:count vunutenn|{2}:count vunutenn|[0,Inf[:count munutenn',
    'a_minute' => '{1}ur vunutenn|{2}:count vunutenn|[0,Inf[:count munutenn',
    'second' => ':count eilenn',
    'a_second' => '{1}un nebeud segondennoù|[0,Inf[:count eilenn',
    'ago' => ':time \'zo',
    'from_now' => 'a-benn :time',
    'diff_now' => 'bremañ',
    'diff_today' => 'Hiziv',
    'diff_today_regexp' => 'Hiziv(?:\\s+da)?',
    'diff_yesterday' => 'decʼh',
    'diff_yesterday_regexp' => 'Dec\'h(?:\\s+da)?',
    'diff_tomorrow' => 'warcʼhoazh',
    'diff_tomorrow_regexp' => 'Warc\'hoazh(?:\\s+da)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D [a viz] MMMM YYYY',
        'LLL' => 'D [a viz] MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D [a viz] MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Hiziv da] LT',
        'nextDay' => '[Warc\'hoazh da] LT',
        'nextWeek' => 'dddd [da] LT',
        'lastDay' => '[Dec\'h da] LT',
        'lastWeek' => 'dddd [paset da] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static fn ($number) => $number.($number === 1 ? 'añ' : 'vet'),
    'months' => ['Genver', 'C\'hwevrer', 'Meurzh', 'Ebrel', 'Mae', 'Mezheven', 'Gouere', 'Eost', 'Gwengolo', 'Here', 'Du', 'Kerzu'],
    'months_short' => ['Gen', 'C\'hwe', 'Meu', 'Ebr', 'Mae', 'Eve', 'Gou', 'Eos', 'Gwe', 'Her', 'Du', 'Ker'],
    'weekdays' => ['Sul', 'Lun', 'Meurzh', 'Merc\'her', 'Yaou', 'Gwener', 'Sadorn'],
    'weekdays_short' => ['Sul', 'Lun', 'Meu', 'Mer', 'Yao', 'Gwe', 'Sad'],
    'weekdays_min' => ['Su', 'Lu', 'Me', 'Mer', 'Ya', 'Gw', 'Sa'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' hag '],
    'meridiem' => ['A.M.', 'G.M.'],

    'y' => ':count bl.',
    'd' => ':count d',
    'h' => ':count e',
    'min' => ':count min',
    's' => ':count s',
];
