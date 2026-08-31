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
 * - Nate Whittaker
 * - John MacAslan
 * - Chanintorn Asavavichairoj
 * - JD Isaacks
 * - ROKAISAKKON
 * - RO'KAISAKKON
 * - Andreas Möller
 * - nithisa
 */
return [
    'year' => ':count ปี',
    'y' => ':count ปี',
    'month' => ':count เดือน',
    'm' => ':count เดือน',
    'week' => ':count สัปดาห์',
    'w' => ':count สัปดาห์',
    'day' => ':count วัน',
    'd' => ':count วัน',
    'hour' => ':count ชั่วโมง',
    'h' => ':count ชั่วโมง',
    'minute' => ':count นาที',
    'min' => ':count นาที',
    'second' => ':count วินาที',
    'a_second' => '{1}ไม่กี่วินาที|[-Inf,Inf]:count วินาที',
    's' => ':count วินาที',
    'ago' => ':timeที่แล้ว',
    'from_now' => 'อีก :time',
    'after' => ':timeหลังจากนี้',
    'before' => ':timeก่อน',
    'diff_now' => 'ขณะนี้',
    'diff_today' => 'วันนี้',
    'diff_today_regexp' => 'วันนี้(?:\\s+เวลา)?',
    'diff_yesterday' => 'เมื่อวาน',
    'diff_yesterday_regexp' => 'เมื่อวานนี้(?:\\s+เวลา)?',
    'diff_tomorrow' => 'พรุ่งนี้',
    'diff_tomorrow_regexp' => 'พรุ่งนี้(?:\\s+เวลา)?',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY เวลา H:mm',
        'LLLL' => 'วันddddที่ D MMMM YYYY เวลา H:mm',
    ],
    'calendar' => [
        'sameDay' => '[วันนี้ เวลา] LT',
        'nextDay' => '[พรุ่งนี้ เวลา] LT',
        'nextWeek' => 'dddd[หน้า เวลา] LT',
        'lastDay' => '[เมื่อวานนี้ เวลา] LT',
        'lastWeek' => '[วัน]dddd[ที่แล้ว เวลา] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => ['ก่อนเที่ยง', 'หลังเที่ยง'],
    'months' => ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
    'months_short' => ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
    'weekdays' => ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'],
    'weekdays_short' => ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์', 'เสาร์'],
    'weekdays_min' => ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'],
    'list' => [', ', ' และ '],
];
