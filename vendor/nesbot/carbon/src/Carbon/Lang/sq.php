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
 * - JD Isaacks
 * - Fadion Dashi
 */
return [
    'year' => ':count vit|:count vjet',
    'a_year' => 'një vit|:count vite',
    'y' => ':count v.',
    'month' => ':count muaj',
    'a_month' => 'një muaj|:count muaj',
    'm' => ':count muaj',
    'week' => ':count javë',
    'a_week' => ':count javë|:count javë',
    'w' => ':count j.',
    'day' => ':count ditë',
    'a_day' => 'një ditë|:count ditë',
    'd' => ':count d.',
    'hour' => ':count orë',
    'a_hour' => 'një orë|:count orë',
    'h' => ':count o.',
    'minute' => ':count minutë|:count minuta',
    'a_minute' => 'një minutë|:count minuta',
    'min' => ':count min.',
    'second' => ':count sekondë|:count sekonda',
    'a_second' => 'disa sekonda|:count sekonda',
    's' => ':count s.',
    'ago' => ':time më parë',
    'from_now' => 'në :time',
    'after' => ':time pas',
    'before' => ':time para',
    'diff_now' => 'tani',
    'diff_today' => 'Sot',
    'diff_today_regexp' => 'Sot(?:\\s+në)?',
    'diff_yesterday' => 'dje',
    'diff_yesterday_regexp' => 'Dje(?:\\s+në)?',
    'diff_tomorrow' => 'nesër',
    'diff_tomorrow_regexp' => 'Nesër(?:\\s+në)?',
    'diff_before_yesterday' => 'pardje',
    'diff_after_tomorrow' => 'pasnesër',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Sot në] LT',
        'nextDay' => '[Nesër në] LT',
        'nextWeek' => 'dddd [në] LT',
        'lastDay' => '[Dje në] LT',
        'lastWeek' => 'dddd [e kaluar në] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'meridiem' => ['PD', 'MD'],
    'months' => ['janar', 'shkurt', 'mars', 'prill', 'maj', 'qershor', 'korrik', 'gusht', 'shtator', 'tetor', 'nëntor', 'dhjetor'],
    'months_short' => ['jan', 'shk', 'mar', 'pri', 'maj', 'qer', 'kor', 'gus', 'sht', 'tet', 'nën', 'dhj'],
    'weekdays' => ['e diel', 'e hënë', 'e martë', 'e mërkurë', 'e enjte', 'e premte', 'e shtunë'],
    'weekdays_short' => ['die', 'hën', 'mar', 'mër', 'enj', 'pre', 'sht'],
    'weekdays_min' => ['d', 'h', 'ma', 'më', 'e', 'p', 'sh'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' dhe '],
];
