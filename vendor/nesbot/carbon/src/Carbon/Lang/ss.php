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
 * - Nicolai Davies
 */
return [
    'year' => '{1}umnyaka|:count iminyaka',
    'month' => '{1}inyanga|:count tinyanga',
    'week' => '{1}:count liviki|:count emaviki',
    'day' => '{1}lilanga|:count emalanga',
    'hour' => '{1}lihora|:count emahora',
    'minute' => '{1}umzuzu|:count emizuzu',
    'second' => '{1}emizuzwana lomcane|:count mzuzwana',
    'ago' => 'wenteka nga :time',
    'from_now' => 'nga :time',
    'diff_yesterday' => 'Itolo',
    'diff_yesterday_regexp' => 'Itolo(?:\\s+nga)?',
    'diff_today' => 'Namuhla',
    'diff_today_regexp' => 'Namuhla(?:\\s+nga)?',
    'diff_tomorrow' => 'Kusasa',
    'diff_tomorrow_regexp' => 'Kusasa(?:\\s+nga)?',
    'formats' => [
        'LT' => 'h:mm A',
        'LTS' => 'h:mm:ss A',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY h:mm A',
        'LLLL' => 'dddd, D MMMM YYYY h:mm A',
    ],
    'calendar' => [
        'sameDay' => '[Namuhla nga] LT',
        'nextDay' => '[Kusasa nga] LT',
        'nextWeek' => 'dddd [nga] LT',
        'lastDay' => '[Itolo nga] LT',
        'lastWeek' => 'dddd [leliphelile] [nga] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number) {
        $lastDigit = $number % 10;

        return $number.(
            ((int) ($number % 100 / 10) === 1) ? 'e' : (
                ($lastDigit === 1 || $lastDigit === 2) ? 'a' : 'e'
            )
        );
    },
    'meridiem' => static function ($hour) {
        if ($hour < 11) {
            return 'ekuseni';
        }
        if ($hour < 15) {
            return 'emini';
        }
        if ($hour < 19) {
            return 'entsambama';
        }

        return 'ebusuku';
    },
    'months' => ['Bhimbidvwane', 'Indlovana', 'Indlov\'lenkhulu', 'Mabasa', 'Inkhwekhweti', 'Inhlaba', 'Kholwane', 'Ingci', 'Inyoni', 'Imphala', 'Lweti', 'Ingongoni'],
    'months_short' => ['Bhi', 'Ina', 'Inu', 'Mab', 'Ink', 'Inh', 'Kho', 'Igc', 'Iny', 'Imp', 'Lwe', 'Igo'],
    'weekdays' => ['Lisontfo', 'Umsombuluko', 'Lesibili', 'Lesitsatfu', 'Lesine', 'Lesihlanu', 'Umgcibelo'],
    'weekdays_short' => ['Lis', 'Umb', 'Lsb', 'Les', 'Lsi', 'Lsh', 'Umg'],
    'weekdays_min' => ['Li', 'Us', 'Lb', 'Lt', 'Ls', 'Lh', 'Ug'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
];
