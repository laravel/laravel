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
 * - Michael Hohl
 * - sheriffmarley
 * - dennisoderwald
 * - Timo
 * - Karag2006
 * - Pete Scopes (pdscopes)
 */
return [
    'year' => ':count Jahr|:count Jahre',
    'a_year' => 'ein Jahr|:count Jahre',
    'y' => ':count J.',
    'month' => ':count Monat|:count Monate',
    'a_month' => 'ein Monat|:count Monate',
    'm' => ':count Mon.',
    'week' => ':count Woche|:count Wochen',
    'a_week' => 'eine Woche|:count Wochen',
    'w' => ':count Wo.',
    'day' => ':count Tag|:count Tage',
    'a_day' => 'ein Tag|:count Tage',
    'd' => ':count Tg.',
    'hour' => ':count Stunde|:count Stunden',
    'a_hour' => 'eine Stunde|:count Stunden',
    'h' => ':count Std.',
    'minute' => ':count Minute|:count Minuten',
    'a_minute' => 'eine Minute|:count Minuten',
    'min' => ':count Min.',
    'second' => ':count Sekunde|:count Sekunden',
    'a_second' => 'ein paar Sekunden|:count Sekunden',
    's' => ':count Sek.',
    'millisecond' => ':count Millisekunde|:count Millisekunden',
    'a_millisecond' => 'eine Millisekunde|:count Millisekunden',
    'ms' => ':countms',
    'microsecond' => ':count Mikrosekunde|:count Mikrosekunden',
    'a_microsecond' => 'eine Mikrosekunde|:count Mikrosekunden',
    'µs' => ':countµs',
    'ago' => 'vor :time',
    'from_now' => 'in :time',
    'after' => ':time später',
    'before' => ':time zuvor',

    'year_from_now' => ':count Jahr|:count Jahren',
    'month_from_now' => ':count Monat|:count Monaten',
    'week_from_now' => ':count Woche|:count Wochen',
    'day_from_now' => ':count Tag|:count Tagen',
    'year_ago' => ':count Jahr|:count Jahren',
    'month_ago' => ':count Monat|:count Monaten',
    'week_ago' => ':count Woche|:count Wochen',
    'day_ago' => ':count Tag|:count Tagen',
    'a_year_from_now' => 'ein Jahr|:count Jahren',
    'a_month_from_now' => 'ein Monat|:count Monaten',
    'a_week_from_now' => 'eine Woche|:count Wochen',
    'a_day_from_now' => 'ein Tag|:count Tagen',
    'a_year_ago' => 'ein Jahr|:count Jahren',
    'a_month_ago' => 'ein Monat|:count Monaten',
    'a_week_ago' => 'eine Woche|:count Wochen',
    'a_day_ago' => 'ein Tag|:count Tagen',

    'diff_now' => 'Gerade eben',
    'diff_today' => 'heute',
    'diff_today_regexp' => 'heute(?:\\s+um)?',
    'diff_yesterday' => 'Gestern',
    'diff_yesterday_regexp' => 'gestern(?:\\s+um)?',
    'diff_tomorrow' => 'Morgen',
    'diff_tomorrow_regexp' => 'morgen(?:\\s+um)?',
    'diff_before_yesterday' => 'Vorgestern',
    'diff_after_tomorrow' => 'Übermorgen',

    'period_recurrences' => 'einmal|:count mal',
    'period_interval' => static function (string $interval = '') {
        /** @var string $output */
        $output = preg_replace('/^(ein|eine|1)\s+/u', '', $interval);

        if (preg_match('/^(ein|1)( Monat| Mon.| Tag| Tg.)/u', $interval)) {
            return "jeden $output";
        }

        if (preg_match('/^(ein|1)( Jahr| J.)/u', $interval)) {
            return "jedes $output";
        }

        return "jede $output";
    },
    'period_start_date' => 'von :date',
    'period_end_date' => 'bis :date',

    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM YYYY',
        'LLL' => 'D. MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D. MMMM YYYY HH:mm',
    ],

    'calendar' => [
        'sameDay' => '[heute um] LT [Uhr]',
        'nextDay' => '[morgen um] LT [Uhr]',
        'nextWeek' => 'dddd [um] LT [Uhr]',
        'lastDay' => '[gestern um] LT [Uhr]',
        'lastWeek' => '[letzten] dddd [um] LT [Uhr]',
        'sameElse' => 'L',
    ],

    'months' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
    'months_short' => ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
    'weekdays' => ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
    'weekdays_short' => ['So.', 'Mo.', 'Di.', 'Mi.', 'Do.', 'Fr.', 'Sa.'],
    'weekdays_min' => ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
    'ordinal' => ':number.',
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' und '],
    'ordinal_words' => [
        'of' => 'im',
        'first' => 'erster',
        'second' => 'zweiter',
        'third' => 'dritter',
        'fourth' => 'vierten',
        'fifth' => 'fünfter',
        'last' => 'letzten',
    ],
];
