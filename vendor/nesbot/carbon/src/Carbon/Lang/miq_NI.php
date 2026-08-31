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
    'formats' => [
        'L' => 'DD/MM/YY',
    ],
    'months' => ['siakwa kati', 'kuswa kati', 'kakamuk kati', 'lî wainhka kati', 'lih mairin kati', 'lî kati', 'pastara kati', 'sikla kati', 'wîs kati', 'waupasa kati', 'yahbra kati', 'trisu kati'],
    'months_short' => ['siakwa kati', 'kuswa kati', 'kakamuk kati', 'lî wainhka kati', 'lih mairin kati', 'lî kati', 'pastara kati', 'sikla kati', 'wîs kati', 'waupasa kati', 'yahbra kati', 'trisu kati'],
    'weekdays' => ['sandi', 'mundi', 'tiusdi', 'wensde', 'tausde', 'praidi', 'satadi'],
    'weekdays_short' => ['san', 'mun', 'tius', 'wens', 'taus', 'prai', 'sat'],
    'weekdays_min' => ['san', 'mun', 'tius', 'wens', 'taus', 'prai', 'sat'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 7,
    'meridiem' => ['VM', 'NM'],

    'month' => ':count kati', // less reliable
    'm' => ':count kati', // less reliable
    'a_month' => ':count kati', // less reliable
]);
