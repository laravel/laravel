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
 * Author:
 * - Abdifatah Abdilahi(@abdifatahz)
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'year' => ':count sanad|:count sanadood',
    'a_year' => 'sanad|:count sanadood',
    'y' => '{1}:countsn|{0}:countsns|[-Inf,Inf]:countsn',
    'month' => ':count bil|:count bilood',
    'a_month' => 'bil|:count bilood',
    'm' => ':countbil',
    'week' => ':count isbuuc',
    'a_week' => 'isbuuc|:count isbuuc',
    'w' => ':countis',
    'day' => ':count maalin|:count maalmood',
    'a_day' => 'maalin|:count maalmood',
    'd' => ':countml',
    'hour' => ':count saac',
    'a_hour' => 'saacad|:count saac',
    'h' => ':countsc',
    'minute' => ':count daqiiqo',
    'a_minute' => 'daqiiqo|:count daqiiqo',
    'min' => ':countdq',
    'second' => ':count ilbidhiqsi',
    'a_second' => 'xooga ilbidhiqsiyo|:count ilbidhiqsi',
    's' => ':countil',
    'ago' => ':time kahor',
    'from_now' => ':time gudahood',
    'after' => ':time kedib',
    'before' => ':time kahor',
    'diff_now' => 'hada',
    'diff_today' => 'maanta',
    'diff_today_regexp' => 'maanta(?:\s+markay\s+(?:tahay|ahayd))?',
    'diff_yesterday' => 'shalayto',
    'diff_yesterday_regexp' => 'shalayto(?:\s+markay\s+ahayd)?',
    'diff_tomorrow' => 'beri',
    'diff_tomorrow_regexp' => 'beri(?:\s+markay\s+tahay)?',
    'diff_before_yesterday' => 'doraato',
    'diff_after_tomorrow' => 'saadanbe',
    'period_recurrences' => 'mar|:count jeer',
    'period_interval' => ':interval kasta',
    'period_start_date' => 'laga bilaabo :date',
    'period_end_date' => 'ilaa :date',
    'months' => ['Janaayo', 'Febraayo', 'Abriil', 'Maajo', 'Juun', 'Luuliyo', 'Agoosto', 'Sebteembar', 'Oktoobar', 'Nofeembar', 'Diseembar'],
    'months_short' => ['Jan', 'Feb', 'Mar', 'Abr', 'Mjo', 'Jun', 'Lyo', 'Agt', 'Seb', 'Okt', 'Nof', 'Dis'],
    'weekdays' => ['Axad', 'Isniin', 'Talaada', 'Arbaca', 'Khamiis', 'Jimce', 'Sabti'],
    'weekdays_short' => ['Axd', 'Isn', 'Tal', 'Arb', 'Kha', 'Jim', 'Sbt'],
    'weekdays_min' => ['Ax', 'Is', 'Ta', 'Ar', 'Kh', 'Ji', 'Sa'],
    'list' => [', ', ' and '],
    'first_day_of_week' => 6,
    'day_of_first_week_of_year' => 1,
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'calendar' => [
        'sameDay' => '[Maanta markay tahay] LT',
        'nextDay' => '[Beri markay tahay] LT',
        'nextWeek' => 'dddd [markay tahay] LT',
        'lastDay' => '[Shalay markay ahayd] LT',
        'lastWeek' => '[Hore] dddd [Markay ahayd] LT',
        'sameElse' => 'L',
    ],
]);
