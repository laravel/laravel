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

class DiffTest extends TestFixture
{
    public function testDiffInYearsPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInYears($dt->copy()->addYear()));
    }

    public function testDiffInYearsNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-1, $dt->diffInYears($dt->copy()->subYear(), false));
    }

    public function testDiffInYearsNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInYears($dt->copy()->subYear()));
    }

    public function testDiffInYearsVsDefaultNow()
    {
        $this->assertSame(1, Carbon::now()->subYear()->diffInYears());
    }

    public function testDiffInYearsEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInYears($dt->copy()->addYear()->addMonths(7)));
    }

    public function testDiffInMonthsPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(13, $dt->diffInMonths($dt->copy()->addYear()->addMonth()));
    }

    public function testDiffInMonthsNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-11, $dt->diffInMonths($dt->copy()->subYear()->addMonth(), false));
    }

    public function testDiffInMonthsNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(11, $dt->diffInMonths($dt->copy()->subYear()->addMonth()));
    }

    public function testDiffInMonthsVsDefaultNow()
    {
        $this->assertSame(12, Carbon::now()->subYear()->diffInMonths());
    }

    public function testDiffInMonthsEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInMonths($dt->copy()->addMonth()->addDays(16)));
    }

    public function testDiffInDaysPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(366, $dt->diffInDays($dt->copy()->addYear()));
    }

    public function testDiffInDaysNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-365, $dt->diffInDays($dt->copy()->subYear(), false));
    }

    public function testDiffInDaysNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(365, $dt->diffInDays($dt->copy()->subYear()));
    }

    public function testDiffInDaysVsDefaultNow()
    {
        $this->assertSame(7, Carbon::now()->subWeek()->diffInDays());
    }

    public function testDiffInDaysEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInDays($dt->copy()->addDay()->addHours(13)));
    }

    public function testDiffInDaysFilteredPositiveWithMutated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(5, $dt->diffInDaysFiltered(function (Carbon $date) {
            return $date->dayOfWeek === 1;
        }, $dt->copy()->endOfMonth()));
    }

    public function testDiffInDaysFilteredPositiveWithSecondObject()
    {
        $dt1 = Carbon::createFromDate(2000, 1, 1);
        $dt2 = Carbon::createFromDate(2000, 1, 31);

        $this->assertSame(5, $dt1->diffInDaysFiltered(function (Carbon $date) {
            return $date->dayOfWeek === Carbon::SUNDAY;
        }, $dt2));
    }

    public function testDiffInDaysFilteredNegativeNoSignWithMutated()
    {
        $dt = Carbon::createFromDate(2000, 1, 31);
        $this->assertSame(5, $dt->diffInDaysFiltered(function (Carbon $date) {
            return $date->dayOfWeek === Carbon::SUNDAY;
        }, $dt->copy()->startOfMonth()));
    }

    public function testDiffInDaysFilteredNegativeNoSignWithSecondObject()
    {
        $dt1 = Carbon::createFromDate(2000, 1, 31);
        $dt2 = Carbon::createFromDate(2000, 1, 1);

        $this->assertSame(5, $dt1->diffInDaysFiltered(function (Carbon $date) {
            return $date->dayOfWeek === Carbon::SUNDAY;
        }, $dt2));
    }

    public function testDiffInDaysFilteredNegativeWithSignWithMutated()
    {
        $dt = Carbon::createFromDate(2000, 1, 31);
        $this->assertSame(-5, $dt->diffInDaysFiltered(function (Carbon $date) {
            return $date->dayOfWeek === 1;
        }, $dt->copy()->startOfMonth(), false));
    }

    public function testDiffInDaysFilteredNegativeWithSignWithSecondObject()
    {
        $dt1 = Carbon::createFromDate(2000, 1, 31);
        $dt2 = Carbon::createFromDate(2000, 1, 1);

        $this->assertSame(-5, $dt1->diffInDaysFiltered(function (Carbon $date) {
            return $date->dayOfWeek === Carbon::SUNDAY;
        }, $dt2, false));
    }

    public function testBug188DiffWithSameDates()
    {
        $start = Carbon::create(2014, 10, 8, 15, 20, 0);
        $end = $start->copy();

        $this->assertSame(0, $start->diffInDays($end));
        $this->assertSame(0, $start->diffInWeekdays($end));
    }

    public function testBug188DiffWithDatesOnlyHoursApart()
    {
        $start = Carbon::create(2014, 10, 8, 15, 20, 0);
        $end = $start->copy();

        $this->assertSame(0, $start->diffInDays($end));
        $this->assertSame(0, $start->diffInWeekdays($end));
    }

    public function testBug188DiffWithSameDates1DayApart()
    {
        $start = Carbon::create(2014, 10, 8, 15, 20, 0);
        $end = $start->copy()->addDay();

        $this->assertSame(1, $start->diffInDays($end));
        $this->assertSame(1, $start->diffInWeekdays($end));
    }

    public function testBug188DiffWithDatesOnTheWeekend()
    {
        $start = Carbon::create(2014, 1, 1, 0, 0, 0);
        $start->next(Carbon::SATURDAY);
        $end = $start->copy()->addDay();

        $this->assertSame(1, $start->diffInDays($end));
        $this->assertSame(0, $start->diffInWeekdays($end));
    }

    public function testDiffInWeekdaysPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(21, $dt->diffInWeekdays($dt->copy()->endOfMonth()));
    }

    public function testDiffInWeekdaysNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 31);
        $this->assertSame(21, $dt->diffInWeekdays($dt->copy()->startOfMonth()));
    }

    public function testDiffInWeekdaysNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 31);
        $this->assertSame(-21, $dt->diffInWeekdays($dt->copy()->startOfMonth(), false));
    }

    public function testDiffInWeekendDaysPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(10, $dt->diffInWeekendDays($dt->copy()->endOfMonth()));
    }

    public function testDiffInWeekendDaysNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 31);
        $this->assertSame(10, $dt->diffInWeekendDays($dt->copy()->startOfMonth()));
    }

    public function testDiffInWeekendDaysNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 31);
        $this->assertSame(-10, $dt->diffInWeekendDays($dt->copy()->startOfMonth(), false));
    }

    public function testDiffInWeeksPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(52, $dt->diffInWeeks($dt->copy()->addYear()));
    }

    public function testDiffInWeeksNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-52, $dt->diffInWeeks($dt->copy()->subYear(), false));
    }

    public function testDiffInWeeksNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(52, $dt->diffInWeeks($dt->copy()->subYear()));
    }

    public function testDiffInWeeksVsDefaultNow()
    {
        $this->assertSame(1, Carbon::now()->subWeek()->diffInWeeks());
    }

    public function testDiffInWeeksEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(0, $dt->diffInWeeks($dt->copy()->addWeek()->subDay()));
    }

    public function testDiffInHoursPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(26, $dt->diffInHours($dt->copy()->addDay()->addHours(2)));
    }

    public function testDiffInHoursNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-22, $dt->diffInHours($dt->copy()->subDay()->addHours(2), false));
    }

    public function testDiffInHoursNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(22, $dt->diffInHours($dt->copy()->subDay()->addHours(2)));
    }

    public function testDiffInHoursVsDefaultNow()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 15));
        $this->assertSame(48, Carbon::now()->subDays(2)->diffInHours());
        Carbon::setTestNow();
    }

    public function testDiffInHoursEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInHours($dt->copy()->addHour()->addMinutes(31)));
    }

    public function testDiffInMinutesPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(62, $dt->diffInMinutes($dt->copy()->addHour()->addMinutes(2)));
    }

    public function testDiffInMinutesPositiveAlot()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1502, $dt->diffInMinutes($dt->copy()->addHours(25)->addMinutes(2)));
    }

    public function testDiffInMinutesNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-58, $dt->diffInMinutes($dt->copy()->subHour()->addMinutes(2), false));
    }

    public function testDiffInMinutesNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(58, $dt->diffInMinutes($dt->copy()->subHour()->addMinutes(2)));
    }

    public function testDiffInMinutesVsDefaultNow()
    {
        $this->assertSame(60, Carbon::now()->subHour()->diffInMinutes());
    }

    public function testDiffInMinutesEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInMinutes($dt->copy()->addMinute()->addSeconds(31)));
    }

    public function testDiffInSecondsPositive()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(62, $dt->diffInSeconds($dt->copy()->addMinute()->addSeconds(2)));
    }

    public function testDiffInSecondsPositiveAlot()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(7202, $dt->diffInSeconds($dt->copy()->addHours(2)->addSeconds(2)));
    }

    public function testDiffInSecondsNegativeWithSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(-58, $dt->diffInSeconds($dt->copy()->subMinute()->addSeconds(2), false));
    }

    public function testDiffInSecondsNegativeNoSign()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(58, $dt->diffInSeconds($dt->copy()->subMinute()->addSeconds(2)));
    }

    public function testDiffInSecondsVsDefaultNow()
    {
        $this->assertSame(3600, Carbon::now()->subHour()->diffInSeconds());
    }

    public function testDiffInSecondsEnsureIsTruncated()
    {
        $dt = Carbon::createFromDate(2000, 1, 1);
        $this->assertSame(1, $dt->diffInSeconds($dt->copy()->addSeconds(1.9)));
    }

    public function testDiffInSecondsWithTimezones()
    {
        $dtOttawa = Carbon::createFromDate(2000, 1, 1, 'America/Toronto');
        $dtVancouver = Carbon::createFromDate(2000, 1, 1, 'America/Vancouver');
        $this->assertSame(3 * 60 * 60, $dtOttawa->diffInSeconds($dtVancouver));
    }

    public function testDiffInSecondsWithTimezonesAndVsDefault()
    {
        $dt = Carbon::now('America/Vancouver');
        $this->assertSame(0, $dt->diffInSeconds());
    }

    public function testDiffForHumansNowAndSecond()
    {
        $d = Carbon::now();
        $this->assertSame('1 second ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndSecondWithTimezone()
    {
        $d = Carbon::now('America/Vancouver');
        $this->assertSame('1 second ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndSeconds()
    {
        $d = Carbon::now()->subSeconds(2);
        $this->assertSame('2 seconds ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyMinute()
    {
        $d = Carbon::now()->subSeconds(59);
        $this->assertSame('59 seconds ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndMinute()
    {
        $d = Carbon::now()->subMinute();
        $this->assertSame('1 minute ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndMinutes()
    {
        $d = Carbon::now()->subMinutes(2);
        $this->assertSame('2 minutes ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyHour()
    {
        $d = Carbon::now()->subMinutes(59);
        $this->assertSame('59 minutes ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndHour()
    {
        $d = Carbon::now()->subHour();
        $this->assertSame('1 hour ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndHours()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 15));
        $d = Carbon::now()->subHours(2);
        $this->assertSame('2 hours ago', $d->diffForHumans());
        Carbon::setTestNow();
    }

    public function testDiffForHumansNowAndNearlyDay()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 15));
        $d = Carbon::now()->subHours(23);
        $this->assertSame('23 hours ago', $d->diffForHumans());
        Carbon::setTestNow();
    }

    public function testDiffForHumansNowAndDay()
    {
        $d = Carbon::now()->subDay();
        $this->assertSame('1 day ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndDays()
    {
        $d = Carbon::now()->subDays(2);
        $this->assertSame('2 days ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyWeek()
    {
        $d = Carbon::now()->subDays(6);
        $this->assertSame('6 days ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndWeek()
    {
        $d = Carbon::now()->subWeek();
        $this->assertSame('1 week ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndWeeks()
    {
        $d = Carbon::now()->subWeeks(2);
        $this->assertSame('2 weeks ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyMonth()
    {
        $d = Carbon::now()->subWeeks(3);
        $this->assertSame('3 weeks ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndMonth()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->subWeeks(4);
        $this->assertSame('4 weeks ago', $d->diffForHumans());
        $d = Carbon::now()->subMonth();
        $this->assertSame('1 month ago', $d->diffForHumans());
        Carbon::setTestNow();
    }

    public function testDiffForHumansNowAndMonths()
    {
        $d = Carbon::now()->subMonths(2);
        $this->assertSame('2 months ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyYear()
    {
        $d = Carbon::now()->subMonths(11);
        $this->assertSame('11 months ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndYear()
    {
        $d = Carbon::now()->subYear();
        $this->assertSame('1 year ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndYears()
    {
        $d = Carbon::now()->subYears(2);
        $this->assertSame('2 years ago', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureSecond()
    {
        $d = Carbon::now()->addSecond();
        $this->assertSame('1 second from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureSeconds()
    {
        $d = Carbon::now()->addSeconds(2);
        $this->assertSame('2 seconds from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyFutureMinute()
    {
        $d = Carbon::now()->addSeconds(59);
        $this->assertSame('59 seconds from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureMinute()
    {
        $d = Carbon::now()->addMinute();
        $this->assertSame('1 minute from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureMinutes()
    {
        $d = Carbon::now()->addMinutes(2);
        $this->assertSame('2 minutes from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyFutureHour()
    {
        $d = Carbon::now()->addMinutes(59);
        $this->assertSame('59 minutes from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureHour()
    {
        $d = Carbon::now()->addHour();
        $this->assertSame('1 hour from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureHours()
    {
        $d = Carbon::now()->addHours(2);
        $this->assertSame('2 hours from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyFutureDay()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->addHours(23);
        $this->assertSame('23 hours from now', $d->diffForHumans());
        Carbon::setTestNow();
    }

    public function testDiffForHumansNowAndFutureDay()
    {
        $d = Carbon::now()->addDay();
        $this->assertSame('1 day from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureDays()
    {
        $d = Carbon::now()->addDays(2);
        $this->assertSame('2 days from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyFutureWeek()
    {
        $d = Carbon::now()->addDays(6);
        $this->assertSame('6 days from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureWeek()
    {
        $d = Carbon::now()->addWeek();
        $this->assertSame('1 week from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureWeeks()
    {
        $d = Carbon::now()->addWeeks(2);
        $this->assertSame('2 weeks from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndNearlyFutureMonth()
    {
        $d = Carbon::now()->addWeeks(3);
        $this->assertSame('3 weeks from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureMonth()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->addWeeks(4);
        $this->assertSame('4 weeks from now', $d->diffForHumans());
        $d = Carbon::now()->addMonth();
        $this->assertSame('1 month from now', $d->diffForHumans());
        Carbon::setTestNow();
    }

    public function testDiffForHumansNowAndFutureMonths()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->addMonths(2);
        $this->assertSame('2 months from now', $d->diffForHumans());
        Carbon::setTestNow();
    }

    public function testDiffForHumansNowAndNearlyFutureYear()
    {
        $d = Carbon::now()->addMonths(11);
        $this->assertSame('11 months from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureYear()
    {
        $d = Carbon::now()->addYear();
        $this->assertSame('1 year from now', $d->diffForHumans());
    }

    public function testDiffForHumansNowAndFutureYears()
    {
        $d = Carbon::now()->addYears(2);
        $this->assertSame('2 years from now', $d->diffForHumans());
    }

    public function testDiffForHumansOtherAndSecond()
    {
        $d = Carbon::now()->addSecond();
        $this->assertSame('1 second before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndSeconds()
    {
        $d = Carbon::now()->addSeconds(2);
        $this->assertSame('2 seconds before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyMinute()
    {
        $d = Carbon::now()->addSeconds(59);
        $this->assertSame('59 seconds before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndMinute()
    {
        $d = Carbon::now()->addMinute();
        $this->assertSame('1 minute before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndMinutes()
    {
        $d = Carbon::now()->addMinutes(2);
        $this->assertSame('2 minutes before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyHour()
    {
        $d = Carbon::now()->addMinutes(59);
        $this->assertSame('59 minutes before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndHour()
    {
        $d = Carbon::now()->addHour();
        $this->assertSame('1 hour before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndHours()
    {
        $d = Carbon::now()->addHours(2);
        $this->assertSame('2 hours before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyDay()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->addHours(23);
        $this->assertSame('23 hours before', Carbon::now()->diffForHumans($d));
        Carbon::setTestNow();
    }

    public function testDiffForHumansOtherAndDay()
    {
        $d = Carbon::now()->addDay();
        $this->assertSame('1 day before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndDays()
    {
        $d = Carbon::now()->addDays(2);
        $this->assertSame('2 days before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyWeek()
    {
        $d = Carbon::now()->addDays(6);
        $this->assertSame('6 days before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndWeek()
    {
        $d = Carbon::now()->addWeek();
        $this->assertSame('1 week before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndWeeks()
    {
        $d = Carbon::now()->addWeeks(2);
        $this->assertSame('2 weeks before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyMonth()
    {
        $d = Carbon::now()->addWeeks(3);
        $this->assertSame('3 weeks before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndMonth()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->addWeeks(4);
        $this->assertSame('4 weeks before', Carbon::now()->diffForHumans($d));
        $d = Carbon::now()->addMonth();
        $this->assertSame('1 month before', Carbon::now()->diffForHumans($d));
        Carbon::setTestNow();
    }

    public function testDiffForHumansOtherAndMonths()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->addMonths(2);
        $this->assertSame('2 months before', Carbon::now()->diffForHumans($d));
        Carbon::setTestNow();
    }

    public function testDiffForHumansOtherAndNearlyYear()
    {
        $d = Carbon::now()->addMonths(11);
        $this->assertSame('11 months before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndYear()
    {
        $d = Carbon::now()->addYear();
        $this->assertSame('1 year before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndYears()
    {
        $d = Carbon::now()->addYears(2);
        $this->assertSame('2 years before', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureSecond()
    {
        $d = Carbon::now()->subSecond();
        $this->assertSame('1 second after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureSeconds()
    {
        $d = Carbon::now()->subSeconds(2);
        $this->assertSame('2 seconds after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyFutureMinute()
    {
        $d = Carbon::now()->subSeconds(59);
        $this->assertSame('59 seconds after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureMinute()
    {
        $d = Carbon::now()->subMinute();
        $this->assertSame('1 minute after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureMinutes()
    {
        $d = Carbon::now()->subMinutes(2);
        $this->assertSame('2 minutes after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyFutureHour()
    {
        $d = Carbon::now()->subMinutes(59);
        $this->assertSame('59 minutes after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureHour()
    {
        $d = Carbon::now()->subHour();
        $this->assertSame('1 hour after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureHours()
    {
        $d = Carbon::now()->subHours(2);
        $this->assertSame('2 hours after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyFutureDay()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 15));
        $d = Carbon::now()->subHours(23);
        $this->assertSame('23 hours after', Carbon::now()->diffForHumans($d));
        Carbon::setTestNow();
    }

    public function testDiffForHumansOtherAndFutureDay()
    {
        $d = Carbon::now()->subDay();
        $this->assertSame('1 day after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureDays()
    {
        $d = Carbon::now()->subDays(2);
        $this->assertSame('2 days after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyFutureWeek()
    {
        $d = Carbon::now()->subDays(6);
        $this->assertSame('6 days after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureWeek()
    {
        $d = Carbon::now()->subWeek();
        $this->assertSame('1 week after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureWeeks()
    {
        $d = Carbon::now()->subWeeks(2);
        $this->assertSame('2 weeks after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyFutureMonth()
    {
        $d = Carbon::now()->subWeeks(3);
        $this->assertSame('3 weeks after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureMonth()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->subWeeks(4);
        $this->assertSame('4 weeks after', Carbon::now()->diffForHumans($d));
        $d = Carbon::now()->subMonth();
        $this->assertSame('1 month after', Carbon::now()->diffForHumans($d));
        Carbon::setTestNow();
    }

    public function testDiffForHumansOtherAndFutureMonths()
    {
        $d = Carbon::now()->subMonths(2);
        $this->assertSame('2 months after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndNearlyFutureYear()
    {
        $d = Carbon::now()->subMonths(11);
        $this->assertSame('11 months after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureYear()
    {
        $d = Carbon::now()->subYear();
        $this->assertSame('1 year after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansOtherAndFutureYears()
    {
        $d = Carbon::now()->subYears(2);
        $this->assertSame('2 years after', Carbon::now()->diffForHumans($d));
    }

    public function testDiffForHumansAbsoluteSeconds()
    {
        $d = Carbon::now()->subSeconds(59);
        $this->assertSame('59 seconds', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addSeconds(59);
        $this->assertSame('59 seconds', Carbon::now()->diffForHumans($d, true));
    }

    public function testDiffForHumansAbsoluteMinutes()
    {
        $d = Carbon::now()->subMinutes(30);
        $this->assertSame('30 minutes', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addMinutes(30);
        $this->assertSame('30 minutes', Carbon::now()->diffForHumans($d, true));
    }

    public function testDiffForHumansAbsoluteHours()
    {
        $d = Carbon::now()->subHours(3);
        $this->assertSame('3 hours', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addHours(3);
        $this->assertSame('3 hours', Carbon::now()->diffForHumans($d, true));
    }

    public function testDiffForHumansAbsoluteDays()
    {
        $d = Carbon::now()->subDays(2);
        $this->assertSame('2 days', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addDays(2);
        $this->assertSame('2 days', Carbon::now()->diffForHumans($d, true));
    }

    public function testDiffForHumansAbsoluteWeeks()
    {
        $d = Carbon::now()->subWeeks(2);
        $this->assertSame('2 weeks', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addWeeks(2);
        $this->assertSame('2 weeks', Carbon::now()->diffForHumans($d, true));
    }

    public function testDiffForHumansAbsoluteMonths()
    {
        Carbon::setTestNow(Carbon::create(2012, 1, 1));
        $d = Carbon::now()->subMonths(2);
        $this->assertSame('2 months', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addMonths(2);
        $this->assertSame('2 months', Carbon::now()->diffForHumans($d, true));
        Carbon::setTestNow();
    }

    public function testDiffForHumansAbsoluteYears()
    {
        $d = Carbon::now()->subYears(1);
        $this->assertSame('1 year', Carbon::now()->diffForHumans($d, true));
        $d = Carbon::now()->addYears(1);
        $this->assertSame('1 year', Carbon::now()->diffForHumans($d, true));
    }

    public function testDiffForHumansWithShorterMonthShouldStillBeAMonth()
    {
        $feb15 = Carbon::parse('2015-02-15');
        $mar15 = Carbon::parse('2015-03-15');
        $this->assertSame('1 month after', $mar15->diffForHumans($feb15));
    }
}
