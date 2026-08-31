<?php

namespace Illuminate\Support\Facades;

use Illuminate\Console\Scheduling\Schedule as ConsoleSchedule;

/**
 * @method static \Illuminate\Console\Scheduling\CallbackEvent call(string|callable $callback, array $parameters = [])
 * @method static \Illuminate\Console\Scheduling\Event command(\Symfony\Component\Console\Command\Command|string $command, array $parameters = [])
 * @method static \Illuminate\Console\Scheduling\CallbackEvent job(object|string $job, \UnitEnum|string|null $queue = null, \UnitEnum|string|null $connection = null)
 * @method static \Illuminate\Console\Scheduling\Event exec(string $command, array $parameters = [])
 * @method static void group(\Closure $events)
 * @method static string compileArrayInput(string|int $key, array $value)
 * @method static bool serverShouldRun(\Illuminate\Console\Scheduling\Event $event, \DateTimeInterface $time)
 * @method static \Illuminate\Support\Collection dueEvents(\Illuminate\Contracts\Foundation\Application $app)
 * @method static \Illuminate\Console\Scheduling\Event[] events()
 * @method static \Illuminate\Console\Scheduling\Schedule useCache(\UnitEnum|string $store)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes withoutOverlapping(int $expiresAt = 1440)
 * @method static void mergeAttributes(\Illuminate\Console\Scheduling\Event $event)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes user(string $user)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes environments(mixed $environments)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes evenInMaintenanceMode()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes onOneServer()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes runInBackground()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes when(\Closure|bool $callback)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes skip(\Closure|bool $callback)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes name(string $description)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes description(string $description)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes cron(string $expression)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes between(string $startTime, string $endTime)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes unlessBetween(string $startTime, string $endTime)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everySecond()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyTwoSeconds()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyFiveSeconds()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyTenSeconds()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyFifteenSeconds()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyTwentySeconds()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyThirtySeconds()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyMinute()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyTwoMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyThreeMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyFourMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyFiveMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyTenMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyFifteenMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyThirtyMinutes()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes hourly()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes hourlyAt(array|string|int|int[] $offset)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyOddHour(array|string|int $offset = 0)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyTwoHours(array|string|int $offset = 0)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyThreeHours(array|string|int $offset = 0)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everyFourHours(array|string|int $offset = 0)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes everySixHours(array|string|int $offset = 0)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes daily()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes at(string $time)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes dailyAt(string $time)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes twiceDaily(int $first = 1, int $second = 13)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes twiceDailyAt(int $first = 1, int $second = 13, int $offset = 0)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes weekdays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes weekends()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes mondays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes tuesdays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes wednesdays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes thursdays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes fridays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes saturdays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes sundays()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes weekly()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes weeklyOn(mixed $dayOfWeek, string $time = '0:0')
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes monthly()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes monthlyOn(int $dayOfMonth = 1, string $time = '0:0')
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes twiceMonthly(int $first = 1, int $second = 16, string $time = '0:0')
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes lastDayOfMonth(string $time = '0:0')
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes daysOfMonth(array|int ...$days)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes quarterly()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes quarterlyOn(int $dayOfQuarter = 1, string $time = '0:0')
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes yearly()
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes yearlyOn(int $month = 1, int|string $dayOfMonth = 1, string $time = '0:0')
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes days(mixed $days)
 * @method static \Illuminate\Console\Scheduling\PendingEventAttributes timezone(\UnitEnum|\DateTimeZone|string $timezone)
 *
 * @see \Illuminate\Console\Scheduling\Schedule
 */
class Schedule extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConsoleSchedule::class;
    }
}
