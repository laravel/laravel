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
 * - Kunal Marwaha
 * - FourwingsY
 * - François B
 * - Jason Katz-Brown
 * - Seokjun Kim
 * - Junho Kim
 * - JD Isaacks
 * - Juwon Kim
 */
return [
    'year' => ':count년',
    'a_year' => '{1}일년|[-Inf,Inf]:count년',
    'y' => ':count년',
    'month' => ':count개월',
    'a_month' => '{1}한달|[-Inf,Inf]:count개월',
    'm' => ':count개월',
    'week' => ':count주',
    'a_week' => '{1}일주일|[-Inf,Inf]:count 주',
    'w' => ':count주일',
    'day' => ':count일',
    'a_day' => '{1}하루|[-Inf,Inf]:count일',
    'd' => ':count일',
    'hour' => ':count시간',
    'a_hour' => '{1}한시간|[-Inf,Inf]:count시간',
    'h' => ':count시간',
    'minute' => ':count분',
    'a_minute' => '{1}일분|[-Inf,Inf]:count분',
    'min' => ':count분',
    'second' => ':count초',
    'a_second' => '{1}몇초|[-Inf,Inf]:count초',
    's' => ':count초',
    'ago' => ':time 전',
    'from_now' => ':time 후',
    'after' => ':time 후',
    'before' => ':time 전',
    'diff_now' => '지금',
    'diff_today' => '오늘',
    'diff_yesterday' => '어제',
    'diff_tomorrow' => '내일',
    'formats' => [
        'LT' => 'A h:mm',
        'LTS' => 'A h:mm:ss',
        'L' => 'YYYY.MM.DD.',
        'LL' => 'YYYY년 MMMM D일',
        'LLL' => 'YYYY년 MMMM D일 A h:mm',
        'LLLL' => 'YYYY년 MMMM D일 dddd A h:mm',
    ],
    'calendar' => [
        'sameDay' => '오늘 LT',
        'nextDay' => '내일 LT',
        'nextWeek' => 'dddd LT',
        'lastDay' => '어제 LT',
        'lastWeek' => '지난주 dddd LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number, $period) {
        return match ($period) {
            'd', 'D', 'DDD' => $number.'일',
            'M' => $number.'월',
            'w', 'W' => $number.'주',
            default => $number,
        };
    },
    'meridiem' => ['오전', '오후'],
    'months' => ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
    'months_short' => ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
    'weekdays' => ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'],
    'weekdays_short' => ['일', '월', '화', '수', '목', '금', '토'],
    'weekdays_min' => ['일', '월', '화', '수', '목', '금', '토'],
    'list' => ' ',
];
