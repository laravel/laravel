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
 * - BAKTETE Miloud
 */
return [
    'year' => ':count ⵓⵙⴳⴳⵯⴰⵙ|:count ⵉⵙⴳⴳⵓⵙⴰ',
    'a_year' => 'ⵓⵙⴳⴳⵯⴰⵙ|:count ⵉⵙⴳⴳⵓⵙⴰ',
    'y' => ':count ⵓⵙⴳⴳⵯⴰⵙ|:count ⵉⵙⴳⴳⵓⵙⴰ',
    'month' => ':count ⵡⴰⵢⵢⵓⵔ|:count ⴰⵢⵢⵓⵔⵏ',
    'a_month' => 'ⵉⴷⵊ ⵡⴰⵢⵢⵓⵔ|:count ⴰⵢⵢⵓⵔⵏ',
    'm' => ':count ⴰⵢⵢⵓⵔⵏ',
    'week' => ':count ⵉⵎⴰⵍⴰⵙⵙ|:count ⵉⵎⴰⵍⴰⵙⵙⵏ',
    'a_week' => 'ⵉⵛⵜ ⵉⵎⴰⵍⴰⵙⵙ|:count ⵉⵎⴰⵍⴰⵙⵙⵏ',
    'w' => ':count ⵉⵎⴰⵍⴰⵙⵙ.',
    'day' => ':count ⵡⴰⵙⵙ|:count ⵓⵙⵙⴰⵏ',
    'a_day' => 'ⵉⴷⵊ ⵡⴰⵙⵙ|:count ⵓⵙⵙⴰⵏ',
    'd' => ':count ⵓ',
    'hour' => ':count ⵜⵙⵔⴰⴳⵜ|:count ⵜⵉⵙⵔⴰⴳⵉⵏ',
    'a_hour' => 'ⵉⵛⵜ ⵜⵙⵔⴰⴳⵜ|:count ⵜⵉⵙⵔⴰⴳⵉⵏ',
    'h' => ':count ⵜ',
    'minute' => ':count ⵜⵓⵙⴷⵉⴷⵜ|:count ⵜⵓⵙⴷⵉⴷⵉⵏ',
    'a_minute' => 'ⵉⵛⵜ ⵜⵓⵙⴷⵉⴷⵜ|:count ⵜⵓⵙⴷⵉⴷⵉⵏ',
    'min' => ':count ⵜⵓⵙ',
    'second' => ':count ⵜⵙⵉⵏⵜ|:count ⵜⵉⵙⵉⵏⴰ',
    'a_second' => 'ⴽⵔⴰ ⵜⵉⵙⵉⵏⴰ|:count ⵜⵉⵙⵉⵏⴰ',
    's' => ':count ⵜ',
    'ago' => 'ⵣⴳ :time',
    'from_now' => 'ⴷⴳ :time',
    'after' => ':time ⴰⵡⴰⵔ',
    'before' => ':time ⴷⴰⵜ',
    'diff_now' => 'ⴰⴷⵡⴰⵍⵉ',
    'diff_today' => 'ⴰⵙⵙ',
    'diff_today_regexp' => 'ⴰⵙⵙ(?:\\s+ⴰ/ⴰⴷ)?(?:\\s+ⴳ)?',
    'diff_yesterday' => 'ⴰⵙⵙⵏⵏⴰⵟ',
    'diff_yesterday_regexp' => 'ⴰⵙⵙⵏⵏⴰⵟ(?:\\s+ⴳ)?',
    'diff_tomorrow' => 'ⴰⵙⴽⴽⴰ',
    'diff_tomorrow_regexp' => 'ⴰⵙⴽⴽⴰ(?:\\s+ⴳ)?',
    'diff_before_yesterday' => 'ⴼⵔ ⵉⴹⵏⵏⴰⵟ',
    'diff_after_tomorrow' => 'ⵏⴰⴼ ⵓⵙⴽⴽⴰ',
    'period_recurrences' => ':count ⵜⵉⴽⴽⴰⵍ',
    'period_interval' => 'ⴽⵓ :interval',
    'period_start_date' => 'ⴳ :date',
    'period_end_date' => 'ⵉ :date',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[ⴰⵙⵙ ⴰ/ⴰⴷ ⴳ] LT',
        'nextDay' => '[ⴰⵙⴽⴽⴰ ⴳ] LT',
        'nextWeek' => 'dddd [ⴳ] LT',
        'lastDay' => '[ⴰⵙⵙⵏⵏⴰⵟ ⴳ] LT',
        'lastWeek' => 'dddd [ⴰⵎⴳⴳⴰⵔⵓ ⴳ] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => ['ⵜⵉⴼⴰⵡⵜ', 'ⵜⴰⴷⴳⴳⵯⴰⵜ'],
    'months' => ['ⵉⵏⵏⴰⵢⵔ', 'ⴱⵕⴰⵢⵕ', 'ⵎⴰⵕⵚ', 'ⵉⴱⵔⵉⵔ', 'ⵎⴰⵢⵢⵓ', 'ⵢⵓⵏⵢⵓ', 'ⵢⵓⵍⵢⵓⵣ', 'ⵖⵓⵛⵜ', 'ⵛⵓⵜⴰⵏⴱⵉⵔ', 'ⴽⵟⵓⴱⵕ', 'ⵏⵓⵡⴰⵏⴱⵉⵔ', 'ⴷⵓⵊⴰⵏⴱⵉⵔ'],
    'months_short' => ['ⵉⵏⵏ', 'ⴱⵕⴰ', 'ⵎⴰⵕ', 'ⵉⴱⵔ', 'ⵎⴰⵢ', 'ⵢⵓⵏ', 'ⵢⵓⵍ', 'ⵖⵓⵛ', 'ⵛⵓⵜ', 'ⴽⵟⵓ', 'ⵏⵓⵡ', 'ⴷⵓⵊ'],
    'weekdays' => ['ⵓⵙⴰⵎⴰⵙ', 'ⵡⴰⵢⵏⴰⵙ', 'ⵓⵙⵉⵏⴰⵙ', 'ⵡⴰⴽⵕⴰⵙ', 'ⵓⴽⵡⴰⵙ', 'ⵓⵙⵉⵎⵡⴰⵙ', 'ⵓⵙⵉⴹⵢⴰⵙ'],
    'weekdays_short' => ['ⵓⵙⴰ', 'ⵡⴰⵢ', 'ⵓⵙⵉ', 'ⵡⴰⴽ', 'ⵓⴽⵡ', 'ⵓⵙⵉⵎ', 'ⵓⵙⵉⴹ'],
    'weekdays_min' => ['ⵓⵙⴰ', 'ⵡⴰⵢ', 'ⵓⵙⵉ', 'ⵡⴰⴽ', 'ⵓⴽⵡ', 'ⵓⵙⵉⵎ', 'ⵓⵙⵉⴹ'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' ⴷ '],
];
