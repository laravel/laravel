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
    'meridiem' => ['Ɛnkakɛnyá', 'Ɛndámâ'],
    'weekdays' => ['Jumapílí', 'Jumatátu', 'Jumane', 'Jumatánɔ', 'Alaámisi', 'Jumáa', 'Jumamósi'],
    'weekdays_short' => ['Jpi', 'Jtt', 'Jnn', 'Jtn', 'Alh', 'Iju', 'Jmo'],
    'weekdays_min' => ['Jpi', 'Jtt', 'Jnn', 'Jtn', 'Alh', 'Iju', 'Jmo'],
    'months' => ['Oladalʉ́', 'Arát', 'Ɔɛnɨ́ɔɨŋɔk', 'Olodoyíóríê inkókúâ', 'Oloilépūnyīē inkókúâ', 'Kújúɔrɔk', 'Mórusásin', 'Ɔlɔ́ɨ́bɔ́rárɛ', 'Kúshîn', 'Olgísan', 'Pʉshʉ́ka', 'Ntʉ́ŋʉ́s'],
    'months_short' => ['Dal', 'Ará', 'Ɔɛn', 'Doy', 'Lép', 'Rok', 'Sás', 'Bɔ́r', 'Kús', 'Gís', 'Shʉ́', 'Ntʉ́'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],

    'year' => ':count olameyu', // less reliable
    'y' => ':count olameyu', // less reliable
    'a_year' => ':count olameyu', // less reliable

    'week' => ':count engolongeare orwiki', // less reliable
    'w' => ':count engolongeare orwiki', // less reliable
    'a_week' => ':count engolongeare orwiki', // less reliable

    'hour' => ':count esahabu', // less reliable
    'h' => ':count esahabu', // less reliable
    'a_hour' => ':count esahabu', // less reliable

    'second' => ':count are', // less reliable
    's' => ':count are', // less reliable
    'a_second' => ':count are', // less reliable

    'month' => ':count olapa',
    'm' => ':count olapa',
    'a_month' => ':count olapa',

    'day' => ':count enkolongʼ',
    'd' => ':count enkolongʼ',
    'a_day' => ':count enkolongʼ',
]);
