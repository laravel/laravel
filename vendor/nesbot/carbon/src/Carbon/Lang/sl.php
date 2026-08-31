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
 * - Philippe Vaucher
 * - Tsutomu Kuroda
 * - tjku
 * - Max Melentiev
 * - Juanito Fatas
 * - Akira Matsuda
 * - Christopher Dell
 * - Enrique Vidal
 * - Simone Carletti
 * - Aaron Patterson
 * - Nicolás Hock Isaza
 * - Miha Rebernik
 * - Gal Jakič (morpheus7CS)
 * - Glavić
 * - Anže Časar
 * - Lovro Tramšek (Lovro1107)
 * - burut13
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count leto|:count leti|:count leta|:count let',
    'y' => ':count leto|:count leti|:count leta|:count let',
    'month' => ':count mesec|:count meseca|:count mesece|:count mesecev',
    'm' => ':count mes.',
    'week' => ':count teden|:count tedna|:count tedne|:count tednov',
    'w' => ':count ted.',
    'day' => ':count dan|:count dni|:count dni|:count dni',
    'd' => ':count dan|:count dni|:count dni|:count dni',
    'hour' => ':count ura|:count uri|:count ure|:count ur',
    'h' => ':count h',
    'minute' => ':count minuta|:count minuti|:count minute|:count minut',
    'min' => ':count min.',
    'second' => ':count sekunda|:count sekundi|:count sekunde|:count sekund',
    'a_second' => '{1}nekaj sekund|:count sekunda|:count sekundi|:count sekunde|:count sekund',
    's' => ':count s',

    'year_ago' => ':count letom|:count letoma|:count leti|:count leti',
    'y_ago' => ':count letom|:count letoma|:count leti|:count leti',
    'month_ago' => ':count mesecem|:count mesecema|:count meseci|:count meseci',
    'week_ago' => ':count tednom|:count tednoma|:count tedni|:count tedni',
    'day_ago' => ':count dnem|:count dnevoma|:count dnevi|:count dnevi',
    'd_ago' => ':count dnem|:count dnevoma|:count dnevi|:count dnevi',
    'hour_ago' => ':count uro|:count urama|:count urami|:count urami',
    'minute_ago' => ':count minuto|:count minutama|:count minutami|:count minutami',
    'second_ago' => ':count sekundo|:count sekundama|:count sekundami|:count sekundami',

    'day_from_now' => ':count dan|:count dneva|:count dni|:count dni',
    'd_from_now' => ':count dan|:count dneva|:count dni|:count dni',
    'hour_from_now' => ':count uro|:count uri|:count ure|:count ur',
    'minute_from_now' => ':count minuto|:count minuti|:count minute|:count minut',
    'second_from_now' => ':count sekundo|:count sekundi|:count sekunde|:count sekund',

    'ago' => 'pred :time',
    'from_now' => 'čez :time',
    'after' => ':time kasneje',
    'before' => ':time prej',

    'diff_now' => 'ravnokar',
    'diff_today' => 'danes',
    'diff_today_regexp' => 'danes(?:\\s+ob)?',
    'diff_yesterday' => 'včeraj',
    'diff_yesterday_regexp' => 'včeraj(?:\\s+ob)?',
    'diff_tomorrow' => 'jutri',
    'diff_tomorrow_regexp' => 'jutri(?:\\s+ob)?',
    'diff_before_yesterday' => 'predvčerajšnjim',
    'diff_after_tomorrow' => 'pojutrišnjem',

    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,

    'period_start_date' => 'od :date',
    'period_end_date' => 'do :date',

    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM YYYY',
        'LLL' => 'D. MMMM YYYY H:mm',
        'LLLL' => 'dddd, D. MMMM YYYY H:mm',
    ],
    'calendar' => [
        'sameDay' => '[danes ob] LT',
        'nextDay' => '[jutri ob] LT',
        'nextWeek' => 'dddd [ob] LT',
        'lastDay' => '[včeraj ob] LT',
        'lastWeek' => static function (CarbonInterface $date) {
            switch ($date->dayOfWeek) {
                case 0:
                    return '[preteklo] [nedeljo] [ob] LT';
                case 1:
                    return '[pretekli] [ponedeljek] [ob] LT';
                case 2:
                    return '[pretekli] [torek] [ob] LT';
                case 3:
                    return '[preteklo] [sredo] [ob] LT';
                case 4:
                    return '[pretekli] [četrtek] [ob] LT';
                case 5:
                    return '[pretekli] [petek] [ob] LT';
                case 6:
                    return '[preteklo] [soboto] [ob] LT';
            }
        },
        'sameElse' => 'L',
    ],
    'months' => ['januarja', 'februarja', 'marca', 'aprila', 'maja', 'junija', 'julija', 'avgusta', 'septembra', 'oktobra', 'novembra', 'decembra'],
    'months_standalone' => ['januar', 'februar', 'marec', 'april', 'maj', 'junij', 'julij', 'avgust', 'september', 'oktober', 'november', 'december'],
    'months_short' => ['jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'avg', 'sep', 'okt', 'nov', 'dec'],
    'months_regexp' => '/(DD?o?\.?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => ['nedelja', 'ponedeljek', 'torek', 'sreda', 'četrtek', 'petek', 'sobota'],
    'weekdays_short' => ['ned', 'pon', 'tor', 'sre', 'čet', 'pet', 'sob'],
    'weekdays_min' => ['ne', 'po', 'to', 'sr', 'če', 'pe', 'so'],
    'list' => [', ', ' in '],
    'meridiem' => ['dopoldan', 'popoldan'],
];
