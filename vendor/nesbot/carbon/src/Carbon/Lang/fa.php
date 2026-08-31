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
 * - Nasser Ghiasi
 * - JD Isaacks
 * - Hossein Jabbari
 * - nimamo
 * - hafezdivandari
 * - Hassan Pezeshk (hpez)
 */
return [
    'year' => ':count سال',
    'a_year' => 'یک سال'.'|:count '.'سال',
    'y' => ':count سال',
    'month' => ':count ماه',
    'a_month' => 'یک ماه'.'|:count '.'ماه',
    'm' => ':count ماه',
    'week' => ':count هفته',
    'a_week' => 'یک هفته'.'|:count '.'هفته',
    'w' => ':count هفته',
    'day' => ':count روز',
    'a_day' => 'یک روز'.'|:count '.'روز',
    'd' => ':count روز',
    'hour' => ':count ساعت',
    'a_hour' => 'یک ساعت'.'|:count '.'ساعت',
    'h' => ':count ساعت',
    'minute' => ':count دقیقه',
    'a_minute' => 'یک دقیقه'.'|:count '.'دقیقه',
    'min' => ':count دقیقه',
    'second' => ':count ثانیه',
    's' => ':count ثانیه',
    'ago' => ':time پیش',
    'from_now' => ':time دیگر',
    'after' => ':time پس از',
    'before' => ':time پیش از',
    'diff_now' => 'اکنون',
    'diff_today' => 'امروز',
    'diff_today_regexp' => 'امروز(?:\\s+ساعت)?',
    'diff_yesterday' => 'دیروز',
    'diff_yesterday_regexp' => 'دیروز(?:\\s+ساعت)?',
    'diff_tomorrow' => 'فردا',
    'diff_tomorrow_regexp' => 'فردا(?:\\s+ساعت)?',
    'formats' => [
        'LT' => 'OH:Om',
        'LTS' => 'OH:Om:Os',
        'L' => 'OD/OM/OY',
        'LL' => 'OD MMMM OY',
        'LLL' => 'OD MMMM OY OH:Om',
        'LLLL' => 'dddd, OD MMMM OY OH:Om',
    ],
    'calendar' => [
        'sameDay' => '[امروز ساعت] LT',
        'nextDay' => '[فردا ساعت] LT',
        'nextWeek' => 'dddd [ساعت] LT',
        'lastDay' => '[دیروز ساعت] LT',
        'lastWeek' => 'dddd [پیش] [ساعت] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => ':timeم',
    'meridiem' => ['قبل از ظهر', 'بعد از ظهر'],
    'months' => ['ژانویه', 'فوریه', 'مارس', 'آوریل', 'مه', 'ژوئن', 'ژوئیه', 'اوت', 'سپتامبر', 'اکتبر', 'نوامبر', 'دسامبر'],
    'months_short' => ['ژانویه', 'فوریه', 'مارس', 'آوریل', 'مه', 'ژوئن', 'ژوئیه', 'اوت', 'سپتامبر', 'اکتبر', 'نوامبر', 'دسامبر'],
    'weekdays' => ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'],
    'weekdays_short' => ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'],
    'weekdays_min' => ['ی', 'د', 'س', 'چ', 'پ', 'ج', 'ش'],
    'first_day_of_week' => 6,
    'day_of_first_week_of_year' => 1,
    'list' => ['، ', ' و '],
    'alt_numbers' => ['۰۰', '۰۱', '۰۲', '۰۳', '۰۴', '۰۵', '۰۶', '۰۷', '۰۸', '۰۹', '۱۰', '۱۱', '۱۲', '۱۳', '۱۴', '۱۵', '۱۶', '۱۷', '۱۸', '۱۹', '۲۰', '۲۱', '۲۲', '۲۳', '۲۴', '۲۵', '۲۶', '۲۷', '۲۸', '۲۹', '۳۰', '۳۱', '۳۲', '۳۳', '۳۴', '۳۵', '۳۶', '۳۷', '۳۸', '۳۹', '۴۰', '۴۱', '۴۲', '۴۳', '۴۴', '۴۵', '۴۶', '۴۷', '۴۸', '۴۹', '۵۰', '۵۱', '۵۲', '۵۳', '۵۴', '۵۵', '۵۶', '۵۷', '۵۸', '۵۹', '۶۰', '۶۱', '۶۲', '۶۳', '۶۴', '۶۵', '۶۶', '۶۷', '۶۸', '۶۹', '۷۰', '۷۱', '۷۲', '۷۳', '۷۴', '۷۵', '۷۶', '۷۷', '۷۸', '۷۹', '۸۰', '۸۱', '۸۲', '۸۳', '۸۴', '۸۵', '۸۶', '۸۷', '۸۸', '۸۹', '۹۰', '۹۱', '۹۲', '۹۳', '۹۴', '۹۵', '۹۶', '۹۷', '۹۸', '۹۹'],
    'months_short_standalone' => ['ژانویه', 'فوریه', 'مارس', 'آوریل', 'مه', 'ژوئن', 'ژوئیه', 'اوت', 'سپتامبر', 'اکتبر', 'نوامبر', 'دسامبر'],
    'weekend' => [5, 5],
];
