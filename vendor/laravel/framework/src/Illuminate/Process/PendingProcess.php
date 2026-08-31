<?php

namespace Illuminate\Process;

use Closure;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use LogicException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;
use Symfony\Component\Process\Process;

class PendingProcess
{
    use Conditionable;

    /**
     * The process factory instance.
     *
     * @var \Illuminate\Process\Factory
     */
    protected $factory;

    /**
     * The command to invoke the process.
     *
     * @var array<array-key, string>|string|null
     */
    public $command;

    /**
     * The working directory of the process.
     *
     * @var string|null
     */
    public $path;

    /**
     * The maximum number of seconds the process may run.
     *
     * @var int|null
     */
    public $timeout = 60;

    /**
     * The maximum number of seconds the process may go without returning output.
     *
     * @var int
     */
    public $idleTimeout;

    /**
     * The additional environment variables for the process.
     *
     * @var array
     */
    public $environment = [];

    /**
     * The standard input data that should be piped into the command.
     *
     * @var string|int|float|bool|resource|\Traversable|null
     */
    public $input;

    /**
     * Indicates whether output should be disabled for the process.
     *
     * @var bool
     */
    public $quietly = false;

    /**
     * Indicates if TTY mode should be enabled.
     *
     * @var bool
     */
    public $tty = false;

    /**
     * The options that will be passed to "proc_open".
     *
     * @var array
     */
    public $options = [];

    /**
     * The registered fake handler callbacks.
     *
     * @var array
     */
    protected $fakeHandlers = [];

    /**
     * Create a new pending process instance.
     *
     * @param  \Illuminate\Process\Factory  $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Specify the command that will invoke the process.
     *
     * @param  array<array-key, string>|string  $command
     * @return $this
     */
    public function command(array|string $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Specify the working directory of the process.
     *
     * @param  string  $path
     * @return $this
     */
    public function path(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Specify the maximum number of seconds the process may run.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function timeout(int $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Specify the maximum number of seconds a process may go without returning output.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function idleTimeout(int $timeout)
    {
        $this->idleTimeout = $timeout;

        return $this;
    }

    /**
     * Indicate that the process may run forever without timing out.
     *
     * @return $this
     */
    public function forever()
    {
        $this->timeout = null;

        return $this;
    }

    /**
     * Set the additional environment variables for the process.
     *
     * @param  array  $environment
     * @return $this
     */
    public function env(array $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Set the standard input that should be provided when invoking the process.
     *
     * @param  \Traversable|resource|string|int|float|bool|null  $input
     * @return $this
     */
    public function input($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Disable output for the process.
     *
     * @return $this
     */
    public function quietly()
    {
        $this->quietly = true;

        return $this;
    }

    /**
     * Enable TTY mode for the process.
     *
     * @param  bool  $tty
     * @return $this
     */
    public function tty(bool $tty = true)
    {
        $this->tty = $tty;

        return $this;
    }

    /**
     * Set the "proc_open" options that should be used when invoking the process.
     *
     * @param  array  $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Run the process.
     *
     * @param  array<array-key, string>|string|null  $command
     * @param  callable|null  $output
     * @return \Illuminate\Contracts\Process\ProcessResult
     *
     * @throws \Illuminate\Process\Exceptions\ProcessTimedOutException
     * @throws \RuntimeException
     */
    public function run(array|string|null $command = null, ?callable $output = null)
    {
        $this->command = $command ?: $this->command;

        $process = $this->toSymfonyProcess($command);

        try {
            if ($fake = $this->fakeFor($command = $process->getCommandline())) {
                return tap($this->resolveSynchronousFake($command, $fake), function ($result) {
                    $this->factory->recordIfRecording($this, $result);
                });
            } elseif ($this->factory->isRecording() && $this->factory->preventingStrayProcesses()) {
                throw new RuntimeException('Attempted process ['.$command.'] without a matching fake.');
            }

            return new ProcessResult(tap($process)->run($output));
        } catch (SymfonyTimeoutException $e) {
            throw new ProcessTimedOutException($e, new ProcessResult($process));
        }
    }

    /**
     * Start the process in the background.
     *
     * @param  array<array-key, string>|string|null  $command
     * @param  callable|null  $output
     * @return \Illuminate\Process\InvokedProcess
     *
     * @throws \RuntimeException
     */
    public function start(array|string|null $command = null, ?callable $output = null)
    {
        $this->command = $command ?: $this->command;

        $process = $this->toSymfonyProcess($command);

        if ($fake = $this->fakeFor($command = $process->getCommandline())) {
            return tap($this->resolveAsynchronousFake($command, $output, $fake), function (FakeInvokedProcess $process) {
                $this->factory->recordIfRecording($this, $process->predictProcessResult());
            });
        } elseif ($this->factory->isRecording() && $this->factory->preventingStrayProcesses()) {
            throw new RuntimeException('Attempted process ['.$command.'] without a matching fake.');
        }

        return new InvokedProcess(tap($process)->start($output));
    }

    /**
     * Get a Symfony Process instance from the current pending command.
     *
     * @param  array<array-key, string>|string|null  $command
     * @return \Symfony\Component\Process\Process
     */
    protected function toSymfonyProcess(array|string|null $command)
    {
        $command = $command ?? $this->command;

        $process = is_iterable($command)
            ? new Process($command, null, $this->environment)
            : Process::fromShellCommandline((string) $command, null, $this->environment);

        $process->setWorkingDirectory((string) ($this->path ?? getcwd()));
        $process->setTimeout($this->timeout);

        if ($this->idleTimeout) {
            $process->setIdleTimeout($this->idleTimeout);
        }

        if ($this->input) {
            $process->setInput($this->input);
        }

        if ($this->quietly) {
            $process->disableOutput();
        }

        if ($this->tty) {
            $process->setTty(true);
        }

        if (! empty($this->options)) {
            $process->setOptions($this->options);
        }

        return $process;
    }

    /**
     * Determine whether TTY is supported on the current operating system.
     *
     * @return bool
     */
    public function supportsTty()
    {
        return Process::isTtySupported();
    }

    /**
     * Specify the fake process result handlers for the pending process.
     *
     * @param  array  $fakeHandlers
     * @return $this
     */
    public function withFakeHandlers(array $fakeHandlers)
    {
        $this->fakeHandlers = $fakeHandlers;

        return $this;
    }

    /**
     * Get the fake handler for the given command, if applicable.
     *
     * @param  string  $command
     * @return \Closure|null
     */
    protected function fakeFor(string $command)
    {
        return (new Collection($this->fakeHandlers))
            ->first(fn ($handler, $pattern) => $pattern === '*' || Str::is($pattern, $command));
    }

    /**
     * Resolve the given fake handler for a synchronous process.
     *
     * @param  string  $command
     * @param  \Closure  $fake
     * @return mixed
     */
    protected function resolveSynchronousFake(string $command, Closure $fake)
    {
        $result = $fake($this);

        if (is_int($result)) {
            return (new FakeProcessResult(exitCode: $result))->withCommand($command);
        }

        if (is_string($result) || is_array($result)) {
            return (new FakeProcessResult(output: $result))->withCommand($command);
        }

        return match (true) {
            $result instanceof ProcessResult => $result,
            $result instanceof FakeProcessResult => $result->withCommand($command),
            $result instanceof FakeProcessDescription => $result->toProcessResult($command),
            $result instanceof FakeProcessSequence => $this->resolveSynchronousFake($command, fn () => $result()),
            $result instanceof \Throwable => throw $result,
            default => throw new LogicException('Unsupported synchronous process fake result provided.'),
        };
    }

    /**
     * Resolve the given fake handler for an asynchronous process.
     *
     * @param  string  $command
     * @param  callable|null  $output
     * @param  \Closure  $fake
     * @return \Illuminate\Process\FakeInvokedProcess
     *
     * @throws \LogicException
     */
    protected function resolveAsynchronousFake(string $command, ?callable $output, Closure $fake)
    {
        $result = $fake($this);

        if (is_string($result) || is_array($result)) {
            $result = new FakeProcessResult(output: $result);
        }

        if ($result instanceof ProcessResult) {
            return (new FakeInvokedProcess(
                $command,
                (new FakeProcessDescription)
                    ->replaceOutput($result->output())
                    ->replaceErrorOutput($result->errorOutput())
                    ->runsFor(iterations: 0)
                    ->exitCode($result->exitCode())
            ))->withOutputHandler($output);
        } elseif ($result instanceof FakeProcessResult) {
            return (new FakeInvokedProcess(
                $command,
                (new FakeProcessDescription)
                    ->replaceOutput($result->output())
                    ->replaceErrorOutput($result->errorOutput())
                    ->runsFor(iterations: 0)
                    ->exitCode($result->exitCode())
            ))->withOutputHandler($output);
        } elseif ($result instanceof FakeProcessDescription) {
            return (new FakeInvokedProcess($command, $result))->withOutputHandler($output);
        } elseif ($result instanceof FakeProcessSequence) {
            return $this->resolveAsynchronousFake($command, $output, fn () => $result());
        }

        throw new LogicException('Unsupported asynchronous process fake result provided.');
    }
}
