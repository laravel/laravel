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
 * - François B
 * - JD Isaacks
 * - Satheez
 */
return [
    'year' => ':count வருடம்|:count ஆண்டுகள்',
    'a_year' => 'ஒரு வருடம்|:count ஆண்டுகள்',
    'y' => ':count வருட.|:count ஆண்.',
    'month' => ':count மாதம்|:count மாதங்கள்',
    'a_month' => 'ஒரு மாதம்|:count மாதங்கள்',
    'm' => ':count மாத.',
    'week' => ':count வாரம்|:count வாரங்கள்',
    'a_week' => 'ஒரு வாரம்|:count வாரங்கள்',
    'w' => ':count வார.',
    'day' => ':count நாள்|:count நாட்கள்',
    'a_day' => 'ஒரு நாள்|:count நாட்கள்',
    'd' => ':count நாள்|:count நாட்.',
    'hour' => ':count மணி நேரம்|:count மணி நேரம்',
    'a_hour' => 'ஒரு மணி நேரம்|:count மணி நேரம்',
    'h' => ':count மணி.',
    'minute' => ':count நிமிடம்|:count நிமிடங்கள்',
    'a_minute' => 'ஒரு நிமிடம்|:count நிமிடங்கள்',
    'min' => ':count நிமி.',
    'second' => ':count சில விநாடிகள்|:count விநாடிகள்',
    'a_second' => 'ஒரு சில விநாடிகள்|:count விநாடிகள்',
    's' => ':count விநா.',
    'ago' => ':time முன்',
    'from_now' => ':time இல்',
    'before' => ':time முன்',
    'after' => ':time பின்',
    'diff_now' => 'இப்போது',
    'diff_today' => 'இன்று',
    'diff_yesterday' => 'நேற்று',
    'diff_tomorrow' => 'நாளை',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY, HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[இன்று] LT',
        'nextDay' => '[நாளை] LT',
        'nextWeek' => 'dddd, LT',
        'lastDay' => '[நேற்று] LT',
        'lastWeek' => '[கடந்த வாரம்] dddd, LT',
        'sameElse' => 'L',
    ],
    'ordinal' => ':numberவது',
    'meridiem' => static function ($hour) {
        if ($hour < 2) {
            return ' யாமம்';
        }
        if ($hour < 6) {
            return ' வைகறை';
        }
        if ($hour < 10) {
            return ' காலை';
        }
        if ($hour < 14) {
            return ' நண்பகல்';
        }
        if ($hour < 18) {
            return ' எற்பாடு';
        }
        if ($hour < 22) {
            return ' மாலை';
        }

        return ' யாமம்';
    },
    'months' => ['ஜனவரி', 'பிப்ரவரி', 'மார்ச்', 'ஏப்ரல்', 'மே', 'ஜூன்', 'ஜூலை', 'ஆகஸ்ட்', 'செப்டெம்பர்', 'அக்டோபர்', 'நவம்பர்', 'டிசம்பர்'],
    'months_short' => ['ஜனவரி', 'பிப்ரவரி', 'மார்ச்', 'ஏப்ரல்', 'மே', 'ஜூன்', 'ஜூலை', 'ஆகஸ்ட்', 'செப்டெம்பர்', 'அக்டோபர்', 'நவம்பர்', 'டிசம்பர்'],
    'weekdays' => ['ஞாயிற்றுக்கிழமை', 'திங்கட்கிழமை', 'செவ்வாய்கிழமை', 'புதன்கிழமை', 'வியாழக்கிழமை', 'வெள்ளிக்கிழமை', 'சனிக்கிழமை'],
    'weekdays_short' => ['ஞாயிறு', 'திங்கள்', 'செவ்வாய்', 'புதன்', 'வியாழன்', 'வெள்ளி', 'சனி'],
    'weekdays_min' => ['ஞா', 'தி', 'செ', 'பு', 'வி', 'வெ', 'ச'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' மற்றும் '],
    'weekend' => [0, 0],
];
