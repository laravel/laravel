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
 * - Alexander Tømmerås
 * - Sigurd Gartmann
 * - JD Isaacks
 */
return [
    'year' => ':count år|:count år',
    'a_year' => 'ett år|:count år',
    'y' => ':count år|:count år',
    'month' => ':count måned|:count måneder',
    'a_month' => 'en måned|:count måneder',
    'm' => ':count md.',
    'week' => ':count uke|:count uker',
    'a_week' => 'en uke|:count uker',
    'w' => ':count u.',
    'day' => ':count dag|:count dager',
    'a_day' => 'en dag|:count dager',
    'd' => ':count d.',
    'hour' => ':count time|:count timer',
    'a_hour' => 'en time|:count timer',
    'h' => ':count t',
    'minute' => ':count minutt|:count minutter',
    'a_minute' => 'ett minutt|:count minutter',
    'min' => ':count min',
    'second' => ':count sekund|:count sekunder',
    'a_second' => 'noen sekunder|:count sekunder',
    's' => ':count sek',
    'ago' => ':time siden',
    'from_now' => 'om :time',
    'after' => ':time etter',
    'before' => ':time før',
    'diff_now' => 'akkurat nå',
    'diff_today' => 'i dag',
    'diff_today_regexp' => 'i dag(?:\\s+kl.)?',
    'diff_yesterday' => 'i går',
    'diff_yesterday_regexp' => 'i går(?:\\s+kl.)?',
    'diff_tomorrow' => 'i morgen',
    'diff_tomorrow_regexp' => 'i morgen(?:\\s+kl.)?',
    'diff_before_yesterday' => 'i forgårs',
    'diff_after_tomorrow' => 'i overmorgen',
    'period_recurrences' => 'en gang|:count ganger',
    'period_interval' => 'hver :interval',
    'period_start_date' => 'fra :date',
    'period_end_date' => 'til :date',
    'months' => ['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember'],
    'months_short' => ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'],
    'weekdays' => ['søndag', 'mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag'],
    'weekdays_short' => ['søn', 'man', 'tir', 'ons', 'tor', 'fre', 'lør'],
    'weekdays_min' => ['sø', 'ma', 'ti', 'on', 'to', 'fr', 'lø'],
    'ordinal' => ':number.',
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM YYYY',
        'LLL' => 'D. MMMM YYYY [kl.] HH:mm',
        'LLLL' => 'dddd D. MMMM YYYY [kl.] HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[i dag kl.] LT',
        'nextDay' => '[i morgen kl.] LT',
        'nextWeek' => 'dddd [kl.] LT',
        'lastDay' => '[i går kl.] LT',
        'lastWeek' => '[forrige] dddd [kl.] LT',
        'sameElse' => 'L',
    ],
    'list' => [', ', ' og '],
    'meridiem' => ['a.m.', 'p.m.'],
];
