<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\CarbonInterface;

$processHoursFunction = static function (CarbonInterface $date, string $format) {
    return $format.'о'.($date->hour === 11 ? 'б' : '').'] LT';
};

/*
 * Authors:
 * - Kunal Marwaha
 * - Josh Soref
 * - François B
 * - Tim Fish
 * - Serhan Apaydın
 * - Max Mykhailenko
 * - JD Isaacks
 * - Max Kovpak
 * - AucT
 * - Philippe Vaucher
 * - Ilya Shaplyko
 * - Vadym Ievsieiev
 * - Denys Kurets
 * - Igor Kasyanchuk
 * - Tsutomu Kuroda
 * - tjku
 * - Max Melentiev
 * - Oleh
 * - epaminond
 * - Juanito Fatas
 * - Vitalii Khustochka
 * - Akira Matsuda
 * - Christopher Dell
 * - Enrique Vidal
 * - Simone Carletti
 * - Aaron Patterson
 * - Andriy Tyurnikov
 * - Nicolás Hock Isaza
 * - Iwakura Taro
 * - Andrii Ponomarov
 * - alecrabbit
 * - vystepanenko
 * - AlexWalkerson
 * - Andre Havryliuk (Andrend)
 * - Max Datsenko (datsenko-md)
 */
return [
    'year' => ':count рік|:count роки|:count років',
    'y' => ':countр|:countрр|:countрр',
    'a_year' => '{1}рік|:count рік|:count роки|:count років',
    'month' => ':count місяць|:count місяці|:count місяців',
    'm' => ':countм',
    'a_month' => '{1}місяць|:count місяць|:count місяці|:count місяців',
    'week' => ':count тиждень|:count тижні|:count тижнів',
    'w' => ':countт',
    'a_week' => '{1}тиждень|:count тиждень|:count тижні|:count тижнів',
    'day' => ':count день|:count дні|:count днів',
    'd' => ':countд',
    'a_day' => '{1}день|:count день|:count дні|:count днів',
    'hour' => ':count година|:count години|:count годин',
    'h' => ':countг',
    'a_hour' => '{1}година|:count година|:count години|:count годин',
    'minute' => ':count хвилина|:count хвилини|:count хвилин',
    'min' => ':countхв',
    'a_minute' => '{1}хвилина|:count хвилина|:count хвилини|:count хвилин',
    'second' => ':count секунда|:count секунди|:count секунд',
    's' => ':countсек',
    'a_second' => '{1}декілька секунд|:count секунда|:count секунди|:count секунд',

    'hour_ago' => ':count годину|:count години|:count годин',
    'a_hour_ago' => '{1}годину|:count годину|:count години|:count годин',
    'minute_ago' => ':count хвилину|:count хвилини|:count хвилин',
    'a_minute_ago' => '{1}хвилину|:count хвилину|:count хвилини|:count хвилин',
    'second_ago' => ':count секунду|:count секунди|:count секунд',
    'a_second_ago' => '{1}декілька секунд|:count секунду|:count секунди|:count секунд',

    'hour_from_now' => ':count годину|:count години|:count годин',
    'a_hour_from_now' => '{1}годину|:count годину|:count години|:count годин',
    'minute_from_now' => ':count хвилину|:count хвилини|:count хвилин',
    'a_minute_from_now' => '{1}хвилину|:count хвилину|:count хвилини|:count хвилин',
    'second_from_now' => ':count секунду|:count секунди|:count секунд',
    'a_second_from_now' => '{1}декілька секунд|:count секунду|:count секунди|:count секунд',

    'hour_after' => ':count годину|:count години|:count годин',
    'a_hour_after' => '{1}годину|:count годину|:count години|:count годин',
    'minute_after' => ':count хвилину|:count хвилини|:count хвилин',
    'a_minute_after' => '{1}хвилину|:count хвилину|:count хвилини|:count хвилин',
    'second_after' => ':count секунду|:count секунди|:count секунд',
    'a_second_after' => '{1}декілька секунд|:count секунду|:count секунди|:count секунд',

    'hour_before' => ':count годину|:count години|:count годин',
    'a_hour_before' => '{1}годину|:count годину|:count години|:count годин',
    'minute_before' => ':count хвилину|:count хвилини|:count хвилин',
    'a_minute_before' => '{1}хвилину|:count хвилину|:count хвилини|:count хвилин',
    'second_before' => ':count секунду|:count секунди|:count секунд',
    'a_second_before' => '{1}декілька секунд|:count секунду|:count секунди|:count секунд',

    'ago' => ':time тому',
    'from_now' => 'за :time',
    'after' => ':time після',
    'before' => ':time до',
    'diff_now' => 'щойно',
    'diff_today' => 'Сьогодні',
    'diff_today_regexp' => 'Сьогодні(?:\\s+о)?',
    'diff_yesterday' => 'вчора',
    'diff_yesterday_regexp' => 'Вчора(?:\\s+о)?',
    'diff_tomorrow' => 'завтра',
    'diff_tomorrow_regexp' => 'Завтра(?:\\s+о)?',
    'diff_before_yesterday' => 'позавчора',
    'diff_after_tomorrow' => 'післязавтра',
    'period_recurrences' => 'один раз|:count рази|:count разів',
    'period_interval' => 'кожні :interval',
    'period_start_date' => 'з :date',
    'period_end_date' => 'до :date',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY, HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY, HH:mm',
    ],
    'calendar' => [
        'sameDay' => static fn (CarbonInterface $date) => $processHoursFunction($date, '[Сьогодні '),
        'nextDay' => static fn (CarbonInterface $date) => $processHoursFunction($date, '[Завтра '),
        'nextWeek' => static fn (CarbonInterface $date) => $processHoursFunction($date, '[У] dddd ['),
        'lastDay' => static fn (CarbonInterface $date) => $processHoursFunction($date, '[Вчора '),
        'lastWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0, 3, 5, 6 => $processHoursFunction($date, '[Минулої] dddd ['),
            default => $processHoursFunction($date, '[Минулого] dddd ['),
        },
        'sameElse' => 'L',
    ],
    'ordinal' => static fn ($number, $period) => match ($period) {
        'M', 'd', 'DDD', 'w', 'W' => $number.'-й',
        'D' => $number.'-го',
        default => $number,
    },
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'ночі';
        }

        if ($hour < 12) {
            return 'ранку';
        }

        if ($hour < 17) {
            return 'дня';
        }

        return 'вечора';
    },
    'months' => ['січня', 'лютого', 'березня', 'квітня', 'травня', 'червня', 'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'],
    'months_standalone' => ['січень', 'лютий', 'березень', 'квітень', 'травень', 'червень', 'липень', 'серпень', 'вересень', 'жовтень', 'листопад', 'грудень'],
    'months_short' => ['січ', 'лют', 'бер', 'кві', 'тра', 'чер', 'лип', 'сер', 'вер', 'жов', 'лис', 'гру'],
    'months_regexp' => '/(D[oD]?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => static function (CarbonInterface $date, $format, $index) {
        static $words = [
            'nominative' => ['неділя', 'понеділок', 'вівторок', 'середа', 'четвер', 'п’ятниця', 'субота'],
            'accusative' => ['неділю', 'понеділок', 'вівторок', 'середу', 'четвер', 'п’ятницю', 'суботу'],
            'genitive' => ['неділі', 'понеділка', 'вівторка', 'середи', 'четверга', 'п’ятниці', 'суботи'],
        ];

        $format ??= '';
        $nounCase = preg_match('/(\[(В|в|У|у)\])\s+dddd/u', $format)
            ? 'accusative'
            : (
                preg_match('/\[?(?:минулої|наступної)?\s*\]\s+dddd/u', $format)
                    ? 'genitive'
                    : 'nominative'
            );

        return $words[$nounCase][$index] ?? null;
    },
    'weekdays_short' => ['нд', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'],
    'weekdays_min' => ['нд', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' i '],
];
