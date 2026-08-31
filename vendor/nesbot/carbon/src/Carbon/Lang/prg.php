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
    'months' => ['M01', 'M02', 'M03', 'M04', 'M05', 'M06', 'M07', 'M08', 'M09', 'M10', 'M11', 'M12'],
    'months_short' => ['M01', 'M02', 'M03', 'M04', 'M05', 'M06', 'M07', 'M08', 'M09', 'M10', 'M11', 'M12'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY-MM-dd',
        'LL' => 'YYYY MMM D',
        'LLL' => 'YYYY MMMM D HH:mm',
        'LLLL' => 'YYYY MMMM D, dddd HH:mm',
    ],

    'year' => ':count meta',
    'y' => ':count meta',
    'a_year' => ':count meta',

    'month' => ':count mēniks', // less reliable
    'm' => ':count mēniks', // less reliable
    'a_month' => ':count mēniks', // less reliable

    'week' => ':count sawaītin', // less reliable
    'w' => ':count sawaītin', // less reliable
    'a_week' => ':count sawaītin', // less reliable

    'day' => ':count di',
    'd' => ':count di',
    'a_day' => ':count di',

    'hour' => ':count bruktēt', // less reliable
    'h' => ':count bruktēt', // less reliable
    'a_hour' => ':count bruktēt', // less reliable

    'minute' => ':count līkuts', // less reliable
    'min' => ':count līkuts', // less reliable
    'a_minute' => ':count līkuts', // less reliable

    'second' => ':count kitan', // less reliable
    's' => ':count kitan', // less reliable
    'a_second' => ':count kitan', // less reliable
]);
