<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\ProcessResult as ProcessResultContract;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Collection;

class FakeProcessResult implements ProcessResultContract
{
    /**
     * The command string.
     *
     * @var string
     */
    protected $command;

    /**
     * The process exit code.
     *
     * @var int
     */
    protected $exitCode;

    /**
     * The process output.
     *
     * @var string
     */
    protected $output = '';

    /**
     * The process error output.
     *
     * @var string
     */
    protected $errorOutput = '';

    /**
     * Create a new process result instance.
     *
     * @param  string  $command
     * @param  int  $exitCode
     * @param  array|string  $output
     * @param  array|string  $errorOutput
     */
    public function __construct(string $command = '', int $exitCode = 0, array|string $output = '', array|string $errorOutput = '')
    {
        $this->command = $command;
        $this->exitCode = $exitCode;
        $this->output = $this->normalizeOutput($output);
        $this->errorOutput = $this->normalizeOutput($errorOutput);
    }

    /**
     * Normalize the given output into a string with newlines.
     *
     * @param  array|string  $output
     * @return string
     */
    protected function normalizeOutput(array|string $output)
    {
        if (empty($output)) {
            return '';
        } elseif (is_string($output)) {
            return rtrim($output, "\n")."\n";
        } elseif (is_array($output)) {
            return rtrim(
                (new Collection($output))
                    ->map(fn ($line) => rtrim($line, "\n")."\n")
                    ->implode(''),
                "\n"
            );
        }
    }

    /**
     * Get the original command executed by the process.
     *
     * @return string
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * Create a new fake process result with the given command.
     *
     * @param  string  $command
     * @return self
     */
    public function withCommand(string $command)
    {
        return new FakeProcessResult($command, $this->exitCode, $this->output, $this->errorOutput);
    }

    /**
     * Determine if the process was successful.
     *
     * @return bool
     */
    public function successful()
    {
        return $this->exitCode === 0;
    }

    /**
     * Determine if the process failed.
     *
     * @return bool
     */
    public function failed()
    {
        return ! $this->successful();
    }

    /**
     * Get the exit code of the process.
     *
     * @return int
     */
    public function exitCode()
    {
        return $this->exitCode;
    }

    /**
     * Get the standard output of the process.
     *
     * @return string
     */
    public function output()
    {
        return $this->output;
    }

    /**
     * Determine if the output contains the given string.
     *
     * @param  string  $output
     * @return bool
     */
    public function seeInOutput(string $output)
    {
        return str_contains($this->output(), $output);
    }

    /**
     * Get the error output of the process.
     *
     * @return string
     */
    public function errorOutput()
    {
        return $this->errorOutput;
    }

    /**
     * Determine if the error output contains the given string.
     *
     * @param  string  $output
     * @return bool
     */
    public function seeInErrorOutput(string $output)
    {
        return str_contains($this->errorOutput(), $output);
    }

    /**
     * Throw an exception if the process failed.
     *
     * @param  callable|null  $callback
     * @return $this
     *
     * @throws \Illuminate\Process\Exceptions\ProcessFailedException
     */
    public function throw(?callable $callback = null)
    {
        if ($this->successful()) {
            return $this;
        }

        $exception = new ProcessFailedException($this);

        if ($callback) {
            $callback($this, $exception);
        }

        throw $exception;
    }

    /**
     * Throw an exception if the process failed and the given condition is true.
     *
     * @param  bool  $condition
     * @param  callable|null  $callback
     * @return $this
     *
     * @throws \Throwable
     */
    public function throwIf(bool $condition, ?callable $callback = null)
    {
        if ($condition) {
            return $this->throw($callback);
        }

        return $this;
    }
}
