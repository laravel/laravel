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
 * - JD Isaacks
 * - Nay Lin Aung
 */
return [
    'year' => ':count နှစ်',
    'a_year' => '{1}တစ်နှစ်|[-Inf,Inf]:count နှစ်',
    'y' => ':count နှစ်',
    'month' => ':count လ',
    'a_month' => '{1}တစ်လ|[-Inf,Inf]:count လ',
    'm' => ':count လ',
    'week' => ':count ပတ်',
    'w' => ':count ပတ်',
    'day' => ':count ရက်',
    'a_day' => '{1}တစ်ရက်|[-Inf,Inf]:count ရက်',
    'd' => ':count ရက်',
    'hour' => ':count နာရီ',
    'a_hour' => '{1}တစ်နာရီ|[-Inf,Inf]:count နာရီ',
    'h' => ':count နာရီ',
    'minute' => ':count မိနစ်',
    'a_minute' => '{1}တစ်မိနစ်|[-Inf,Inf]:count မိနစ်',
    'min' => ':count မိနစ်',
    'second' => ':count စက္ကန့်',
    'a_second' => '{0,1}စက္ကန်.အနည်းငယ်|[-Inf,Inf]:count စက္ကန့်',
    's' => ':count စက္ကန့်',
    'ago' => 'လွန်ခဲ့သော :time က',
    'from_now' => 'လာမည့် :time မှာ',
    'after' => ':time ကြာပြီးနောက်',
    'before' => ':time မတိုင်ခင်',
    'diff_now' => 'အခုလေးတင်',
    'diff_today' => 'ယနေ.',
    'diff_yesterday' => 'မနေ့က',
    'diff_yesterday_regexp' => 'မနေ.က',
    'diff_tomorrow' => 'မနက်ဖြန်',
    'diff_before_yesterday' => 'တမြန်နေ့က',
    'diff_after_tomorrow' => 'တဘက်ခါ',
    'period_recurrences' => ':count ကြိမ်',
    'formats' => [
        'LT' => 'Oh:Om A',
        'LTS' => 'Oh:Om:Os A',
        'L' => 'OD/OM/OY',
        'LL' => 'OD MMMM OY',
        'LLL' => 'OD MMMM OY Oh:Om A',
        'LLLL' => 'dddd OD MMMM OY Oh:Om A',
    ],
    'calendar' => [
        'sameDay' => '[ယနေ.] LT [မှာ]',
        'nextDay' => '[မနက်ဖြန်] LT [မှာ]',
        'nextWeek' => 'dddd LT [မှာ]',
        'lastDay' => '[မနေ.က] LT [မှာ]',
        'lastWeek' => '[ပြီးခဲ့သော] dddd LT [မှာ]',
        'sameElse' => 'L',
    ],
    'months' => ['ဇန်နဝါရီ', 'ဖေဖော်ဝါရီ', 'မတ်', 'ဧပြီ', 'မေ', 'ဇွန်', 'ဇူလိုင်', 'သြဂုတ်', 'စက်တင်ဘာ', 'အောက်တိုဘာ', 'နိုဝင်ဘာ', 'ဒီဇင်ဘာ'],
    'months_short' => ['ဇန်', 'ဖေ', 'မတ်', 'ပြီ', 'မေ', 'ဇွန်', 'လိုင်', 'သြ', 'စက်', 'အောက်', 'နို', 'ဒီ'],
    'weekdays' => ['တနင်္ဂနွေ', 'တနင်္လာ', 'အင်္ဂါ', 'ဗုဒ္ဓဟူး', 'ကြာသပတေး', 'သောကြာ', 'စနေ'],
    'weekdays_short' => ['နွေ', 'လာ', 'ဂါ', 'ဟူး', 'ကြာ', 'သော', 'နေ'],
    'weekdays_min' => ['နွေ', 'လာ', 'ဂါ', 'ဟူး', 'ကြာ', 'သော', 'နေ'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'alt_numbers' => ['၀၀', '၀၁', '၀၂', '၀၃', '၀၄', '၀၅', '၀၆', '၀၇', '၀၈', '၀၉', '၁၀', '၁၁', '၁၂', '၁၃', '၁၄', '၁၅', '၁၆', '၁၇', '၁၈', '၁၉', '၂၀', '၂၁', '၂၂', '၂၃', '၂၄', '၂၅', '၂၆', '၂၇', '၂၈', '၂၉', '၃၀', '၃၁', '၃၂', '၃၃', '၃၄', '၃၅', '၃၆', '၃၇', '၃၈', '၃၉', '၄၀', '၄၁', '၄၂', '၄၃', '၄၄', '၄၅', '၄၆', '၄၇', '၄၈', '၄၉', '၅၀', '၅၁', '၅၂', '၅၃', '၅၄', '၅၅', '၅၆', '၅၇', '၅၈', '၅၉', '၆၀', '၆၁', '၆၂', '၆၃', '၆၄', '၆၅', '၆၆', '၆၇', '၆၈', '၆၉', '၇၀', '၇၁', '၇၂', '၇၃', '၇၄', '၇၅', '၇၆', '၇၇', '၇၈', '၇၉', '၈၀', '၈၁', '၈၂', '၈၃', '၈၄', '၈၅', '၈၆', '၈၇', '၈၈', '၈၉', '၉၀', '၉၁', '၉၂', '၉၃', '၉၄', '၉၅', '၉၆', '၉၇', '၉၈', '၉၉'],
    'meridiem' => ['နံနက်', 'ညနေ'],
];
