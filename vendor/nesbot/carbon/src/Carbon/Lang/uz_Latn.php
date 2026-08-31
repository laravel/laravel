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
 * - Rasulbek
 * - Ilyosjon Kamoldinov (ilyosjon09)
 */
return [
    'year' => ':count yil',
    'a_year' => '{1}bir yil|:count yil',
    'y' => ':count y',
    'month' => ':count oy',
    'a_month' => '{1}bir oy|:count oy',
    'm' => ':count o',
    'week' => ':count hafta',
    'a_week' => '{1}bir hafta|:count hafta',
    'w' => ':count h',
    'day' => ':count kun',
    'a_day' => '{1}bir kun|:count kun',
    'd' => ':count k',
    'hour' => ':count soat',
    'a_hour' => '{1}bir soat|:count soat',
    'h' => ':count soat',
    'minute' => ':count daqiqa',
    'a_minute' => '{1}bir daqiqa|:count daqiqa',
    'min' => ':count d',
    'second' => ':count soniya',
    'a_second' => '{1}soniya|:count soniya',
    's' => ':count son.',
    'ago' => ':time avval',
    'from_now' => 'Yaqin :time ichida',
    'after' => ':timedan keyin',
    'before' => ':time oldin',
    'diff_yesterday' => 'Kecha',
    'diff_yesterday_regexp' => 'Kecha(?:\\s+soat)?',
    'diff_today' => 'Bugun',
    'diff_today_regexp' => 'Bugun(?:\\s+soat)?',
    'diff_tomorrow' => 'Ertaga',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'D MMMM YYYY, dddd HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Bugun soat] LT [da]',
        'nextDay' => '[Ertaga] LT [da]',
        'nextWeek' => 'dddd [kuni soat] LT [da]',
        'lastDay' => '[Kecha soat] LT [da]',
        'lastWeek' => '[O\'tgan] dddd [kuni soat] LT [da]',
        'sameElse' => 'L',
    ],
    'months' => ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'],
    'months_short' => ['Yan', 'Fev', 'Mar', 'Apr', 'May', 'Iyun', 'Iyul', 'Avg', 'Sen', 'Okt', 'Noy', 'Dek'],
    'weekdays' => ['Yakshanba', 'Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'],
    'weekdays_short' => ['Yak', 'Dush', 'Sesh', 'Chor', 'Pay', 'Jum', 'Shan'],
    'weekdays_min' => ['Ya', 'Du', 'Se', 'Cho', 'Pa', 'Ju', 'Sha'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' va '],
    'meridiem' => ['TO', 'TK'],
];
