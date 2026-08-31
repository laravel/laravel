<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$bengaliNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

/*
 * Authors:
 * - Josh Soref
 * - Shakib Hossain
 * - Raju
 * - Aniruddha Adhikary
 * - JD Isaacks
 * - Saiful Islam
 * - Faisal Islam
 * - Hayatunnabi Nabil
 */
return [
    'year' => ':count বছর',
    'a_year' => 'এক বছর|:count বছর',
    'y' => '১ বছর|:count বছর',
    'month' => ':count মাস',
    'a_month' => 'এক মাস|:count মাস',
    'm' => '১ মাস|:count মাস',
    'week' => ':count সপ্তাহ',
    'a_week' => '১ সপ্তাহ|:count সপ্তাহ',
    'w' => '১ সপ্তাহ|:count সপ্তাহ',
    'day' => ':count দিন',
    'a_day' => 'এক দিন|:count দিন',
    'd' => '১ দিন|:count দিন',
    'hour' => ':count ঘন্টা',
    'a_hour' => 'এক ঘন্টা|:count ঘন্টা',
    'h' => '১ ঘন্টা|:count ঘন্টা',
    'minute' => ':count মিনিট',
    'a_minute' => 'এক মিনিট|:count মিনিট',
    'min' => '১ মিনিট|:count মিনিট',
    'second' => ':count সেকেন্ড',
    'a_second' => 'কয়েক সেকেন্ড|:count সেকেন্ড',
    's' => '১ সেকেন্ড|:count সেকেন্ড',
    'millisecond' => ':count মিলিসেকেন্ড',
    'a_millisecond' => 'এক মিলিসেকেন্ড|:count মিলিসেকেন্ড',
    'ms' => '১ মিলিসেকেন্ড|:count মিলিসেকেন্ড',
    'microsecond' => ':count মাইক্রোসেকেন্ড',
    'a_microsecond' => 'এক মাইক্রোসেকেন্ড|:count মাইক্রোসেকেন্ড',
    'µs' => '১ মাইক্রোসেকেন্ড|:count মাইক্রোসেকেন্ড',
    'ago' => ':time আগে',
    'from_now' => ':time পরে',
    'after' => ':time পরে',
    'before' => ':time আগে',
    'diff_now' => 'এখন',
    'diff_today' => 'আজ',
    'diff_yesterday' => 'গতকাল',
    'diff_tomorrow' => 'আগামীকাল',
    'diff_before_yesterday' => 'গত পরশু',
    'diff_after_tomorrow' => 'আগামী পরশু',
    'period_recurrences' => ':count বার|:count বার',
    'period_interval' => 'প্রতি :interval',
    'period_start_date' => ':date থেকে',
    'period_end_date' => ':date পর্যন্ত',
    'formats' => [
        'LT' => 'A Oh:Om সময়',
        'LTS' => 'A Oh:Om:Os সময়',
        'L' => 'OD/OM/OY',
        'LL' => 'OD MMMM OY',
        'LLL' => 'OD MMMM OY, A Oh:Om সময়',
        'LLLL' => 'dddd, OD MMMM OY, A Oh:Om সময়',
    ],
    'calendar' => [
        'sameDay' => '[আজ] LT',
        'nextDay' => '[আগামীকাল] LT',
        'nextWeek' => 'dddd, LT',
        'lastDay' => '[গতকাল] LT',
        'lastWeek' => '[গত] dddd, LT',
        'sameElse' => 'L',
    ],
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'রাত';
        }
        if ($hour < 10) {
            return 'সকাল';
        }
        if ($hour < 17) {
            return 'দুপুর';
        }
        if ($hour < 20) {
            return 'বিকাল';
        }

        return 'রাত';
    },
    'months' => ['জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'],
    'months_short' => ['জানু', 'ফেব', 'মার্চ', 'এপ্র', 'মে', 'জুন', 'জুল', 'আগ', 'সেপ্ট', 'অক্টো', 'নভে', 'ডিসে'],
    'weekdays' => ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'],
    'weekdays_short' => ['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহস্পতি', 'শুক্র', 'শনি'],
    'weekdays_min' => ['রবি', 'সোম', 'মঙ্গ', 'বুধ', 'বৃহঃ', 'শুক্র', 'শনি'],
    'ordinal' => static function ($number) use ($bengaliNumbers) {
        // Convert to Bengali numerals
        $bengaliNumber = str_replace(
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            $bengaliNumbers,
            (string) $number
        );

        // Apply Bengali ordinal rules
        $lastDigit = $number % 10;
        $lastTwoDigits = $number % 100;

        // Special cases for teens (11-19) always use তম
        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 19) {
            return $bengaliNumber.'তম';
        }

        // For numbers 1-10, use specific rules
        if ($number <= 10) {
            switch ($number) {
                case 1:
                case 5:
                case 7:
                case 8:
                case 9:
                case 10:
                    return $bengaliNumber.'ম';
                case 2:
                case 3:
                    return $bengaliNumber.'য়';
                case 4:
                    return $bengaliNumber.'র্থ';
                case 6:
                    return $bengaliNumber.'ষ্ঠ';
                default:
                    return $bengaliNumber.'তম';
            }
        }

        // For numbers > 20, all use তম
        return $bengaliNumber.'তম';
    },
    'list' => [', ', ' এবং '],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'weekdays_standalone' => ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহষ্পতিবার', 'শুক্রবার', 'শনিবার'],
    'weekdays_min_standalone' => ['রঃ', 'সোঃ', 'মঃ', 'বুঃ', 'বৃঃ', 'শুঃ', 'শনি'],
    'months_short_standalone' => ['জানুয়ারী', 'ফেব্রুয়ারী', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'],
    'alt_numbers' => $bengaliNumbers,
];
