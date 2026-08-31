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
    'months' => ['Ianuali', 'Pepeluali', 'Malaki', 'ʻApelila', 'Mei', 'Iune', 'Iulai', 'ʻAukake', 'Kepakemapa', 'ʻOkakopa', 'Nowemapa', 'Kekemapa'],
    'months_short' => ['Ian.', 'Pep.', 'Mal.', 'ʻAp.', 'Mei', 'Iun.', 'Iul.', 'ʻAu.', 'Kep.', 'ʻOk.', 'Now.', 'Kek.'],
    'weekdays' => ['Lāpule', 'Poʻakahi', 'Poʻalua', 'Poʻakolu', 'Poʻahā', 'Poʻalima', 'Poʻaono'],
    'weekdays_short' => ['LP', 'P1', 'P2', 'P3', 'P4', 'P5', 'P6'],
    'weekdays_min' => ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
    'formats' => [
        'LT' => 'h:mm a',
        'LTS' => 'h:mm:ss a',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY h:mm a',
        'LLLL' => 'dddd, D MMMM YYYY h:mm a',
    ],

    'year' => ':count makahiki',
    'y' => ':count makahiki',
    'a_year' => ':count makahiki',

    'month' => ':count mahina',
    'm' => ':count mahina',
    'a_month' => ':count mahina',

    'week' => ':count pule',
    'w' => ':count pule',
    'a_week' => ':count pule',

    'day' => ':count lā',
    'd' => ':count lā',
    'a_day' => ':count lā',

    'hour' => ':count hola',
    'h' => ':count hola',
    'a_hour' => ':count hola',

    'minute' => ':count minuke',
    'min' => ':count minuke',
    'a_minute' => ':count minuke',

    'second' => ':count lua',
    's' => ':count lua',
    'a_second' => ':count lua',
]);
