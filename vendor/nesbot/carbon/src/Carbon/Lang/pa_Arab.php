<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/ur.php', [
    'weekdays' => ['اتوار', 'پیر', 'منگل', 'بُدھ', 'جمعرات', 'جمعہ', 'ہفتہ'],
    'weekdays_short' => ['اتوار', 'پیر', 'منگل', 'بُدھ', 'جمعرات', 'جمعہ', 'ہفتہ'],
    'weekdays_min' => ['اتوار', 'پیر', 'منگل', 'بُدھ', 'جمعرات', 'جمعہ', 'ہفتہ'],
    'months' => ['جنوری', 'فروری', 'مارچ', 'اپریل', 'مئ', 'جون', 'جولائی', 'اگست', 'ستمبر', 'اکتوبر', 'نومبر', 'دسمبر'],
    'months_short' => ['جنوری', 'فروری', 'مارچ', 'اپریل', 'مئ', 'جون', 'جولائی', 'اگست', 'ستمبر', 'اکتوبر', 'نومبر', 'دسمبر'],
    'formats' => [
        'LT' => 'h:mm a',
        'LTS' => 'h:mm:ss a',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY h:mm a',
        'LLLL' => 'dddd, DD MMMM YYYY h:mm a',
    ],
]);
