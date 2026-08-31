<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/en.php', [
    'first_day_of_week' => 0,
    'meridiem' => ['RŨ', 'ŨG'],
    'weekdays' => ['Kiumia', 'Muramuko', 'Wairi', 'Wethatu', 'Wena', 'Wetano', 'Jumamosi'],
    'weekdays_short' => ['KIU', 'MRA', 'WAI', 'WET', 'WEN', 'WTN', 'JUM'],
    'weekdays_min' => ['KIU', 'MRA', 'WAI', 'WET', 'WEN', 'WTN', 'JUM'],
    'months' => ['Januarĩ', 'Feburuarĩ', 'Machi', 'Ĩpurũ', 'Mĩĩ', 'Njuni', 'Njuraĩ', 'Agasti', 'Septemba', 'Oktũba', 'Novemba', 'Dicemba'],
    'months_short' => ['JAN', 'FEB', 'MAC', 'ĨPU', 'MĨĨ', 'NJU', 'NJR', 'AGA', 'SPT', 'OKT', 'NOV', 'DEC'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],

    'year' => ':count murume', // less reliable
    'y' => ':count murume', // less reliable
    'a_year' => ':count murume', // less reliable

    'month' => ':count muchaara', // less reliable
    'm' => ':count muchaara', // less reliable
    'a_month' => ':count muchaara', // less reliable

    'minute' => ':count monto', // less reliable
    'min' => ':count monto', // less reliable
    'a_minute' => ':count monto', // less reliable

    'second' => ':count gikeno', // less reliable
    's' => ':count gikeno', // less reliable
    'a_second' => ':count gikeno', // less reliable
]);
