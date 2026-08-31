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
 * - Information from Michael Wolf Andrzej Krzysztofowicz ankry@mif.pg.gda.pl
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'DD. MMMM YYYY',
        'LLL' => 'DD. MMMM, HH:mm [hodź.]',
        'LLLL' => 'dddd, DD. MMMM YYYY, HH:mm [hodź.]',
    ],
    'months' => ['januara', 'februara', 'měrca', 'apryla', 'meje', 'junija', 'julija', 'awgusta', 'septembra', 'oktobra', 'nowembra', 'decembra'],
    'months_short' => ['Jan', 'Feb', 'Měr', 'Apr', 'Mej', 'Jun', 'Jul', 'Awg', 'Sep', 'Okt', 'Now', 'Dec'],
    'weekdays' => ['Njedźela', 'Póndźela', 'Wutora', 'Srjeda', 'Štvórtk', 'Pjatk', 'Sobota'],
    'weekdays_short' => ['Nj', 'Pó', 'Wu', 'Sr', 'Št', 'Pj', 'So'],
    'weekdays_min' => ['Nj', 'Pó', 'Wu', 'Sr', 'Št', 'Pj', 'So'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,

    'year' => ':count lěto',
    'y' => ':count lěto',
    'a_year' => ':count lěto',

    'month' => ':count měsac',
    'm' => ':count měsac',
    'a_month' => ':count měsac',

    'week' => ':count tydźeń',
    'w' => ':count tydźeń',
    'a_week' => ':count tydźeń',

    'day' => ':count dźeń',
    'd' => ':count dźeń',
    'a_day' => ':count dźeń',

    'hour' => ':count hodźina',
    'h' => ':count hodźina',
    'a_hour' => ':count hodźina',

    'minute' => ':count chwila',
    'min' => ':count chwila',
    'a_minute' => ':count chwila',

    'second' => ':count druhi',
    's' => ':count druhi',
    'a_second' => ':count druhi',
]);
