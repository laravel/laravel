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

class ConstructTest extends TestFixture
{
    public function testCreatesAnInstanceDefaultToNow()
    {
        $c = new Carbon();
        $now = Carbon::now();
        $this->assertInstanceOfCarbon($c);
        $this->assertSame($now->tzName, $c->tzName);
        $this->assertCarbon($c, $now->year, $now->month, $now->day, $now->hour, $now->minute, $now->second);
    }

    public function testParseCreatesAnInstanceDefaultToNow()
    {
        $c = Carbon::parse();
        $now = Carbon::now();
        $this->assertInstanceOfCarbon($c);
        $this->assertSame($now->tzName, $c->tzName);
        $this->assertCarbon($c, $now->year, $now->month, $now->day, $now->hour, $now->minute, $now->second);
    }

    public function testWithFancyString()
    {
        $c = new Carbon('first day of January 2008');
        $this->assertCarbon($c, 2008, 1, 1, 0, 0, 0);
    }

    public function testParseWithFancyString()
    {
        $c = Carbon::parse('first day of January 2008');
        $this->assertCarbon($c, 2008, 1, 1, 0, 0, 0);
    }

    public function testDefaultTimezone()
    {
        $c = new Carbon('now');
        $this->assertSame('America/Toronto', $c->tzName);
    }

    public function testParseWithDefaultTimezone()
    {
        $c = Carbon::parse('now');
        $this->assertSame('America/Toronto', $c->tzName);
    }

    public function testSettingTimezone()
    {
        $timezone = 'Europe/London';
        $dtz = new \DateTimeZone($timezone);
        $dt = new \DateTime('now', $dtz);
        $dayLightSavingTimeOffset = $dt->format('I');

        $c = new Carbon('now', $dtz);
        $this->assertSame($timezone, $c->tzName);
        $this->assertSame(0 + $dayLightSavingTimeOffset, $c->offsetHours);
    }

    public function testParseSettingTimezone()
    {
        $timezone = 'Europe/London';
        $dtz = new \DateTimeZone($timezone);
        $dt = new \DateTime('now', $dtz);
        $dayLightSavingTimeOffset = $dt->format('I');

        $c = Carbon::parse('now', $dtz);
        $this->assertSame($timezone, $c->tzName);
        $this->assertSame(0 + $dayLightSavingTimeOffset, $c->offsetHours);
    }

    public function testSettingTimezoneWithString()
    {
        $timezone = 'Asia/Tokyo';
        $dtz = new \DateTimeZone($timezone);
        $dt = new \DateTime('now', $dtz);
        $dayLightSavingTimeOffset = $dt->format('I');

        $c = new Carbon('now', $timezone);
        $this->assertSame($timezone, $c->tzName);
        $this->assertSame(9 + $dayLightSavingTimeOffset, $c->offsetHours);
    }

    public function testParseSettingTimezoneWithString()
    {
        $timezone = 'Asia/Tokyo';
        $dtz = new \DateTimeZone($timezone);
        $dt = new \DateTime('now', $dtz);
        $dayLightSavingTimeOffset = $dt->format('I');

        $c = Carbon::parse('now', $timezone);
        $this->assertSame($timezone, $c->tzName);
        $this->assertSame(9 + $dayLightSavingTimeOffset, $c->offsetHours);
    }
}
