<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\InvokedProcess as InvokedProcessContract;

class FakeInvokedProcess implements InvokedProcessContract
{
    /**
     * The command being faked.
     *
     * @var string
     */
    protected $command;

    /**
     * The underlying process description.
     *
     * @var \Illuminate\Process\FakeProcessDescription
     */
    protected $process;

    /**
     * The signals that have been received.
     *
     * @var array
     */
    protected $receivedSignals = [];

    /**
     * The number of times the process should indicate that it is "running".
     *
     * @var int|null
     */
    protected $remainingRunIterations;

    /**
     * The general output handler callback.
     *
     * @var callable|null
     */
    protected $outputHandler;

    /**
     * The current output's index.
     *
     * @var int
     */
    protected $nextOutputIndex = 0;

    /**
     * The current error output's index.
     *
     * @var int
     */
    protected $nextErrorOutputIndex = 0;

    /**
     * Create a new invoked process instance.
     *
     * @param  string  $command
     * @param  \Illuminate\Process\FakeProcessDescription  $process
     */
    public function __construct(string $command, FakeProcessDescription $process)
    {
        $this->command = $command;
        $this->process = $process;
    }

    /**
     * Get the process ID if the process is still running.
     *
     * @return int|null
     */
    public function id()
    {
        $this->invokeOutputHandlerWithNextLineOfOutput();

        return $this->process->processId;
    }

    /**
     * Get the command line for the process.
     *
     * @return string
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * Send a signal to the process.
     *
     * @param  int  $signal
     * @return $this
     */
    public function signal(int $signal)
    {
        $this->invokeOutputHandlerWithNextLineOfOutput();

        $this->receivedSignals[] = $signal;

        return $this;
    }

    /**
     * Determine if the process has received the given signal.
     *
     * @param  int  $signal
     * @return bool
     */
    public function hasReceivedSignal(int $signal)
    {
        return in_array($signal, $this->receivedSignals);
    }

    /**
     * Determine if the process is still running.
     *
     * @return bool
     */
    public function running()
    {
        $this->invokeOutputHandlerWithNextLineOfOutput();

        $this->remainingRunIterations = is_null($this->remainingRunIterations)
            ? $this->process->runIterations
            : $this->remainingRunIterations;

        if ($this->remainingRunIterations === 0) {
            while ($this->invokeOutputHandlerWithNextLineOfOutput()) {
            }

            return false;
        }

        $this->remainingRunIterations = $this->remainingRunIterations - 1;

        return true;
    }

    /**
     * Invoke the asynchronous output handler with the next single line of output if necessary.
     *
     * @return array|false
     */
    protected function invokeOutputHandlerWithNextLineOfOutput()
    {
        if (! $this->outputHandler) {
            return false;
        }

        [$outputCount, $outputStartingPoint] = [
            count($this->process->output),
            min($this->nextOutputIndex, $this->nextErrorOutputIndex),
        ];

        for ($i = $outputStartingPoint; $i < $outputCount; $i++) {
            $currentOutput = $this->process->output[$i];

            if ($currentOutput['type'] === 'out' && $i >= $this->nextOutputIndex) {
                call_user_func($this->outputHandler, 'out', $currentOutput['buffer']);
                $this->nextOutputIndex = $i + 1;

                return $currentOutput;
            } elseif ($currentOutput['type'] === 'err' && $i >= $this->nextErrorOutputIndex) {
                call_user_func($this->outputHandler, 'err', $currentOutput['buffer']);
                $this->nextErrorOutputIndex = $i + 1;

                return $currentOutput;
            }
        }

        return false;
    }

    /**
     * Get the standard output for the process.
     *
     * @return string
     */
    public function output()
    {
        $this->latestOutput();

        $output = [];

        for ($i = 0; $i < $this->nextOutputIndex; $i++) {
            if ($this->process->output[$i]['type'] === 'out') {
                $output[] = $this->process->output[$i]['buffer'];
            }
        }

        return rtrim(implode('', $output), "\n")."\n";
    }

    /**
     * Get the error output for the process.
     *
     * @return string
     */
    public function errorOutput()
    {
        $this->latestErrorOutput();

        $output = [];

        for ($i = 0; $i < $this->nextErrorOutputIndex; $i++) {
            if ($this->process->output[$i]['type'] === 'err') {
                $output[] = $this->process->output[$i]['buffer'];
            }
        }

        return rtrim(implode('', $output), "\n")."\n";
    }

    /**
     * Get the latest standard output for the process.
     *
     * @return string
     */
    public function latestOutput()
    {
        $outputCount = count($this->process->output);

        for ($i = $this->nextOutputIndex; $i < $outputCount; $i++) {
            if ($this->process->output[$i]['type'] === 'out') {
                $output = $this->process->output[$i]['buffer'];
                $this->nextOutputIndex = $i + 1;

                break;
            }

            $this->nextOutputIndex = $i + 1;
        }

        return $output ?? '';
    }

    /**
     * Get the latest error output for the process.
     *
     * @return string
     */
    public function latestErrorOutput()
    {
        $outputCount = count($this->process->output);

        for ($i = $this->nextErrorOutputIndex; $i < $outputCount; $i++) {
            if ($this->process->output[$i]['type'] === 'err') {
                $output = $this->process->output[$i]['buffer'];
                $this->nextErrorOutputIndex = $i + 1;

                break;
            }

            $this->nextErrorOutputIndex = $i + 1;
        }

        return $output ?? '';
    }

    /**
     * Wait for the process to finish.
     *
     * @param  callable|null  $output
     * @return \Illuminate\Contracts\Process\ProcessResult
     */
    public function wait(?callable $output = null)
    {
        $this->outputHandler = $output ?: $this->outputHandler;

        if (! $this->outputHandler) {
            $this->remainingRunIterations = 0;

            return $this->predictProcessResult();
        }

        while ($this->invokeOutputHandlerWithNextLineOfOutput()) {
            //
        }

        $this->remainingRunIterations = 0;

        return $this->process->toProcessResult($this->command);
    }

    /**
     * Wait until the given callback returns true.
     *
     * @param  callable|null  $output
     * @return \Illuminate\Contracts\Process\ProcessResult
     */
    public function waitUntil(?callable $output = null)
    {
        $shouldStop = false;

        $this->outputHandler = $output
            ? function ($type, $buffer) use ($output, &$shouldStop) {
                $shouldStop = call_user_func($output, $type, $buffer);
            }
        : $this->outputHandler;

        if (! $this->outputHandler) {
            $this->remainingRunIterations = 0;

            return $this->predictProcessResult();
        }

        while ($this->running() && ! $shouldStop) {
            //
        }

        $this->remainingRunIterations = 0;

        return $this->process->toProcessResult($this->command);
    }

    /**
     * Get the ultimate process result that will be returned by this "process".
     *
     * @return \Illuminate\Contracts\Process\ProcessResult
     */
    public function predictProcessResult()
    {
        return $this->process->toProcessResult($this->command);
    }

    /**
     * Set the general output handler for the fake invoked process.
     *
     * @param  callable|null  $outputHandler
     * @return $this
     */
    public function withOutputHandler(?callable $outputHandler)
    {
        $this->outputHandler = $outputHandler;

        return $this;
    }
}
