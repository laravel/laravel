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
 * - MOHAN M U
 * - François B
 * - rajeevnaikte
 */
return [
    'year' => '{1}:count ವರ್ಷ|[-Inf,Inf]:count ವರ್ಷಗಳು',
    'a_year' => '{1}ಒಂದು ವರ್ಷ|[-Inf,Inf]:count ವರ್ಷಗಳು',
    'month' => ':count ತಿಂಗಳು',
    'a_month' => '{1}ಒಂದು ತಿಂಗಳು|[-Inf,Inf]:count ತಿಂಗಳು',
    'week' => '{1}:count ವಾರ|[-Inf,Inf]:count ವಾರಗಳು',
    'a_week' => '{1}ಒಂದು ವಾರ|[-Inf,Inf]:count ವಾರಗಳು',
    'day' => '{1}:count ದಿನ|[-Inf,Inf]:count ದಿನಗಳು',
    'a_day' => '{1}ಒಂದು ದಿನ|[-Inf,Inf]:count ದಿನಗಳು',
    'hour' => '{1}:count ಗಂಟೆ|[-Inf,Inf]:count ಗಂಟೆಗಳು',
    'a_hour' => '{1}ಒಂದು ಗಂಟೆ|[-Inf,Inf]:count ಗಂಟೆಗಳು',
    'minute' => '{1}:count ನಿಮಿಷ|[-Inf,Inf]:count ನಿಮಿಷಗಳು',
    'a_minute' => '{1}ಒಂದು ನಿಮಿಷ|[-Inf,Inf]:count ನಿಮಿಷಗಳು',
    'second' => '{0,1}:count ಸೆಕೆಂಡ್|[-Inf,Inf]:count ಸೆಕೆಂಡುಗಳು',
    'a_second' => '{0,1}ಕೆಲವು ಕ್ಷಣಗಳು|[-Inf,Inf]:count ಸೆಕೆಂಡುಗಳು',
    'ago' => ':time ಹಿಂದೆ',
    'from_now' => ':time ನಂತರ',
    'diff_now' => 'ಈಗ',
    'diff_today' => 'ಇಂದು',
    'diff_yesterday' => 'ನಿನ್ನೆ',
    'diff_tomorrow' => 'ನಾಳೆ',
    'formats' => [
        'LT' => 'A h:mm',
        'LTS' => 'A h:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, A h:mm',
        'LLLL' => 'dddd, D MMMM YYYY, A h:mm',
    ],
    'calendar' => [
        'sameDay' => '[ಇಂದು] LT',
        'nextDay' => '[ನಾಳೆ] LT',
        'nextWeek' => 'dddd, LT',
        'lastDay' => '[ನಿನ್ನೆ] LT',
        'lastWeek' => '[ಕೊನೆಯ] dddd, LT',
        'sameElse' => 'L',
    ],
    'ordinal' => ':numberನೇ',
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'ರಾತ್ರಿ';
        }
        if ($hour < 10) {
            return 'ಬೆಳಿಗ್ಗೆ';
        }
        if ($hour < 17) {
            return 'ಮಧ್ಯಾಹ್ನ';
        }
        if ($hour < 20) {
            return 'ಸಂಜೆ';
        }

        return 'ರಾತ್ರಿ';
    },
    'months' => ['ಜನವರಿ', 'ಫೆಬ್ರವರಿ', 'ಮಾರ್ಚ್', 'ಏಪ್ರಿಲ್', 'ಮೇ', 'ಜೂನ್', 'ಜುಲೈ', 'ಆಗಸ್ಟ್', 'ಸೆಪ್ಟೆಂಬರ್', 'ಅಕ್ಟೋಬರ್', 'ನವೆಂಬರ್', 'ಡಿಸೆಂಬರ್'],
    'months_short' => ['ಜನ', 'ಫೆಬ್ರ', 'ಮಾರ್ಚ್', 'ಏಪ್ರಿಲ್', 'ಮೇ', 'ಜೂನ್', 'ಜುಲೈ', 'ಆಗಸ್ಟ್', 'ಸೆಪ್ಟೆಂ', 'ಅಕ್ಟೋ', 'ನವೆಂ', 'ಡಿಸೆಂ'],
    'weekdays' => ['ಭಾನುವಾರ', 'ಸೋಮವಾರ', 'ಮಂಗಳವಾರ', 'ಬುಧವಾರ', 'ಗುರುವಾರ', 'ಶುಕ್ರವಾರ', 'ಶನಿವಾರ'],
    'weekdays_short' => ['ಭಾನು', 'ಸೋಮ', 'ಮಂಗಳ', 'ಬುಧ', 'ಗುರು', 'ಶುಕ್ರ', 'ಶನಿ'],
    'weekdays_min' => ['ಭಾ', 'ಸೋ', 'ಮಂ', 'ಬು', 'ಗು', 'ಶು', 'ಶ'],
    'list' => ', ',
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'weekend' => [0, 0],
];
