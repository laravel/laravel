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
 * - Josh Soref
 * - François B
 * - shaishavgandhi05
 * - Serhan Apaydın
 * - JD Isaacks
 * - Glavić
 * - Milos Sakovic
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count godina|:count godine|:count godina',
    'y' => ':count g.',
    'month' => ':count mesec|:count meseca|:count meseci',
    'm' => ':count mes.',
    'week' => ':count nedelja|:count nedelje|:count nedelja',
    'w' => ':count ned.',
    'day' => ':count dan|:count dana|:count dana',
    'd' => ':count d.',
    'hour' => ':count sat|:count sata|:count sati',
    'h' => ':count č.',
    'minute' => ':count minut|:count minuta|:count minuta',
    'min' => ':count min.',
    'second' => ':count sekundu|:count sekunde|:count sekundi',
    's' => ':count sek.',

    'ago' => 'pre :time',
    'from_now' => 'za :time',
    'after' => 'nakon :time',
    'before' => 'pre :time',

    'year_ago' => ':count godinu|:count godine|:count godina',
    'year_from_now' => ':count godinu|:count godine|:count godina',
    'week_ago' => ':count nedelju|:count nedelje|:count nedelja',
    'week_from_now' => ':count nedelju|:count nedelje|:count nedelja',

    'diff_now' => 'upravo sada',
    'diff_today' => 'danas',
    'diff_today_regexp' => 'danas(?:\\s+u)?',
    'diff_yesterday' => 'juče',
    'diff_yesterday_regexp' => 'juče(?:\\s+u)?',
    'diff_tomorrow' => 'sutra',
    'diff_tomorrow_regexp' => 'sutra(?:\\s+u)?',
    'diff_before_yesterday' => 'prekjuče',
    'diff_after_tomorrow' => 'preksutra',
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
        'nextWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0 => '[u nedelju u] LT',
            3 => '[u sredu u] LT',
            6 => '[u subotu u] LT',
            default => '[u] dddd [u] LT',
        },
        'lastDay' => '[juče u] LT',
        'lastWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0 => '[prošle nedelje u] LT',
            1 => '[prošlog ponedeljka u] LT',
            2 => '[prošlog utorka u] LT',
            3 => '[prošle srede u] LT',
            4 => '[prošlog četvrtka u] LT',
            5 => '[prošlog petka u] LT',
            default => '[prošle subote u] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['januar', 'februar', 'mart', 'april', 'maj', 'jun', 'jul', 'avgust', 'septembar', 'oktobar', 'novembar', 'decembar'],
    'months_short' => ['jan.', 'feb.', 'mar.', 'apr.', 'maj', 'jun', 'jul', 'avg.', 'sep.', 'okt.', 'nov.', 'dec.'],
    'weekdays' => ['nedelja', 'ponedeljak', 'utorak', 'sreda', 'četvrtak', 'petak', 'subota'],
    'weekdays_short' => ['ned.', 'pon.', 'uto.', 'sre.', 'čet.', 'pet.', 'sub.'],
    'weekdays_min' => ['ne', 'po', 'ut', 'sr', 'če', 'pe', 'su'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' i '],
];
