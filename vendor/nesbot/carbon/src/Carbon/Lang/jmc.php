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
    'meridiem' => ['utuko', 'kyiukonyi'],
    'weekdays' => ['Jumapilyi', 'Jumatatuu', 'Jumanne', 'Jumatanu', 'Alhamisi', 'Ijumaa', 'Jumamosi'],
    'weekdays_short' => ['Jpi', 'Jtt', 'Jnn', 'Jtn', 'Alh', 'Iju', 'Jmo'],
    'weekdays_min' => ['Jpi', 'Jtt', 'Jnn', 'Jtn', 'Alh', 'Iju', 'Jmo'],
    'months' => ['Januari', 'Februari', 'Machi', 'Aprilyi', 'Mei', 'Junyi', 'Julyai', 'Agusti', 'Septemba', 'Oktoba', 'Novemba', 'Desemba'],
    'months_short' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Sep', 'Okt', 'Nov', 'Des'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
