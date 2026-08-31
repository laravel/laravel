<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'year' => ':count voros|:count vorsam',
    'y' => ':countv',
    'month' => ':count mhoino|:count mhoine',
    'm' => ':countmh',
    'week' => ':count satolleacho|:count satolleache',
    'w' => ':countsa|:countsa',
    'day' => ':count dis',
    'd' => ':countd',
    'hour' => ':count hor|:count horam',
    'h' => ':counth',
    'minute' => ':count minute|:count mintam',
    'min' => ':countm',
    'second' => ':count second',
    's' => ':counts',

    'diff_today' => 'Aiz',
    'diff_yesterday' => 'Kal',
    'diff_tomorrow' => 'Faleam',
    'formats' => [
        'LT' => 'A h:mm [vazta]',
        'LTS' => 'A h:mm:ss [vazta]',
        'L' => 'DD-MM-YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY A h:mm [vazta]',
        'LLLL' => 'dddd, MMMM[achea] Do, YYYY, A h:mm [vazta]',
        'llll' => 'ddd, D MMM YYYY, A h:mm [vazta]',
    ],

    'calendar' => [
        'sameDay' => '[Aiz] LT',
        'nextDay' => '[Faleam] LT',
        'nextWeek' => '[Ieta to] dddd[,] LT',
        'lastDay' => '[Kal] LT',
        'lastWeek' => '[Fatlo] dddd[,] LT',
        'sameElse' => 'L',
    ],

    'months' => ['Janer', 'Febrer', 'Mars', 'Abril', 'Mai', 'Jun', 'Julai', 'Agost', 'Setembr', 'Otubr', 'Novembr', 'Dezembr'],
    'months_short' => ['Jan.', 'Feb.', 'Mars', 'Abr.', 'Mai', 'Jun', 'Jul.', 'Ago.', 'Set.', 'Otu.', 'Nov.', 'Dez.'],
    'weekdays' => ['Aitar', 'Somar', 'Mongllar', 'Budvar', 'Brestar', 'Sukrar', 'Son\'var'],
    'weekdays_short' => ['Ait.', 'Som.', 'Mon.', 'Bud.', 'Bre.', 'Suk.', 'Son.'],
    'weekdays_min' => ['Ai', 'Sm', 'Mo', 'Bu', 'Br', 'Su', 'Sn'],

    'ordinal' => static fn ($number, $period) => $number.($period === 'D' ? 'er' : ''),

    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'rati';
        }
        if ($hour < 12) {
            return 'sokalli';
        }
        if ($hour < 16) {
            return 'donparam';
        }
        if ($hour < 20) {
            return 'sanje';
        }

        return 'rati';
    },
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' ani '],
];
