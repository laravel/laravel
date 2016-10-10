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

class GettersTest extends TestFixture
{
    public function testGettersThrowExceptionOnUnknownGetter()
    {
        $this->setExpectedException('InvalidArgumentException');
        Carbon::create(1234, 5, 6, 7, 8, 9)->sdfsdfss;
    }

    public function testYearGetter()
    {
        $d = Carbon::create(1234, 5, 6, 7, 8, 9);
        $this->assertSame(1234, $d->year);
    }
    
    public function testYearIsoGetter()
    {
        $d = Carbon::createFromDate(2012, 12, 31);
        $this->assertSame(2013, $d->yearIso);
    }

    public function testMonthGetter()
    {
        $d = Carbon::create(1234, 5, 6, 7, 8, 9);
        $this->assertSame(5, $d->month);
    }

    public function testDayGetter()
    {
        $d = Carbon::create(1234, 5, 6, 7, 8, 9);
        $this->assertSame(6, $d->day);
    }

    public function testHourGetter()
    {
        $d = Carbon::create(1234, 5, 6, 7, 8, 9);
        $this->assertSame(7, $d->hour);
    }

    public function testMinuteGetter()
    {
        $d = Carbon::create(1234, 5, 6, 7, 8, 9);
        $this->assertSame(8, $d->minute);
    }

    public function testSecondGetter()
    {
        $d = Carbon::create(1234, 5, 6, 7, 8, 9);
        $this->assertSame(9, $d->second);
    }

    public function testMicroGetter()
    {
        $micro = 345678;
        $d = Carbon::parse('2014-01-05 12:34:11.'.$micro);
        $this->assertSame($micro, $d->micro);
    }

    public function testMicroGetterWithDefaultNow()
    {
        $d = Carbon::now();
        $this->assertSame(0, $d->micro);
    }

    public function testDayOfWeeGetter()
    {
        $d = Carbon::create(2012, 5, 7, 7, 8, 9);
        $this->assertSame(Carbon::MONDAY, $d->dayOfWeek);
    }

    public function testDayOfYearGetter()
    {
        $d = Carbon::createFromDate(2012, 5, 7);
        $this->assertSame(127, $d->dayOfYear);
    }

    public function testDaysInMonthGetter()
    {
        $d = Carbon::createFromDate(2012, 5, 7);
        $this->assertSame(31, $d->daysInMonth);
    }

    public function testTimestampGetter()
    {
        $d = Carbon::create();
        $d->setTimezone('GMT');
        $this->assertSame(0, $d->setDateTime(1970, 1, 1, 0, 0, 0)->timestamp);
    }

    public function testGetAge()
    {
        $d = Carbon::now();
        $this->assertSame(0, $d->age);
    }

    public function testGetAgeWithRealAge()
    {
        $d = Carbon::createFromDate(1975, 5, 21);
        $age = intval(substr(date('Ymd') - date('Ymd', $d->timestamp), 0, -4));

        $this->assertSame($age, $d->age);
    }

    public function testGetQuarterFirst()
    {
        $d = Carbon::createFromDate(2012, 1, 1);
        $this->assertSame(1, $d->quarter);
    }

    public function testGetQuarterFirstEnd()
    {
        $d = Carbon::createFromDate(2012, 3, 31);
        $this->assertSame(1, $d->quarter);
    }

    public function testGetQuarterSecond()
    {
        $d = Carbon::createFromDate(2012, 4, 1);
        $this->assertSame(2, $d->quarter);
    }

    public function testGetQuarterThird()
    {
        $d = Carbon::createFromDate(2012, 7, 1);
        $this->assertSame(3, $d->quarter);
    }

    public function testGetQuarterFourth()
    {
        $d = Carbon::createFromDate(2012, 10, 1);
        $this->assertSame(4, $d->quarter);
    }

    public function testGetQuarterFirstLast()
    {
        $d = Carbon::createFromDate(2012, 12, 31);
        $this->assertSame(4, $d->quarter);
    }

    public function testGetLocalTrue()
    {
        // Default timezone has been set to America/Toronto in TestFixture.php
        // @see : http://en.wikipedia.org/wiki/List_of_UTC_time_offsets
        $this->assertTrue(Carbon::createFromDate(2012, 1, 1, 'America/Toronto')->local);
        $this->assertTrue(Carbon::createFromDate(2012, 1, 1, 'America/New_York')->local);
    }

    public function testGetLocalFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2012, 7, 1, 'UTC')->local);
        $this->assertFalse(Carbon::createFromDate(2012, 7, 1, 'Europe/London')->local);
    }

    public function testGetUtcFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2013, 1, 1, 'America/Toronto')->utc);
        $this->assertFalse(Carbon::createFromDate(2013, 1, 1, 'Europe/Paris')->utc);
    }

    public function testGetUtcTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'Atlantic/Reykjavik')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'Europe/Lisbon')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'Africa/Casablanca')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'Africa/Dakar')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'Europe/Dublin')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'Europe/London')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'UTC')->utc);
        $this->assertTrue(Carbon::createFromDate(2013, 1, 1, 'GMT')->utc);
    }

    public function testGetDstFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2012, 1, 1, 'America/Toronto')->dst);
    }

    public function testGetDstTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2012, 7, 1, 'America/Toronto')->dst);
    }

    public function testOffsetForTorontoWithDST()
    {
        $this->assertSame(-18000, Carbon::createFromDate(2012, 1, 1, 'America/Toronto')->offset);
    }

    public function testOffsetForTorontoNoDST()
    {
        $this->assertSame(-14400, Carbon::createFromDate(2012, 6, 1, 'America/Toronto')->offset);
    }

    public function testOffsetForGMT()
    {
        $this->assertSame(0, Carbon::createFromDate(2012, 6, 1, 'GMT')->offset);
    }

    public function testOffsetHoursForTorontoWithDST()
    {
        $this->assertSame(-5, Carbon::createFromDate(2012, 1, 1, 'America/Toronto')->offsetHours);
    }

    public function testOffsetHoursForTorontoNoDST()
    {
        $this->assertSame(-4, Carbon::createFromDate(2012, 6, 1, 'America/Toronto')->offsetHours);
    }

    public function testOffsetHoursForGMT()
    {
        $this->assertSame(0, Carbon::createFromDate(2012, 6, 1, 'GMT')->offsetHours);
    }

    public function testIsLeapYearTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2012, 1, 1)->isLeapYear());
    }

    public function testIsLeapYearFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2011, 1, 1)->isLeapYear());
    }

    public function testWeekOfMonth()
    {
        $this->assertSame(5, Carbon::createFromDate(2012, 9, 30)->weekOfMonth);
        $this->assertSame(4, Carbon::createFromDate(2012, 9, 28)->weekOfMonth);
        $this->assertSame(3, Carbon::createFromDate(2012, 9, 20)->weekOfMonth);
        $this->assertSame(2, Carbon::createFromDate(2012, 9, 8)->weekOfMonth);
        $this->assertSame(1, Carbon::createFromDate(2012, 9, 1)->weekOfMonth);
    }

    public function testWeekOfYearFirstWeek()
    {
        $this->assertSame(52, Carbon::createFromDate(2012, 1, 1)->weekOfYear);
        $this->assertSame(1, Carbon::createFromDate(2012, 1, 2)->weekOfYear);
    }

    public function testWeekOfYearLastWeek()
    {
        $this->assertSame(52, Carbon::createFromDate(2012, 12, 30)->weekOfYear);
        $this->assertSame(1, Carbon::createFromDate(2012, 12, 31)->weekOfYear);
    }

    public function testGetTimezone()
    {
        $dt = Carbon::createFromDate(2000, 1, 1, 'America/Toronto');
        $this->assertSame('America/Toronto', $dt->timezone->getName());
    }

    public function testGetTz()
    {
        $dt = Carbon::createFromDate(2000, 1, 1, 'America/Toronto');
        $this->assertSame('America/Toronto', $dt->tz->getName());
    }

    public function testGetTimezoneName()
    {
        $dt = Carbon::createFromDate(2000, 1, 1, 'America/Toronto');
        $this->assertSame('America/Toronto', $dt->timezoneName);
    }

    public function testGetTzName()
    {
        $dt = Carbon::createFromDate(2000, 1, 1, 'America/Toronto');
        $this->assertSame('America/Toronto', $dt->tzName);
    }

    public function testInvalidGetter()
    {
        $this->setExpectedException('InvalidArgumentException');
        $d = Carbon::now();
        $bb = $d->doesNotExit;
    }
}
