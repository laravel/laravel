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

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use ReturnTypeWillChange;

/**
 * Trait Modifiers.
 *
 * Returns dates relative to current date using modifier short-hand.
 */
trait Modifiers
{
    /**
     * Midday/noon hour.
     *
     * @var int
     */
    protected static $midDayAt = 12;

    /**
     * get midday/noon hour
     *
     * @return int
     */
    public static function getMidDayAt()
    {
        return static::$midDayAt;
    }

    /**
     * @deprecated To avoid conflict between different third-party libraries, static setters should not be used.
     *             You should rather consider mid-day is always 12pm, then if you need to test if it's an other
     *             hour, test it explicitly:
     *                 $date->format('G') == 13
     *             or to set explicitly to a given hour:
     *                 $date->setTime(13, 0, 0, 0)
     *
     * Set midday/noon hour
     *
     * @param int $hour midday hour
     *
     * @return void
     */
    public static function setMidDayAt($hour)
    {
        static::$midDayAt = $hour;
    }

    /**
     * Modify to midday, default to self::$midDayAt
     *
     * @return static
     */
    public function midDay()
    {
        return $this->setTime(static::$midDayAt, 0, 0, 0);
    }

    /**
     * Modify to the next occurrence of a given modifier such as a day of
     * the week. If no modifier is provided, modify to the next occurrence
     * of the current day of the week. Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param string|int|null $modifier
     *
     * @return static
     */
    public function next($modifier = null)
    {
        if ($modifier === null) {
            $modifier = $this->dayOfWeek;
        }

        return $this->change(
            'next '.(\is_string($modifier) ? $modifier : static::$days[$modifier]),
        );
    }

    /**
     * Go forward or backward to the next week- or weekend-day.
     *
     * @param bool $weekday
     * @param bool $forward
     *
     * @return static
     */
    private function nextOrPreviousDay($weekday = true, $forward = true)
    {
        /** @var CarbonInterface $date */
        $date = $this;
        $step = $forward ? 1 : -1;

        do {
            $date = $date->addDays($step);
        } while ($weekday ? $date->isWeekend() : $date->isWeekday());

        return $date;
    }

    /**
     * Go forward to the next weekday.
     *
     * @return static
     */
    public function nextWeekday()
    {
        return $this->nextOrPreviousDay();
    }

    /**
     * Go backward to the previous weekday.
     *
     * @return static
     */
    public function previousWeekday()
    {
        return $this->nextOrPreviousDay(true, false);
    }

    /**
     * Go forward to the next weekend day.
     *
     * @return static
     */
    public function nextWeekendDay()
    {
        return $this->nextOrPreviousDay(false);
    }

    /**
     * Go backward to the previous weekend day.
     *
     * @return static
     */
    public function previousWeekendDay()
    {
        return $this->nextOrPreviousDay(false, false);
    }

    /**
     * Modify to the previous occurrence of a given modifier such as a day of
     * the week. If no dayOfWeek is provided, modify to the previous occurrence
     * of the current day of the week. Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param string|int|null $modifier
     *
     * @return static
     */
    public function previous($modifier = null)
    {
        if ($modifier === null) {
            $modifier = $this->dayOfWeek;
        }

        return $this->change(
            'last '.(\is_string($modifier) ? $modifier : static::$days[$modifier]),
        );
    }

    /**
     * Modify to the first occurrence of a given day of the week
     * in the current month. If no dayOfWeek is provided, modify to the
     * first day of the current month.  Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int|null $dayOfWeek
     *
     * @return static
     */
    public function firstOfMonth($dayOfWeek = null)
    {
        $date = $this->startOfDay();

        if ($dayOfWeek === null) {
            return $date->day(1);
        }

        return $date->modify('first '.static::$days[$dayOfWeek].' of '.$date->rawFormat('F').' '.$date->year);
    }

    /**
     * Modify to the last occurrence of a given day of the week
     * in the current month. If no dayOfWeek is provided, modify to the
     * last day of the current month.  Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int|null $dayOfWeek
     *
     * @return static
     */
    public function lastOfMonth($dayOfWeek = null)
    {
        $date = $this->startOfDay();

        if ($dayOfWeek === null) {
            return $date->day($date->daysInMonth);
        }

        return $date->modify('last '.static::$days[$dayOfWeek].' of '.$date->rawFormat('F').' '.$date->year);
    }

    /**
     * Modify to the given occurrence of a given day of the week
     * in the current month. If the calculated occurrence is outside the scope
     * of the current month, then return false and no modifications are made.
     * Use the supplied constants to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $nth
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function nthOfMonth($nth, $dayOfWeek)
    {
        $date = $this->avoidMutation()->firstOfMonth();
        $check = $date->rawFormat('Y-m');
        $date = $date->modify('+'.$nth.' '.static::$days[$dayOfWeek]);

        return $date->rawFormat('Y-m') === $check ? $this->modify((string) $date) : false;
    }

    /**
     * Modify to the first occurrence of a given day of the week
     * in the current quarter. If no dayOfWeek is provided, modify to the
     * first day of the current quarter.  Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int|null $dayOfWeek day of the week default null
     *
     * @return static
     */
    public function firstOfQuarter($dayOfWeek = null)
    {
        return $this->setDate($this->year, $this->quarter * static::MONTHS_PER_QUARTER - 2, 1)->firstOfMonth($dayOfWeek);
    }

    /**
     * Modify to the last occurrence of a given day of the week
     * in the current quarter. If no dayOfWeek is provided, modify to the
     * last day of the current quarter.  Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int|null $dayOfWeek day of the week default null
     *
     * @return static
     */
    public function lastOfQuarter($dayOfWeek = null)
    {
        return $this->setDate($this->year, $this->quarter * static::MONTHS_PER_QUARTER, 1)->lastOfMonth($dayOfWeek);
    }

    /**
     * Modify to the given occurrence of a given day of the week
     * in the current quarter. If the calculated occurrence is outside the scope
     * of the current quarter, then return false and no modifications are made.
     * Use the supplied constants to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $nth
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function nthOfQuarter($nth, $dayOfWeek)
    {
        $date = $this->avoidMutation()->day(1)->month($this->quarter * static::MONTHS_PER_QUARTER);
        $lastMonth = $date->month;
        $year = $date->year;
        $date = $date->firstOfQuarter()->modify('+'.$nth.' '.static::$days[$dayOfWeek]);

        return ($lastMonth < $date->month || $year !== $date->year) ? false : $this->modify((string) $date);
    }

    /**
     * Modify to the first occurrence of a given day of the week
     * in the current year. If no dayOfWeek is provided, modify to the
     * first day of the current year.  Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int|null $dayOfWeek day of the week default null
     *
     * @return static
     */
    public function firstOfYear($dayOfWeek = null)
    {
        return $this->month(1)->firstOfMonth($dayOfWeek);
    }

    /**
     * Modify to the last occurrence of a given day of the week
     * in the current year. If no dayOfWeek is provided, modify to the
     * last day of the current year.  Use the supplied constants
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int|null $dayOfWeek day of the week default null
     *
     * @return static
     */
    public function lastOfYear($dayOfWeek = null)
    {
        return $this->month(static::MONTHS_PER_YEAR)->lastOfMonth($dayOfWeek);
    }

    /**
     * Modify to the given occurrence of a given day of the week
     * in the current year. If the calculated occurrence is outside the scope
     * of the current year, then return false and no modifications are made.
     * Use the supplied constants to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $nth
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function nthOfYear($nth, $dayOfWeek)
    {
        $date = $this->avoidMutation()->firstOfYear()->modify('+'.$nth.' '.static::$days[$dayOfWeek]);

        return $this->year === $date->year ? $this->modify((string) $date) : false;
    }

    /**
     * Modify the current instance to the average of a given instance (default now) and the current instance
     * (second-precision).
     *
     * @param \Carbon\Carbon|\DateTimeInterface|null $date
     *
     * @return static
     */
    public function average($date = null)
    {
        return $this->addRealMicroseconds((int) ($this->diffInMicroseconds($this->resolveCarbon($date), false) / 2));
    }

    /**
     * Get the closest date from the instance (second-precision).
     *
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date1
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date2
     *
     * @return static
     */
    public function closest($date1, $date2)
    {
        return $this->diffInMicroseconds($date1, true) < $this->diffInMicroseconds($date2, true) ? $date1 : $date2;
    }

    /**
     * Get the farthest date from the instance (second-precision).
     *
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date1
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date2
     *
     * @return static
     */
    public function farthest($date1, $date2)
    {
        return $this->diffInMicroseconds($date1, true) > $this->diffInMicroseconds($date2, true) ? $date1 : $date2;
    }

    /**
     * Get the minimum instance between a given instance (default now) and the current instance.
     *
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date
     *
     * @return static
     */
    public function min($date = null)
    {
        $date = $this->resolveCarbon($date);

        return $this->lt($date) ? $this : $date;
    }

    /**
     * Get the minimum instance between a given instance (default now) and the current instance.
     *
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date
     *
     * @see min()
     *
     * @return static
     */
    public function minimum($date = null)
    {
        return $this->min($date);
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date
     *
     * @return static
     */
    public function max($date = null)
    {
        $date = $this->resolveCarbon($date);

        return $this->gt($date) ? $this : $date;
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param \Carbon\Carbon|\DateTimeInterface|mixed $date
     *
     * @see max()
     *
     * @return static
     */
    public function maximum($date = null)
    {
        return $this->max($date);
    }

    /**
     * Calls \DateTime::modify if mutable or \DateTimeImmutable::modify else.
     *
     * @see https://php.net/manual/en/datetime.modify.php
     *
     * @return static
     */
    #[ReturnTypeWillChange]
    public function modify($modify)
    {
        return parent::modify((string) $modify)
            ?: throw new InvalidFormatException('Could not modify with: '.var_export($modify, true));
    }

    /**
     * Similar to native modify() method of DateTime but can handle more grammars.
     *
     * @example
     * ```
     * echo Carbon::now()->change('next 2pm');
     * ```
     *
     * @link https://php.net/manual/en/datetime.modify.php
     *
     * @param string $modifier
     *
     * @return static
     */
    public function change($modifier)
    {
        return $this->modify(preg_replace_callback('/^(next|previous|last)\s+(\d{1,2}(h|am|pm|:\d{1,2}(:\d{1,2})?))$/i', function ($match) {
            $match[2] = str_replace('h', ':00', $match[2]);
            $test = $this->avoidMutation()->modify($match[2]);
            $method = $match[1] === 'next' ? 'lt' : 'gt';
            $match[1] = $test->$method($this) ? $match[1].' day' : 'today';

            return $match[1].' '.$match[2];
        }, strtr(trim($modifier), [
            ' at ' => ' ',
            'just now' => 'now',
            'after tomorrow' => 'tomorrow +1 day',
            'before yesterday' => 'yesterday -1 day',
        ])));
    }
}
