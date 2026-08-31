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
 * - François B
 * - John Corrigan
 * - François B
 */
return [
    'year' => ':count tau',
    'a_year' => '{1}he tau|:count tau',
    'month' => ':count marama',
    'a_month' => '{1}he marama|:count marama',
    'week' => ':count wiki',
    'a_week' => '{1}he wiki|:count wiki',
    'day' => ':count ra',
    'a_day' => '{1}he ra|:count ra',
    'hour' => ':count haora',
    'a_hour' => '{1}te haora|:count haora',
    'minute' => ':count meneti',
    'a_minute' => '{1}he meneti|:count meneti',
    'second' => ':count hēkona',
    'a_second' => '{1}te hēkona ruarua|:count hēkona',
    'ago' => ':time i mua',
    'from_now' => 'i roto i :time',
    'diff_yesterday' => 'inanahi',
    'diff_yesterday_regexp' => 'inanahi(?:\\s+i)?',
    'diff_today' => 'i teie',
    'diff_today_regexp' => 'i teie(?:\\s+mahana,)?(?:\\s+i)?',
    'diff_tomorrow' => 'apopo',
    'diff_tomorrow_regexp' => 'apopo(?:\\s+i)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY [i] HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY [i] HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[i teie mahana, i] LT',
        'nextDay' => '[apopo i] LT',
        'nextWeek' => 'dddd [i] LT',
        'lastDay' => '[inanahi i] LT',
        'lastWeek' => 'dddd [whakamutunga i] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => ':numberº',
    'months' => ['Kohi-tāte', 'Hui-tanguru', 'Poutū-te-rangi', 'Paenga-whāwhā', 'Haratua', 'Pipiri', 'Hōngoingoi', 'Here-turi-kōkā', 'Mahuru', 'Whiringa-ā-nuku', 'Whiringa-ā-rangi', 'Hakihea'],
    'months_short' => ['Kohi', 'Hui', 'Pou', 'Pae', 'Hara', 'Pipi', 'Hōngoi', 'Here', 'Mahu', 'Whi-nu', 'Whi-ra', 'Haki'],
    'weekdays' => ['Rātapu', 'Mane', 'Tūrei', 'Wenerei', 'Tāite', 'Paraire', 'Hātarei'],
    'weekdays_short' => ['Ta', 'Ma', 'Tū', 'We', 'Tāi', 'Pa', 'Hā'],
    'weekdays_min' => ['Ta', 'Ma', 'Tū', 'We', 'Tāi', 'Pa', 'Hā'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' me te '],
];
