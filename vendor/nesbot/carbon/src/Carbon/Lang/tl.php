<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'year' => ':count taon',
    'a_year' => '{1}isang taon|:count taon',
    'month' => ':count buwan',
    'a_month' => '{1}isang buwan|:count buwan',
    'week' => ':count linggo',
    'a_week' => '{1}isang linggo|:count linggo',
    'day' => ':count araw',
    'a_day' => '{1}isang araw|:count araw',
    'hour' => ':count oras',
    'a_hour' => '{1}isang oras|:count oras',
    'minute' => ':count minuto',
    'a_minute' => '{1}isang minuto|:count minuto',
    'min' => ':count min.',
    'second' => ':count segundo',
    'a_second' => '{1}ilang segundo|:count segundo',
    's' => ':count seg.',
    'ago' => ':time ang nakalipas',
    'from_now' => 'sa loob ng :time',
    'diff_now' => 'ngayon',
    'diff_today' => 'ngayong',
    'diff_today_regexp' => 'ngayong(?:\\s+araw)?',
    'diff_yesterday' => 'kahapon',
    'diff_tomorrow' => 'bukas',
    'diff_tomorrow_regexp' => 'Bukas(?:\\s+ng)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'MM/D/YYYY',
        'LL' => 'MMMM D, YYYY',
        'LLL' => 'MMMM D, YYYY HH:mm',
        'LLLL' => 'dddd, MMMM DD, YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => 'LT [ngayong araw]',
        'nextDay' => '[Bukas ng] LT',
        'nextWeek' => 'LT [sa susunod na] dddd',
        'lastDay' => 'LT [kahapon]',
        'lastWeek' => 'LT [noong nakaraang] dddd',
        'sameElse' => 'L',
    ],
    'months' => ['Enero', 'Pebrero', 'Marso', 'Abril', 'Mayo', 'Hunyo', 'Hulyo', 'Agosto', 'Setyembre', 'Oktubre', 'Nobyembre', 'Disyembre'],
    'months_short' => ['Ene', 'Peb', 'Mar', 'Abr', 'May', 'Hun', 'Hul', 'Ago', 'Set', 'Okt', 'Nob', 'Dis'],
    'weekdays' => ['Linggo', 'Lunes', 'Martes', 'Miyerkules', 'Huwebes', 'Biyernes', 'Sabado'],
    'weekdays_short' => ['Lin', 'Lun', 'Mar', 'Miy', 'Huw', 'Biy', 'Sab'],
    'weekdays_min' => ['Li', 'Lu', 'Ma', 'Mi', 'Hu', 'Bi', 'Sab'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' at '],
];
