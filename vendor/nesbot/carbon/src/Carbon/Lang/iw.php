<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/en.php', [
    'first_day_of_week' => 0,
    'months' => ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'],
    'months_short' => ['ינו׳', 'פבר׳', 'מרץ', 'אפר׳', 'מאי', 'יוני', 'יולי', 'אוג׳', 'ספט׳', 'אוק׳', 'נוב׳', 'דצמ׳'],
    'weekdays' => ['יום ראשון', 'יום שני', 'יום שלישי', 'יום רביעי', 'יום חמישי', 'יום שישי', 'יום שבת'],
    'weekdays_short' => ['יום א׳', 'יום ב׳', 'יום ג׳', 'יום ד׳', 'יום ה׳', 'יום ו׳', 'שבת'],
    'weekdays_min' => ['א׳', 'ב׳', 'ג׳', 'ד׳', 'ה׳', 'ו׳', 'ש׳'],
    'meridiem' => ['לפנה״צ', 'אחה״צ'],
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'D.M.YYYY',
        'LL' => 'D בMMM YYYY',
        'LLL' => 'D בMMMM YYYY H:mm',
        'LLLL' => 'dddd, D בMMMM YYYY H:mm',
    ],

    'year' => ':count שנה',
    'y' => ':count שנה',
    'a_year' => ':count שנה',

    'month' => ':count חודש',
    'm' => ':count חודש',
    'a_month' => ':count חודש',

    'week' => ':count שבוע',
    'w' => ':count שבוע',
    'a_week' => ':count שבוע',

    'day' => ':count יום',
    'd' => ':count יום',
    'a_day' => ':count יום',

    'hour' => ':count שעה',
    'h' => ':count שעה',
    'a_hour' => ':count שעה',

    'minute' => ':count דקה',
    'min' => ':count דקה',
    'a_minute' => ':count דקה',

    'second' => ':count שניה',
    's' => ':count שניה',
    'a_second' => ':count שניה',

    'ago' => 'לפני :time',
    'from_now' => 'בעוד :time',
]);
