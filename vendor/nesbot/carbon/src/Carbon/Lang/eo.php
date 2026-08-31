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
 * - Mia Nordentoft
 * - JD Isaacks
 */
return [
    'year' => ':count jaro|:count jaroj',
    'a_year' => 'jaro|:count jaroj',
    'y' => ':count j.',
    'month' => ':count monato|:count monatoj',
    'a_month' => 'monato|:count monatoj',
    'm' => ':count mo.',
    'week' => ':count semajno|:count semajnoj',
    'a_week' => 'semajno|:count semajnoj',
    'w' => ':count sem.',
    'day' => ':count tago|:count tagoj',
    'a_day' => 'tago|:count tagoj',
    'd' => ':count t.',
    'hour' => ':count horo|:count horoj',
    'a_hour' => 'horo|:count horoj',
    'h' => ':count h.',
    'minute' => ':count minuto|:count minutoj',
    'a_minute' => 'minuto|:count minutoj',
    'min' => ':count min.',
    'second' => ':count sekundo|:count sekundoj',
    'a_second' => 'sekundoj|:count sekundoj',
    's' => ':count sek.',
    'ago' => 'antaŭ :time',
    'from_now' => 'post :time',
    'after' => ':time poste',
    'before' => ':time antaŭe',
    'diff_yesterday' => 'Hieraŭ',
    'diff_yesterday_regexp' => 'Hieraŭ(?:\\s+je)?',
    'diff_today' => 'Hodiaŭ',
    'diff_today_regexp' => 'Hodiaŭ(?:\\s+je)?',
    'diff_tomorrow' => 'Morgaŭ',
    'diff_tomorrow_regexp' => 'Morgaŭ(?:\\s+je)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY-MM-DD',
        'LL' => 'D[-a de] MMMM, YYYY',
        'LLL' => 'D[-a de] MMMM, YYYY HH:mm',
        'LLLL' => 'dddd, [la] D[-a de] MMMM, YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Hodiaŭ je] LT',
        'nextDay' => '[Morgaŭ je] LT',
        'nextWeek' => 'dddd [je] LT',
        'lastDay' => '[Hieraŭ je] LT',
        'lastWeek' => '[pasinta] dddd [je] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => ':numbera',
    'meridiem' => ['a.t.m.', 'p.t.m.'],
    'months' => ['januaro', 'februaro', 'marto', 'aprilo', 'majo', 'junio', 'julio', 'aŭgusto', 'septembro', 'oktobro', 'novembro', 'decembro'],
    'months_short' => ['jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aŭg', 'sep', 'okt', 'nov', 'dec'],
    'weekdays' => ['dimanĉo', 'lundo', 'mardo', 'merkredo', 'ĵaŭdo', 'vendredo', 'sabato'],
    'weekdays_short' => ['dim', 'lun', 'mard', 'merk', 'ĵaŭ', 'ven', 'sab'],
    'weekdays_min' => ['di', 'lu', 'ma', 'me', 'ĵa', 've', 'sa'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' kaj '],
];
