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
 * - bokideckonja
 * - Josh Soref
 * - François B
 * - shaishavgandhi05
 * - Serhan Apaydın
 * - JD Isaacks
 * - Ademir Šehić
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count godina|:count godine|:count godina',
    'y' => ':count godina|:count godine|:count godina',
    'month' => ':count mjesec|:count mjeseca|:count mjeseci',
    'm' => ':count mjesec|:count mjeseca|:count mjeseci',
    'week' => ':count sedmica|:count sedmice|:count sedmica',
    'w' => ':count sedmica|:count sedmice|:count sedmica',
    'day' => ':count dan|:count dana|:count dana',
    'd' => ':count dan|:count dana|:count dana',
    'hour' => ':count sat|:count sata|:count sati',
    'h' => ':count sat|:count sata|:count sati',
    'minute' => ':count minut|:count minuta|:count minuta',
    'min' => ':count minut|:count minuta|:count minuta',
    'second' => ':count sekund|:count sekunda|:count sekundi',
    's' => ':count sekund|:count sekunda|:count sekundi',

    'ago' => 'prije :time',
    'from_now' => 'za :time',
    'after' => 'nakon :time',
    'before' => ':time ranije',

    'year_ago' => ':count godinu|:count godine|:count godina',
    'year_from_now' => ':count godinu|:count godine|:count godina',
    'week_ago' => ':count sedmicu|:count sedmice|:count sedmica',
    'week_from_now' => ':count sedmicu|:count sedmice|:count sedmica',

    'diff_now' => 'sada',
    'diff_today' => 'danas',
    'diff_today_regexp' => 'danas(?:\\s+u)?',
    'diff_yesterday' => 'jučer',
    'diff_yesterday_regexp' => 'jučer(?:\\s+u)?',
    'diff_tomorrow' => 'sutra',
    'diff_tomorrow_regexp' => 'sutra(?:\\s+u)?',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM YYYY',
        'LLL' => 'D. MMMM YYYY H:mm',
        'LLLL' => 'dddd, D. MMMM YYYY H:mm',
    ],
    'calendar' => [
        'sameDay' => '[danas u] LT',
        'nextDay' => '[sutra u] LT',
        'nextWeek' => static fn (CarbonInterface $current) => match ($current->dayOfWeek) {
            0 => '[u] [nedjelju] [u] LT',
            3 => '[u] [srijedu] [u] LT',
            6 => '[u] [subotu] [u] LT',
            default => '[u] dddd [u] LT',
        },
        'lastDay' => '[jučer u] LT',
        'lastWeek' => static fn (CarbonInterface $current) => match ($current->dayOfWeek) {
            0, 3 => '[prošlu] dddd [u] LT',
            6 => '[prošle] [subote] [u] LT',
            default => '[prošli] dddd [u] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['januar', 'februar', 'mart', 'april', 'maj', 'juni', 'juli', 'august', 'septembar', 'oktobar', 'novembar', 'decembar'],
    'months_short' => ['jan.', 'feb.', 'mar.', 'apr.', 'maj.', 'jun.', 'jul.', 'aug.', 'sep.', 'okt.', 'nov.', 'dec.'],
    'weekdays' => ['nedjelja', 'ponedjeljak', 'utorak', 'srijeda', 'četvrtak', 'petak', 'subota'],
    'weekdays_short' => ['ned.', 'pon.', 'uto.', 'sri.', 'čet.', 'pet.', 'sub.'],
    'weekdays_min' => ['ne', 'po', 'ut', 'sr', 'če', 'pe', 'su'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' i '],
    'meridiem' => ['prijepodne', 'popodne'],
];
