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
 * - Estelle Comment
 */
return [
    'year' => 'san :count',
    'a_year' => '{1}san kelen|san :count',
    'y' => 'san :count',
    'month' => 'kalo :count',
    'a_month' => '{1}kalo kelen|kalo :count',
    'm' => 'k. :count',
    'week' => 'dɔgɔkun :count',
    'a_week' => 'dɔgɔkun kelen',
    'w' => 'd. :count',
    'day' => 'tile :count',
    'd' => 't. :count',
    'a_day' => '{1}tile kelen|tile :count',
    'hour' => 'lɛrɛ :count',
    'a_hour' => '{1}lɛrɛ kelen|lɛrɛ :count',
    'h' => 'l. :count',
    'minute' => 'miniti :count',
    'a_minute' => '{1}miniti kelen|miniti :count',
    'min' => 'm. :count',
    'second' => 'sekondi :count',
    'a_second' => '{1}sanga dama dama|sekondi :count',
    's' => 'sek. :count',
    'ago' => 'a bɛ :time bɔ',
    'from_now' => ':time kɔnɔ',
    'diff_today' => 'Bi',
    'diff_yesterday' => 'Kunu',
    'diff_yesterday_regexp' => 'Kunu(?:\\s+lɛrɛ)?',
    'diff_tomorrow' => 'Sini',
    'diff_tomorrow_regexp' => 'Sini(?:\\s+lɛrɛ)?',
    'diff_today_regexp' => 'Bi(?:\\s+lɛrɛ)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'MMMM [tile] D [san] YYYY',
        'LLL' => 'MMMM [tile] D [san] YYYY [lɛrɛ] HH:mm',
        'LLLL' => 'dddd MMMM [tile] D [san] YYYY [lɛrɛ] HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Bi lɛrɛ] LT',
        'nextDay' => '[Sini lɛrɛ] LT',
        'nextWeek' => 'dddd [don lɛrɛ] LT',
        'lastDay' => '[Kunu lɛrɛ] LT',
        'lastWeek' => 'dddd [tɛmɛnen lɛrɛ] LT',
        'sameElse' => 'L',
    ],
    'months' => ['Zanwuyekalo', 'Fewuruyekalo', 'Marisikalo', 'Awirilikalo', 'Mɛkalo', 'Zuwɛnkalo', 'Zuluyekalo', 'Utikalo', 'Sɛtanburukalo', 'ɔkutɔburukalo', 'Nowanburukalo', 'Desanburukalo'],
    'months_short' => ['Zan', 'Few', 'Mar', 'Awi', 'Mɛ', 'Zuw', 'Zul', 'Uti', 'Sɛt', 'ɔku', 'Now', 'Des'],
    'weekdays' => ['Kari', 'Ntɛnɛn', 'Tarata', 'Araba', 'Alamisa', 'Juma', 'Sibiri'],
    'weekdays_short' => ['Kar', 'Ntɛ', 'Tar', 'Ara', 'Ala', 'Jum', 'Sib'],
    'weekdays_min' => ['Ka', 'Nt', 'Ta', 'Ar', 'Al', 'Ju', 'Si'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' ni '],
];
