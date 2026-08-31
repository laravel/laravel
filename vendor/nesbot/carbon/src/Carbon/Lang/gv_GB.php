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
 * - Alastair McKinstry    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YY',
    ],
    'months' => ['Jerrey-geuree', 'Toshiaght-arree', 'Mayrnt', 'Averil', 'Boaldyn', 'Mean-souree', 'Jerrey-souree', 'Luanistyn', 'Mean-fouyir', 'Jerrey-fouyir', 'Mee Houney', 'Mee ny Nollick'],
    'months_short' => ['J-guer', 'T-arree', 'Mayrnt', 'Avrril', 'Boaldyn', 'M-souree', 'J-souree', 'Luanistyn', 'M-fouyir', 'J-fouyir', 'M.Houney', 'M.Nollick'],
    'weekdays' => ['Jedoonee', 'Jelhein', 'Jemayrt', 'Jercean', 'Jerdein', 'Jeheiney', 'Jesarn'],
    'weekdays_short' => ['Jed', 'Jel', 'Jem', 'Jerc', 'Jerd', 'Jeh', 'Jes'],
    'weekdays_min' => ['Jed', 'Jel', 'Jem', 'Jerc', 'Jerd', 'Jeh', 'Jes'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,

    'year' => ':count blein',
    'y' => ':count blein',
    'a_year' => ':count blein',

    'month' => ':count mee',
    'm' => ':count mee',
    'a_month' => ':count mee',

    'week' => ':count shiaghtin',
    'w' => ':count shiaghtin',
    'a_week' => ':count shiaghtin',

    'day' => ':count laa',
    'd' => ':count laa',
    'a_day' => ':count laa',

    'hour' => ':count oor',
    'h' => ':count oor',
    'a_hour' => ':count oor',

    'minute' => ':count feer veg',
    'min' => ':count feer veg',
    'a_minute' => ':count feer veg',

    'second' => ':count derrey',
    's' => ':count derrey',
    'a_second' => ':count derrey',
]);
