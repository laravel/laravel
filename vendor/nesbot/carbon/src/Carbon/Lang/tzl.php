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
    'year' => '[0,1]:count ar|:count ars',
    'y' => '[0,1]:count ar|:count ars',
    'month' => '[0,1]:count mes|:count mesen',
    'm' => '[0,1]:count mes|:count mesen',
    'week' => '[0,1]:count seifetziua|:count seifetziuas',
    'w' => '[0,1]:count seifetziua|:count seifetziuas',
    'day' => '[0,1]:count ziua|:count ziuas',
    'd' => '[0,1]:count ziua|:count ziuas',
    'hour' => '[0,1]:count þora|:count þoras',
    'h' => '[0,1]:count þora|:count þoras',
    'minute' => '[0,1]:count míut|:count míuts',
    'min' => '[0,1]:count míut|:count míuts',
    'second' => ':count secunds',
    's' => ':count secunds',

    'ago' => 'ja :time',
    'from_now' => 'osprei :time',

    'diff_yesterday' => 'ieiri',
    'diff_yesterday_regexp' => 'ieiri(?:\\s+à)?',
    'diff_today' => 'oxhi',
    'diff_today_regexp' => 'oxhi(?:\\s+à)?',
    'diff_tomorrow' => 'demà',
    'diff_tomorrow_regexp' => 'demà(?:\\s+à)?',

    'formats' => [
        'LT' => 'HH.mm',
        'LTS' => 'HH.mm.ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM [dallas] YYYY',
        'LLL' => 'D. MMMM [dallas] YYYY HH.mm',
        'LLLL' => 'dddd, [li] D. MMMM [dallas] YYYY HH.mm',
    ],

    'calendar' => [
        'sameDay' => '[oxhi à] LT',
        'nextDay' => '[demà à] LT',
        'nextWeek' => 'dddd [à] LT',
        'lastDay' => '[ieiri à] LT',
        'lastWeek' => '[sür el] dddd [lasteu à] LT',
        'sameElse' => 'L',
    ],

    'meridiem' => ["D'A", "D'O"],
    'months' => ['Januar', 'Fevraglh', 'Març', 'Avrïu', 'Mai', 'Gün', 'Julia', 'Guscht', 'Setemvar', 'Listopäts', 'Noemvar', 'Zecemvar'],
    'months_short' => ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Gün', 'Jul', 'Gus', 'Set', 'Lis', 'Noe', 'Zec'],
    'weekdays' => ['Súladi', 'Lúneçi', 'Maitzi', 'Márcuri', 'Xhúadi', 'Viénerçi', 'Sáturi'],
    'weekdays_short' => ['Súl', 'Lún', 'Mai', 'Már', 'Xhú', 'Vié', 'Sát'],
    'weekdays_min' => ['Sú', 'Lú', 'Ma', 'Má', 'Xh', 'Vi', 'Sá'],
    'ordinal' => ':number.',
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
];
