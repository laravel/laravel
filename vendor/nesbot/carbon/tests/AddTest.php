<?php

/*
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;

class AddTest extends TestFixture
{
    public function testAddYearsPositive()
    {
        $this->assertSame(1976, Carbon::createFromDate(1975)->addYears(1)->year);
    }

    public function testAddYearsZero()
    {
        $this->assertSame(1975, Carbon::createFromDate(1975)->addYears(0)->year);
    }

    public function testAddYearsNegative()
    {
        $this->assertSame(1974, Carbon::createFromDate(1975)->addYears(-1)->year);
    }

    public function testAddYear()
    {
        $this->assertSame(1976, Carbon::createFromDate(1975)->addYear()->year);
    }

    public function testAddMonthsPositive()
    {
        $this->assertSame(1, Carbon::createFromDate(1975, 12)->addMonths(1)->month);
    }

    public function testAddMonthsZero()
    {
        $this->assertSame(12, Carbon::createFromDate(1975, 12)->addMonths(0)->month);
    }

    public function testAddMonthsNegative()
    {
        $this->assertSame(11, Carbon::createFromDate(1975, 12, 1)->addMonths(-1)->month);
    }

    public function testAddMonth()
    {
        $this->assertSame(1, Carbon::createFromDate(1975, 12)->addMonth()->month);
    }

    public function testAddMonthWithOverflow()
    {
        $this->assertSame(3, Carbon::createFromDate(2012, 1, 31)->addMonth()->month);
    }

    public function testAddMonthsNoOverflowPositive()
    {
        $this->assertSame('2012-02-29', Carbon::createFromDate(2012, 1, 31)->addMonthNoOverflow()->toDateString());
        $this->assertSame('2012-03-31', Carbon::createFromDate(2012, 1, 31)->addMonthsNoOverflow(2)->toDateString());
        $this->assertSame('2012-03-29', Carbon::createFromDate(2012, 2, 29)->addMonthNoOverflow()->toDateString());
        $this->assertSame('2012-02-29', Carbon::createFromDate(2011, 12, 31)->addMonthsNoOverflow(2)->toDateString());
    }

    public function testAddMonthsNoOverflowZero()
    {
        $this->assertSame(12, Carbon::createFromDate(1975, 12)->addMonths(0)->month);
    }

    public function testAddMonthsNoOverflowNegative()
    {
        $this->assertSame('2012-01-29', Carbon::createFromDate(2012, 2, 29)->addMonthsNoOverflow(-1)->toDateString());
        $this->assertSame('2012-01-31', Carbon::createFromDate(2012, 3, 31)->addMonthsNoOverflow(-2)->toDateString());
        $this->assertSame('2012-02-29', Carbon::createFromDate(2012, 3, 31)->addMonthsNoOverflow(-1)->toDateString());
        $this->assertSame('2011-12-31', Carbon::createFromDate(2012, 1, 31)->addMonthsNoOverflow(-1)->toDateString());
    }

    public function testAddDaysPositive()
    {
        $this->assertSame(1, Carbon::createFromDate(1975, 5, 31)->addDays(1)->day);
    }

    public function testAddDaysZero()
    {
        $this->assertSame(31, Carbon::createFromDate(1975, 5, 31)->addDays(0)->day);
    }

    public function testAddDaysNegative()
    {
        $this->assertSame(30, Carbon::createFromDate(1975, 5, 31)->addDays(-1)->day);
    }

    public function testAddDay()
    {
        $this->assertSame(1, Carbon::createFromDate(1975, 5, 31)->addDay()->day);
    }

    public function testAddWeekdaysPositive()
    {
        $this->assertSame(17, Carbon::createFromDate(2012, 1, 4)->addWeekdays(9)->day);
    }

    public function testAddWeekdaysZero()
    {
        $this->assertSame(4, Carbon::createFromDate(2012, 1, 4)->addWeekdays(0)->day);
    }

    public function testAddWeekdaysNegative()
    {
        $this->assertSame(18, Carbon::createFromDate(2012, 1, 31)->addWeekdays(-9)->day);
    }

    public function testAddWeekday()
    {
        $this->assertSame(9, Carbon::createFromDate(2012, 1, 6)->addWeekday()->day);
    }

    public function testAddWeeksPositive()
    {
        $this->assertSame(28, Carbon::createFromDate(1975, 5, 21)->addWeeks(1)->day);
    }

    public function testAddWeeksZero()
    {
        $this->assertSame(21, Carbon::createFromDate(1975, 5, 21)->addWeeks(0)->day);
    }

    public function testAddWeeksNegative()
    {
        $this->assertSame(14, Carbon::createFromDate(1975, 5, 21)->addWeeks(-1)->day);
    }

    public function testAddWeek()
    {
        $this->assertSame(28, Carbon::createFromDate(1975, 5, 21)->addWeek()->day);
    }

    public function testAddHoursPositive()
    {
        $this->assertSame(1, Carbon::createFromTime(0)->addHours(1)->hour);
    }

    public function testAddHoursZero()
    {
        $this->assertSame(0, Carbon::createFromTime(0)->addHours(0)->hour);
    }

    public function testAddHoursNegative()
    {
        $this->assertSame(23, Carbon::createFromTime(0)->addHours(-1)->hour);
    }

    public function testAddHour()
    {
        $this->assertSame(1, Carbon::createFromTime(0)->addHour()->hour);
    }

    public function testAddMinutesPositive()
    {
        $this->assertSame(1, Carbon::createFromTime(0, 0)->addMinutes(1)->minute);
    }

    public function testAddMinutesZero()
    {
        $this->assertSame(0, Carbon::createFromTime(0, 0)->addMinutes(0)->minute);
    }

    public function testAddMinutesNegative()
    {
        $this->assertSame(59, Carbon::createFromTime(0, 0)->addMinutes(-1)->minute);
    }

    public function testAddMinute()
    {
        $this->assertSame(1, Carbon::createFromTime(0, 0)->addMinute()->minute);
    }

    public function testAddSecondsPositive()
    {
        $this->assertSame(1, Carbon::createFromTime(0, 0, 0)->addSeconds(1)->second);
    }

    public function testAddSecondsZero()
    {
        $this->assertSame(0, Carbon::createFromTime(0, 0, 0)->addSeconds(0)->second);
    }

    public function testAddSecondsNegative()
    {
        $this->assertSame(59, Carbon::createFromTime(0, 0, 0)->addSeconds(-1)->second);
    }

    public function testAddSecond()
    {
        $this->assertSame(1, Carbon::createFromTime(0, 0, 0)->addSecond()->second);
    }
}
