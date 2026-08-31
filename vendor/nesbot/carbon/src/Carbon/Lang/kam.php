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
    'meridiem' => ['Ĩyakwakya', 'Ĩyawĩoo'],
    'weekdays' => ['Wa kyumwa', 'Wa kwambĩlĩlya', 'Wa kelĩ', 'Wa katatũ', 'Wa kana', 'Wa katano', 'Wa thanthatũ'],
    'weekdays_short' => ['Wky', 'Wkw', 'Wkl', 'Wtũ', 'Wkn', 'Wtn', 'Wth'],
    'weekdays_min' => ['Wky', 'Wkw', 'Wkl', 'Wtũ', 'Wkn', 'Wtn', 'Wth'],
    'months' => ['Mwai wa mbee', 'Mwai wa kelĩ', 'Mwai wa katatũ', 'Mwai wa kana', 'Mwai wa katano', 'Mwai wa thanthatũ', 'Mwai wa muonza', 'Mwai wa nyaanya', 'Mwai wa kenda', 'Mwai wa ĩkumi', 'Mwai wa ĩkumi na ĩmwe', 'Mwai wa ĩkumi na ilĩ'],
    'months_short' => ['Mbe', 'Kel', 'Ktũ', 'Kan', 'Ktn', 'Tha', 'Moo', 'Nya', 'Knd', 'Ĩku', 'Ĩkm', 'Ĩkl'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],

    // Too unreliable
    /*
    'year' => ':count mbua', // less reliable
    'y' => ':count mbua', // less reliable
    'a_year' => ':count mbua', // less reliable

    'month' => ':count ndakitali', // less reliable
    'm' => ':count ndakitali', // less reliable
    'a_month' => ':count ndakitali', // less reliable

    'day' => ':count wia', // less reliable
    'd' => ':count wia', // less reliable
    'a_day' => ':count wia', // less reliable

    'hour' => ':count orasan', // less reliable
    'h' => ':count orasan', // less reliable
    'a_hour' => ':count orasan', // less reliable

    'minute' => ':count orasan', // less reliable
    'min' => ':count orasan', // less reliable
    'a_minute' => ':count orasan', // less reliable
    */
]);
