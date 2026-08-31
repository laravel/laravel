<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Authors:
 * - Josh Soref
 * - Nusret Parlak
 * - JD Isaacks
 * - Atef Ben Ali (atefBB)
 * - Mohamed Sabil (mohamedsabil83)
 * - Abdullah-Alhariri
 */
$months = [
    'يناير',
    'فبراير',
    'مارس',
    'أبريل',
    'ماي',
    'يونيو',
    'يوليوز',
    'غشت',
    'شتنبر',
    'أكتوبر',
    'نونبر',
    'دجنبر',
];

return [
    'year' => implode('|', ['{0}:count سنة', '{1}سنة', '{2}سنتين', ']2,11[:count سنوات', ']10,Inf[:count سنة']),
    'a_year' => implode('|', ['{0}:count سنة', '{1}سنة', '{2}سنتين', ']2,11[:count سنوات', ']10,Inf[:count سنة']),
    'month' => implode('|', ['{0}:count شهر', '{1}شهر', '{2}شهرين', ']2,11[:count أشهر', ']10,Inf[:count شهر']),
    'a_month' => implode('|', ['{0}:count شهر', '{1}شهر', '{2}شهرين', ']2,11[:count أشهر', ']10,Inf[:count شهر']),
    'week' => implode('|', ['{0}:count أسبوع', '{1}أسبوع', '{2}أسبوعين', ']2,11[:count أسابيع', ']10,Inf[:count أسبوع']),
    'a_week' => implode('|', ['{0}:count أسبوع', '{1}أسبوع', '{2}أسبوعين', ']2,11[:count أسابيع', ']10,Inf[:count أسبوع']),
    'day' => implode('|', ['{0}:count يوم', '{1}يوم', '{2}يومين', ']2,11[:count أيام', ']10,Inf[:count يوم']),
    'a_day' => implode('|', ['{0}:count يوم', '{1}يوم', '{2}يومين', ']2,11[:count أيام', ']10,Inf[:count يوم']),
    'hour' => implode('|', ['{0}:count ساعة', '{1}ساعة', '{2}ساعتين', ']2,11[:count ساعات', ']10,Inf[:count ساعة']),
    'a_hour' => implode('|', ['{0}:count ساعة', '{1}ساعة', '{2}ساعتين', ']2,11[:count ساعات', ']10,Inf[:count ساعة']),
    'minute' => implode('|', ['{0}:count دقيقة', '{1}دقيقة', '{2}دقيقتين', ']2,11[:count دقائق', ']10,Inf[:count دقيقة']),
    'a_minute' => implode('|', ['{0}:count دقيقة', '{1}دقيقة', '{2}دقيقتين', ']2,11[:count دقائق', ']10,Inf[:count دقيقة']),
    'second' => implode('|', ['{0}:count ثانية', '{1}ثانية', '{2}ثانيتين', ']2,11[:count ثواني', ']10,Inf[:count ثانية']),
    'a_second' => implode('|', ['{0}:count ثانية', '{1}ثانية', '{2}ثانيتين', ']2,11[:count ثواني', ']10,Inf[:count ثانية']),
    'ago' => 'منذ :time',
    'from_now' => 'في :time',
    'after' => 'بعد :time',
    'before' => 'قبل :time',
    'diff_now' => 'الآن',
    'diff_today' => 'اليوم',
    'diff_today_regexp' => 'اليوم(?:\\s+على)?(?:\\s+الساعة)?',
    'diff_yesterday' => 'أمس',
    'diff_yesterday_regexp' => 'أمس(?:\\s+على)?(?:\\s+الساعة)?',
    'diff_tomorrow' => 'غداً',
    'diff_tomorrow_regexp' => 'غدا(?:\\s+على)?(?:\\s+الساعة)?',
    'diff_before_yesterday' => 'قبل الأمس',
    'diff_after_tomorrow' => 'بعد غد',
    'period_recurrences' => implode('|', ['{0}مرة', '{1}مرة', '{2}:count مرتين', ']2,11[:count مرات', ']10,Inf[:count مرة']),
    'period_interval' => 'كل :interval',
    'period_start_date' => 'من :date',
    'period_end_date' => 'إلى :date',
    'months' => $months,
    'months_short' => $months,
    'weekdays' => ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
    'weekdays_short' => ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'],
    'weekdays_min' => ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'],
    'list' => ['، ', ' و '],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[اليوم على الساعة] LT',
        'nextDay' => '[غدا على الساعة] LT',
        'nextWeek' => 'dddd [على الساعة] LT',
        'lastDay' => '[أمس على الساعة] LT',
        'lastWeek' => 'dddd [على الساعة] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => ['ص', 'م'],
    'weekend' => [5, 6],
    'alt_numbers' => ['۰۰', '۰۱', '۰۲', '۰۳', '۰٤', '۰٥', '۰٦', '۰۷', '۰۸', '۰۹', '۱۰', '۱۱', '۱۲', '۱۳', '۱٤', '۱٥', '۱٦', '۱۷', '۱۸', '۱۹', '۲۰', '۲۱', '۲۲', '۲۳', '۲٤', '۲٥', '۲٦', '۲۷', '۲۸', '۲۹', '۳۰', '۳۱', '۳۲', '۳۳', '۳٤', '۳٥', '۳٦', '۳۷', '۳۸', '۳۹', '٤۰', '٤۱', '٤۲', '٤۳', '٤٤', '٤٥', '٤٦', '٤۷', '٤۸', '٤۹', '٥۰', '٥۱', '٥۲', '٥۳', '٥٤', '٥٥', '٥٦', '٥۷', '٥۸', '٥۹', '٦۰', '٦۱', '٦۲', '٦۳', '٦٤', '٦٥', '٦٦', '٦۷', '٦۸', '٦۹', '۷۰', '۷۱', '۷۲', '۷۳', '۷٤', '۷٥', '۷٦', '۷۷', '۷۸', '۷۹', '۸۰', '۸۱', '۸۲', '۸۳', '۸٤', '۸٥', '۸٦', '۸۷', '۸۸', '۸۹', '۹۰', '۹۱', '۹۲', '۹۳', '۹٤', '۹٥', '۹٦', '۹۷', '۹۸', '۹۹'],
];
