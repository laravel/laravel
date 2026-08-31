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
    'weekdays' => ['Jumapiri', 'Jumatatu', 'Jumanne', 'Jumatano', 'Murwa wa Kanne', 'Murwa wa Katano', 'Jumamosi'],
    'weekdays_short' => ['J2', 'J3', 'J4', 'J5', 'Al', 'Ij', 'J1'],
    'weekdays_min' => ['J2', 'J3', 'J4', 'J5', 'Al', 'Ij', 'J1'],
    'months' => ['Januari', 'Februari', 'Machi', 'Aprili', 'Mei', 'Juni', 'Julai', 'Agosti', 'Septemba', 'Oktoba', 'Novemba', 'Desemba'],
    'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Sep', 'Okt', 'Nov', 'Des'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],

    // Too unreliable
    /*
    'year' => ':count liliino', // less reliable
    'y' => ':count liliino', // less reliable
    'a_year' => ':count liliino', // less reliable

    'month' => ':count kumwesi', // less reliable
    'm' => ':count kumwesi', // less reliable
    'a_month' => ':count kumwesi', // less reliable

    'week' => ':count olutambi', // less reliable
    'w' => ':count olutambi', // less reliable
    'a_week' => ':count olutambi', // less reliable

    'day' => ':count luno', // less reliable
    'd' => ':count luno', // less reliable
    'a_day' => ':count luno', // less reliable

    'hour' => ':count ekengele', // less reliable
    'h' => ':count ekengele', // less reliable
    'a_hour' => ':count ekengele', // less reliable

    'minute' => ':count omundu', // less reliable
    'min' => ':count omundu', // less reliable
    'a_minute' => ':count omundu', // less reliable

    'second' => ':count liliino', // less reliable
    's' => ':count liliino', // less reliable
    'a_second' => ':count liliino', // less reliable
    */
]);
