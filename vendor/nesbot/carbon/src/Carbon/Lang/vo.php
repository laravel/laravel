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

    'year' => ':count yel',
    'y' => ':count yel',
    'a_year' => ':count yel',

    'month' => ':count mul',
    'm' => ':count mul',
    'a_month' => ':count mul',

    'week' => ':count vig',
    'w' => ':count vig',
    'a_week' => ':count vig',

    'day' => ':count del',
    'd' => ':count del',
    'a_day' => ':count del',

    'hour' => ':count düp',
    'h' => ':count düp',
    'a_hour' => ':count düp',

    'minute' => ':count minut',
    'min' => ':count minut',
    'a_minute' => ':count minut',

    'second' => ':count sekun',
    's' => ':count sekun',
    'a_second' => ':count sekun',
]);
