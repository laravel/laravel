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
 * - mestremuten
 * - François B
 * - Marc Ordinas i Llopis
 * - Pere Orga
 * - JD Isaacks
 * - Quentí
 * - Víctor Díaz
 * - Xavi
 * - qcardona
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count any|:count anys',
    'a_year' => 'un any|:count anys',
    'y' => ':count any|:count anys',
    'month' => ':count mes|:count mesos',
    'a_month' => 'un mes|:count mesos',
    'm' => ':count mes|:count mesos',
    'week' => ':count setmana|:count setmanes',
    'a_week' => 'una setmana|:count setmanes',
    'w' => ':count setmana|:count setmanes',
    'day' => ':count dia|:count dies',
    'a_day' => 'un dia|:count dies',
    'd' => ':count d',
    'hour' => ':count hora|:count hores',
    'a_hour' => 'una hora|:count hores',
    'h' => ':count h',
    'minute' => ':count minut|:count minuts',
    'a_minute' => 'un minut|:count minuts',
    'min' => ':count min',
    'second' => ':count segon|:count segons',
    'a_second' => 'uns segons|:count segons',
    's' => ':count s',
    'ago' => 'fa :time',
    'from_now' => 'd\'aquí a :time',
    'after' => ':time després',
    'before' => ':time abans',
    'diff_now' => 'ara mateix',
    'diff_today' => 'avui',
    'diff_today_regexp' => 'avui(?:\\s+a)?(?:\\s+les)?',
    'diff_yesterday' => 'ahir',
    'diff_yesterday_regexp' => 'ahir(?:\\s+a)?(?:\\s+les)?',
    'diff_tomorrow' => 'demà',
    'diff_tomorrow_regexp' => 'demà(?:\\s+a)?(?:\\s+les)?',
    'diff_before_yesterday' => 'abans d\'ahir',
    'diff_after_tomorrow' => 'demà passat',
    'period_recurrences' => ':count cop|:count cops',
    'period_interval' => 'cada :interval',
    'period_start_date' => 'de :date',
    'period_end_date' => 'fins a :date',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM [de] YYYY',
        'LLL' => 'D MMMM [de] YYYY [a les] H:mm',
        'LLLL' => 'dddd D MMMM [de] YYYY [a les] H:mm',
    ],
    'calendar' => [
        'sameDay' => static function (CarbonInterface $current) {
            return '[avui a '.($current->hour !== 1 ? 'les' : 'la').'] LT';
        },
        'nextDay' => static function (CarbonInterface $current) {
            return '[demà a '.($current->hour !== 1 ? 'les' : 'la').'] LT';
        },
        'nextWeek' => static function (CarbonInterface $current) {
            return 'dddd [a '.($current->hour !== 1 ? 'les' : 'la').'] LT';
        },
        'lastDay' => static function (CarbonInterface $current) {
            return '[ahir a '.($current->hour !== 1 ? 'les' : 'la').'] LT';
        },
        'lastWeek' => static function (CarbonInterface $current) {
            return '[el] dddd [passat a '.($current->hour !== 1 ? 'les' : 'la').'] LT';
        },
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number, $period) {
        return $number.(
            ($period === 'w' || $period === 'W') ? 'a' : (
                ($number === 1) ? 'r' : (
                    ($number === 2) ? 'n' : (
                        ($number === 3) ? 'r' : (
                            ($number === 4) ? 't' : 'è'
                        )
                    )
                )
            )
        );
    },
    'months' => ['de gener', 'de febrer', 'de març', 'd\'abril', 'de maig', 'de juny', 'de juliol', 'd\'agost', 'de setembre', 'd\'octubre', 'de novembre', 'de desembre'],
    'months_standalone' => ['gener', 'febrer', 'març', 'abril', 'maig', 'juny', 'juliol', 'agost', 'setembre', 'octubre', 'novembre', 'desembre'],
    'months_short' => ['de gen.', 'de febr.', 'de març', 'd\'abr.', 'de maig', 'de juny', 'de jul.', 'd\'ag.', 'de set.', 'd\'oct.', 'de nov.', 'de des.'],
    'months_short_standalone' => ['gen.', 'febr.', 'març', 'abr.', 'maig', 'juny', 'jul.', 'ag.', 'set.', 'oct.', 'nov.', 'des.'],
    'months_regexp' => '/(D[oD]?[\s,]+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => ['diumenge', 'dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte'],
    'weekdays_short' => ['dg.', 'dl.', 'dt.', 'dc.', 'dj.', 'dv.', 'ds.'],
    'weekdays_min' => ['dg', 'dl', 'dt', 'dc', 'dj', 'dv', 'ds'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' i '],
    'meridiem' => ['a. m.', 'p. m.'],
];
