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
 */
return [
    'year' => 'ལོ:count',
    'a_year' => '{1}ལོ་གཅིག|[-Inf,Inf]ལོ:count',
    'month' => 'ཟླ་བ:count',
    'a_month' => '{1}ཟླ་བ་གཅིག|[-Inf,Inf]ཟླ་བ:count',
    'week' => 'གཟའ་འཁོར་:count',
    'a_week' => 'གཟའ་འཁོར་གཅིག',
    'day' => 'ཉིན:count་',
    'a_day' => '{1}ཉིན་གཅིག|[-Inf,Inf]ཉིན:count',
    'hour' => 'ཆུ་ཚོད:count',
    'a_hour' => '{1}ཆུ་ཚོད་གཅིག|[-Inf,Inf]ཆུ་ཚོད:count',
    'minute' => 'སྐར་མ་:count',
    'a_minute' => '{1}སྐར་མ་གཅིག|[-Inf,Inf]སྐར་མ་:count',
    'second' => 'སྐར་ཆ:count',
    'a_second' => '{01}ལམ་སང|[-Inf,Inf]སྐར་ཆ:count',
    'ago' => ':time སྔན་ལ',
    'from_now' => ':time ལ་',
    'diff_yesterday' => 'ཁ་སང',
    'diff_today' => 'དི་རིང',
    'diff_tomorrow' => 'སང་ཉིན',
    'formats' => [
        'LT' => 'A h:mm',
        'LTS' => 'A h:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, A h:mm',
        'LLLL' => 'dddd, D MMMM YYYY, A h:mm',
    ],
    'calendar' => [
        'sameDay' => '[དི་རིང] LT',
        'nextDay' => '[སང་ཉིན] LT',
        'nextWeek' => '[བདུན་ཕྲག་རྗེས་མ], LT',
        'lastDay' => '[ཁ་སང] LT',
        'lastWeek' => '[བདུན་ཕྲག་མཐའ་མ] dddd, LT',
        'sameElse' => 'L',
    ],
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'མཚན་མོ';
        }
        if ($hour < 10) {
            return 'ཞོགས་ཀས';
        }
        if ($hour < 17) {
            return 'ཉིན་གུང';
        }
        if ($hour < 20) {
            return 'དགོང་དག';
        }

        return 'མཚན་མོ';
    },
    'months' => ['ཟླ་བ་དང་པོ', 'ཟླ་བ་གཉིས་པ', 'ཟླ་བ་གསུམ་པ', 'ཟླ་བ་བཞི་པ', 'ཟླ་བ་ལྔ་པ', 'ཟླ་བ་དྲུག་པ', 'ཟླ་བ་བདུན་པ', 'ཟླ་བ་བརྒྱད་པ', 'ཟླ་བ་དགུ་པ', 'ཟླ་བ་བཅུ་པ', 'ཟླ་བ་བཅུ་གཅིག་པ', 'ཟླ་བ་བཅུ་གཉིས་པ'],
    'months_short' => ['ཟླ་བ་དང་པོ', 'ཟླ་བ་གཉིས་པ', 'ཟླ་བ་གསུམ་པ', 'ཟླ་བ་བཞི་པ', 'ཟླ་བ་ལྔ་པ', 'ཟླ་བ་དྲུག་པ', 'ཟླ་བ་བདུན་པ', 'ཟླ་བ་བརྒྱད་པ', 'ཟླ་བ་དགུ་པ', 'ཟླ་བ་བཅུ་པ', 'ཟླ་བ་བཅུ་གཅིག་པ', 'ཟླ་བ་བཅུ་གཉིས་པ'],
    'weekdays' => ['གཟའ་ཉི་མ་', 'གཟའ་ཟླ་བ་', 'གཟའ་མིག་དམར་', 'གཟའ་ལྷག་པ་', 'གཟའ་ཕུར་བུ', 'གཟའ་པ་སངས་', 'གཟའ་སྤེན་པ་'],
    'weekdays_short' => ['ཉི་མ་', 'ཟླ་བ་', 'མིག་དམར་', 'ལྷག་པ་', 'ཕུར་བུ', 'པ་སངས་', 'སྤེན་པ་'],
    'weekdays_min' => ['ཉི་མ་', 'ཟླ་བ་', 'མིག་དམར་', 'ལྷག་པ་', 'ཕུར་བུ', 'པ་སངས་', 'སྤེན་པ་'],
    'list' => [', ', ' ཨནད་ '],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'months_standalone' => ['ཟླ་བ་དང་པོ་', 'ཟླ་བ་གཉིས་པ་', 'ཟླ་བ་གསུམ་པ་', 'ཟླ་བ་བཞི་པ་', 'ཟླ་བ་ལྔ་པ་', 'ཟླ་བ་དྲུག་པ་', 'ཟླ་བ་བདུན་པ་', 'ཟླ་བ་བརྒྱད་པ་', 'ཟླ་བ་དགུ་པ་', 'ཟླ་བ་བཅུ་པ་', 'ཟླ་བ་བཅུ་གཅིག་པ་', 'ཟླ་བ་བཅུ་གཉིས་པ་'],
];
