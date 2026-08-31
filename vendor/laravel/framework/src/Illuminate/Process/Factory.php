<?php

namespace Illuminate\Process;

use Closure;
use Illuminate\Contracts\Process\ProcessResult as ProcessResultContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;

class Factory
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * Indicates if the process factory has faked process handlers.
     *
     * @var bool
     */
    protected $recording = false;

    /**
     * All of the recorded processes.
     *
     * @var array
     */
    protected $recorded = [];

    /**
     * The registered fake handler callbacks.
     *
     * @var array
     */
    protected $fakeHandlers = [];

    /**
     * Indicates that an exception should be thrown if any process is not faked.
     *
     * @var bool
     */
    protected $preventStrayProcesses = false;

    /**
     * Create a new fake process response for testing purposes.
     *
     * @param  array|string  $output
     * @param  array|string  $errorOutput
     * @param  int  $exitCode
     * @return \Illuminate\Process\FakeProcessResult
     */
    public function result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
    {
        return new FakeProcessResult(
            output: $output,
            errorOutput: $errorOutput,
            exitCode: $exitCode,
        );
    }

    /**
     * Begin describing a fake process lifecycle.
     *
     * @return \Illuminate\Process\FakeProcessDescription
     */
    public function describe()
    {
        return new FakeProcessDescription;
    }

    /**
     * Begin describing a fake process sequence.
     *
     * @param  array  $processes
     * @return \Illuminate\Process\FakeProcessSequence
     */
    public function sequence(array $processes = [])
    {
        return new FakeProcessSequence($processes);
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param  \Closure|array|null  $callback
     * @return $this
     */
    public function fake(Closure|array|null $callback = null)
    {
        $this->recording = true;

        if (is_null($callback)) {
            $this->fakeHandlers = ['*' => fn () => new FakeProcessResult];

            return $this;
        }

        if ($callback instanceof Closure) {
            $this->fakeHandlers = ['*' => $callback];

            return $this;
        }

        foreach ($callback as $command => $handler) {
            $this->fakeHandlers[is_numeric($command) ? '*' : $command] = $handler instanceof Closure
                ? $handler
                : fn () => $handler;
        }

        return $this;
    }

    /**
     * Determine if the process factory has fake process handlers and is recording processes.
     *
     * @return bool
     */
    public function isRecording()
    {
        return $this->recording;
    }

    /**
     * Record the given process if processes should be recorded.
     *
     * @param  \Illuminate\Process\PendingProcess  $process
     * @param  \Illuminate\Contracts\Process\ProcessResult  $result
     * @return $this
     */
    public function recordIfRecording(PendingProcess $process, ProcessResultContract $result)
    {
        if ($this->isRecording()) {
            $this->record($process, $result);
        }

        return $this;
    }

    /**
     * Record the given process.
     *
     * @param  \Illuminate\Process\PendingProcess  $process
     * @param  \Illuminate\Contracts\Process\ProcessResult  $result
     * @return $this
     */
    public function record(PendingProcess $process, ProcessResultContract $result)
    {
        $this->recorded[] = [$process, $result];

        return $this;
    }

    /**
     * Indicate that an exception should be thrown if any process is not faked.
     *
     * @param  bool  $prevent
     * @return $this
     */
    public function preventStrayProcesses(bool $prevent = true)
    {
        $this->preventStrayProcesses = $prevent;

        return $this;
    }

    /**
     * Determine if stray processes are being prevented.
     *
     * @return bool
     */
    public function preventingStrayProcesses()
    {
        return $this->preventStrayProcesses;
    }

    /**
     * Assert that a process was recorded matching a given truth test.
     *
     * @param  \Closure|string  $callback
     * @return $this
     */
    public function assertRan(Closure|string $callback)
    {
        $callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

        PHPUnit::assertTrue(
            (new Collection($this->recorded))->filter(function ($pair) use ($callback) {
                return $callback($pair[0], $pair[1]);
            })->count() > 0,
            'An expected process was not invoked.'
        );

        return $this;
    }

    /**
     * Assert that a process was recorded a given number of times matching a given truth test.
     *
     * @param  \Closure|string  $callback
     * @param  int  $times
     * @return $this
     */
    public function assertRanTimes(Closure|string $callback, int $times = 1)
    {
        $callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

        $count = (new Collection($this->recorded))
            ->filter(fn ($pair) => $callback($pair[0], $pair[1]))
            ->count();

        PHPUnit::assertSame(
            $times, $count,
            "An expected process ran {$count} times instead of {$times} times."
        );

        return $this;
    }

    /**
     * Assert that a process was not recorded matching a given truth test.
     *
     * @param  \Closure|string  $callback
     * @return $this
     */
    public function assertNotRan(Closure|string $callback)
    {
        $callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

        PHPUnit::assertTrue(
            (new Collection($this->recorded))->filter(function ($pair) use ($callback) {
                return $callback($pair[0], $pair[1]);
            })->count() === 0,
            'An unexpected process was invoked.'
        );

        return $this;
    }

    /**
     * Assert that a process was not recorded matching a given truth test.
     *
     * @param  \Closure|string  $callback
     * @return $this
     */
    public function assertDidntRun(Closure|string $callback)
    {
        return $this->assertNotRan($callback);
    }

    /**
     * Assert that no processes were recorded.
     *
     * @return $this
     */
    public function assertNothingRan()
    {
        PHPUnit::assertEmpty(
            $this->recorded,
            'An unexpected process was invoked.'
        );

        return $this;
    }

    /**
     * Start defining a pool of processes.
     *
     * @param  callable  $callback
     * @return \Illuminate\Process\Pool
     */
    public function pool(callable $callback)
    {
        return new Pool($this, $callback);
    }

    /**
     * Start defining a series of piped processes.
     *
     * @param  callable|array  $callback
     * @return \Illuminate\Contracts\Process\ProcessResult
     */
    public function pipe(callable|array $callback, ?callable $output = null)
    {
        return is_array($callback)
            ? (new Pipe($this, fn ($pipe) => (new Collection($callback))->each(
                fn ($command) => $pipe->command($command)
            )))->run(output: $output)
            : (new Pipe($this, $callback))->run(output: $output);
    }

    /**
     * Run a pool of processes and wait for them to finish executing.
     *
     * @param  callable  $callback
     * @param  callable|null  $output
     * @return \Illuminate\Process\ProcessPoolResults
     */
    public function concurrently(callable $callback, ?callable $output = null)
    {
        return (new Pool($this, $callback))->start($output)->wait();
    }

    /**
     * Create a new pending process associated with this factory.
     *
     * @return \Illuminate\Process\PendingProcess
     */
    public function newPendingProcess()
    {
        return (new PendingProcess($this))->withFakeHandlers($this->fakeHandlers);
    }

    /**
     * Dynamically proxy methods to a new pending process instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->newPendingProcess()->{$method}(...$parameters);
    }
}
