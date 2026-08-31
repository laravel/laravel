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
        'L' => 'DD/MM/YY',
    ],
    'months' => ['zanvie', 'fevriye', 'mars', 'avril', 'me', 'zin', 'zilye', 'out', 'septam', 'oktob', 'novam', 'desam'],
    'months_short' => ['zan', 'fev', 'mar', 'avr', 'me', 'zin', 'zil', 'out', 'sep', 'okt', 'nov', 'des'],
    'weekdays' => ['dimans', 'lindi', 'mardi', 'merkredi', 'zedi', 'vandredi', 'samdi'],
    'weekdays_short' => ['dim', 'lin', 'mar', 'mer', 'ze', 'van', 'sam'],
    'weekdays_min' => ['dim', 'lin', 'mar', 'mer', 'ze', 'van', 'sam'],

    'year' => ':count banané',
    'y' => ':count banané',
    'a_year' => ':count banané',

    'month' => ':count mwa',
    'm' => ':count mwa',
    'a_month' => ':count mwa',

    'week' => ':count sémenn',
    'w' => ':count sémenn',
    'a_week' => ':count sémenn',

    'day' => ':count zour',
    'd' => ':count zour',
    'a_day' => ':count zour',

    'hour' => ':count -er-tan',
    'h' => ':count -er-tan',
    'a_hour' => ':count -er-tan',

    'minute' => ':count minitt',
    'min' => ':count minitt',
    'a_minute' => ':count minitt',

    'second' => ':count déziém',
    's' => ':count déziém',
    'a_second' => ':count déziém',
]);
