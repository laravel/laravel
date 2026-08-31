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
 * - Milos Sakovic
 * - Paul
 * - Pete Scopes (pdscopes)
 */
return [
    /*
     * {1}, {0} and [-Inf,Inf] are not needed as it's the default for English pluralization.
     * But as some languages are using en.php as a fallback, it's better to specify it
     * explicitly so those languages also fallback to English pluralization when a unit
     * is missing.
     */
    'year' => '{1}:count year|{0}:count years|[-Inf,Inf]:count years',
    'a_year' => '{1}a year|{0}:count years|[-Inf,Inf]:count years',
    'y' => '{1}:countyr|{0}:countyrs|[-Inf,Inf]:countyrs',
    'month' => '{1}:count month|{0}:count months|[-Inf,Inf]:count months',
    'a_month' => '{1}a month|{0}:count months|[-Inf,Inf]:count months',
    'm' => '{1}:countmo|{0}:countmos|[-Inf,Inf]:countmos',
    'week' => '{1}:count week|{0}:count weeks|[-Inf,Inf]:count weeks',
    'a_week' => '{1}a week|{0}:count weeks|[-Inf,Inf]:count weeks',
    'w' => ':countw',
    'day' => '{1}:count day|{0}:count days|[-Inf,Inf]:count days',
    'a_day' => '{1}a day|{0}:count days|[-Inf,Inf]:count days',
    'd' => ':countd',
    'hour' => '{1}:count hour|{0}:count hours|[-Inf,Inf]:count hours',
    'a_hour' => '{1}an hour|{0}:count hours|[-Inf,Inf]:count hours',
    'h' => ':counth',
    'minute' => '{1}:count minute|{0}:count minutes|[-Inf,Inf]:count minutes',
    'a_minute' => '{1}a minute|{0}:count minutes|[-Inf,Inf]:count minutes',
    'min' => ':countm',
    'second' => '{1}:count second|{0}:count seconds|[-Inf,Inf]:count seconds',
    'a_second' => '{0,1}a few seconds|[-Inf,Inf]:count seconds',
    's' => ':counts',
    'millisecond' => '{1}:count millisecond|{0}:count milliseconds|[-Inf,Inf]:count milliseconds',
    'a_millisecond' => '{1}a millisecond|{0}:count milliseconds|[-Inf,Inf]:count milliseconds',
    'ms' => ':countms',
    'microsecond' => '{1}:count microsecond|{0}:count microseconds|[-Inf,Inf]:count microseconds',
    'a_microsecond' => '{1}a microsecond|{0}:count microseconds|[-Inf,Inf]:count microseconds',
    'µs' => ':countµs',
    'ago' => ':time ago',
    'from_now' => ':time from now',
    'after' => ':time after',
    'before' => ':time before',
    'diff_now' => 'just now',
    'diff_today' => 'today',
    'diff_yesterday' => 'yesterday',
    'diff_tomorrow' => 'tomorrow',
    'diff_before_yesterday' => 'before yesterday',
    'diff_after_tomorrow' => 'after tomorrow',
    'period_recurrences' => '{1}once|{0}:count times|[-Inf,Inf]:count times',
    'period_interval' => 'every :interval',
    'period_start_date' => 'from :date',
    'period_end_date' => 'to :date',
    'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    'weekdays' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    'weekdays_short' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    'weekdays_min' => ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    'ordinal' => static function ($number) {
        $lastDigit = $number % 10;

        return $number.(
            ((int) ($number % 100 / 10) === 1) ? 'th' : (
                ($lastDigit === 1) ? 'st' : (
                    ($lastDigit === 2) ? 'nd' : (
                        ($lastDigit === 3) ? 'rd' : 'th'
                    )
                )
            )
        );
    },
    'formats' => [
        'LT' => 'h:mm A',
        'LTS' => 'h:mm:ss A',
        'L' => 'MM/DD/YYYY',
        'LL' => 'MMMM D, YYYY',
        'LLL' => 'MMMM D, YYYY h:mm A',
        'LLLL' => 'dddd, MMMM D, YYYY h:mm A',
    ],
    'list' => [', ', ' and '],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['AM', 'PM'],
];
