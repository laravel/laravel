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
 * - Samsung Electronics Co., Ltd.    akhilesh.k@samsung.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['Janueri', 'Februeri', 'Mas', 'Epril', 'Me', 'Jun', 'Julai', 'Ogas', 'Septemba', 'Oktoba', 'Novemba', 'Desemba'],
    'months_short' => ['Jan', 'Feb', 'Mas', 'Epr', 'Me', 'Jun', 'Jul', 'Oga', 'Sep', 'Okt', 'Nov', 'Des'],
    'weekdays' => ['Sande', 'Mande', 'Tunde', 'Trinde', 'Fonde', 'Fraide', 'Sarere'],
    'weekdays_short' => ['San', 'Man', 'Tun', 'Tri', 'Fon', 'Fra', 'Sar'],
    'weekdays_min' => ['San', 'Man', 'Tun', 'Tri', 'Fon', 'Fra', 'Sar'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['biknait', 'apinun'],

    'year' => 'yia :count',
    'y' => 'yia :count',
    'a_year' => 'yia :count',

    'month' => ':count mun',
    'm' => ':count mun',
    'a_month' => ':count mun',

    'week' => ':count wik',
    'w' => ':count wik',
    'a_week' => ':count wik',

    'day' => ':count de',
    'd' => ':count de',
    'a_day' => ':count de',

    'hour' => ':count aua',
    'h' => ':count aua',
    'a_hour' => ':count aua',

    'minute' => ':count minit',
    'min' => ':count minit',
    'a_minute' => ':count minit',

    'second' => ':count namba tu',
    's' => ':count namba tu',
    'a_second' => ':count namba tu',
]);
