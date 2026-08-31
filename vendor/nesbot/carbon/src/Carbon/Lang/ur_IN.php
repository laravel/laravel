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
 * - Red Hat, Pune    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/ur.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['جنوری', 'فروری', 'مارچ', 'اپریل', 'مئی', 'جون', 'جولائی', 'اگست', 'ستمبر', 'اکتوبر', 'نومبر', 'دسمبر'],
    'months_short' => ['جنوری', 'فروری', 'مارچ', 'اپریل', 'مئی', 'جون', 'جولائی', 'اگست', 'ستمبر', 'اکتوبر', 'نومبر', 'دسمبر'],
    'weekdays' => ['اتوار', 'پیر', 'منگل', 'بدھ', 'جمعرات', 'جمعہ', 'سنیچر'],
    'weekdays_short' => ['اتوار', 'پیر', 'منگل', 'بدھ', 'جمعرات', 'جمعہ', 'سنیچر'],
    'weekdays_min' => ['اتوار', 'پیر', 'منگل', 'بدھ', 'جمعرات', 'جمعہ', 'سنیچر'],
    'day_of_first_week_of_year' => 1,
]);
