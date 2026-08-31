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
 * - Ahmed Ali
 */

$months = [
    'ޖެނުއަރީ',
    'ފެބްރުއަރީ',
    'މާރިޗު',
    'އޭޕްރީލު',
    'މޭ',
    'ޖޫން',
    'ޖުލައި',
    'އޯގަސްޓު',
    'ސެޕްޓެމްބަރު',
    'އޮކްޓޯބަރު',
    'ނޮވެމްބަރު',
    'ޑިސެމްބަރު',
];

$weekdays = [
    'އާދިއްތަ',
    'ހޯމަ',
    'އަންގާރަ',
    'ބުދަ',
    'ބުރާސްފަތި',
    'ހުކުރު',
    'ހޮނިހިރު',
];

return [
    'year' => '{0}އަހަރެއް|[1,Inf]:count އަހަރު',
    'y' => '{0}އަހަރެއް|[1,Inf]:count އަހަރު',
    'month' => '{0}މައްސަރެއް|[1,Inf]:count މަސް',
    'm' => '{0}މައްސަރެއް|[1,Inf]:count މަސް',
    'week' => '{0}ހަފްތާއެއް|[1,Inf]:count ހަފްތާ',
    'w' => '{0}ހަފްތާއެއް|[1,Inf]:count ހަފްތާ',
    'day' => '{0}ދުވަސް|[1,Inf]:count ދުވަސް',
    'd' => '{0}ދުވަސް|[1,Inf]:count ދުވަސް',
    'hour' => '{0}ގަޑިއިރެއް|[1,Inf]:count ގަޑި',
    'h' => '{0}ގަޑިއިރެއް|[1,Inf]:count ގަޑި',
    'minute' => '{0}މިނެޓެއް|[1,Inf]:count މިނެޓް',
    'min' => '{0}މިނެޓެއް|[1,Inf]:count މިނެޓް',
    'second' => '{0}ސިކުންތެއް|[1,Inf]:count ސިކުންތު',
    's' => '{0}ސިކުންތެއް|[1,Inf]:count ސިކުންތު',
    'ago' => ':time ކުރިން',
    'from_now' => ':time ފަހުން',
    'after' => ':time ފަހުން',
    'before' => ':time ކުރި',
    'diff_yesterday' => 'އިއްޔެ',
    'diff_today' => 'މިއަދު',
    'diff_tomorrow' => 'މާދަމާ',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[މިއަދު] LT',
        'nextDay' => '[މާދަމާ] LT',
        'nextWeek' => 'dddd LT',
        'lastDay' => '[އިއްޔެ] LT',
        'lastWeek' => '[ފާއިތުވި] dddd LT',
        'sameElse' => 'L',
    ],
    'meridiem' => ['މކ', 'މފ'],
    'months' => $months,
    'months_short' => $months,
    'weekdays' => $weekdays,
    'weekdays_short' => $weekdays,
    'weekdays_min' => ['އާދި', 'ހޯމަ', 'އަން', 'ބުދަ', 'ބުރާ', 'ހުކު', 'ހޮނި'],
    'list' => [', ', ' އަދި '],
];
