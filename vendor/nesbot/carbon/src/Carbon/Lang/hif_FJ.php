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
    'first_day_of_week' => 0,
    'formats' => [
        'L' => 'dddd DD MMM YYYY',
    ],
    'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    'weekdays' => ['Ravivar', 'Somvar', 'Mangalvar', 'Budhvar', 'Guruvar', 'Shukravar', 'Shanivar'],
    'weekdays_short' => ['Ravi', 'Som', 'Mangal', 'Budh', 'Guru', 'Shukra', 'Shani'],
    'weekdays_min' => ['Ravi', 'Som', 'Mangal', 'Budh', 'Guru', 'Shukra', 'Shani'],
    'meridiem' => ['Purvahan', 'Aparaahna'],

    'hour' => ':count minit', // less reliable
    'h' => ':count minit', // less reliable
    'a_hour' => ':count minit', // less reliable

    'year' => ':count saal',
    'y' => ':count saal',
    'a_year' => ':count saal',

    'month' => ':count Mahina',
    'm' => ':count Mahina',
    'a_month' => ':count Mahina',

    'week' => ':count Hafta',
    'w' => ':count Hafta',
    'a_week' => ':count Hafta',

    'day' => ':count Din',
    'd' => ':count Din',
    'a_day' => ':count Din',

    'minute' => ':count Minit',
    'min' => ':count Minit',
    'a_minute' => ':count Minit',

    'second' => ':count Second',
    's' => ':count Second',
    'a_second' => ':count Second',
]);
