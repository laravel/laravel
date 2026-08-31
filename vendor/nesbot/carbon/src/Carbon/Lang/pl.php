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
 * - Wacław Jacek
 * - François B
 * - Tim Fish
 * - Serhan Apaydın
 * - Massimiliano Caniparoli
 * - JD Isaacks
 * - Jakub Szwacz
 * - Jan
 * - Paul
 * - damlys
 * - Marek (marast78)
 * - Peter (UnrulyNatives)
 * - Qrzysio
 * - Jan (aso824)
 * - diverpl
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count rok|:count lata|:count lat',
    'a_year' => 'rok|:count lata|:count lat',
    'y' => ':count r|:count l|:count l',
    'month' => ':count miesiąc|:count miesiące|:count miesięcy',
    'a_month' => 'miesiąc|:count miesiące|:count miesięcy',
    'm' => ':count mies.',
    'week' => ':count tydzień|:count tygodnie|:count tygodni',
    'a_week' => 'tydzień|:count tygodnie|:count tygodni',
    'w' => ':count tyg.',
    'day' => ':count dzień|:count dni|:count dni',
    'a_day' => 'dzień|:count dni|:count dni',
    'd' => ':count d',
    'hour' => ':count godzina|:count godziny|:count godzin',
    'a_hour' => 'godzina|:count godziny|:count godzin',
    'h' => ':count godz.',
    'minute' => ':count minuta|:count minuty|:count minut',
    'a_minute' => 'minuta|:count minuty|:count minut',
    'min' => ':count min',
    'second' => ':count sekunda|:count sekundy|:count sekund',
    'a_second' => '{1}kilka sekund|:count sekunda|:count sekundy|:count sekund',
    's' => ':count sek.',
    'ago' => ':time temu',
    'from_now' => static function ($time) {
        return 'za '.strtr($time, [
            'godzina' => 'godzinę',
            'minuta' => 'minutę',
            'sekunda' => 'sekundę',
        ]);
    },
    'after' => ':time po',
    'before' => ':time przed',
    'diff_now' => 'teraz',
    'diff_today' => 'Dziś',
    'diff_today_regexp' => 'Dziś(?:\\s+o)?',
    'diff_yesterday' => 'wczoraj',
    'diff_yesterday_regexp' => 'Wczoraj(?:\\s+o)?',
    'diff_tomorrow' => 'jutro',
    'diff_tomorrow_regexp' => 'Jutro(?:\\s+o)?',
    'diff_before_yesterday' => 'przedwczoraj',
    'diff_after_tomorrow' => 'pojutrze',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Dziś o] LT',
        'nextDay' => '[Jutro o] LT',
        'nextWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0 => '[W niedzielę o] LT',
            2 => '[We wtorek o] LT',
            3 => '[W środę o] LT',
            6 => '[W sobotę o] LT',
            default => '[W] dddd [o] LT',
        },
        'lastDay' => '[Wczoraj o] LT',
        'lastWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0 => '[W zeszłą niedzielę o] LT',
            3 => '[W zeszłą środę o] LT',
            6 => '[W zeszłą sobotę o] LT',
            default => '[W zeszły] dddd [o] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'],
    'months_standalone' => ['styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'],
    'months_short' => ['sty', 'lut', 'mar', 'kwi', 'maj', 'cze', 'lip', 'sie', 'wrz', 'paź', 'lis', 'gru'],
    'months_regexp' => '/(DD?o?\.?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => ['niedziela', 'poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota'],
    'weekdays_short' => ['ndz', 'pon', 'wt', 'śr', 'czw', 'pt', 'sob'],
    'weekdays_min' => ['Nd', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' i '],
    'meridiem' => ['przed południem', 'po południu'],
];
