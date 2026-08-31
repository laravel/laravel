<?php

namespace Illuminate\Support\Facades;

use Closure;
use Illuminate\Process\Factory;

/**
 * @method static \Illuminate\Process\PendingProcess command(array|string $command)
 * @method static \Illuminate\Process\PendingProcess path(string $path)
 * @method static \Illuminate\Process\PendingProcess timeout(int $timeout)
 * @method static \Illuminate\Process\PendingProcess idleTimeout(int $timeout)
 * @method static \Illuminate\Process\PendingProcess forever()
 * @method static \Illuminate\Process\PendingProcess env(array $environment)
 * @method static \Illuminate\Process\PendingProcess input(\Traversable|resource|string|int|float|bool|null $input)
 * @method static \Illuminate\Process\PendingProcess quietly()
 * @method static \Illuminate\Process\PendingProcess tty(bool $tty = true)
 * @method static \Illuminate\Process\PendingProcess options(array $options)
 * @method static \Illuminate\Contracts\Process\ProcessResult run(array|string|null $command = null, callable|null $output = null)
 * @method static \Illuminate\Process\InvokedProcess start(array|string|null $command = null, callable|null $output = null)
 * @method static bool supportsTty()
 * @method static \Illuminate\Process\PendingProcess withFakeHandlers(array $fakeHandlers)
 * @method static \Illuminate\Process\PendingProcess|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Process\PendingProcess|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Process\FakeProcessResult result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
 * @method static \Illuminate\Process\FakeProcessDescription describe()
 * @method static \Illuminate\Process\FakeProcessSequence sequence(array $processes = [])
 * @method static bool isRecording()
 * @method static \Illuminate\Process\Factory recordIfRecording(\Illuminate\Process\PendingProcess $process, \Illuminate\Contracts\Process\ProcessResult $result)
 * @method static \Illuminate\Process\Factory record(\Illuminate\Process\PendingProcess $process, \Illuminate\Contracts\Process\ProcessResult $result)
 * @method static \Illuminate\Process\Factory preventStrayProcesses(bool $prevent = true)
 * @method static bool preventingStrayProcesses()
 * @method static \Illuminate\Process\Factory assertRan(\Closure|string $callback)
 * @method static \Illuminate\Process\Factory assertRanTimes(\Closure|string $callback, int $times = 1)
 * @method static \Illuminate\Process\Factory assertNotRan(\Closure|string $callback)
 * @method static \Illuminate\Process\Factory assertDidntRun(\Closure|string $callback)
 * @method static \Illuminate\Process\Factory assertNothingRan()
 * @method static \Illuminate\Process\Pool pool(callable $callback)
 * @method static \Illuminate\Contracts\Process\ProcessResult pipe(callable|array $callback, callable|null $output = null)
 * @method static \Illuminate\Process\ProcessPoolResults concurrently(callable $callback, callable|null $output = null)
 * @method static \Illuminate\Process\PendingProcess newPendingProcess()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 *
 * @see \Illuminate\Process\PendingProcess
 * @see \Illuminate\Process\Factory
 */
class Process extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param  \Closure|array|null  $callback
     * @return \Illuminate\Process\Factory
     */
    public static function fake(Closure|array|null $callback = null)
    {
        return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }
}
