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
 * - tgfjt
 * - JD Isaacks
 */
return [
    'year' => ':count taun',
    'a_year' => '{1}setaun|[-Inf,Inf]:count taun',
    'month' => ':count wulan',
    'a_month' => '{1}sewulan|[-Inf,Inf]:count wulan',
    'week' => ':count minggu',
    'a_week' => '{1}sakminggu|[-Inf,Inf]:count minggu',
    'day' => ':count dina',
    'a_day' => '{1}sedina|[-Inf,Inf]:count dina',
    'hour' => ':count jam',
    'a_hour' => '{1}setunggal jam|[-Inf,Inf]:count jam',
    'minute' => ':count menit',
    'a_minute' => '{1}setunggal menit|[-Inf,Inf]:count menit',
    'second' => ':count detik',
    'a_second' => '{0,1}sawetawis detik|[-Inf,Inf]:count detik',
    'ago' => ':time ingkang kepengker',
    'from_now' => 'wonten ing :time',
    'diff_today' => 'Dinten',
    'diff_yesterday' => 'Kala',
    'diff_yesterday_regexp' => 'Kala(?:\\s+wingi)?(?:\\s+pukul)?',
    'diff_tomorrow' => 'Mbenjang',
    'diff_tomorrow_regexp' => 'Mbenjang(?:\\s+pukul)?',
    'diff_today_regexp' => 'Dinten(?:\\s+puniko)?(?:\\s+pukul)?',
    'formats' => [
        'LT' => 'HH.mm',
        'LTS' => 'HH.mm.ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY [pukul] HH.mm',
        'LLLL' => 'dddd, D MMMM YYYY [pukul] HH.mm',
    ],
    'calendar' => [
        'sameDay' => '[Dinten puniko pukul] LT',
        'nextDay' => '[Mbenjang pukul] LT',
        'nextWeek' => 'dddd [pukul] LT',
        'lastDay' => '[Kala wingi pukul] LT',
        'lastWeek' => 'dddd [kepengker pukul] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => static function ($hour) {
        if ($hour < 11) {
            return 'enjing';
        }
        if ($hour < 15) {
            return 'siyang';
        }
        if ($hour < 19) {
            return 'sonten';
        }

        return 'ndalu';
    },
    'months' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'Nopember', 'Desember'],
    'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nop', 'Des'],
    'weekdays' => ['Minggu', 'Senen', 'Seloso', 'Rebu', 'Kemis', 'Jemuwah', 'Septu'],
    'weekdays_short' => ['Min', 'Sen', 'Sel', 'Reb', 'Kem', 'Jem', 'Sep'],
    'weekdays_min' => ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sp'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' lan '],
];
