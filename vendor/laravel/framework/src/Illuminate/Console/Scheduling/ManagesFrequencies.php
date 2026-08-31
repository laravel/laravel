<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Support\Carbon;
use InvalidArgumentException;

use function Illuminate\Support\enum_value;

trait ManagesFrequencies
{
    /**
     * The Cron expression representing the event's frequency.
     *
     * @param  string  $expression
     * @return $this
     */
    public function cron($expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Schedule the event to run between start and end time.
     *
     * @param  string  $startTime
     * @param  string  $endTime
     * @return $this
     */
    public function between($startTime, $endTime)
    {
        return $this->when($this->inTimeInterval($startTime, $endTime));
    }

    /**
     * Schedule the event to not run between start and end time.
     *
     * @param  string  $startTime
     * @param  string  $endTime
     * @return $this
     */
    public function unlessBetween($startTime, $endTime)
    {
        return $this->skip($this->inTimeInterval($startTime, $endTime));
    }

    /**
     * Schedule the event to run between start and end time.
     *
     * @param  string  $startTime
     * @param  string  $endTime
     * @return \Closure
     */
    private function inTimeInterval($startTime, $endTime)
    {
        [$now, $startTime, $endTime] = [
            Carbon::now($this->timezone),
            Carbon::parse($startTime, $this->timezone),
            Carbon::parse($endTime, $this->timezone),
        ];

        if ($endTime->lessThan($startTime)) {
            if ($startTime->greaterThan($now)) {
                $startTime = $startTime->subDay(1);
            } else {
                $endTime = $endTime->addDay(1);
            }
        }

        return fn () => $now->between($startTime, $endTime);
    }

    /**
     * Schedule the event to run every second.
     *
     * @return $this
     */
    public function everySecond()
    {
        return $this->repeatEvery(1);
    }

    /**
     * Schedule the event to run every two seconds.
     *
     * @return $this
     */
    public function everyTwoSeconds()
    {
        return $this->repeatEvery(2);
    }

    /**
     * Schedule the event to run every five seconds.
     *
     * @return $this
     */
    public function everyFiveSeconds()
    {
        return $this->repeatEvery(5);
    }

    /**
     * Schedule the event to run every ten seconds.
     *
     * @return $this
     */
    public function everyTenSeconds()
    {
        return $this->repeatEvery(10);
    }

    /**
     * Schedule the event to run every fifteen seconds.
     *
     * @return $this
     */
    public function everyFifteenSeconds()
    {
        return $this->repeatEvery(15);
    }

    /**
     * Schedule the event to run every twenty seconds.
     *
     * @return $this
     */
    public function everyTwentySeconds()
    {
        return $this->repeatEvery(20);
    }

    /**
     * Schedule the event to run every thirty seconds.
     *
     * @return $this
     */
    public function everyThirtySeconds()
    {
        return $this->repeatEvery(30);
    }

    /**
     * Schedule the event to run multiple times per minute.
     *
     * @param  int<1, 59>  $seconds
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    protected function repeatEvery($seconds)
    {
        if ($seconds <= 0) {
            throw new InvalidArgumentException("The seconds [$seconds] must be greater than zero.");
        }

        if (60 % $seconds !== 0) {
            throw new InvalidArgumentException("The seconds [$seconds] are not evenly divisible by 60.");
        }

        $this->repeatSeconds = $seconds;

        return $this->everyMinute();
    }

    /**
     * Schedule the event to run every minute.
     *
     * @return $this
     */
    public function everyMinute()
    {
        return $this->spliceIntoPosition(1, '*');
    }

    /**
     * Schedule the event to run every two minutes.
     *
     * @return $this
     */
    public function everyTwoMinutes()
    {
        return $this->spliceIntoPosition(1, '*/2');
    }

    /**
     * Schedule the event to run every three minutes.
     *
     * @return $this
     */
    public function everyThreeMinutes()
    {
        return $this->spliceIntoPosition(1, '*/3');
    }

    /**
     * Schedule the event to run every four minutes.
     *
     * @return $this
     */
    public function everyFourMinutes()
    {
        return $this->spliceIntoPosition(1, '*/4');
    }

    /**
     * Schedule the event to run every five minutes.
     *
     * @return $this
     */
    public function everyFiveMinutes()
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    /**
     * Schedule the event to run every ten minutes.
     *
     * @return $this
     */
    public function everyTenMinutes()
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    /**
     * Schedule the event to run every fifteen minutes.
     *
     * @return $this
     */
    public function everyFifteenMinutes()
    {
        return $this->spliceIntoPosition(1, '*/15');
    }

    /**
     * Schedule the event to run every thirty minutes.
     *
     * @return $this
     */
    public function everyThirtyMinutes()
    {
        return $this->spliceIntoPosition(1, '*/30');
    }

    /**
     * Schedule the event to run hourly.
     *
     * @return $this
     */
    public function hourly()
    {
        return $this->spliceIntoPosition(1, 0);
    }

    /**
     * Schedule the event to run hourly at a given offset in the hour.
     *
     * @param  array|string|int<0, 59>|int<0, 59>[]  $offset
     * @return $this
     */
    public function hourlyAt($offset)
    {
        return $this->hourBasedSchedule($offset, '*');
    }

    /**
     * Schedule the event to run every odd hour.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyOddHour($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '1-23/2');
    }

    /**
     * Schedule the event to run every two hours.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyTwoHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/2');
    }

    /**
     * Schedule the event to run every three hours.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyThreeHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/3');
    }

    /**
     * Schedule the event to run every four hours.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyFourHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/4');
    }

    /**
     * Schedule the event to run every six hours.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everySixHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/6');
    }

    /**
     * Schedule the event to run daily.
     *
     * @return $this
     */
    public function daily()
    {
        return $this->hourBasedSchedule(0, 0);
    }

    /**
     * Schedule the command at a given time.
     *
     * @param  string  $time
     * @return $this
     */
    public function at($time)
    {
        return $this->dailyAt($time);
    }

    /**
     * Schedule the event to run daily at a given time (10:00, 19:30, etc).
     *
     * @param  string  $time
     * @return $this
     */
    public function dailyAt($time)
    {
        $segments = explode(':', $time);

        return $this->hourBasedSchedule(
            count($segments) >= 2 ? (int) $segments[1] : '0',
            (int) $segments[0]
        );
    }

    /**
     * Schedule the event to run twice daily.
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @return $this
     */
    public function twiceDaily($first = 1, $second = 13)
    {
        return $this->twiceDailyAt($first, $second, 0);
    }

    /**
     * Schedule the event to run twice daily at a given offset.
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @param  int<0, 59>  $offset
     * @return $this
     */
    public function twiceDailyAt($first = 1, $second = 13, $offset = 0)
    {
        $hours = $first.','.$second;

        return $this->hourBasedSchedule($offset, $hours);
    }

    /**
     * Schedule the event to run at the given minutes and hours.
     *
     * @param  array|string|int<0, 59>  $minutes
     * @param  array|string|int<0, 23>  $hours
     * @return $this
     */
    protected function hourBasedSchedule($minutes, $hours)
    {
        $minutes = is_array($minutes) ? implode(',', $minutes) : $minutes;

        $hours = is_array($hours) ? implode(',', $hours) : $hours;

        return $this->spliceIntoPosition(1, $minutes)
            ->spliceIntoPosition(2, $hours);
    }

    /**
     * Schedule the event to run only on weekdays.
     *
     * @return $this
     */
    public function weekdays()
    {
        return $this->days(Schedule::MONDAY.'-'.Schedule::FRIDAY);
    }

    /**
     * Schedule the event to run only on weekends.
     *
     * @return $this
     */
    public function weekends()
    {
        return $this->days(Schedule::SATURDAY.','.Schedule::SUNDAY);
    }

    /**
     * Schedule the event to run only on Mondays.
     *
     * @return $this
     */
    public function mondays()
    {
        return $this->days(Schedule::MONDAY);
    }

    /**
     * Schedule the event to run only on Tuesdays.
     *
     * @return $this
     */
    public function tuesdays()
    {
        return $this->days(Schedule::TUESDAY);
    }

    /**
     * Schedule the event to run only on Wednesdays.
     *
     * @return $this
     */
    public function wednesdays()
    {
        return $this->days(Schedule::WEDNESDAY);
    }

    /**
     * Schedule the event to run only on Thursdays.
     *
     * @return $this
     */
    public function thursdays()
    {
        return $this->days(Schedule::THURSDAY);
    }

    /**
     * Schedule the event to run only on Fridays.
     *
     * @return $this
     */
    public function fridays()
    {
        return $this->days(Schedule::FRIDAY);
    }

    /**
     * Schedule the event to run only on Saturdays.
     *
     * @return $this
     */
    public function saturdays()
    {
        return $this->days(Schedule::SATURDAY);
    }

    /**
     * Schedule the event to run only on Sundays.
     *
     * @return $this
     */
    public function sundays()
    {
        return $this->days(Schedule::SUNDAY);
    }

    /**
     * Schedule the event to run weekly.
     *
     * @return $this
     */
    public function weekly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(5, 0);
    }

    /**
     * Schedule the event to run weekly on a given day and time.
     *
     * @param  mixed  $dayOfWeek
     * @param  string  $time
     * @return $this
     */
    public function weeklyOn($dayOfWeek, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    /**
     * Schedule the event to run monthly.
     *
     * @return $this
     */
    public function monthly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1);
    }

    /**
     * Schedule the event to run monthly on a given day and time.
     *
     * @param  int<1, 31>  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function monthlyOn($dayOfMonth = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth);
    }

    /**
     * Schedule the event to run twice monthly at a given time.
     *
     * @param  int<1, 31>  $first
     * @param  int<1, 31>  $second
     * @param  string  $time
     * @return $this
     */
    public function twiceMonthly($first = 1, $second = 16, $time = '0:0')
    {
        $daysOfMonth = $first.','.$second;

        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $daysOfMonth);
    }

    /**
     * Schedule the event to run on the last day of the month.
     *
     * @param  string  $time
     * @return $this
     */
    public function lastDayOfMonth($time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, Carbon::now()->endOfMonth()->day);
    }

    /**
     * Schedule the event to run on specific days of the month.
     *
     * @param  array<int<1, 31>>|int<1, 31>  ...$days
     * @return $this
     */
    public function daysOfMonth(...$days)
    {
        $days = count($days) === 1 && is_array($days[0]) ? $days[0] : $days;

        $this->dailyAt('0:0');

        return $this->spliceIntoPosition(3, implode(',', $days));
    }

    /**
     * Schedule the event to run quarterly.
     *
     * @return $this
     */
    public function quarterly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Schedule the event to run quarterly on a given day and time.
     *
     * @param  int  $dayOfQuarter
     * @param  string  $time
     * @return $this
     */
    public function quarterlyOn($dayOfQuarter = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfQuarter)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Schedule the event to run yearly.
     *
     * @return $this
     */
    public function yearly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, 1);
    }

    /**
     * Schedule the event to run yearly on a given month, day, and time.
     *
     * @param  int  $month
     * @param  int<1, 31>|string  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function yearlyOn($month = 1, $dayOfMonth = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth)
            ->spliceIntoPosition(4, $month);
    }

    /**
     * Set the days of the week the command should run on.
     *
     * @param  mixed  $days
     * @return $this
     */
    public function days($days)
    {
        $days = is_array($days) ? $days : func_get_args();

        return $this->spliceIntoPosition(5, implode(',', $days));
    }

    /**
     * Set the timezone the date should be evaluated on.
     *
     * @param  \UnitEnum|\DateTimeZone|string  $timezone
     * @return $this
     */
    public function timezone($timezone)
    {
        $this->timezone = enum_value($timezone);

        return $this;
    }

    /**
     * Splice the given value into the given position of the expression.
     *
     * @param  int  $position
     * @param  string|int  $value
     * @return $this
     */
    protected function spliceIntoPosition($position, $value)
    {
        $segments = preg_split("/\s+/", $this->expression);

        $segments[$position - 1] = $value;

        return $this->cron(implode(' ', $segments));
    }
}
