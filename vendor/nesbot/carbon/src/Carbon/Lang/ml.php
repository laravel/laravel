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
 * - JD Isaacks
 */
return [
    'year' => ':count വർഷം',
    'a_year' => 'ഒരു വർഷം|:count വർഷം',
    'month' => ':count മാസം',
    'a_month' => 'ഒരു മാസം|:count മാസം',
    'week' => ':count ആഴ്ച',
    'a_week' => 'ഒരാഴ്ച|:count ആഴ്ച',
    'day' => ':count ദിവസം',
    'a_day' => 'ഒരു ദിവസം|:count ദിവസം',
    'hour' => ':count മണിക്കൂർ',
    'a_hour' => 'ഒരു മണിക്കൂർ|:count മണിക്കൂർ',
    'minute' => ':count മിനിറ്റ്',
    'a_minute' => 'ഒരു മിനിറ്റ്|:count മിനിറ്റ്',
    'second' => ':count സെക്കൻഡ്',
    'a_second' => 'അൽപ നിമിഷങ്ങൾ|:count സെക്കൻഡ്',
    'ago' => ':time മുൻപ്',
    'from_now' => ':time കഴിഞ്ഞ്',
    'diff_now' => 'ഇപ്പോൾ',
    'diff_today' => 'ഇന്ന്',
    'diff_yesterday' => 'ഇന്നലെ',
    'diff_tomorrow' => 'നാളെ',
    'formats' => [
        'LT' => 'A h:mm -നു',
        'LTS' => 'A h:mm:ss -നു',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, A h:mm -നു',
        'LLLL' => 'dddd, D MMMM YYYY, A h:mm -നു',
    ],
    'calendar' => [
        'sameDay' => '[ഇന്ന്] LT',
        'nextDay' => '[നാളെ] LT',
        'nextWeek' => 'dddd, LT',
        'lastDay' => '[ഇന്നലെ] LT',
        'lastWeek' => '[കഴിഞ്ഞ] dddd, LT',
        'sameElse' => 'L',
    ],
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'രാത്രി';
        }
        if ($hour < 12) {
            return 'രാവിലെ';
        }
        if ($hour < 17) {
            return 'ഉച്ച കഴിഞ്ഞ്';
        }
        if ($hour < 20) {
            return 'വൈകുന്നേരം';
        }

        return 'രാത്രി';
    },
    'months' => ['ജനുവരി', 'ഫെബ്രുവരി', 'മാർച്ച്', 'ഏപ്രിൽ', 'മേയ്', 'ജൂൺ', 'ജൂലൈ', 'ഓഗസ്റ്റ്', 'സെപ്റ്റംബർ', 'ഒക്ടോബർ', 'നവംബർ', 'ഡിസംബർ'],
    'months_short' => ['ജനു.', 'ഫെബ്രു.', 'മാർ.', 'ഏപ്രി.', 'മേയ്', 'ജൂൺ', 'ജൂലൈ.', 'ഓഗ.', 'സെപ്റ്റ.', 'ഒക്ടോ.', 'നവം.', 'ഡിസം.'],
    'weekdays' => ['ഞായറാഴ്ച', 'തിങ്കളാഴ്ച', 'ചൊവ്വാഴ്ച', 'ബുധനാഴ്ച', 'വ്യാഴാഴ്ച', 'വെള്ളിയാഴ്ച', 'ശനിയാഴ്ച'],
    'weekdays_short' => ['ഞായർ', 'തിങ്കൾ', 'ചൊവ്വ', 'ബുധൻ', 'വ്യാഴം', 'വെള്ളി', 'ശനി'],
    'weekdays_min' => ['ഞാ', 'തി', 'ചൊ', 'ബു', 'വ്യാ', 'വെ', 'ശ'],
    'list' => ', ',
    'weekend' => [0, 0],
];
