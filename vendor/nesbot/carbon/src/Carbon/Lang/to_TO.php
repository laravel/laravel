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
 * - International Components for Unicode    akhilesh.k@samsung.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'first_day_of_week' => 0,
    'formats' => [
        'L' => 'dddd DD MMM YYYY',
    ],
    'months' => ['Sānuali', 'Fēpueli', 'Maʻasi', 'ʻEpeleli', 'Mē', 'Sune', 'Siulai', 'ʻAokosi', 'Sepitema', 'ʻOkatopa', 'Nōvema', 'Tīsema'],
    'months_short' => ['Sān', 'Fēp', 'Maʻa', 'ʻEpe', 'Mē', 'Sun', 'Siu', 'ʻAok', 'Sep', 'ʻOka', 'Nōv', 'Tīs'],
    'weekdays' => ['Sāpate', 'Mōnite', 'Tūsite', 'Pulelulu', 'Tuʻapulelulu', 'Falaite', 'Tokonaki'],
    'weekdays_short' => ['Sāp', 'Mōn', 'Tūs', 'Pul', 'Tuʻa', 'Fal', 'Tok'],
    'weekdays_min' => ['Sāp', 'Mōn', 'Tūs', 'Pul', 'Tuʻa', 'Fal', 'Tok'],
    'meridiem' => ['hengihengi', 'efiafi'],

    'year' => ':count fitu', // less reliable
    'y' => ':count fitu', // less reliable
    'a_year' => ':count fitu', // less reliable

    'month' => ':count mahina', // less reliable
    'm' => ':count mahina', // less reliable
    'a_month' => ':count mahina', // less reliable

    'week' => ':count Sapate', // less reliable
    'w' => ':count Sapate', // less reliable
    'a_week' => ':count Sapate', // less reliable

    'day' => ':count ʻaho', // less reliable
    'd' => ':count ʻaho', // less reliable
    'a_day' => ':count ʻaho', // less reliable

    'hour' => ':count houa',
    'h' => ':count houa',
    'a_hour' => ':count houa',

    'minute' => ':count miniti',
    'min' => ':count miniti',
    'a_minute' => ':count miniti',

    'second' => ':count sekoni',
    's' => ':count sekoni',
    'a_second' => ':count sekoni',
]);
