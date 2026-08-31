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
 * - Atolagbe Abisoye
 */
return [
    'year' => 'ọdún :count',
    'a_year' => '{1}ọdún kan|ọdún :count',
    'month' => 'osù :count',
    'a_month' => '{1}osù kan|osù :count',
    'week' => 'ọsẹ :count',
    'a_week' => '{1}ọsẹ kan|ọsẹ :count',
    'day' => 'ọjọ́ :count',
    'a_day' => '{1}ọjọ́ kan|ọjọ́ :count',
    'hour' => 'wákati :count',
    'a_hour' => '{1}wákati kan|wákati :count',
    'minute' => 'ìsẹjú :count',
    'a_minute' => '{1}ìsẹjú kan|ìsẹjú :count',
    'second' => 'iaayá :count',
    'a_second' => '{1}ìsẹjú aayá die|aayá :count',
    'ago' => ':time kọjá',
    'from_now' => 'ní :time',
    'diff_yesterday' => 'Àna',
    'diff_yesterday_regexp' => 'Àna(?:\\s+ni)?',
    'diff_today' => 'Ònì',
    'diff_today_regexp' => 'Ònì(?:\\s+ni)?',
    'diff_tomorrow' => 'Ọ̀la',
    'diff_tomorrow_regexp' => 'Ọ̀la(?:\\s+ni)?',
    'formats' => [
        'LT' => 'h:mm A',
        'LTS' => 'h:mm:ss A',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY h:mm A',
        'LLLL' => 'dddd, D MMMM YYYY h:mm A',
    ],
    'calendar' => [
        'sameDay' => '[Ònì ni] LT',
        'nextDay' => '[Ọ̀la ni] LT',
        'nextWeek' => 'dddd [Ọsẹ̀ tón\'bọ] [ni] LT',
        'lastDay' => '[Àna ni] LT',
        'lastWeek' => 'dddd [Ọsẹ̀ tólọ́] [ni] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => 'ọjọ́ :number',
    'months' => ['Sẹ́rẹ́', 'Èrèlè', 'Ẹrẹ̀nà', 'Ìgbé', 'Èbibi', 'Òkùdu', 'Agẹmo', 'Ògún', 'Owewe', 'Ọ̀wàrà', 'Bélú', 'Ọ̀pẹ̀̀'],
    'months_short' => ['Sẹ́r', 'Èrl', 'Ẹrn', 'Ìgb', 'Èbi', 'Òkù', 'Agẹ', 'Ògú', 'Owe', 'Ọ̀wà', 'Bél', 'Ọ̀pẹ̀̀'],
    'weekdays' => ['Àìkú', 'Ajé', 'Ìsẹ́gun', 'Ọjọ́rú', 'Ọjọ́bọ', 'Ẹtì', 'Àbámẹ́ta'],
    'weekdays_short' => ['Àìk', 'Ajé', 'Ìsẹ́', 'Ọjr', 'Ọjb', 'Ẹtì', 'Àbá'],
    'weekdays_min' => ['Àì', 'Aj', 'Ìs', 'Ọr', 'Ọb', 'Ẹt', 'Àb'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'meridiem' => ['Àárọ̀', 'Ọ̀sán'],
];
