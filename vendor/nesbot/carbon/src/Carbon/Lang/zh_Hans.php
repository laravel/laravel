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
 * - monkeycon
 * - François B
 * - Jason Katz-Brown
 * - Konstantin Konev
 * - Chris Lam
 * - Serhan Apaydın
 * - Gary Lo
 * - JD Isaacks
 * - Chris Hemp
 * - shankesgk2
 * - Daniel Cheung (danvim)
 */
return [
    'year' => ':count:optional-space年',
    'y' => ':count:optional-space年',
    'month' => ':count:optional-space个月',
    'm' => ':count:optional-space个月',
    'week' => ':count:optional-space周',
    'w' => ':count:optional-space周',
    'day' => ':count:optional-space天',
    'd' => ':count:optional-space天',
    'hour' => ':count:optional-space小时',
    'h' => ':count:optional-space小时',
    'minute' => ':count:optional-space分钟',
    'min' => ':count:optional-space分钟',
    'second' => ':count:optional-space秒',
    'a_second' => '{1}几秒|[-Inf,Inf]:count:optional-space秒',
    's' => ':count:optional-space秒',
    'ago' => ':time前',
    'from_now' => ':time后',
    'after' => ':time后',
    'before' => ':time前',
    'diff_now' => '现在',
    'diff_today' => '今天',
    'diff_yesterday' => '昨天',
    'diff_tomorrow' => '明天',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY/MM/DD',
        'LL' => 'YYYY年M月D日',
        'LLL' => 'YYYY年M月D日 HH:mm',
        'LLLL' => 'YYYY年M月D日dddd HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[今天]LT',
        'nextDay' => '[明天]LT',
        'nextWeek' => '[下]ddddLT',
        'lastDay' => '[昨天]LT',
        'lastWeek' => '[上]ddddLT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number, $period) {
        return match ($period) {
            'd', 'D', 'DDD' => $number.'日',
            'M' => $number.'月',
            'w', 'W' => $number.'周',
            default => $number,
        };
    },
    'meridiem' => static function ($hour, $minute) {
        $time = $hour * 100 + $minute;
        if ($time < 600) {
            return '凌晨';
        }
        if ($time < 900) {
            return '早上';
        }
        if ($time < 1130) {
            return '上午';
        }
        if ($time < 1230) {
            return '中午';
        }
        if ($time < 1800) {
            return '下午';
        }

        return '晚上';
    },
    'months' => ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
    'months_short' => ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
    'weekdays' => ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
    'weekdays_short' => ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
    'weekdays_min' => ['日', '一', '二', '三', '四', '五', '六'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => '',
];
