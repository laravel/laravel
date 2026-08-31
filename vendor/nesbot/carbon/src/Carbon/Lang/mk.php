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
 * - Sashko Todorov
 * - Josh Soref
 * - François B
 * - Serhan Apaydın
 * - Borislav Mickov
 * - JD Isaacks
 * - Tomi Atanasoski
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count година|:count години',
    'a_year' => 'година|:count години',
    'y' => ':count год.',
    'month' => ':count месец|:count месеци',
    'a_month' => 'месец|:count месеци',
    'm' => ':count месец|:count месеци',
    'week' => ':count седмица|:count седмици',
    'a_week' => 'седмица|:count седмици',
    'w' => ':count седмица|:count седмици',
    'day' => ':count ден|:count дена',
    'a_day' => 'ден|:count дена',
    'd' => ':count ден|:count дена',
    'hour' => ':count час|:count часа',
    'a_hour' => 'час|:count часа',
    'h' => ':count час|:count часа',
    'minute' => ':count минута|:count минути',
    'a_minute' => 'минута|:count минути',
    'min' => ':count мин.',
    'second' => ':count секунда|:count секунди',
    'a_second' => 'неколку секунди|:count секунди',
    's' => ':count сек.',
    'ago' => 'пред :time',
    'from_now' => 'после :time',
    'after' => 'по :time',
    'before' => 'пред :time',
    'diff_now' => 'сега',
    'diff_today' => 'Денес',
    'diff_today_regexp' => 'Денес(?:\\s+во)?',
    'diff_yesterday' => 'вчера',
    'diff_yesterday_regexp' => 'Вчера(?:\\s+во)?',
    'diff_tomorrow' => 'утре',
    'diff_tomorrow_regexp' => 'Утре(?:\\s+во)?',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'D.MM.YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY H:mm',
        'LLLL' => 'dddd, D MMMM YYYY H:mm',
    ],
    'calendar' => [
        'sameDay' => '[Денес во] LT',
        'nextDay' => '[Утре во] LT',
        'nextWeek' => '[Во] dddd [во] LT',
        'lastDay' => '[Вчера во] LT',
        'lastWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0, 3, 6 => '[Изминатата] dddd [во] LT',
            default => '[Изминатиот] dddd [во] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number) {
        $lastDigit = $number % 10;
        $last2Digits = $number % 100;
        if ($number === 0) {
            return $number.'-ев';
        }
        if ($last2Digits === 0) {
            return $number.'-ен';
        }
        if ($last2Digits > 10 && $last2Digits < 20) {
            return $number.'-ти';
        }
        if ($lastDigit === 1) {
            return $number.'-ви';
        }
        if ($lastDigit === 2) {
            return $number.'-ри';
        }
        if ($lastDigit === 7 || $lastDigit === 8) {
            return $number.'-ми';
        }

        return $number.'-ти';
    },
    'months' => ['јануари', 'февруари', 'март', 'април', 'мај', 'јуни', 'јули', 'август', 'септември', 'октомври', 'ноември', 'декември'],
    'months_short' => ['јан', 'фев', 'мар', 'апр', 'мај', 'јун', 'јул', 'авг', 'сеп', 'окт', 'ное', 'дек'],
    'weekdays' => ['недела', 'понеделник', 'вторник', 'среда', 'четврток', 'петок', 'сабота'],
    'weekdays_short' => ['нед', 'пон', 'вто', 'сре', 'чет', 'пет', 'саб'],
    'weekdays_min' => ['нe', 'пo', 'вт', 'ср', 'че', 'пе', 'сa'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' и '],
    'meridiem' => ['АМ', 'ПМ'],
];
