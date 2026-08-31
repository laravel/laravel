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
 * - François B
 * - Andre Polykanine A.K.A. Menelion Elensúlë
 * - JD Isaacks
 */
return [
    'year' => ':count năm',
    'a_year' => '{1}một năm|]1, Inf[:count năm',
    'y' => ':count năm',
    'month' => ':count tháng',
    'a_month' => '{1}một tháng|]1, Inf[:count tháng',
    'm' => ':count tháng',
    'week' => ':count tuần',
    'a_week' => '{1}một tuần|]1, Inf[:count tuần',
    'w' => ':count tuần',
    'day' => ':count ngày',
    'a_day' => '{1}một ngày|]1, Inf[:count ngày',
    'd' => ':count ngày',
    'hour' => ':count giờ',
    'a_hour' => '{1}một giờ|]1, Inf[:count giờ',
    'h' => ':count giờ',
    'minute' => ':count phút',
    'a_minute' => '{1}một phút|]1, Inf[:count phút',
    'min' => ':count phút',
    'second' => ':count giây',
    'a_second' => '{1}vài giây|]1, Inf[:count giây',
    's' => ':count giây',
    'ago' => ':time trước',
    'from_now' => ':time tới',
    'after' => ':time sau',
    'before' => ':time trước',
    'diff_now' => 'bây giờ',
    'diff_today' => 'Hôm',
    'diff_today_regexp' => 'Hôm(?:\\s+nay)?(?:\\s+lúc)?',
    'diff_yesterday' => 'Hôm qua',
    'diff_yesterday_regexp' => 'Hôm(?:\\s+qua)?(?:\\s+lúc)?',
    'diff_tomorrow' => 'Ngày mai',
    'diff_tomorrow_regexp' => 'Ngày(?:\\s+mai)?(?:\\s+lúc)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM [năm] YYYY',
        'LLL' => 'D MMMM [năm] YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM [năm] YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Hôm nay lúc] LT',
        'nextDay' => '[Ngày mai lúc] LT',
        'nextWeek' => 'dddd [tuần tới lúc] LT',
        'lastDay' => '[Hôm qua lúc] LT',
        'lastWeek' => 'dddd [tuần trước lúc] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => ['SA', 'CH'],
    'months' => ['tháng 1', 'tháng 2', 'tháng 3', 'tháng 4', 'tháng 5', 'tháng 6', 'tháng 7', 'tháng 8', 'tháng 9', 'tháng 10', 'tháng 11', 'tháng 12'],
    'months_short' => ['Th01', 'Th02', 'Th03', 'Th04', 'Th05', 'Th06', 'Th07', 'Th08', 'Th09', 'Th10', 'Th11', 'Th12'],
    'weekdays' => ['chủ nhật', 'thứ hai', 'thứ ba', 'thứ tư', 'thứ năm', 'thứ sáu', 'thứ bảy'],
    'weekdays_short' => ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
    'weekdays_min' => ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' và '],
];
