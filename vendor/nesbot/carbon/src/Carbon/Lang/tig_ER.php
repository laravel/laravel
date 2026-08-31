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
    'months' => ['ጥሪ', 'ለካቲት', 'መጋቢት', 'ሚያዝያ', 'ግንቦት', 'ሰነ', 'ሓምለ', 'ነሓሰ', 'መስከረም', 'ጥቅምቲ', 'ሕዳር', 'ታሕሳስ'],
    'months_short' => ['ጥሪ ', 'ለካቲ', 'መጋቢ', 'ሚያዝ', 'ግንቦ', 'ሰነ ', 'ሓምለ', 'ነሓሰ', 'መስከ', 'ጥቅም', 'ሕዳር', 'ታሕሳ'],
    'weekdays' => ['ሰንበት ዓባይ', 'ሰኖ', 'ታላሸኖ', 'ኣረርባዓ', 'ከሚሽ', 'ጅምዓት', 'ሰንበት ንኢሽ'],
    'weekdays_short' => ['ሰ//ዓ', 'ሰኖ ', 'ታላሸ', 'ኣረር', 'ከሚሽ', 'ጅምዓ', 'ሰ//ን'],
    'weekdays_min' => ['ሰ//ዓ', 'ሰኖ ', 'ታላሸ', 'ኣረር', 'ከሚሽ', 'ጅምዓ', 'ሰ//ን'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['ቀደም ሰር ምዕል', 'ሓቆ ሰር ምዕል'],

    'year' => ':count ማይ', // less reliable
    'y' => ':count ማይ', // less reliable
    'a_year' => ':count ማይ', // less reliable

    'month' => ':count ሸምሽ', // less reliable
    'm' => ':count ሸምሽ', // less reliable
    'a_month' => ':count ሸምሽ', // less reliable

    'week' => ':count ሰቡዕ', // less reliable
    'w' => ':count ሰቡዕ', // less reliable
    'a_week' => ':count ሰቡዕ', // less reliable

    'day' => ':count ዎሮ', // less reliable
    'd' => ':count ዎሮ', // less reliable
    'a_day' => ':count ዎሮ', // less reliable

    'hour' => ':count ሰዓት', // less reliable
    'h' => ':count ሰዓት', // less reliable
    'a_hour' => ':count ሰዓት', // less reliable

    'minute' => ':count ካልኣይት', // less reliable
    'min' => ':count ካልኣይት', // less reliable
    'a_minute' => ':count ካልኣይት', // less reliable

    'second' => ':count ካልኣይ',
    's' => ':count ካልኣይ',
    'a_second' => ':count ካልኣይ',
]);
