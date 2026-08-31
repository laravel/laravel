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
 * - mhamlet
 */
return [
    'year' => ':count տարի',
    'a_year' => 'տարի|:count տարի',
    'y' => ':countտ',
    'month' => ':count ամիս',
    'a_month' => 'ամիս|:count ամիս',
    'm' => ':countամ',
    'week' => ':count շաբաթ',
    'a_week' => 'շաբաթ|:count շաբաթ',
    'w' => ':countշ',
    'day' => ':count օր',
    'a_day' => 'օր|:count օր',
    'd' => ':countօր',
    'hour' => ':count ժամ',
    'a_hour' => 'ժամ|:count ժամ',
    'h' => ':countժ',
    'minute' => ':count րոպե',
    'a_minute' => 'րոպե|:count րոպե',
    'min' => ':countր',
    'second' => ':count վայրկյան',
    'a_second' => 'մի քանի վայրկյան|:count վայրկյան',
    's' => ':countվրկ',
    'ago' => ':time առաջ',
    'from_now' => ':timeից',
    'after' => ':time հետո',
    'before' => ':time առաջ',
    'diff_now' => 'հիմա',
    'diff_today' => 'այսօր',
    'diff_yesterday' => 'երեկ',
    'diff_tomorrow' => 'վաղը',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMMM YYYY թ.',
        'LLL' => 'D MMMM YYYY թ., HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY թ., HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[այսօր] LT',
        'nextDay' => '[վաղը] LT',
        'nextWeek' => 'dddd [օրը ժամը] LT',
        'lastDay' => '[երեկ] LT',
        'lastWeek' => '[անցած] dddd [օրը ժամը] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number, $period) {
        return match ($period) {
            'DDD', 'w', 'W', 'DDDo' => $number.($number === 1 ? '-ին' : '-րդ'),
            default => $number,
        };
    },
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'գիշերվա';
        }
        if ($hour < 12) {
            return 'առավոտվա';
        }
        if ($hour < 17) {
            return 'ցերեկվա';
        }

        return 'երեկոյան';
    },
    'months' => ['հունվարի', 'փետրվարի', 'մարտի', 'ապրիլի', 'մայիսի', 'հունիսի', 'հուլիսի', 'օգոստոսի', 'սեպտեմբերի', 'հոկտեմբերի', 'նոյեմբերի', 'դեկտեմբերի'],
    'months_standalone' => ['հունվար', 'փետրվար', 'մարտ', 'ապրիլ', 'մայիս', 'հունիս', 'հուլիս', 'օգոստոս', 'սեպտեմբեր', 'հոկտեմբեր', 'նոյեմբեր', 'դեկտեմբեր'],
    'months_short' => ['հնվ', 'փտր', 'մրտ', 'ապր', 'մյս', 'հնս', 'հլս', 'օգս', 'սպտ', 'հկտ', 'նմբ', 'դկտ'],
    'months_regexp' => '/(D[oD]?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => ['կիրակի', 'երկուշաբթի', 'երեքշաբթի', 'չորեքշաբթի', 'հինգշաբթի', 'ուրբաթ', 'շաբաթ'],
    'weekdays_short' => ['կրկ', 'երկ', 'երք', 'չրք', 'հնգ', 'ուրբ', 'շբթ'],
    'weekdays_min' => ['կրկ', 'երկ', 'երք', 'չրք', 'հնգ', 'ուրբ', 'շբթ'],
    'list' => [', ', ' եւ '],
    'first_day_of_week' => 1,
];
