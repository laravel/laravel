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
 * - Tim Fish
 * - shaishavgandhi05
 * - Serhan Apaydın
 * - JD Isaacks
 * - tomhorvat
 * - Josh Soref
 * - François B
 * - shaishavgandhi05
 * - Serhan Apaydın
 * - JD Isaacks
 * - tomhorvat
 * - Stjepan Majdak
 * - Vanja Retkovac (vr00)
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count godinu|:count godine|:count godina',
    'y' => ':count god.|:count god.|:count god.',
    'month' => ':count mjesec|:count mjeseca|:count mjeseci',
    'm' => ':count mj.|:count mj.|:count mj.',
    'week' => ':count tjedan|:count tjedna|:count tjedana',
    'w' => ':count tj.|:count tj.|:count tj.',
    'day' => ':count dan|:count dana|:count dana',
    'd' => ':count d.|:count d.|:count d.',
    'hour' => ':count sat|:count sata|:count sati',
    'h' => ':count sat|:count sata|:count sati',
    'minute' => ':count minutu|:count minute|:count minuta',
    'min' => ':count min.|:count min.|:count min.',
    'second' => ':count sekundu|:count sekunde|:count sekundi',
    'a_second' => 'nekoliko sekundi|:count sekunde|:count sekundi',
    's' => ':count sek.|:count sek.|:count sek.',
    'ago' => 'prije :time',
    'from_now' => 'za :time',
    'after' => ':time poslije',
    'before' => ':time prije',
    'diff_now' => 'sad',
    'diff_today' => 'danas',
    'diff_today_regexp' => 'danas(?:\\s+u)?',
    'diff_yesterday' => 'jučer',
    'diff_yesterday_regexp' => 'jučer(?:\\s+u)?',
    'diff_tomorrow' => 'sutra',
    'diff_tomorrow_regexp' => 'sutra(?:\\s+u)?',
    'diff_before_yesterday' => 'prekjučer',
    'diff_after_tomorrow' => 'prekosutra',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'D. M. YYYY.',
        'LL' => 'D. MMMM YYYY.',
        'LLL' => 'D. MMMM YYYY. H:mm',
        'LLLL' => 'dddd, D. MMMM YYYY. H:mm',
    ],
    'calendar' => [
        'sameDay' => '[danas u] LT',
        'nextDay' => '[sutra u] LT',
        'nextWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0 => '[u] [nedjelju] [u] LT',
            3 => '[u] [srijedu] [u] LT',
            6 => '[u] [subotu] [u] LT',
            default => '[u] dddd [u] LT',
        },
        'lastDay' => '[jučer u] LT',
        'lastWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0, 3 => '[prošlu] dddd [u] LT',
            6 => '[prošle] [subote] [u] LT',
            default => '[prošli] dddd [u] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['siječnja', 'veljače', 'ožujka', 'travnja', 'svibnja', 'lipnja', 'srpnja', 'kolovoza', 'rujna', 'listopada', 'studenoga', 'prosinca'],
    'months_standalone' => ['siječanj', 'veljača', 'ožujak', 'travanj', 'svibanj', 'lipanj', 'srpanj', 'kolovoz', 'rujan', 'listopad', 'studeni', 'prosinac'],
    'months_short' => ['sij.', 'velj.', 'ožu.', 'tra.', 'svi.', 'lip.', 'srp.', 'kol.', 'ruj.', 'lis.', 'stu.', 'pro.'],
    'months_regexp' => '/(D[oD]?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => ['nedjelju', 'ponedjeljak', 'utorak', 'srijedu', 'četvrtak', 'petak', 'subotu'],
    'weekdays_standalone' => ['nedjelja', 'ponedjeljak', 'utorak', 'srijeda', 'četvrtak', 'petak', 'subota'],
    'weekdays_short' => ['ned.', 'pon.', 'uto.', 'sri.', 'čet.', 'pet.', 'sub.'],
    'weekdays_min' => ['ne', 'po', 'ut', 'sr', 'če', 'pe', 'su'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' i '],
];
