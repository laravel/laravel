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
 * - Information from Michael Wolf    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'DD. MMMM YYYY',
        'LLL' => 'DD. MMMM, HH:mm [góź.]',
        'LLLL' => 'dddd, DD. MMMM YYYY, HH:mm [góź.]',
    ],
    'months' => ['januara', 'februara', 'měrca', 'apryla', 'maja', 'junija', 'julija', 'awgusta', 'septembra', 'oktobra', 'nowembra', 'decembra'],
    'months_short' => ['Jan', 'Feb', 'Měr', 'Apr', 'Maj', 'Jun', 'Jul', 'Awg', 'Sep', 'Okt', 'Now', 'Dec'],
    'weekdays' => ['Njeźela', 'Pónjeźele', 'Wałtora', 'Srjoda', 'Stwórtk', 'Pětk', 'Sobota'],
    'weekdays_short' => ['Nj', 'Pó', 'Wa', 'Sr', 'St', 'Pě', 'So'],
    'weekdays_min' => ['Nj', 'Pó', 'Wa', 'Sr', 'St', 'Pě', 'So'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,

    'year' => ':count lěto',
    'y' => ':count lěto',
    'a_year' => ':count lěto',

    'month' => ':count mjasec',
    'm' => ':count mjasec',
    'a_month' => ':count mjasec',

    'week' => ':count tyźeń',
    'w' => ':count tyźeń',
    'a_week' => ':count tyźeń',

    'day' => ':count źeń',
    'd' => ':count źeń',
    'a_day' => ':count źeń',

    'hour' => ':count góźina',
    'h' => ':count góźina',
    'a_hour' => ':count góźina',

    'minute' => ':count minuta',
    'min' => ':count minuta',
    'a_minute' => ':count minuta',

    'second' => ':count drugi',
    's' => ':count drugi',
    'a_second' => ':count drugi',
]);
