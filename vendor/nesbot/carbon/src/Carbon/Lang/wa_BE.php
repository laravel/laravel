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
 * - Djan SACRE Pablo Saratxaga pablo@mandrakesoft.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['di djanvî', 'di fevrî', 'di måss', 'd’ avri', 'di may', 'di djun', 'di djulete', 'd’ awousse', 'di setimbe', 'd’ octôbe', 'di nôvimbe', 'di decimbe'],
    'months_short' => ['dja', 'fev', 'mås', 'avr', 'may', 'djn', 'djl', 'awo', 'set', 'oct', 'nôv', 'dec'],
    'weekdays' => ['dimegne', 'londi', 'mårdi', 'mierkidi', 'djudi', 'vénrdi', 'semdi'],
    'weekdays_short' => ['dim', 'lon', 'mår', 'mie', 'dju', 'vén', 'sem'],
    'weekdays_min' => ['dim', 'lon', 'mår', 'mie', 'dju', 'vén', 'sem'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,

    'year' => ':count anêye',
    'y' => ':count anêye',
    'a_year' => ':count anêye',

    'month' => ':count meûs',
    'm' => ':count meûs',
    'a_month' => ':count meûs',

    'week' => ':count samwinne',
    'w' => ':count samwinne',
    'a_week' => ':count samwinne',

    'day' => ':count djoû',
    'd' => ':count djoû',
    'a_day' => ':count djoû',

    'hour' => ':count eure',
    'h' => ':count eure',
    'a_hour' => ':count eure',

    'minute' => ':count munute',
    'min' => ':count munute',
    'a_minute' => ':count munute',

    'second' => ':count Sigonde',
    's' => ':count Sigonde',
    'a_second' => ':count Sigonde',
]);
