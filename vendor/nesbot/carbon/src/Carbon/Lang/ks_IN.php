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
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'M/D/YY',
    ],
    'months' => ['جنؤری', 'فرؤری', 'مارٕچ', 'اپریل', 'میٔ', 'جوٗن', 'جوٗلایی', 'اگست', 'ستمبر', 'اکتوٗبر', 'نومبر', 'دسمبر'],
    'months_short' => ['جنؤری', 'فرؤری', 'مارٕچ', 'اپریل', 'میٔ', 'جوٗن', 'جوٗلایی', 'اگست', 'ستمبر', 'اکتوٗبر', 'نومبر', 'دسمبر'],
    'weekdays' => ['آتهوار', 'ژءندروار', 'بوءںوار', 'بودهوار', 'برىسوار', 'جمع', 'بٹوار'],
    'weekdays_short' => ['آتهوار', 'ژءنتروار', 'بوءںوار', 'بودهوار', 'برىسوار', 'جمع', 'بٹوار'],
    'weekdays_min' => ['آتهوار', 'ژءنتروار', 'بوءںوار', 'بودهوار', 'برىسوار', 'جمع', 'بٹوار'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['دوپھربرونھ', 'دوپھرپتھ'],

    'year' => ':count آب', // less reliable
    'y' => ':count آب', // less reliable
    'a_year' => ':count آب', // less reliable

    'month' => ':count रान्', // less reliable
    'm' => ':count रान्', // less reliable
    'a_month' => ':count रान्', // less reliable

    'week' => ':count آتھٕوار', // less reliable
    'w' => ':count آتھٕوار', // less reliable
    'a_week' => ':count آتھٕوار', // less reliable

    'hour' => ':count سۄن', // less reliable
    'h' => ':count سۄن', // less reliable
    'a_hour' => ':count سۄن', // less reliable

    'minute' => ':count فَن', // less reliable
    'min' => ':count فَن', // less reliable
    'a_minute' => ':count فَن', // less reliable

    'second' => ':count दोʼयुम', // less reliable
    's' => ':count दोʼयुम', // less reliable
    'a_second' => ':count दोʼयुम', // less reliable
]);
