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
 * - IBM Globalization Center of Competency, Yamato Software Laboratory    bug-glibc-locales@gnu.org
 * - Abdullah-Alhariri
 */
return array_replace_recursive(require __DIR__.'/ar.php', [
    'formats' => [
        'L' => 'DD MMM, YYYY',
    ],
    'months' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
    'months_short' => ['ينا', 'فبر', 'مار', 'أبر', 'ماي', 'يون', 'يول', 'أغس', 'سبت', 'أكت', 'نوف', 'ديس'],
    'weekdays' => ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
    'weekdays_short' => ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'],
    'weekdays_min' => ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'],
    'first_day_of_week' => 6,
    'day_of_first_week_of_year' => 1,
    'alt_numbers' => ['۰۰', '۰۱', '۰۲', '۰۳', '۰٤', '۰٥', '۰٦', '۰۷', '۰۸', '۰۹', '۱۰', '۱۱', '۱۲', '۱۳', '۱٤', '۱٥', '۱٦', '۱۷', '۱۸', '۱۹', '۲۰', '۲۱', '۲۲', '۲۳', '۲٤', '۲٥', '۲٦', '۲۷', '۲۸', '۲۹', '۳۰', '۳۱', '۳۲', '۳۳', '۳٤', '۳٥', '۳٦', '۳۷', '۳۸', '۳۹', '٤۰', '٤۱', '٤۲', '٤۳', '٤٤', '٤٥', '٤٦', '٤۷', '٤۸', '٤۹', '٥۰', '٥۱', '٥۲', '٥۳', '٥٤', '٥٥', '٥٦', '٥۷', '٥۸', '٥۹', '٦۰', '٦۱', '٦۲', '٦۳', '٦٤', '٦٥', '٦٦', '٦۷', '٦۸', '٦۹', '۷۰', '۷۱', '۷۲', '۷۳', '۷٤', '۷٥', '۷٦', '۷۷', '۷۸', '۷۹', '۸۰', '۸۱', '۸۲', '۸۳', '۸٤', '۸٥', '۸٦', '۸۷', '۸۸', '۸۹', '۹۰', '۹۱', '۹۲', '۹۳', '۹٤', '۹٥', '۹٦', '۹۷', '۹۸', '۹۹'],
]);
