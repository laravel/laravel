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
 * - JD Isaacks
 */
return [
    'year' => 'urte bat|:count urte',
    'y' => 'Urte 1|:count urte',
    'month' => 'hilabete bat|:count hilabete',
    'm' => 'Hile 1|:count hile',
    'week' => 'Aste 1|:count aste',
    'w' => 'Aste 1|:count aste',
    'day' => 'egun bat|:count egun',
    'd' => 'Egun 1|:count egun',
    'hour' => 'ordu bat|:count ordu',
    'h' => 'Ordu 1|:count ordu',
    'minute' => 'minutu bat|:count minutu',
    'min' => 'Minutu 1|:count minutu',
    'second' => 'segundo batzuk|:count segundo',
    's' => 'Segundu 1|:count segundu',
    'ago' => 'duela :time',
    'from_now' => ':time barru',
    'after' => ':time geroago',
    'before' => ':time lehenago',
    'diff_now' => 'orain',
    'diff_today' => 'gaur',
    'diff_yesterday' => 'atzo',
    'diff_tomorrow' => 'bihar',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY-MM-DD',
        'LL' => 'YYYY[ko] MMMM[ren] D[a]',
        'LLL' => 'YYYY[ko] MMMM[ren] D[a] HH:mm',
        'LLLL' => 'dddd, YYYY[ko] MMMM[ren] D[a] HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[gaur] LT[etan]',
        'nextDay' => '[bihar] LT[etan]',
        'nextWeek' => 'dddd LT[etan]',
        'lastDay' => '[atzo] LT[etan]',
        'lastWeek' => '[aurreko] dddd LT[etan]',
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['urtarrila', 'otsaila', 'martxoa', 'apirila', 'maiatza', 'ekaina', 'uztaila', 'abuztua', 'iraila', 'urria', 'azaroa', 'abendua'],
    'months_short' => ['urt.', 'ots.', 'mar.', 'api.', 'mai.', 'eka.', 'uzt.', 'abu.', 'ira.', 'urr.', 'aza.', 'abe.'],
    'weekdays' => ['igandea', 'astelehena', 'asteartea', 'asteazkena', 'osteguna', 'ostirala', 'larunbata'],
    'weekdays_short' => ['ig.', 'al.', 'ar.', 'az.', 'og.', 'ol.', 'lr.'],
    'weekdays_min' => ['ig', 'al', 'ar', 'az', 'og', 'ol', 'lr'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' eta '],
    'meridiem' => ['g', 'a'],
];
