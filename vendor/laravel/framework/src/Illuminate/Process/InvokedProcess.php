<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\InvokedProcess as InvokedProcessContract;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;
use Symfony\Component\Process\Process;

class InvokedProcess implements InvokedProcessContract
{
    /**
     * The underlying process instance.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * Create a new invoked process instance.
     *
     * @param  \Symfony\Component\Process\Process  $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * Get the process ID if the process is still running.
     *
     * @return int|null
     */
    public function id()
    {
        return $this->process->getPid();
    }

    /**
     * Get the command line for the process.
     *
     * @return string
     */
    public function command()
    {
        return $this->process->getCommandLine();
    }

    /**
     * Send a signal to the process.
     *
     * @param  int  $signal
     * @return $this
     */
    public function signal(int $signal)
    {
        $this->process->signal($signal);

        return $this;
    }

    /**
     * Stop the process if it is still running.
     *
     * @param  float  $timeout
     * @param  int|null  $signal
     * @return int|null
     */
    public function stop(float $timeout = 10, ?int $signal = null)
    {
        return $this->process->stop($timeout, $signal);
    }

    /**
     * Determine if the process is still running.
     *
     * @return bool
     */
    public function running()
    {
        return $this->process->isRunning();
    }

    /**
     * Get the standard output for the process.
     *
     * @return string
     */
    public function output()
    {
        return $this->process->getOutput();
    }

    /**
     * Get the error output for the process.
     *
     * @return string
     */
    public function errorOutput()
    {
        return $this->process->getErrorOutput();
    }

    /**
     * Get the latest standard output for the process.
     *
     * @return string
     */
    public function latestOutput()
    {
        return $this->process->getIncrementalOutput();
    }

    /**
     * Get the latest error output for the process.
     *
     * @return string
     */
    public function latestErrorOutput()
    {
        return $this->process->getIncrementalErrorOutput();
    }

    /**
     * Ensure that the process has not timed out.
     *
     * @return void
     *
     * @throws \Illuminate\Process\Exceptions\ProcessTimedOutException
     */
    public function ensureNotTimedOut()
    {
        try {
            $this->process->checkTimeout();
        } catch (SymfonyTimeoutException $e) {
            throw new ProcessTimedOutException($e, new ProcessResult($this->process));
        }
    }

    /**
     * Wait for the process to finish.
     *
     * @param  callable|null  $output
     * @return \Illuminate\Process\ProcessResult
     *
     * @throws \Illuminate\Process\Exceptions\ProcessTimedOutException
     */
    public function wait(?callable $output = null)
    {
        try {
            $this->process->wait($output);

            return new ProcessResult($this->process);
        } catch (SymfonyTimeoutException $e) {
            throw new ProcessTimedOutException($e, new ProcessResult($this->process));
        }
    }

    /**
     * Wait until the given callback returns true.
     *
     * @param  callable|null  $output
     * @return \Illuminate\Process\ProcessResult
     *
     * @throws \Illuminate\Process\Exceptions\ProcessTimedOutException
     */
    public function waitUntil(?callable $output = null)
    {
        try {
            $this->process->waitUntil($output);

            return new ProcessResult($this->process);
        } catch (SymfonyTimeoutException $e) {
            throw new ProcessTimedOutException($e, new ProcessResult($this->process));
        }
    }
}
