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
 * - Kamusi Project Martin Benjamin locales@kamusi.org
 */
return array_replace_recursive(require __DIR__.'/sw.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['Januari', 'Februari', 'Machi', 'Aprili', 'Mei', 'Juni', 'Julai', 'Agosti', 'Septemba', 'Oktoba', 'Novemba', 'Desemba'],
    'months_short' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Sep', 'Okt', 'Nov', 'Des'],
    'weekdays' => ['Jumapili', 'Jumatatu', 'Jumanne', 'Jumatano', 'Alhamisi', 'Ijumaa', 'Jumamosi'],
    'weekdays_short' => ['J2', 'J3', 'J4', 'J5', 'Alh', 'Ij', 'J1'],
    'weekdays_min' => ['J2', 'J3', 'J4', 'J5', 'Alh', 'Ij', 'J1'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['asubuhi', 'alasiri'],
]);
