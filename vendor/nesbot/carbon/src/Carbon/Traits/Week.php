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

namespace Carbon\Traits;

use Carbon\CarbonInterval;

/**
 * Trait Week.
 *
 * week and ISO week number, year and count in year.
 *
 * Depends on the following properties:
 *
 * @property int $daysInYear
 * @property int $dayOfWeek
 * @property int $dayOfYear
 * @property int $year
 *
 * Depends on the following methods:
 *
 * @method static addWeeks(int $weeks = 1)
 * @method static copy()
 * @method static dayOfYear(int $dayOfYear)
 * @method string getTranslationMessage(string $key, ?string $locale = null, ?string $default = null, $translator = null)
 * @method static next(int|string $modifier = null)
 * @method static startOfWeek(int $day = null)
 * @method static subWeeks(int $weeks = 1)
 * @method static year(int $year = null)
 */
trait Week
{
    /**
     * Set/get the week number of year using given first day of week and first
     * day of year included in the first week. Or use ISO format if no settings
     * given.
     *
     * @param int|null $year      if null, act as a getter, if not null, set the year and return current instance.
     * @param int|null $dayOfWeek first date of week from 0 (Sunday) to 6 (Saturday)
     * @param int|null $dayOfYear first day of year included in the week #1
     *
     * @return int|static
     */
    public function isoWeekYear($year = null, $dayOfWeek = null, $dayOfYear = null)
    {
        return $this->weekYear(
            $year,
            $dayOfWeek ?? static::MONDAY,
            $dayOfYear ?? static::THURSDAY,
        );
    }

    /**
     * Set/get the week number of year using given first day of week and first
     * day of year included in the first week. Or use US format if no settings
     * given (Sunday / Jan 6).
     *
     * @param int|null $year      if null, act as a getter, if not null, set the year and return current instance.
     * @param int|null $dayOfWeek first date of week from 0 (Sunday) to 6 (Saturday)
     * @param int|null $dayOfYear first day of year included in the week #1
     *
     * @return int|static
     */
    public function weekYear($year = null, $dayOfWeek = null, $dayOfYear = null)
    {
        $dayOfWeek = $dayOfWeek ?? $this->getTranslationMessage('first_day_of_week') ?? static::SUNDAY;
        $dayOfYear = $dayOfYear ?? $this->getTranslationMessage('day_of_first_week_of_year') ?? 1;

        if ($year !== null) {
            $year = (int) round($year);

            if ($this->weekYear(null, $dayOfWeek, $dayOfYear) === $year) {
                return $this->avoidMutation();
            }

            $week = $this->week(null, $dayOfWeek, $dayOfYear);
            $day = $this->dayOfWeek;
            $date = $this->year($year);

            $date = match ($date->weekYear(null, $dayOfWeek, $dayOfYear) - $year) {
                CarbonInterval::POSITIVE => $date->subWeeks(static::WEEKS_PER_YEAR / 2),
                CarbonInterval::NEGATIVE => $date->addWeeks(static::WEEKS_PER_YEAR / 2),
                default => $date,
            };

            $date = $date
                ->addWeeks($week - $date->week(null, $dayOfWeek, $dayOfYear))
                ->startOfWeek($dayOfWeek);

            if ($date->dayOfWeek === $day) {
                return $date;
            }

            return $date->next($day);
        }

        $year = $this->year;
        $day = $this->dayOfYear;
        $date = $this->avoidMutation()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);

        if ($date->year === $year && $day < $date->dayOfYear) {
            return $year - 1;
        }

        $date = $this->avoidMutation()->addYear()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);

        if ($date->year === $year && $day >= $date->dayOfYear) {
            return $year + 1;
        }

        return $year;
    }

    /**
     * Get the number of weeks of the current week-year using given first day of week and first
     * day of year included in the first week. Or use ISO format if no settings
     * given.
     *
     * @param int|null $dayOfWeek first date of week from 0 (Sunday) to 6 (Saturday)
     * @param int|null $dayOfYear first day of year included in the week #1
     *
     * @return int
     */
    public function isoWeeksInYear($dayOfWeek = null, $dayOfYear = null)
    {
        return $this->weeksInYear(
            $dayOfWeek ?? static::MONDAY,
            $dayOfYear ?? static::THURSDAY,
        );
    }

    /**
     * Get the number of weeks of the current week-year using given first day of week and first
     * day of year included in the first week. Or use US format if no settings
     * given (Sunday / Jan 6).
     *
     * @param int|null $dayOfWeek first date of week from 0 (Sunday) to 6 (Saturday)
     * @param int|null $dayOfYear first day of year included in the week #1
     *
     * @return int
     */
    public function weeksInYear($dayOfWeek = null, $dayOfYear = null)
    {
        $dayOfWeek = $dayOfWeek ?? $this->getTranslationMessage('first_day_of_week') ?? static::SUNDAY;
        $dayOfYear = $dayOfYear ?? $this->getTranslationMessage('day_of_first_week_of_year') ?? 1;
        $year = $this->year;
        $start = $this->avoidMutation()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
        $startDay = $start->dayOfYear;
        if ($start->year !== $year) {
            $startDay -= $start->daysInYear;
        }
        $end = $this->avoidMutation()->addYear()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
        $endDay = $end->dayOfYear;
        if ($end->year !== $year) {
            $endDay += $this->daysInYear;
        }

        return (int) round(($endDay - $startDay) / static::DAYS_PER_WEEK);
    }

    /**
     * Get/set the week number using given first day of week and first
     * day of year included in the first week. Or use US format if no settings
     * given (Sunday / Jan 6).
     *
     * @param int|null $week
     * @param int|null $dayOfWeek
     * @param int|null $dayOfYear
     *
     * @return int|static
     */
    public function week($week = null, $dayOfWeek = null, $dayOfYear = null)
    {
        $date = $this;
        $dayOfWeek = $dayOfWeek ?? $this->getTranslationMessage('first_day_of_week') ?? 0;
        $dayOfYear = $dayOfYear ?? $this->getTranslationMessage('day_of_first_week_of_year') ?? 1;

        if ($week !== null) {
            return $date->addWeeks(round($week) - $this->week(null, $dayOfWeek, $dayOfYear));
        }

        $start = $date->avoidMutation()->shiftTimezone('UTC')->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
        $end = $date->avoidMutation()->shiftTimezone('UTC')->startOfWeek($dayOfWeek);

        if ($start > $end) {
            $start = $start->subWeeks(static::WEEKS_PER_YEAR / 2)->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
        }

        $week = (int) ($start->diffInDays($end) / static::DAYS_PER_WEEK + 1);

        return $week > $end->weeksInYear($dayOfWeek, $dayOfYear) ? 1 : $week;
    }

    /**
     * Get/set the week number using given first day of week and first
     * day of year included in the first week. Or use ISO format if no settings
     * given.
     *
     * @param int|null $week
     * @param int|null $dayOfWeek
     * @param int|null $dayOfYear
     *
     * @return int|static
     */
    public function isoWeek($week = null, $dayOfWeek = null, $dayOfYear = null)
    {
        return $this->week(
            $week,
            $dayOfWeek ?? static::MONDAY,
            $dayOfYear ?? static::THURSDAY,
        );
    }
}
