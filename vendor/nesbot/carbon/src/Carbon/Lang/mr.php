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
 * - Vikram-enyota
 */
return [
    'year' => ':count वर्ष',
    'y' => ':count वर्ष',
    'month' => ':count महिना|:count महिने',
    'm' => ':count महिना|:count महिने',
    'week' => ':count आठवडा|:count आठवडे',
    'w' => ':count आठवडा|:count आठवडे',
    'day' => ':count दिवस',
    'd' => ':count दिवस',
    'hour' => ':count तास',
    'h' => ':count तास',
    'minute' => ':count मिनिटे',
    'min' => ':count मिनिटे',
    'second' => ':count सेकंद',
    's' => ':count सेकंद',

    'ago' => ':timeपूर्वी',
    'from_now' => ':timeमध्ये',
    'before' => ':timeपूर्वी',
    'after' => ':timeनंतर',

    'diff_now' => 'आत्ता',
    'diff_today' => 'आज',
    'diff_yesterday' => 'काल',
    'diff_tomorrow' => 'उद्या',

    'formats' => [
        'LT' => 'A h:mm वाजता',
        'LTS' => 'A h:mm:ss वाजता',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, A h:mm वाजता',
        'LLLL' => 'dddd, D MMMM YYYY, A h:mm वाजता',
    ],

    'calendar' => [
        'sameDay' => '[आज] LT',
        'nextDay' => '[उद्या] LT',
        'nextWeek' => 'dddd, LT',
        'lastDay' => '[काल] LT',
        'lastWeek' => '[मागील] dddd, LT',
        'sameElse' => 'L',
    ],

    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'रात्री';
        }
        if ($hour < 10) {
            return 'सकाळी';
        }
        if ($hour < 17) {
            return 'दुपारी';
        }
        if ($hour < 20) {
            return 'सायंकाळी';
        }

        return 'रात्री';
    },

    'months' => ['जानेवारी', 'फेब्रुवारी', 'मार्च', 'एप्रिल', 'मे', 'जून', 'जुलै', 'ऑगस्ट', 'सप्टेंबर', 'ऑक्टोबर', 'नोव्हेंबर', 'डिसेंबर'],
    'months_short' => ['जाने.', 'फेब्रु.', 'मार्च.', 'एप्रि.', 'मे.', 'जून.', 'जुलै.', 'ऑग.', 'सप्टें.', 'ऑक्टो.', 'नोव्हें.', 'डिसें.'],
    'weekdays' => ['रविवार', 'सोमवार', 'मंगळवार', 'बुधवार', 'गुरूवार', 'शुक्रवार', 'शनिवार'],
    'weekdays_short' => ['रवि', 'सोम', 'मंगळ', 'बुध', 'गुरू', 'शुक्र', 'शनि'],
    'weekdays_min' => ['र', 'सो', 'मं', 'बु', 'गु', 'शु', 'श'],
    'list' => [', ', ' आणि '],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'weekend' => [0, 0],
];
