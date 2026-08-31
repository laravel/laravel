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
use Symfony\Component\Translation\PluralizationRules;

// @codeCoverageIgnoreStart
if (class_exists(PluralizationRules::class)) {
    PluralizationRules::set(static function ($number) {
        return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
    }, 'be');
}
// @codeCoverageIgnoreEnd

/*
 * Authors:
 * - Josh Soref
 * - SobakaSlava
 * - François B
 * - Serhan Apaydın
 * - JD Isaacks
 * - AbadonnaAbbys
 * - Siomkin Alexander
 */
return [
    'year' => ':count год|:count гады|:count гадоў',
    'a_year' => '{1}год|:count год|:count гады|:count гадоў',
    'y' => ':count год|:count гады|:count гадоў',
    'month' => ':count месяц|:count месяцы|:count месяцаў',
    'a_month' => '{1}месяц|:count месяц|:count месяцы|:count месяцаў',
    'm' => ':count месяц|:count месяцы|:count месяцаў',
    'week' => ':count тыдзень|:count тыдні|:count тыдняў',
    'a_week' => '{1}тыдзень|:count тыдзень|:count тыдні|:count тыдняў',
    'w' => ':count тыдзень|:count тыдні|:count тыдняў',
    'day' => ':count дзень|:count дні|:count дзён',
    'a_day' => '{1}дзень|:count дзень|:count дні|:count дзён',
    'd' => ':count дн',
    'hour' => ':count гадзіну|:count гадзіны|:count гадзін',
    'a_hour' => '{1}гадзіна|:count гадзіна|:count гадзіны|:count гадзін',
    'h' => ':count гадзіна|:count гадзіны|:count гадзін',
    'minute' => ':count хвіліна|:count хвіліны|:count хвілін',
    'a_minute' => '{1}хвіліна|:count хвіліна|:count хвіліны|:count хвілін',
    'min' => ':count хв',
    'second' => ':count секунда|:count секунды|:count секунд',
    'a_second' => '{1}некалькі секунд|:count секунда|:count секунды|:count секунд',
    's' => ':count сек',

    'hour_ago' => ':count гадзіну|:count гадзіны|:count гадзін',
    'a_hour_ago' => '{1}гадзіну|:count гадзіну|:count гадзіны|:count гадзін',
    'h_ago' => ':count гадзіну|:count гадзіны|:count гадзін',
    'minute_ago' => ':count хвіліну|:count хвіліны|:count хвілін',
    'a_minute_ago' => '{1}хвіліну|:count хвіліну|:count хвіліны|:count хвілін',
    'min_ago' => ':count хвіліну|:count хвіліны|:count хвілін',
    'second_ago' => ':count секунду|:count секунды|:count секунд',
    'a_second_ago' => '{1}некалькі секунд|:count секунду|:count секунды|:count секунд',
    's_ago' => ':count секунду|:count секунды|:count секунд',

    'hour_from_now' => ':count гадзіну|:count гадзіны|:count гадзін',
    'a_hour_from_now' => '{1}гадзіну|:count гадзіну|:count гадзіны|:count гадзін',
    'h_from_now' => ':count гадзіну|:count гадзіны|:count гадзін',
    'minute_from_now' => ':count хвіліну|:count хвіліны|:count хвілін',
    'a_minute_from_now' => '{1}хвіліну|:count хвіліну|:count хвіліны|:count хвілін',
    'min_from_now' => ':count хвіліну|:count хвіліны|:count хвілін',
    'second_from_now' => ':count секунду|:count секунды|:count секунд',
    'a_second_from_now' => '{1}некалькі секунд|:count секунду|:count секунды|:count секунд',
    's_from_now' => ':count секунду|:count секунды|:count секунд',

    'hour_after' => ':count гадзіну|:count гадзіны|:count гадзін',
    'a_hour_after' => '{1}гадзіну|:count гадзіну|:count гадзіны|:count гадзін',
    'h_after' => ':count гадзіну|:count гадзіны|:count гадзін',
    'minute_after' => ':count хвіліну|:count хвіліны|:count хвілін',
    'a_minute_after' => '{1}хвіліну|:count хвіліну|:count хвіліны|:count хвілін',
    'min_after' => ':count хвіліну|:count хвіліны|:count хвілін',
    'second_after' => ':count секунду|:count секунды|:count секунд',
    'a_second_after' => '{1}некалькі секунд|:count секунду|:count секунды|:count секунд',
    's_after' => ':count секунду|:count секунды|:count секунд',

    'hour_before' => ':count гадзіну|:count гадзіны|:count гадзін',
    'a_hour_before' => '{1}гадзіну|:count гадзіну|:count гадзіны|:count гадзін',
    'h_before' => ':count гадзіну|:count гадзіны|:count гадзін',
    'minute_before' => ':count хвіліну|:count хвіліны|:count хвілін',
    'a_minute_before' => '{1}хвіліну|:count хвіліну|:count хвіліны|:count хвілін',
    'min_before' => ':count хвіліну|:count хвіліны|:count хвілін',
    'second_before' => ':count секунду|:count секунды|:count секунд',
    'a_second_before' => '{1}некалькі секунд|:count секунду|:count секунды|:count секунд',
    's_before' => ':count секунду|:count секунды|:count секунд',

    'ago' => ':time таму',
    'from_now' => 'праз :time',
    'after' => ':time пасля',
    'before' => ':time да',
    'diff_now' => 'цяпер',
    'diff_today' => 'Сёння',
    'diff_today_regexp' => 'Сёння(?:\\s+ў)?',
    'diff_yesterday' => 'учора',
    'diff_yesterday_regexp' => 'Учора(?:\\s+ў)?',
    'diff_tomorrow' => 'заўтра',
    'diff_tomorrow_regexp' => 'Заўтра(?:\\s+ў)?',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMMM YYYY г.',
        'LLL' => 'D MMMM YYYY г., HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY г., HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Сёння ў] LT',
        'nextDay' => '[Заўтра ў] LT',
        'nextWeek' => '[У] dddd [ў] LT',
        'lastDay' => '[Учора ў] LT',
        'lastWeek' => static fn (CarbonInterface $current) => match ($current->dayOfWeek) {
            1, 2, 4 => '[У мінулы] dddd [ў] LT',
            default => '[У мінулую] dddd [ў] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => static fn ($number, $period) => match ($period) {
        'M', 'd', 'DDD', 'w', 'W' => ($number % 10 === 2 || $number % 10 === 3) &&
                ($number % 100 !== 12 && $number % 100 !== 13) ? $number.'-і' : $number.'-ы',
        'D' => $number.'-га',
        default => $number,
    },
    'meridiem' => static function ($hour) {
        if ($hour < 4) {
            return 'ночы';
        }

        if ($hour < 12) {
            return 'раніцы';
        }

        if ($hour < 17) {
            return 'дня';
        }

        return 'вечара';
    },
    'months' => ['студзеня', 'лютага', 'сакавіка', 'красавіка', 'траўня', 'чэрвеня', 'ліпеня', 'жніўня', 'верасня', 'кастрычніка', 'лістапада', 'снежня'],
    'months_standalone' => ['студзень', 'люты', 'сакавік', 'красавік', 'травень', 'чэрвень', 'ліпень', 'жнівень', 'верасень', 'кастрычнік', 'лістапад', 'снежань'],
    'months_short' => ['студ', 'лют', 'сак', 'крас', 'трав', 'чэрв', 'ліп', 'жнів', 'вер', 'каст', 'ліст', 'снеж'],
    'months_regexp' => '/(DD?o?\.?(\[[^\[\]]*\]|\s)+MMMM?|L{2,4}|l{2,4})/',
    'weekdays' => ['нядзелю', 'панядзелак', 'аўторак', 'сераду', 'чацвер', 'пятніцу', 'суботу'],
    'weekdays_standalone' => ['нядзеля', 'панядзелак', 'аўторак', 'серада', 'чацвер', 'пятніца', 'субота'],
    'weekdays_short' => ['нд', 'пн', 'ат', 'ср', 'чц', 'пт', 'сб'],
    'weekdays_min' => ['нд', 'пн', 'ат', 'ср', 'чц', 'пт', 'сб'],
    'weekdays_regexp' => '/\[ ?[Ууў] ?(?:мінулую|наступную)? ?\] ?dddd/',
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' і '],
    'months_short_standalone' => ['сту', 'лют', 'сак', 'кра', 'май', 'чэр', 'ліп', 'жні', 'вер', 'кас', 'ліс', 'сне'],
];
