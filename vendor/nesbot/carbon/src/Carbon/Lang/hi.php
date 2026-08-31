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
 * - abhimanyu003
 * - Josh Soref
 * - JD Isaacks
 */
return [
    'year' => 'एक वर्ष|:count वर्ष',
    'y' => '1 वर्ष|:count वर्षों',
    'month' => 'एक महीने|:count महीने',
    'm' => '1 माह|:count महीने',
    'week' => '1 सप्ताह|:count सप्ताह',
    'w' => '1 सप्ताह|:count सप्ताह',
    'day' => 'एक दिन|:count दिन',
    'd' => '1 दिन|:count दिनों',
    'hour' => 'एक घंटा|:count घंटे',
    'h' => '1 घंटा|:count घंटे',
    'minute' => 'एक मिनट|:count मिनट',
    'min' => '1 मिनट|:count मिनटों',
    'second' => 'कुछ ही क्षण|:count सेकंड',
    's' => '1 सेकंड|:count सेकंड',
    'ago' => ':time पहले',
    'from_now' => ':time में',
    'after' => ':time के बाद',
    'before' => ':time के पहले',
    'diff_now' => 'अब',
    'diff_today' => 'आज',
    'diff_yesterday' => 'कल',
    'diff_tomorrow' => 'कल',
    'formats' => [
        'LT' => 'A h:mm बजे',
        'LTS' => 'A h:mm:ss बजे',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, A h:mm बजे',
        'LLLL' => 'dddd, D MMMM YYYY, A h:mm बजे',
    ],
    'calendar' => [
        'sameDay' => '[आज] LT',
        'nextDay' => '[कल] LT',
        'nextWeek' => 'dddd, LT',
        'lastDay' => '[कल] LT',
        'lastWeek' => '[पिछले] dddd, LT',
        'sameElse' => 'L',
    ],
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'रात';
        }
        if ($hour < 10) {
            return 'सुबह';
        }
        if ($hour < 17) {
            return 'दोपहर';
        }
        if ($hour < 20) {
            return 'शाम';
        }

        return 'रात';
    },
    'months' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'months_short' => ['जन.', 'फ़र.', 'मार्च', 'अप्रै.', 'मई', 'जून', 'जुल.', 'अग.', 'सित.', 'अक्टू.', 'नव.', 'दिस.'],
    'weekdays' => ['रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरूवार', 'शुक्रवार', 'शनिवार'],
    'weekdays_short' => ['रवि', 'सोम', 'मंगल', 'बुध', 'गुरू', 'शुक्र', 'शनि'],
    'weekdays_min' => ['र', 'सो', 'मं', 'बु', 'गु', 'शु', 'श'],
    'list' => [', ', ' और '],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'weekend' => [0, 0],
];
