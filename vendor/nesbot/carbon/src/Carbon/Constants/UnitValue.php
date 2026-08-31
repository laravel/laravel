<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Constants;

interface UnitValue
{
    /**
     * The day constants.
     */
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    /**
     * The month constants.
     * These aren't used by Carbon itself but exist for
     * convenience's sake alone.
     */
    public const JANUARY = 1;
    public const FEBRUARY = 2;
    public const MARCH = 3;
    public const APRIL = 4;
    public const MAY = 5;
    public const JUNE = 6;
    public const JULY = 7;
    public const AUGUST = 8;
    public const SEPTEMBER = 9;
    public const OCTOBER = 10;
    public const NOVEMBER = 11;
    public const DECEMBER = 12;

    /**
     * Number of X in Y.
     */
    public const YEARS_PER_MILLENNIUM = 1_000;
    public const YEARS_PER_CENTURY = 100;
    public const YEARS_PER_DECADE = 10;
    public const MONTHS_PER_YEAR = 12;
    public const MONTHS_PER_QUARTER = 3;
    public const QUARTERS_PER_YEAR = 4;
    public const WEEKS_PER_YEAR = 52;
    public const WEEKS_PER_MONTH = 4;
    public const DAYS_PER_YEAR = 365;
    public const DAYS_PER_WEEK = 7;
    public const HOURS_PER_DAY = 24;
    public const MINUTES_PER_HOUR = 60;
    public const SECONDS_PER_MINUTE = 60;
    public const MILLISECONDS_PER_SECOND = 1_000;
    public const MICROSECONDS_PER_MILLISECOND = 1_000;
    public const MICROSECONDS_PER_SECOND = 1_000_000;
}
