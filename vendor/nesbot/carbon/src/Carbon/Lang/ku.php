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
 * - Unicode, Inc.
 */

return array_replace_recursive(require __DIR__.'/en.php', [
    'ago' => 'berî :time',
    'from_now' => 'di :time de',
    'after' => ':time piştî',
    'before' => ':time berê',
    'year' => ':count sal',
    'a_year' => ':count sal',
    'y' => ':count sal',
    'year_ago' => ':count salê|:count salan',
    'y_ago' => ':count salê|:count salan',
    'year_from_now' => 'salekê|:count salan',
    'y_from_now' => 'salekê|:count salan',
    'month' => ':count meh',
    'a_month' => ':count meh',
    'm' => ':count meh',
    'week' => ':count hefte',
    'a_week' => ':count hefte',
    'w' => ':count hefte',
    'day' => ':count roj',
    'a_day' => ':count roj',
    'd' => ':count roj',
    'hour' => ':count saet',
    'a_hour' => ':count saet',
    'h' => ':count saet',
    'minute' => ':count deqîqe',
    'a_minute' => ':count deqîqe',
    'min' => ':count deqîqe',
    'second' => ':count saniye',
    'a_second' => ':count saniye',
    's' => ':count saniye',
    'months' => ['rêbendanê', 'reşemiyê', 'adarê', 'avrêlê', 'gulanê', 'pûşperê', 'tîrmehê', 'gelawêjê', 'rezberê', 'kewçêrê', 'sermawezê', 'berfanbarê'],
    'months_standalone' => ['rêbendan', 'reşemî', 'adar', 'avrêl', 'gulan', 'pûşper', 'tîrmeh', 'gelawêj', 'rezber', 'kewçêr', 'sermawez', 'berfanbar'],
    'months_short' => ['rêb', 'reş', 'ada', 'avr', 'gul', 'pûş', 'tîr', 'gel', 'rez', 'kew', 'ser', 'ber'],
    'weekdays' => ['yekşem', 'duşem', 'sêşem', 'çarşem', 'pêncşem', 'în', 'şemî'],
    'weekdays_short' => ['yş', 'dş', 'sş', 'çş', 'pş', 'în', 'ş'],
    'weekdays_min' => ['Y', 'D', 'S', 'Ç', 'P', 'Î', 'Ş'],
    'list' => [', ', ' û '],
    'ordinal' => ':number',
    'first_day_of_week' => 6,
    'day_of_first_week_of_year' => 1,
]);
