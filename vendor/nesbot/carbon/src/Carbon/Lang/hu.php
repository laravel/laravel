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
 * - Adam Brunner
 * - Brett Johnson
 * - balping
 */

use Carbon\CarbonInterface;

$huWeekEndings = ['vasárnap', 'hétfőn', 'kedden', 'szerdán', 'csütörtökön', 'pénteken', 'szombaton'];

return [
    'year' => ':count év',
    'y' => ':count év',
    'month' => ':count hónap',
    'm' => ':count hónap',
    'week' => ':count hét',
    'w' => ':count hét',
    'day' => ':count nap',
    'd' => ':count nap',
    'hour' => ':count óra',
    'h' => ':count óra',
    'minute' => ':count perc',
    'min' => ':count perc',
    'second' => ':count másodperc',
    's' => ':count másodperc',
    'ago' => ':time',
    'from_now' => ':time múlva',
    'after' => ':time később',
    'before' => ':time korábban',
    'year_ago' => ':count éve',
    'y_ago' => ':count éve',
    'month_ago' => ':count hónapja',
    'm_ago' => ':count hónapja',
    'week_ago' => ':count hete',
    'w_ago' => ':count hete',
    'day_ago' => ':count napja',
    'd_ago' => ':count napja',
    'hour_ago' => ':count órája',
    'h_ago' => ':count órája',
    'minute_ago' => ':count perce',
    'min_ago' => ':count perce',
    'second_ago' => ':count másodperce',
    's_ago' => ':count másodperce',
    'year_after' => ':count évvel',
    'y_after' => ':count évvel',
    'month_after' => ':count hónappal',
    'm_after' => ':count hónappal',
    'week_after' => ':count héttel',
    'w_after' => ':count héttel',
    'day_after' => ':count nappal',
    'd_after' => ':count nappal',
    'hour_after' => ':count órával',
    'h_after' => ':count órával',
    'minute_after' => ':count perccel',
    'min_after' => ':count perccel',
    'second_after' => ':count másodperccel',
    's_after' => ':count másodperccel',
    'year_before' => ':count évvel',
    'y_before' => ':count évvel',
    'month_before' => ':count hónappal',
    'm_before' => ':count hónappal',
    'week_before' => ':count héttel',
    'w_before' => ':count héttel',
    'day_before' => ':count nappal',
    'd_before' => ':count nappal',
    'hour_before' => ':count órával',
    'h_before' => ':count órával',
    'minute_before' => ':count perccel',
    'min_before' => ':count perccel',
    'second_before' => ':count másodperccel',
    's_before' => ':count másodperccel',
    'months' => ['január', 'február', 'március', 'április', 'május', 'június', 'július', 'augusztus', 'szeptember', 'október', 'november', 'december'],
    'months_short' => ['jan.', 'febr.', 'márc.', 'ápr.', 'máj.', 'jún.', 'júl.', 'aug.', 'szept.', 'okt.', 'nov.', 'dec.'],
    'weekdays' => ['vasárnap', 'hétfő', 'kedd', 'szerda', 'csütörtök', 'péntek', 'szombat'],
    'weekdays_short' => ['vas', 'hét', 'kedd', 'sze', 'csüt', 'pén', 'szo'],
    'weekdays_min' => ['v', 'h', 'k', 'sze', 'cs', 'p', 'sz'],
    'ordinal' => ':number.',
    'diff_now' => 'most',
    'diff_today' => 'ma',
    'diff_yesterday' => 'tegnap',
    'diff_tomorrow' => 'holnap',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'YYYY.MM.DD.',
        'LL' => 'YYYY. MMMM D.',
        'LLL' => 'YYYY. MMMM D. H:mm',
        'LLLL' => 'YYYY. MMMM D., dddd H:mm',
    ],
    'calendar' => [
        'sameDay' => '[ma] LT[-kor]',
        'nextDay' => '[holnap] LT[-kor]',
        'nextWeek' => static function (CarbonInterface $date) use ($huWeekEndings) {
            return '['.$huWeekEndings[$date->dayOfWeek].'] LT[-kor]';
        },
        'lastDay' => '[tegnap] LT[-kor]',
        'lastWeek' => static function (CarbonInterface $date) use ($huWeekEndings) {
            return '[múlt '.$huWeekEndings[$date->dayOfWeek].'] LT[-kor]';
        },
        'sameElse' => 'L',
    ],
    'meridiem' => ['DE', 'DU'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' és '],
];
