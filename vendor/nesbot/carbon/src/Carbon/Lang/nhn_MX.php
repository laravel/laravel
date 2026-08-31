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
 * - RAP    libc-alpha@sourceware.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YY',
    ],
    'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
    'months_short' => ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'],
    'weekdays' => ['teoilhuitl', 'ceilhuitl', 'omeilhuitl', 'yeilhuitl', 'nahuilhuitl', 'macuililhuitl', 'chicuaceilhuitl'],
    'weekdays_short' => ['teo', 'cei', 'ome', 'yei', 'nau', 'mac', 'chi'],
    'weekdays_min' => ['teo', 'cei', 'ome', 'yei', 'nau', 'mac', 'chi'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'month' => ':count metztli', // less reliable
    'm' => ':count metztli', // less reliable
    'a_month' => ':count metztli', // less reliable

    'week' => ':count tonalli', // less reliable
    'w' => ':count tonalli', // less reliable
    'a_week' => ':count tonalli', // less reliable

    'day' => ':count tonatih', // less reliable
    'd' => ':count tonatih', // less reliable
    'a_day' => ':count tonatih', // less reliable

    'minute' => ':count toltecayotl', // less reliable
    'min' => ':count toltecayotl', // less reliable
    'a_minute' => ':count toltecayotl', // less reliable

    'second' => ':count ome', // less reliable
    's' => ':count ome', // less reliable
    'a_second' => ':count ome', // less reliable

    'year' => ':count xihuitl',
    'y' => ':count xihuitl',
    'a_year' => ':count xihuitl',
]);
