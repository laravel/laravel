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
 * - Ge'ez Frontier Foundation    locales@geez.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['ጠሐረ', 'ከተተ', 'መገበ', 'አኀዘ', 'ግንባት', 'ሠንየ', 'ሐመለ', 'ነሐሰ', 'ከረመ', 'ጠቀመ', 'ኀደረ', 'ኀሠሠ'],
    'months_short' => ['ጠሐረ', 'ከተተ', 'መገበ', 'አኀዘ', 'ግንባ', 'ሠንየ', 'ሐመለ', 'ነሐሰ', 'ከረመ', 'ጠቀመ', 'ኀደረ', 'ኀሠሠ'],
    'weekdays' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚት'],
    'weekdays_short' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚ'],
    'weekdays_min' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚ'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['ጽባሕ', 'ምሴት'],

    'month' => ':count ወርሕ', // less reliable
    'm' => ':count ወርሕ', // less reliable
    'a_month' => ':count ወርሕ', // less reliable

    'week' => ':count ሰብዑ', // less reliable
    'w' => ':count ሰብዑ', // less reliable
    'a_week' => ':count ሰብዑ', // less reliable

    'hour' => ':count አንትሙ', // less reliable
    'h' => ':count አንትሙ', // less reliable
    'a_hour' => ':count አንትሙ', // less reliable

    'minute' => ':count ንኡስ', // less reliable
    'min' => ':count ንኡስ', // less reliable
    'a_minute' => ':count ንኡስ', // less reliable

    'year' => ':count ዓመት',
    'y' => ':count ዓመት',
    'a_year' => ':count ዓመት',

    'day' => ':count ዕለት',
    'd' => ':count ዕለት',
    'a_day' => ':count ዕለት',

    'second' => ':count ካልእ',
    's' => ':count ካልእ',
    'a_second' => ':count ካልእ',
]);
