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

class ComparisonTest extends TestFixture
{
    public function testEqualToTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->eq(Carbon::createFromDate(2000, 1, 1)));
    }

    public function testEqualToFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->eq(Carbon::createFromDate(2000, 1, 2)));
    }

    public function testEqualWithTimezoneTrue()
    {
        $this->assertTrue(Carbon::create(2000, 1, 1, 12, 0, 0, 'America/Toronto')->eq(Carbon::create(2000, 1, 1, 9, 0, 0, 'America/Vancouver')));
    }

    public function testEqualWithTimezoneFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1, 'America/Toronto')->eq(Carbon::createFromDate(2000, 1, 1, 'America/Vancouver')));
    }

    public function testNotEqualToTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->ne(Carbon::createFromDate(2000, 1, 2)));
    }

    public function testNotEqualToFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->ne(Carbon::createFromDate(2000, 1, 1)));
    }

    public function testNotEqualWithTimezone()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1, 'America/Toronto')->ne(Carbon::createFromDate(2000, 1, 1, 'America/Vancouver')));
    }

    public function testGreaterThanTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->gt(Carbon::createFromDate(1999, 12, 31)));
    }

    public function testGreaterThanFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->gt(Carbon::createFromDate(2000, 1, 2)));
    }

    public function testGreaterThanWithTimezoneTrue()
    {
        $dt1 = Carbon::create(2000, 1, 1, 12, 0, 0, 'America/Toronto');
        $dt2 = Carbon::create(2000, 1, 1, 8, 59, 59, 'America/Vancouver');
        $this->assertTrue($dt1->gt($dt2));
    }

    public function testGreaterThanWithTimezoneFalse()
    {
        $dt1 = Carbon::create(2000, 1, 1, 12, 0, 0, 'America/Toronto');
        $dt2 = Carbon::create(2000, 1, 1, 9, 0, 1, 'America/Vancouver');
        $this->assertFalse($dt1->gt($dt2));
    }

    public function testGreaterThanOrEqualTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->gte(Carbon::createFromDate(1999, 12, 31)));
    }

    public function testGreaterThanOrEqualTrueEqual()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->gte(Carbon::createFromDate(2000, 1, 1)));
    }

    public function testGreaterThanOrEqualFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->gte(Carbon::createFromDate(2000, 1, 2)));
    }

    public function testLessThanTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->lt(Carbon::createFromDate(2000, 1, 2)));
    }

    public function testLessThanFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->lt(Carbon::createFromDate(1999, 12, 31)));
    }

    public function testLessThanOrEqualTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->lte(Carbon::createFromDate(2000, 1, 2)));
    }

    public function testLessThanOrEqualTrueEqual()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 1)->lte(Carbon::createFromDate(2000, 1, 1)));
    }

    public function testLessThanOrEqualFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->lte(Carbon::createFromDate(1999, 12, 31)));
    }

    public function testBetweenEqualTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 15)->between(Carbon::createFromDate(2000, 1, 1), Carbon::createFromDate(2000, 1, 31), true));
    }

    public function testBetweenNotEqualTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 15)->between(Carbon::createFromDate(2000, 1, 1), Carbon::createFromDate(2000, 1, 31), false));
    }

    public function testBetweenEqualFalse()
    {
        $this->assertFalse(Carbon::createFromDate(1999, 12, 31)->between(Carbon::createFromDate(2000, 1, 1), Carbon::createFromDate(2000, 1, 31), true));
    }

    public function testBetweenNotEqualFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->between(Carbon::createFromDate(2000, 1, 1), Carbon::createFromDate(2000, 1, 31), false));
    }

    public function testBetweenEqualSwitchTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 15)->between(Carbon::createFromDate(2000, 1, 31), Carbon::createFromDate(2000, 1, 1), true));
    }

    public function testBetweenNotEqualSwitchTrue()
    {
        $this->assertTrue(Carbon::createFromDate(2000, 1, 15)->between(Carbon::createFromDate(2000, 1, 31), Carbon::createFromDate(2000, 1, 1), false));
    }

    public function testBetweenEqualSwitchFalse()
    {
        $this->assertFalse(Carbon::createFromDate(1999, 12, 31)->between(Carbon::createFromDate(2000, 1, 31), Carbon::createFromDate(2000, 1, 1), true));
    }

    public function testBetweenNotEqualSwitchFalse()
    {
        $this->assertFalse(Carbon::createFromDate(2000, 1, 1)->between(Carbon::createFromDate(2000, 1, 31), Carbon::createFromDate(2000, 1, 1), false));
    }

    public function testMinIsFluid()
    {
        $dt = Carbon::now();
        $this->assertTrue($dt->min() instanceof Carbon);
    }

    public function testMinWithNow()
    {
        $dt = Carbon::create(2012, 1, 1, 0, 0, 0)->min();
        $this->assertCarbon($dt, 2012, 1, 1, 0, 0, 0);
    }

    public function testMinWithInstance()
    {
        $dt1 = Carbon::create(2013, 12, 31, 23, 59, 59);
        $dt2 = Carbon::create(2012, 1, 1, 0, 0, 0)->min($dt1);
        $this->assertCarbon($dt2, 2012, 1, 1, 0, 0, 0);
    }

    public function testMaxIsFluid()
    {
        $dt = Carbon::now();
        $this->assertTrue($dt->max() instanceof Carbon);
    }

    public function testMaxWithNow()
    {
        $dt = Carbon::create(2099, 12, 31, 23, 59, 59)->max();
        $this->assertCarbon($dt, 2099, 12, 31, 23, 59, 59);
    }

    public function testMaxWithInstance()
    {
        $dt1 = Carbon::create(2012, 1, 1, 0, 0, 0);
        $dt2 = Carbon::create(2099, 12, 31, 23, 59, 59)->max($dt1);
        $this->assertCarbon($dt2, 2099, 12, 31, 23, 59, 59);
    }
    public function testIsBirthday()
    {
        $dt1 = Carbon::createFromDate(1987, 4, 23);
        $dt2 = Carbon::createFromDate(2014, 9, 26);
        $dt3 = Carbon::createFromDate(2014, 4, 23);
        $this->assertFalse($dt2->isBirthday($dt1));
        $this->assertTrue($dt3->isBirthday($dt1));
    }
}
