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
    'meridiem' => ['ND', 'LK'],
    'weekdays' => ['Bikua-ôko', 'Bïkua-ûse', 'Bïkua-ptâ', 'Bïkua-usïö', 'Bïkua-okü', 'Lâpôsö', 'Lâyenga'],
    'weekdays_short' => ['Bk1', 'Bk2', 'Bk3', 'Bk4', 'Bk5', 'Lâp', 'Lây'],
    'weekdays_min' => ['Bk1', 'Bk2', 'Bk3', 'Bk4', 'Bk5', 'Lâp', 'Lây'],
    'months' => ['Nyenye', 'Fulundïgi', 'Mbängü', 'Ngubùe', 'Bêläwü', 'Föndo', 'Lengua', 'Kükürü', 'Mvuka', 'Ngberere', 'Nabändüru', 'Kakauka'],
    'months_short' => ['Nye', 'Ful', 'Mbä', 'Ngu', 'Bêl', 'Fön', 'Len', 'Kük', 'Mvu', 'Ngb', 'Nab', 'Kak'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM, YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],

    'year' => ':count dā', // less reliable
    'y' => ':count dā', // less reliable
    'a_year' => ':count dā', // less reliable

    'week' => ':count bïkua-okü', // less reliable
    'w' => ':count bïkua-okü', // less reliable
    'a_week' => ':count bïkua-okü', // less reliable

    'day' => ':count ziggawâ', // less reliable
    'd' => ':count ziggawâ', // less reliable
    'a_day' => ':count ziggawâ', // less reliable

    'hour' => ':count yângâködörö', // less reliable
    'h' => ':count yângâködörö', // less reliable
    'a_hour' => ':count yângâködörö', // less reliable

    'second' => ':count bïkua-ôko', // less reliable
    's' => ':count bïkua-ôko', // less reliable
    'a_second' => ':count bïkua-ôko', // less reliable

    'month' => ':count Nze tî ngu',
    'm' => ':count Nze tî ngu',
    'a_month' => ':count Nze tî ngu',
]);
