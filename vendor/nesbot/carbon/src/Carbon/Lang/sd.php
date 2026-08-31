<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$months = [
    'جنوري',
    'فيبروري',
    'مارچ',
    'اپريل',
    'مئي',
    'جون',
    'جولاءِ',
    'آگسٽ',
    'سيپٽمبر',
    'آڪٽوبر',
    'نومبر',
    'ڊسمبر',
];

$weekdays = [
    'آچر',
    'سومر',
    'اڱارو',
    'اربع',
    'خميس',
    'جمع',
    'ڇنڇر',
];

/*
 * Authors:
 * - Narain Sagar
 * - Sawood Alam
 */
return [
    'year' => ':count '.'سال',
    'a_year' => '{1}'.'هڪ سال'.'|:count '.'سال',
    'month' => ':count '.'مهينا',
    'a_month' => '{1}'.'هڪ مهينو'.'|:count '.'مهينا',
    'week' => ':count '.'هفتا',
    'a_week' => '{1}'.'ھڪ ھفتو'.'|:count '.'هفتا',
    'day' => ':count '.'ڏينهن',
    'a_day' => '{1}'.'هڪ ڏينهن'.'|:count '.'ڏينهن',
    'hour' => ':count '.'ڪلاڪ',
    'a_hour' => '{1}'.'هڪ ڪلاڪ'.'|:count '.'ڪلاڪ',
    'minute' => ':count '.'منٽ',
    'a_minute' => '{1}'.'هڪ منٽ'.'|:count '.'منٽ',
    'second' => ':count '.'سيڪنڊ',
    'a_second' => '{1}'.'چند سيڪنڊ'.'|:count '.'سيڪنڊ',
    'ago' => ':time اڳ',
    'from_now' => ':time پوء',
    'diff_yesterday' => 'ڪالهه',
    'diff_today' => 'اڄ',
    'diff_tomorrow' => 'سڀاڻي',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd، D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[اڄ] LT',
        'nextDay' => '[سڀاڻي] LT',
        'nextWeek' => 'dddd [اڳين هفتي تي] LT',
        'lastDay' => '[ڪالهه] LT',
        'lastWeek' => '[گزريل هفتي] dddd [تي] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => ['صبح', 'شام'],
    'months' => $months,
    'months_short' => $months,
    'weekdays' => $weekdays,
    'weekdays_short' => $weekdays,
    'weekdays_min' => $weekdays,
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => ['، ', ' ۽ '],
];
