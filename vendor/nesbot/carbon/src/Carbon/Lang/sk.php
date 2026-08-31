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
 * - Martin Suja
 * - Tsutomu Kuroda
 * - tjku
 * - Max Melentiev
 * - Juanito Fatas
 * - Ivan Stana
 * - Akira Matsuda
 * - Christopher Dell
 * - James McKinney
 * - Enrique Vidal
 * - Simone Carletti
 * - Aaron Patterson
 * - Jozef Fulop
 * - Nicolás Hock Isaza
 * - Tom Hughes
 * - Simon Hürlimann (CyT)
 * - jofi
 * - Jakub ADAMEC
 * - Marek Adamický
 * - AlterwebStudio
 * - Peter Kundis
 */

use Carbon\CarbonInterface;

$fromNow = function ($time) {
    return 'o '.strtr($time, [
            'hodina' => 'hodinu',
            'minúta' => 'minútu',
            'sekunda' => 'sekundu',
        ]);
};

$ago = function ($time) {
    $replacements = [
        '/\bhodina\b/' => 'hodinou',
        '/\bminúta\b/' => 'minútou',
        '/\bsekunda\b/' => 'sekundou',
        '/\bdeň\b/u' => 'dňom',
        '/\btýždeň\b/u' => 'týždňom',
        '/\bmesiac\b/' => 'mesiacom',
        '/\brok\b/' => 'rokom',
    ];

    $replacementsPlural = [
        '/\b(?:hodiny|hodín)\b/' => 'hodinami',
        '/\b(?:minúty|minút)\b/' => 'minútami',
        '/\b(?:sekundy|sekúnd)\b/' => 'sekundami',
        '/\bdeň\b/' => 'dňom',
        '/\bdni\b/' => 'dňami',
        '/\bdní\b/u' => 'dňami',
        '/\b(?:týždne|týždňov)\b/' => 'týždňami',
        '/\b(?:mesiace|mesiacov)\b/' => 'mesiacmi',
        '/\b(?:roky|rokov)\b/' => 'rokmi',
    ];

    foreach ($replacements + $replacementsPlural as $pattern => $replacement) {
        $time = preg_replace($pattern, $replacement, $time);
    }

    return "pred $time";
};

return [
    'year' => ':count rok|:count roky|:count rokov',
    'a_year' => 'rok|:count roky|:count rokov',
    'y' => ':count r',
    'month' => ':count mesiac|:count mesiace|:count mesiacov',
    'a_month' => 'mesiac|:count mesiace|:count mesiacov',
    'm' => ':count m',
    'week' => ':count týždeň|:count týždne|:count týždňov',
    'a_week' => 'týždeň|:count týždne|:count týždňov',
    'w' => ':count t',
    'day' => ':count deň|:count dni|:count dní',
    'a_day' => 'deň|:count dni|:count dní',
    'd' => ':count d',
    'hour' => ':count hodina|:count hodiny|:count hodín',
    'a_hour' => 'hodina|:count hodiny|:count hodín',
    'h' => ':count h',
    'minute' => ':count minúta|:count minúty|:count minút',
    'a_minute' => 'minúta|:count minúty|:count minút',
    'min' => ':count min',
    'second' => ':count sekunda|:count sekundy|:count sekúnd',
    'a_second' => 'sekunda|:count sekundy|:count sekúnd',
    's' => ':count s',
    'millisecond' => ':count milisekunda|:count milisekundy|:count milisekúnd',
    'a_millisecond' => 'milisekunda|:count milisekundy|:count milisekúnd',
    'ms' => ':count ms',
    'microsecond' => ':count mikrosekunda|:count mikrosekundy|:count mikrosekúnd',
    'a_microsecond' => 'mikrosekunda|:count mikrosekundy|:count mikrosekúnd',
    'µs' => ':count µs',

    'ago' => $ago,
    'from_now' => $fromNow,
    'before' => ':time pred',
    'after' => ':time po',

    'hour_after' => ':count hodinu|:count hodiny|:count hodín',
    'minute_after' => ':count minútu|:count minúty|:count minút',
    'second_after' => ':count sekundu|:count sekundy|:count sekúnd',

    'hour_before' => ':count hodinu|:count hodiny|:count hodín',
    'minute_before' => ':count minútu|:count minúty|:count minút',
    'second_before' => ':count sekundu|:count sekundy|:count sekúnd',

    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' a '],
    'diff_now' => 'teraz',
    'diff_yesterday' => 'včera',
    'diff_tomorrow' => 'zajtra',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'DD. MMMM YYYY',
        'LLL' => 'D. M. HH:mm',
        'LLLL' => 'dddd D. MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[dnes o] LT',
        'nextDay' => '[zajtra o] LT',
        'lastDay' => '[včera o] LT',
        'nextWeek' => 'dddd [o] LT',
        'lastWeek' => static function (CarbonInterface $date) {
            switch ($date->dayOfWeek) {
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[minulý] dddd [o] LT'; //pondelok/utorok/štvrtok/piatok
                default:
                    return '[minulá] dddd [o] LT';
            }
        },
        'sameElse' => 'L',
    ],
    'weekdays' => ['nedeľa', 'pondelok', 'utorok', 'streda', 'štvrtok', 'piatok', 'sobota'],
    'weekdays_short' => ['ned', 'pon', 'uto', 'str', 'štv', 'pia', 'sob'],
    'weekdays_min' => ['ne', 'po', 'ut', 'st', 'št', 'pi', 'so'],
    'months' => ['januára', 'februára', 'marca', 'apríla', 'mája', 'júna', 'júla', 'augusta', 'septembra', 'októbra', 'novembra', 'decembra'],
    'months_standalone' => ['január', 'február', 'marec', 'apríl', 'máj', 'jún', 'júl', 'august', 'september', 'október', 'november', 'december'],
    'months_short' => ['jan', 'feb', 'mar', 'apr', 'máj', 'jún', 'júl', 'aug', 'sep', 'okt', 'nov', 'dec'],
    'months_regexp' => '/(DD?o?\.?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'meridiem' => ['dopoludnia', 'popoludní'],
];
