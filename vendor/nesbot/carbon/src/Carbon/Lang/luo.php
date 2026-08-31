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
    'meridiem' => ['OD', 'OT'],
    'weekdays' => ['Jumapil', 'Wuok Tich', 'Tich Ariyo', 'Tich Adek', 'Tich Ang’wen', 'Tich Abich', 'Ngeso'],
    'weekdays_short' => ['JMP', 'WUT', 'TAR', 'TAD', 'TAN', 'TAB', 'NGS'],
    'weekdays_min' => ['JMP', 'WUT', 'TAR', 'TAD', 'TAN', 'TAB', 'NGS'],
    'months' => ['Dwe mar Achiel', 'Dwe mar Ariyo', 'Dwe mar Adek', 'Dwe mar Ang’wen', 'Dwe mar Abich', 'Dwe mar Auchiel', 'Dwe mar Abiriyo', 'Dwe mar Aboro', 'Dwe mar Ochiko', 'Dwe mar Apar', 'Dwe mar gi achiel', 'Dwe mar Apar gi ariyo'],
    'months_short' => ['DAC', 'DAR', 'DAD', 'DAN', 'DAH', 'DAU', 'DAO', 'DAB', 'DOC', 'DAP', 'DGI', 'DAG'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],

    'year' => 'higni :count',
    'y' => 'higni :count',
    'a_year' => ':higni :count',

    'month' => 'dweche :count',
    'm' => 'dweche :count',
    'a_month' => 'dweche :count',

    'week' => 'jumbe :count',
    'w' => 'jumbe :count',
    'a_week' => 'jumbe :count',

    'day' => 'ndalo :count',
    'd' => 'ndalo :count',
    'a_day' => 'ndalo :count',

    'hour' => 'seche :count',
    'h' => 'seche :count',
    'a_hour' => 'seche :count',

    'minute' => 'dakika :count',
    'min' => 'dakika :count',
    'a_minute' => 'dakika :count',

    'second' => 'nus dakika :count',
    's' => 'nus dakika :count',
    'a_second' => 'nus dakika :count',
]);
