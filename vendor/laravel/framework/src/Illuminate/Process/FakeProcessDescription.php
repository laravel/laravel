<?php

namespace Illuminate\Process;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class FakeProcessDescription
{
    /**
     * The process' ID.
     *
     * @var int|null
     */
    public $processId = 1000;

    /**
     * All of the process' output in the order it was described.
     *
     * @var array
     */
    public $output = [];

    /**
     * The process' exit code.
     *
     * @var int
     */
    public $exitCode = 0;

    /**
     * The number of times the process should indicate that it is "running".
     *
     * @var int
     */
    public $runIterations = 0;

    /**
     * Specify the process ID that should be assigned to the process.
     *
     * @param  int  $processId
     * @return $this
     */
    public function id(int $processId)
    {
        $this->processId = $processId;

        return $this;
    }

    /**
     * Describe a line of standard output.
     *
     * @param  array|string  $output
     * @return $this
     */
    public function output(array|string $output)
    {
        if (is_array($output)) {
            (new Collection($output))->each(fn ($line) => $this->output($line));

            return $this;
        }

        $this->output[] = ['type' => 'out', 'buffer' => rtrim($output, "\n")."\n"];

        return $this;
    }

    /**
     * Describe a line of error output.
     *
     * @param  array|string  $output
     * @return $this
     */
    public function errorOutput(array|string $output)
    {
        if (is_array($output)) {
            (new Collection($output))->each(fn ($line) => $this->errorOutput($line));

            return $this;
        }

        $this->output[] = ['type' => 'err', 'buffer' => rtrim($output, "\n")."\n"];

        return $this;
    }

    /**
     * Replace the entire output buffer with the given string.
     *
     * @param  string  $output
     * @return $this
     */
    public function replaceOutput(string $output)
    {
        $this->output = (new Collection($this->output))
            ->reject(fn ($output) => $output['type'] === 'out')
            ->values()
            ->all();

        if (strlen($output) > 0) {
            $this->output[] = [
                'type' => 'out',
                'buffer' => rtrim($output, "\n")."\n",
            ];
        }

        return $this;
    }

    /**
     * Replace the entire error output buffer with the given string.
     *
     * @param  string  $output
     * @return $this
     */
    public function replaceErrorOutput(string $output)
    {
        $this->output = (new Collection($this->output))
            ->reject(fn ($output) => $output['type'] === 'err')
            ->values()
            ->all();

        if (strlen($output) > 0) {
            $this->output[] = [
                'type' => 'err',
                'buffer' => rtrim($output, "\n")."\n",
            ];
        }

        return $this;
    }

    /**
     * Specify the process exit code.
     *
     * @param  int  $exitCode
     * @return $this
     */
    public function exitCode(int $exitCode)
    {
        $this->exitCode = $exitCode;

        return $this;
    }

    /**
     * Specify how many times the "isRunning" method should return "true".
     *
     * @param  int  $iterations
     * @return $this
     */
    public function iterations(int $iterations)
    {
        return $this->runsFor(iterations: $iterations);
    }

    /**
     * Specify how many times the "isRunning" method should return "true".
     *
     * @param  int  $iterations
     * @return $this
     */
    public function runsFor(int $iterations)
    {
        $this->runIterations = $iterations;

        return $this;
    }

    /**
     * Turn the fake process description into an actual process.
     *
     * @param  string  $command
     * @return \Symfony\Component\Process\Process
     */
    public function toSymfonyProcess(string $command)
    {
        return Process::fromShellCommandline($command);
    }

    /**
     * Convert the process description into a process result.
     *
     * @param  string  $command
     * @return \Illuminate\Contracts\Process\ProcessResult
     */
    public function toProcessResult(string $command)
    {
        return new FakeProcessResult(
            command: $command,
            exitCode: $this->exitCode,
            output: $this->resolveOutput(),
            errorOutput: $this->resolveErrorOutput(),
        );
    }

    /**
     * Resolve the standard output as a string.
     *
     * @return string
     */
    protected function resolveOutput()
    {
        $output = (new Collection($this->output))
            ->filter(fn ($output) => $output['type'] === 'out');

        return $output->isNotEmpty()
            ? rtrim($output->map->buffer->implode(''), "\n")."\n"
            : '';
    }

    /**
     * Resolve the error output as a string.
     *
     * @return string
     */
    protected function resolveErrorOutput()
    {
        $output = (new Collection($this->output))
            ->filter(fn ($output) => $output['type'] === 'err');

        return $output->isNotEmpty()
            ? rtrim($output->map->buffer->implode(''), "\n")."\n"
            : '';
    }
}
