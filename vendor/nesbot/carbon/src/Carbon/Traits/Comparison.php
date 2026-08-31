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

use BackedEnum;
use BadMethodCallException;
use Carbon\CarbonConverterInterface;
use Carbon\CarbonInterface;
use Carbon\Exceptions\BadComparisonUnitException;
use Carbon\FactoryImmutable;
use Carbon\Month;
use Carbon\Unit;
use Carbon\WeekDay;
use Closure;
use DateInterval;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Trait Comparison.
 *
 * Comparison utils and testers. All the following methods return booleans.
 * nowWithSameTz
 *
 * Depends on the following methods:
 *
 * @method static        resolveCarbon($date)
 * @method static        copy()
 * @method static        nowWithSameTz()
 * @method static static yesterday($timezone = null)
 * @method static static tomorrow($timezone = null)
 */
trait Comparison
{
    protected bool $endOfTime = false;

    protected bool $startOfTime = false;

    /**
     * Determines if the instance is equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->eq('2018-07-25 12:45:16'); // true
     * Carbon::parse('2018-07-25 12:45:16')->eq(Carbon::parse('2018-07-25 12:45:16')); // true
     * Carbon::parse('2018-07-25 12:45:16')->eq('2018-07-25 12:45:17'); // false
     * ```
     *
     * @see equalTo()
     */
    public function eq(DateTimeInterface|string $date): bool
    {
        return $this->equalTo($date);
    }

    /**
     * Determines if the instance is equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->equalTo('2018-07-25 12:45:16'); // true
     * Carbon::parse('2018-07-25 12:45:16')->equalTo(Carbon::parse('2018-07-25 12:45:16')); // true
     * Carbon::parse('2018-07-25 12:45:16')->equalTo('2018-07-25 12:45:17'); // false
     * ```
     */
    public function equalTo(DateTimeInterface|string $date): bool
    {
        return $this == $this->resolveCarbon($date);
    }

    /**
     * Determines if the instance is not equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->ne('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->ne(Carbon::parse('2018-07-25 12:45:16')); // false
     * Carbon::parse('2018-07-25 12:45:16')->ne('2018-07-25 12:45:17'); // true
     * ```
     *
     * @see notEqualTo()
     */
    public function ne(DateTimeInterface|string $date): bool
    {
        return $this->notEqualTo($date);
    }

    /**
     * Determines if the instance is not equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->notEqualTo('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->notEqualTo(Carbon::parse('2018-07-25 12:45:16')); // false
     * Carbon::parse('2018-07-25 12:45:16')->notEqualTo('2018-07-25 12:45:17'); // true
     * ```
     */
    public function notEqualTo(DateTimeInterface|string $date): bool
    {
        return !$this->equalTo($date);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->gt('2018-07-25 12:45:15'); // true
     * Carbon::parse('2018-07-25 12:45:16')->gt('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->gt('2018-07-25 12:45:17'); // false
     * ```
     *
     * @see greaterThan()
     */
    public function gt(DateTimeInterface|string $date): bool
    {
        return $this->greaterThan($date);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->greaterThan('2018-07-25 12:45:15'); // true
     * Carbon::parse('2018-07-25 12:45:16')->greaterThan('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->greaterThan('2018-07-25 12:45:17'); // false
     * ```
     */
    public function greaterThan(DateTimeInterface|string $date): bool
    {
        return $this > $this->resolveCarbon($date);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->isAfter('2018-07-25 12:45:15'); // true
     * Carbon::parse('2018-07-25 12:45:16')->isAfter('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->isAfter('2018-07-25 12:45:17'); // false
     * ```
     *
     * @see greaterThan()
     */
    public function isAfter(DateTimeInterface|string $date): bool
    {
        return $this->greaterThan($date);
    }

    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->gte('2018-07-25 12:45:15'); // true
     * Carbon::parse('2018-07-25 12:45:16')->gte('2018-07-25 12:45:16'); // true
     * Carbon::parse('2018-07-25 12:45:16')->gte('2018-07-25 12:45:17'); // false
     * ```
     *
     * @see greaterThanOrEqualTo()
     */
    public function gte(DateTimeInterface|string $date): bool
    {
        return $this->greaterThanOrEqualTo($date);
    }

    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->greaterThanOrEqualTo('2018-07-25 12:45:15'); // true
     * Carbon::parse('2018-07-25 12:45:16')->greaterThanOrEqualTo('2018-07-25 12:45:16'); // true
     * Carbon::parse('2018-07-25 12:45:16')->greaterThanOrEqualTo('2018-07-25 12:45:17'); // false
     * ```
     */
    public function greaterThanOrEqualTo(DateTimeInterface|string $date): bool
    {
        return $this >= $this->resolveCarbon($date);
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->lt('2018-07-25 12:45:15'); // false
     * Carbon::parse('2018-07-25 12:45:16')->lt('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->lt('2018-07-25 12:45:17'); // true
     * ```
     *
     * @see lessThan()
     */
    public function lt(DateTimeInterface|string $date): bool
    {
        return $this->lessThan($date);
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->lessThan('2018-07-25 12:45:15'); // false
     * Carbon::parse('2018-07-25 12:45:16')->lessThan('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->lessThan('2018-07-25 12:45:17'); // true
     * ```
     */
    public function lessThan(DateTimeInterface|string $date): bool
    {
        return $this < $this->resolveCarbon($date);
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->isBefore('2018-07-25 12:45:15'); // false
     * Carbon::parse('2018-07-25 12:45:16')->isBefore('2018-07-25 12:45:16'); // false
     * Carbon::parse('2018-07-25 12:45:16')->isBefore('2018-07-25 12:45:17'); // true
     * ```
     *
     * @see lessThan()
     */
    public function isBefore(DateTimeInterface|string $date): bool
    {
        return $this->lessThan($date);
    }

    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->lte('2018-07-25 12:45:15'); // false
     * Carbon::parse('2018-07-25 12:45:16')->lte('2018-07-25 12:45:16'); // true
     * Carbon::parse('2018-07-25 12:45:16')->lte('2018-07-25 12:45:17'); // true
     * ```
     *
     * @see lessThanOrEqualTo()
     */
    public function lte(DateTimeInterface|string $date): bool
    {
        return $this->lessThanOrEqualTo($date);
    }

    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25 12:45:16')->lessThanOrEqualTo('2018-07-25 12:45:15'); // false
     * Carbon::parse('2018-07-25 12:45:16')->lessThanOrEqualTo('2018-07-25 12:45:16'); // true
     * Carbon::parse('2018-07-25 12:45:16')->lessThanOrEqualTo('2018-07-25 12:45:17'); // true
     * ```
     */
    public function lessThanOrEqualTo(DateTimeInterface|string $date): bool
    {
        return $this <= $this->resolveCarbon($date);
    }

    /**
     * Determines if the instance is between two others.
     *
     * The third argument allow you to specify if bounds are included or not (true by default)
     * but for when you including/excluding bounds may produce different results in your application,
     * we recommend to use the explicit methods ->betweenIncluded() or ->betweenExcluded() instead.
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25')->between('2018-07-14', '2018-08-01'); // true
     * Carbon::parse('2018-07-25')->between('2018-08-01', '2018-08-20'); // false
     * Carbon::parse('2018-07-25')->between('2018-07-25', '2018-08-01'); // true
     * Carbon::parse('2018-07-25')->between('2018-07-25', '2018-08-01', false); // false
     * ```
     *
     * @param bool $equal Indicates if an equal to comparison should be done
     */
    public function between(DateTimeInterface|string $date1, DateTimeInterface|string $date2, bool $equal = true): bool
    {
        $date1 = $this->resolveCarbon($date1);
        $date2 = $this->resolveCarbon($date2);

        if ($date1->greaterThan($date2)) {
            [$date1, $date2] = [$date2, $date1];
        }

        if ($equal) {
            return $this >= $date1 && $this <= $date2;
        }

        return $this > $date1 && $this < $date2;
    }

    /**
     * Determines if the instance is between two others, bounds included.
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25')->betweenIncluded('2018-07-14', '2018-08-01'); // true
     * Carbon::parse('2018-07-25')->betweenIncluded('2018-08-01', '2018-08-20'); // false
     * Carbon::parse('2018-07-25')->betweenIncluded('2018-07-25', '2018-08-01'); // true
     * ```
     */
    public function betweenIncluded(DateTimeInterface|string $date1, DateTimeInterface|string $date2): bool
    {
        return $this->between($date1, $date2, true);
    }

    /**
     * Determines if the instance is between two others, bounds excluded.
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25')->betweenExcluded('2018-07-14', '2018-08-01'); // true
     * Carbon::parse('2018-07-25')->betweenExcluded('2018-08-01', '2018-08-20'); // false
     * Carbon::parse('2018-07-25')->betweenExcluded('2018-07-25', '2018-08-01'); // false
     * ```
     */
    public function betweenExcluded(DateTimeInterface|string $date1, DateTimeInterface|string $date2): bool
    {
        return $this->between($date1, $date2, false);
    }

    /**
     * Determines if the instance is between two others
     *
     * @example
     * ```
     * Carbon::parse('2018-07-25')->isBetween('2018-07-14', '2018-08-01'); // true
     * Carbon::parse('2018-07-25')->isBetween('2018-08-01', '2018-08-20'); // false
     * Carbon::parse('2018-07-25')->isBetween('2018-07-25', '2018-08-01'); // true
     * Carbon::parse('2018-07-25')->isBetween('2018-07-25', '2018-08-01', false); // false
     * ```
     *
     * @param bool $equal Indicates if an equal to comparison should be done
     */
    public function isBetween(DateTimeInterface|string $date1, DateTimeInterface|string $date2, bool $equal = true): bool
    {
        return $this->between($date1, $date2, $equal);
    }

    /**
     * Determines if the instance is a weekday.
     *
     * @example
     * ```
     * Carbon::parse('2019-07-14')->isWeekday(); // false
     * Carbon::parse('2019-07-15')->isWeekday(); // true
     * ```
     */
    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }

    /**
     * Determines if the instance is a weekend day.
     *
     * @example
     * ```
     * Carbon::parse('2019-07-14')->isWeekend(); // true
     * Carbon::parse('2019-07-15')->isWeekend(); // false
     * ```
     */
    public function isWeekend(): bool
    {
        return \in_array(
            $this->dayOfWeek,
            $this->transmitFactory(static fn () => static::getWeekendDays()),
            true,
        );
    }

    /**
     * Determines if the instance is yesterday.
     *
     * @example
     * ```
     * Carbon::yesterday()->isYesterday(); // true
     * Carbon::tomorrow()->isYesterday(); // false
     * ```
     */
    public function isYesterday(): bool
    {
        return $this->toDateString() === $this->transmitFactory(
            fn () => static::yesterday($this->getTimezone())->toDateString(),
        );
    }

    /**
     * Determines if the instance is today.
     *
     * @example
     * ```
     * Carbon::today()->isToday(); // true
     * Carbon::tomorrow()->isToday(); // false
     * ```
     */
    public function isToday(): bool
    {
        return $this->toDateString() === $this->nowWithSameTz()->toDateString();
    }

    /**
     * Determines if the instance is tomorrow.
     *
     * @example
     * ```
     * Carbon::tomorrow()->isTomorrow(); // true
     * Carbon::yesterday()->isTomorrow(); // false
     * ```
     */
    public function isTomorrow(): bool
    {
        return $this->toDateString() === $this->transmitFactory(
            fn () => static::tomorrow($this->getTimezone())->toDateString(),
        );
    }

    /**
     * Determines if the instance is in the future, ie. greater (after) than now.
     *
     * @example
     * ```
     * Carbon::now()->addHours(5)->isFuture(); // true
     * Carbon::now()->subHours(5)->isFuture(); // false
     * ```
     */
    public function isFuture(): bool
    {
        return $this->greaterThan($this->nowWithSameTz());
    }

    /**
     * Determines if the instance is in the past, ie. less (before) than now.
     *
     * @example
     * ```
     * Carbon::now()->subHours(5)->isPast(); // true
     * Carbon::now()->addHours(5)->isPast(); // false
     * ```
     */
    public function isPast(): bool
    {
        return $this->lessThan($this->nowWithSameTz());
    }

    /**
     * Determines if the instance is now or in the future, ie. greater (after) than or equal to now.
     *
     * @example
     * ```
     * Carbon::now()->isNowOrFuture(); // true
     * Carbon::now()->addHours(5)->isNowOrFuture(); // true
     * Carbon::now()->subHours(5)->isNowOrFuture(); // false
     * ```
     */
    public function isNowOrFuture(): bool
    {
        return $this->greaterThanOrEqualTo($this->nowWithSameTz());
    }

    /**
     * Determines if the instance is now or in the past, ie. less (before) than or equal to now.
     *
     * @example
     * ```
     * Carbon::now()->isNowOrPast(); // true
     * Carbon::now()->subHours(5)->isNowOrPast(); // true
     * Carbon::now()->addHours(5)->isNowOrPast(); // false
     * ```
     */
    public function isNowOrPast(): bool
    {
        return $this->lessThanOrEqualTo($this->nowWithSameTz());
    }

    /**
     * Determines if the instance is a leap year.
     *
     * @example
     * ```
     * Carbon::parse('2020-01-01')->isLeapYear(); // true
     * Carbon::parse('2019-01-01')->isLeapYear(); // false
     * ```
     */
    public function isLeapYear(): bool
    {
        return $this->rawFormat('L') === '1';
    }

    /**
     * Determines if the instance is a long year (using calendar year).
     *
     * ⚠️ This method completely ignores month and day to use the numeric year number,
     * it's not correct if the exact date matters. For instance as `2019-12-30` is already
     * in the first week of the 2020 year, if you want to know from this date if ISO week
     * year 2020 is a long year, use `isLongIsoYear` instead.
     *
     * @example
     * ```
     * Carbon::create(2015)->isLongYear(); // true
     * Carbon::create(2016)->isLongYear(); // false
     * ```
     *
     * @see https://en.wikipedia.org/wiki/ISO_8601#Week_dates
     */
    public function isLongYear(): bool
    {
        return static::create($this->year, 12, 28, 0, 0, 0, $this->tz)->weekOfYear === static::WEEKS_PER_YEAR + 1;
    }

    /**
     * Determines if the instance is a long year (using ISO 8601 year).
     *
     * @example
     * ```
     * Carbon::parse('2015-01-01')->isLongIsoYear(); // true
     * Carbon::parse('2016-01-01')->isLongIsoYear(); // true
     * Carbon::parse('2016-01-03')->isLongIsoYear(); // false
     * Carbon::parse('2019-12-29')->isLongIsoYear(); // false
     * Carbon::parse('2019-12-30')->isLongIsoYear(); // true
     * ```
     *
     * @see https://en.wikipedia.org/wiki/ISO_8601#Week_dates
     */
    public function isLongIsoYear(): bool
    {
        return static::create($this->isoWeekYear, 12, 28, 0, 0, 0, $this->tz)->weekOfYear === 53;
    }

    /**
     * Compares the formatted values of the two dates.
     *
     * @example
     * ```
     * Carbon::parse('2019-06-13')->isSameAs('Y-d', Carbon::parse('2019-12-13')); // true
     * Carbon::parse('2019-06-13')->isSameAs('Y-d', Carbon::parse('2019-06-14')); // false
     * ```
     *
     * @param string                   $format date formats to compare.
     * @param DateTimeInterface|string $date   instance to compare with or null to use current day.
     */
    public function isSameAs(string $format, DateTimeInterface|string $date): bool
    {
        return $this->rawFormat($format) === $this->resolveCarbon($date)->rawFormat($format);
    }

    /**
     * Determines if the instance is in the current unit given.
     *
     * @example
     * ```
     * Carbon::parse('2019-01-13')->isSameUnit('year', Carbon::parse('2019-12-25')); // true
     * Carbon::parse('2018-12-13')->isSameUnit('year', Carbon::parse('2019-12-25')); // false
     * ```
     *
     * @param string                   $unit singular unit string
     * @param DateTimeInterface|string $date instance to compare with or null to use current day.
     *
     * @throws BadComparisonUnitException
     *
     * @return bool
     */
    public function isSameUnit(string $unit, DateTimeInterface|string $date): bool
    {
        if ($unit === /* @call isSameUnit */ 'quarter') {
            $other = $this->resolveCarbon($date);

            return $other->year === $this->year && $other->quarter === $this->quarter;
        }

        $units = [
            // @call isSameUnit
            'year' => 'Y',
            // @call isSameUnit
            'month' => 'Y-n',
            // @call isSameUnit
            'week' => 'o-W',
            // @call isSameUnit
            'day' => 'Y-m-d',
            // @call isSameUnit
            'hour' => 'Y-m-d H',
            // @call isSameUnit
            'minute' => 'Y-m-d H:i',
            // @call isSameUnit
            'second' => 'Y-m-d H:i:s',
            // @call isSameUnit
            'milli' => 'Y-m-d H:i:s.v',
            // @call isSameUnit
            'millisecond' => 'Y-m-d H:i:s.v',
            // @call isSameUnit
            'micro' => 'Y-m-d H:i:s.u',
            // @call isSameUnit
            'microsecond' => 'Y-m-d H:i:s.u',
        ];

        if (isset($units[$unit])) {
            return $this->isSameAs($units[$unit], $date);
        }

        if (isset($this->$unit)) {
            return $this->resolveCarbon($date)->$unit === $this->$unit;
        }

        if ($this->isLocalStrictModeEnabled()) {
            throw new BadComparisonUnitException($unit);
        }

        return false;
    }

    /**
     * Determines if the instance is in the current unit given.
     *
     * @example
     * ```
     * Carbon::now()->isCurrentUnit('hour'); // true
     * Carbon::now()->subHours(2)->isCurrentUnit('hour'); // false
     * ```
     *
     * @param string $unit The unit to test.
     *
     * @throws BadMethodCallException
     */
    public function isCurrentUnit(string $unit): bool
    {
        return $this->{'isSame'.ucfirst($unit)}('now');
    }

    /**
     * Checks if the passed in date is in the same quarter as the instance quarter (and year if needed).
     *
     * @example
     * ```
     * Carbon::parse('2019-01-12')->isSameQuarter(Carbon::parse('2019-03-01')); // true
     * Carbon::parse('2019-01-12')->isSameQuarter(Carbon::parse('2019-04-01')); // false
     * Carbon::parse('2019-01-12')->isSameQuarter(Carbon::parse('2018-03-01')); // false
     * Carbon::parse('2019-01-12')->isSameQuarter(Carbon::parse('2018-03-01'), false); // true
     * ```
     *
     * @param DateTimeInterface|string $date       The instance to compare with or null to use current day.
     * @param bool                     $ofSameYear Check if it is the same month in the same year.
     *
     * @return bool
     */
    public function isSameQuarter(DateTimeInterface|string $date, bool $ofSameYear = true): bool
    {
        $date = $this->resolveCarbon($date);

        return $this->quarter === $date->quarter && (!$ofSameYear || $this->isSameYear($date));
    }

    /**
     * Checks if the passed in date is in the same month as the instance´s month.
     *
     * @example
     * ```
     * Carbon::parse('2019-01-12')->isSameMonth(Carbon::parse('2019-01-01')); // true
     * Carbon::parse('2019-01-12')->isSameMonth(Carbon::parse('2019-02-01')); // false
     * Carbon::parse('2019-01-12')->isSameMonth(Carbon::parse('2018-01-01')); // false
     * Carbon::parse('2019-01-12')->isSameMonth(Carbon::parse('2018-01-01'), false); // true
     * ```
     *
     * @param DateTimeInterface|string $date       The instance to compare with or null to use the current date.
     * @param bool                     $ofSameYear Check if it is the same month in the same year.
     *
     * @return bool
     */
    public function isSameMonth(DateTimeInterface|string $date, bool $ofSameYear = true): bool
    {
        return $this->isSameAs($ofSameYear ? 'Y-m' : 'm', $date);
    }

    /**
     * Checks if this day is a specific day of the week.
     *
     * @example
     * ```
     * Carbon::parse('2019-07-17')->isDayOfWeek(Carbon::WEDNESDAY); // true
     * Carbon::parse('2019-07-17')->isDayOfWeek(Carbon::FRIDAY); // false
     * Carbon::parse('2019-07-17')->isDayOfWeek('Wednesday'); // true
     * Carbon::parse('2019-07-17')->isDayOfWeek('Friday'); // false
     * ```
     *
     * @param int|string $dayOfWeek
     *
     * @return bool
     */
    public function isDayOfWeek($dayOfWeek): bool
    {
        if (\is_string($dayOfWeek) && \defined($constant = static::class.'::'.strtoupper($dayOfWeek))) {
            $dayOfWeek = \constant($constant);
        }

        return $this->dayOfWeek === $dayOfWeek;
    }

    /**
     * Check if its the birthday. Compares the date/month values of the two dates.
     *
     * @example
     * ```
     * Carbon::now()->subYears(5)->isBirthday(); // true
     * Carbon::now()->subYears(5)->subDay()->isBirthday(); // false
     * Carbon::parse('2019-06-05')->isBirthday(Carbon::parse('2001-06-05')); // true
     * Carbon::parse('2019-06-05')->isBirthday(Carbon::parse('2001-06-06')); // false
     * ```
     *
     * @param DateTimeInterface|string|null $date The instance to compare with or null to use current day.
     *
     * @return bool
     */
    public function isBirthday(DateTimeInterface|string|null $date = null): bool
    {
        return $this->isSameAs('md', $date ?? 'now');
    }

    /**
     * Check if today is the last day of the Month
     *
     * @example
     * ```
     * Carbon::parse('2019-02-28')->isLastOfMonth(); // true
     * Carbon::parse('2019-03-28')->isLastOfMonth(); // false
     * Carbon::parse('2019-03-30')->isLastOfMonth(); // false
     * Carbon::parse('2019-03-31')->isLastOfMonth(); // true
     * Carbon::parse('2019-04-30')->isLastOfMonth(); // true
     * ```
     */
    public function isLastOfMonth(): bool
    {
        return $this->day === $this->daysInMonth;
    }

    /**
     * Check if the instance is start of a given unit (tolerating a given interval).
     *
     * @example
     * ```
     * // Check if a date-time is the first 15 minutes of the hour it's in
     * Carbon::parse('2019-02-28 20:13:00')->isStartOfUnit(Unit::Hour, '15 minutes'); // true
     * ```
     */
    public function isStartOfUnit(
        Unit $unit,
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
        mixed ...$params,
    ): bool {
        $interval ??= match ($unit) {
            Unit::Day, Unit::Hour, Unit::Minute, Unit::Second, Unit::Millisecond, Unit::Microsecond => Unit::Microsecond,
            default => Unit::Day,
        };

        $startOfUnit = $this->avoidMutation()->startOf($unit, ...$params);
        $startOfUnitDateTime = $startOfUnit->rawFormat('Y-m-d H:i:s.u');
        $maximumDateTime = $startOfUnit
            ->add($interval instanceof Unit ? '1  '.$interval->value : $interval)
            ->rawFormat('Y-m-d H:i:s.u');

        if ($maximumDateTime < $startOfUnitDateTime) {
            return false;
        }

        return $this->rawFormat('Y-m-d H:i:s.u') < $maximumDateTime;
    }

    /**
     * Check if the instance is end of a given unit (tolerating a given interval).
     *
     * @example
     * ```
     * // Check if a date-time is the last 15 minutes of the hour it's in
     * Carbon::parse('2019-02-28 20:13:00')->isEndOfUnit(Unit::Hour, '15 minutes'); // false
     * ```
     */
    public function isEndOfUnit(
        Unit $unit,
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
        mixed ...$params,
    ): bool {
        $interval ??= match ($unit) {
            Unit::Day, Unit::Hour, Unit::Minute, Unit::Second, Unit::Millisecond, Unit::Microsecond => Unit::Microsecond,
            default => Unit::Day,
        };

        $endOfUnit = $this->avoidMutation()->endOf($unit, ...$params);
        $endOfUnitDateTime = $endOfUnit->rawFormat('Y-m-d H:i:s.u');
        $minimumDateTime = $endOfUnit
            ->sub($interval instanceof Unit ? '1  '.$interval->value : $interval)
            ->rawFormat('Y-m-d H:i:s.u');

        if ($minimumDateTime > $endOfUnitDateTime) {
            return false;
        }

        return $this->rawFormat('Y-m-d H:i:s.u') > $minimumDateTime;
    }

    /**
     * Determines if the instance is start of millisecond (first microsecond by default but interval can be customized).
     */
    public function isStartOfMillisecond(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Millisecond, $interval);
    }

    /**
     * Determines if the instance is end of millisecond (last microsecond by default but interval can be customized).
     */
    public function isEndOfMillisecond(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Millisecond, $interval);
    }

    /**
     * Determines if the instance is start of second (first microsecond by default but interval can be customized).
     */
    public function isStartOfSecond(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Second, $interval);
    }

    /**
     * Determines if the instance is end of second (last microsecond by default but interval can be customized).
     */
    public function isEndOfSecond(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Second, $interval);
    }

    /**
     * Determines if the instance is start of minute (first microsecond by default but interval can be customized).
     */
    public function isStartOfMinute(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Minute, $interval);
    }

    /**
     * Determines if the instance is end of minute (last microsecond by default but interval can be customized).
     */
    public function isEndOfMinute(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Minute, $interval);
    }

    /**
     * Determines if the instance is start of hour (first microsecond by default but interval can be customized).
     */
    public function isStartOfHour(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Hour, $interval);
    }

    /**
     * Determines if the instance is end of hour (last microsecond by default but interval can be customized).
     */
    public function isEndOfHour(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Hour, $interval);
    }

    /**
     * Check if the instance is start of day / midnight.
     *
     * @example
     * ```
     * Carbon::parse('2019-02-28 00:00:00')->isStartOfDay(); // true
     * Carbon::parse('2019-02-28 00:00:00.999999')->isStartOfDay(); // true
     * Carbon::parse('2019-02-28 00:00:01')->isStartOfDay(); // false
     * Carbon::parse('2019-02-28 00:00:00.000000')->isStartOfDay(true); // true
     * Carbon::parse('2019-02-28 00:00:00.000012')->isStartOfDay(true); // false
     * ```
     *
     * @param bool                                                           $checkMicroseconds check time at microseconds precision
     * @param Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval          if an interval is specified it will be used as precision
     *                                                                                          for instance with "15 minutes", it checks if current date-time
     *                                                                                          is in the last 15 minutes of the day, with Unit::Hour, it
     *                                                                                          checks if it's in the last hour of the day.
     */
    public function isStartOfDay(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|bool $checkMicroseconds = false,
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        if ($checkMicroseconds === true) {
            @trigger_error(
                "Since 3.8.0, it's deprecated to use \$checkMicroseconds.\n".
                "It will be removed in 4.0.0.\n".
                "Instead, you should use either isStartOfDay(interval: Unit::Microsecond) or isStartOfDay(interval: Unit::Second)\n".
                'And you can now use any custom interval as precision, such as isStartOfDay(interval: "15 minutes")',
                \E_USER_DEPRECATED,
            );
        }

        if ($interval === null && !\is_bool($checkMicroseconds)) {
            $interval = $checkMicroseconds;
        }

        if ($interval !== null) {
            if ($interval instanceof Unit) {
                $interval = '1  '.$interval->value;
            }

            $date = $this->rawFormat('Y-m-d');
            $time = $this->rawFormat('H:i:s.u');
            $maximum = $this->avoidMutation()->startOfDay()->add($interval);
            $maximumDate = $maximum->rawFormat('Y-m-d');

            if ($date === $maximumDate) {
                return $time < $maximum->rawFormat('H:i:s.u');
            }

            return $maximumDate > $date;
        }

        /* @var CarbonInterface $this */
        return $checkMicroseconds
            ? $this->rawFormat('H:i:s.u') === '00:00:00.000000'
            : $this->rawFormat('H:i:s') === '00:00:00';
    }

    /**
     * Check if the instance is end of day.
     *
     * @example
     * ```
     * Carbon::parse('2019-02-28 23:59:59.999999')->isEndOfDay(); // true
     * Carbon::parse('2019-02-28 23:59:59.123456')->isEndOfDay(); // true
     * Carbon::parse('2019-02-28 23:59:59')->isEndOfDay(); // true
     * Carbon::parse('2019-02-28 23:59:58.999999')->isEndOfDay(); // false
     * Carbon::parse('2019-02-28 23:59:59.999999')->isEndOfDay(true); // true
     * Carbon::parse('2019-02-28 23:59:59.123456')->isEndOfDay(true); // false
     * Carbon::parse('2019-02-28 23:59:59')->isEndOfDay(true); // false
     * ```
     *
     * @param bool                                                           $checkMicroseconds check time at microseconds precision
     * @param Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval          if an interval is specified it will be used as precision
     *                                                                                          for instance with "15 minutes", it checks if current date-time
     *                                                                                          is in the last 15 minutes of the day, with Unit::Hour, it
     *                                                                                          checks if it's in the last hour of the day.
     */
    public function isEndOfDay(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|bool $checkMicroseconds = false,
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        if ($checkMicroseconds === true) {
            @trigger_error(
                "Since 3.8.0, it's deprecated to use \$checkMicroseconds.\n".
                "It will be removed in 4.0.0.\n".
                "Instead, you should use either isEndOfDay(interval: Unit::Microsecond) or isEndOfDay(interval: Unit::Second)\n".
                'And you can now use any custom interval as precision, such as isEndOfDay(interval: "15 minutes")',
                \E_USER_DEPRECATED,
            );
        }

        if ($interval === null && !\is_bool($checkMicroseconds)) {
            $interval = $checkMicroseconds;
        }

        if ($interval !== null) {
            $date = $this->rawFormat('Y-m-d');
            $time = $this->rawFormat('H:i:s.u');
            $minimum = $this->avoidMutation()
                ->endOfDay()
                ->sub($interval instanceof Unit ? '1  '.$interval->value : $interval);
            $minimumDate = $minimum->rawFormat('Y-m-d');

            if ($date === $minimumDate) {
                return $time > $minimum->rawFormat('H:i:s.u');
            }

            return $minimumDate < $date;
        }

        /* @var CarbonInterface $this */
        return $checkMicroseconds
            ? $this->rawFormat('H:i:s.u') === '23:59:59.999999'
            : $this->rawFormat('H:i:s') === '23:59:59';
    }

    /**
     * Determines if the instance is start of week (first day by default but interval can be customized).
     *
     * @example
     * ```
     * Carbon::parse('2024-08-31')->startOfWeek()->isStartOfWeek(); // true
     * Carbon::parse('2024-08-31')->isStartOfWeek(); // false
     * ```
     */
    public function isStartOfWeek(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
        WeekDay|int|null $weekStartsAt = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Week, $interval, $weekStartsAt);
    }

    /**
     * Determines if the instance is end of week (last day by default but interval can be customized).
     *
     * @example
     * ```
     * Carbon::parse('2024-08-31')->endOfWeek()->isEndOfWeek(); // true
     * Carbon::parse('2024-08-31')->isEndOfWeek(); // false
     * ```
     */
    public function isEndOfWeek(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
        WeekDay|int|null $weekEndsAt = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Week, $interval, $weekEndsAt);
    }

    /**
     * Determines if the instance is start of month (first day by default but interval can be customized).
     */
    public function isStartOfMonth(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Month, $interval);
    }

    /**
     * Determines if the instance is end of month (last day by default but interval can be customized).
     */
    public function isEndOfMonth(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Month, $interval);
    }

    /**
     * Determines if the instance is start of quarter (first day by default but interval can be customized).
     */
    public function isStartOfQuarter(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Quarter, $interval);
    }

    /**
     * Determines if the instance is end of quarter (last day by default but interval can be customized).
     */
    public function isEndOfQuarter(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Quarter, $interval);
    }

    /**
     * Determines if the instance is start of year (first day by default but interval can be customized).
     */
    public function isStartOfYear(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Year, $interval);
    }

    /**
     * Determines if the instance is end of year (last day by default but interval can be customized).
     */
    public function isEndOfYear(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Year, $interval);
    }

    /**
     * Determines if the instance is start of decade (first day by default but interval can be customized).
     */
    public function isStartOfDecade(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Decade, $interval);
    }

    /**
     * Determines if the instance is end of decade (last day by default but interval can be customized).
     */
    public function isEndOfDecade(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Decade, $interval);
    }

    /**
     * Determines if the instance is start of century (first day by default but interval can be customized).
     */
    public function isStartOfCentury(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Century, $interval);
    }

    /**
     * Determines if the instance is end of century (last day by default but interval can be customized).
     */
    public function isEndOfCentury(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Century, $interval);
    }

    /**
     * Determines if the instance is start of millennium (first day by default but interval can be customized).
     */
    public function isStartOfMillennium(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isStartOfUnit(Unit::Millennium, $interval);
    }

    /**
     * Determines if the instance is end of millennium (last day by default but interval can be customized).
     */
    public function isEndOfMillennium(
        Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
    ): bool {
        return $this->isEndOfUnit(Unit::Millennium, $interval);
    }

    /**
     * Check if the instance is start of day / midnight.
     *
     * @example
     * ```
     * Carbon::parse('2019-02-28 00:00:00')->isMidnight(); // true
     * Carbon::parse('2019-02-28 00:00:00.999999')->isMidnight(); // true
     * Carbon::parse('2019-02-28 00:00:01')->isMidnight(); // false
     * ```
     */
    public function isMidnight(): bool
    {
        return $this->isStartOfDay();
    }

    /**
     * Check if the instance is midday.
     *
     * @example
     * ```
     * Carbon::parse('2019-02-28 11:59:59.999999')->isMidday(); // false
     * Carbon::parse('2019-02-28 12:00:00')->isMidday(); // true
     * Carbon::parse('2019-02-28 12:00:00.999999')->isMidday(); // true
     * Carbon::parse('2019-02-28 12:00:01')->isMidday(); // false
     * ```
     */
    public function isMidday(): bool
    {
        /* @var CarbonInterface $this */
        return $this->rawFormat('G:i:s') === static::$midDayAt.':00:00';
    }

    /**
     * Checks if the (date)time string is in a given format.
     *
     * @example
     * ```
     * Carbon::hasFormat('11:12:45', 'h:i:s'); // true
     * Carbon::hasFormat('13:12:45', 'h:i:s'); // false
     * ```
     */
    public static function hasFormat(string $date, string $format): bool
    {
        return FactoryImmutable::getInstance()->hasFormat($date, $format);
    }

    /**
     * Checks if the (date)time string is in a given format.
     *
     * @example
     * ```
     * Carbon::hasFormatWithModifiers('31/08/2015', 'd#m#Y'); // true
     * Carbon::hasFormatWithModifiers('31/08/2015', 'm#d#Y'); // false
     * ```
     *
     * @param string $date
     * @param string $format
     *
     * @return bool
     */
    public static function hasFormatWithModifiers(?string $date, string $format): bool
    {
        return FactoryImmutable::getInstance()->hasFormatWithModifiers($date, $format);
    }

    /**
     * Checks if the (date)time string is in a given format and valid to create a
     * new instance.
     *
     * @example
     * ```
     * Carbon::canBeCreatedFromFormat('11:12:45', 'h:i:s'); // true
     * Carbon::canBeCreatedFromFormat('13:12:45', 'h:i:s'); // false
     * ```
     */
    public static function canBeCreatedFromFormat(?string $date, string $format): bool
    {
        if ($date === null) {
            return false;
        }

        try {
            // Try to create a DateTime object. Throws an InvalidArgumentException if the provided time string
            // doesn't match the format in any way.
            if (!static::rawCreateFromFormat($format, $date)) {
                return false;
            }
        } catch (InvalidArgumentException) {
            return false;
        }

        return static::hasFormatWithModifiers($date, $format);
    }

    /**
     * Returns true if the current date matches the given string.
     *
     * @example
     * ```
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('2019')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('2018')); // false
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('2019-06')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('06-02')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('2019-06-02')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('Sunday')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('June')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('12:23')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('12:23:45')); // true
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('12:23:00')); // false
     * var_dump(Carbon::parse('2019-06-02 12:23:45')->is('12h')); // true
     * var_dump(Carbon::parse('2019-06-02 15:23:45')->is('3pm')); // true
     * var_dump(Carbon::parse('2019-06-02 15:23:45')->is('3am')); // false
     * ```
     *
     * @param string $tester day name, month name, hour, date, etc. as string
     */
    public function is(WeekDay|Month|string $tester): bool
    {
        if ($tester instanceof BackedEnum) {
            $tester = $tester->name;
        }

        $tester = trim($tester);

        if (preg_match('/^\d+$/', $tester)) {
            return $this->year === (int) $tester;
        }

        if (preg_match('/^(?:Jan|January|Feb|February|Mar|March|Apr|April|May|Jun|June|Jul|July|Aug|August|Sep|September|Oct|October|Nov|November|Dec|December)$/i', $tester)) {
            return $this->isSameMonth(
                $this->transmitFactory(static fn () => static::parse("$tester 1st")),
                false,
            );
        }

        if (preg_match('/^\d{3,}-\d{1,2}$/', $tester)) {
            return $this->isSameMonth(
                $this->transmitFactory(static fn () => static::parse($tester)),
            );
        }

        if (preg_match('/^(\d{1,2})-(\d{1,2})$/', $tester, $match)) {
            return $this->month === (int) $match[1] && $this->day === (int) $match[2];
        }

        $modifier = preg_replace('/(\d)h$/i', '$1:00', $tester);

        /* @var CarbonInterface $max */
        $median = $this->transmitFactory(static fn () => static::parse('5555-06-15 12:30:30.555555'))
            ->modify($modifier);
        $current = $this->avoidMutation();
        /* @var CarbonInterface $other */
        $other = $this->avoidMutation()->modify($modifier);

        if ($current->eq($other)) {
            return true;
        }

        if (preg_match('/\d:\d{1,2}:\d{1,2}$/', $tester)) {
            return $current->startOfSecond()->eq($other);
        }

        if (preg_match('/\d:\d{1,2}$/', $tester)) {
            return $current->startOfMinute()->eq($other);
        }

        if (preg_match('/\d(?:h|am|pm)$/', $tester)) {
            return $current->startOfHour()->eq($other);
        }

        if (preg_match(
            '/^(?:january|february|march|april|may|june|july|august|september|october|november|december)(?:\s+\d+)?$/i',
            $tester,
        )) {
            return $current->startOfMonth()->eq($other->startOfMonth());
        }

        $units = [
            'month' => [1, 'year'],
            'day' => [1, 'month'],
            'hour' => [0, 'day'],
            'minute' => [0, 'hour'],
            'second' => [0, 'minute'],
            'microsecond' => [0, 'second'],
        ];

        foreach ($units as $unit => [$minimum, $startUnit]) {
            if ($minimum === $median->$unit) {
                $current = $current->startOf($startUnit);

                break;
            }
        }

        return $current->eq($other);
    }

    /**
     * Returns true if the date was created using CarbonImmutable::startOfTime()
     *
     * @return bool
     */
    public function isStartOfTime(): bool
    {
        return $this->startOfTime ?? false;
    }

    /**
     * Returns true if the date was created using CarbonImmutable::endOfTime()
     *
     * @return bool
     */
    public function isEndOfTime(): bool
    {
        return $this->endOfTime ?? false;
    }
}
