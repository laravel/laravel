<?php

namespace Illuminate\Queue;

use Closure;
use Symfony\Component\Process\Process;

use function Illuminate\Support\artisan_binary;
use function Illuminate\Support\php_binary;

class Listener
{
    /**
     * The command working path.
     *
     * @var string
     */
    protected $commandPath;

    /**
     * The environment the workers should run under.
     *
     * @var string
     */
    protected $environment;

    /**
     * The amount of seconds to wait before polling the queue.
     *
     * @var int
     */
    protected $sleep = 3;

    /**
     * The number of times to try a job before logging it failed.
     *
     * @var int
     */
    protected $maxTries = 0;

    /**
     * The output handler callback.
     *
     * @var \Closure|null
     */
    protected $outputHandler;

    /**
     * Create a new queue listener.
     *
     * @param  string  $commandPath
     */
    public function __construct($commandPath)
    {
        $this->commandPath = $commandPath;
    }

    /**
     * Get the PHP binary.
     *
     * @return string
     */
    protected function phpBinary()
    {
        return php_binary();
    }

    /**
     * Get the Artisan binary.
     *
     * @return string
     */
    protected function artisanBinary()
    {
        return artisan_binary();
    }

    /**
     * Listen to the given queue connection.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return void
     */
    public function listen($connection, $queue, ListenerOptions $options)
    {
        $process = $this->makeProcess($connection, $queue, $options);

        while (true) {
            $this->runProcess($process, $options->memory);

            if ($options->rest) {
                sleep($options->rest);
            }
        }
    }

    /**
     * Create a new Symfony process for the worker.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess($connection, $queue, ListenerOptions $options)
    {
        $command = $this->createCommand(
            $connection,
            $queue,
            $options
        );

        // If the environment is set, we will append it to the command array so the
        // workers will run under the specified environment. Otherwise, they will
        // just run under the production environment which is not always right.
        if (isset($options->environment)) {
            $command = $this->addEnvironment($command, $options);
        }

        return new Process(
            $command,
            $this->commandPath,
            null,
            null,
            $options->timeout
        );
    }

    /**
     * Add the environment option to the given command.
     *
     * @param  array  $command
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return array
     */
    protected function addEnvironment($command, ListenerOptions $options)
    {
        return array_merge($command, ["--env={$options->environment}"]);
    }

    /**
     * Create the command with the listener options.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return array
     */
    protected function createCommand($connection, $queue, ListenerOptions $options)
    {
        return array_filter([
            $this->phpBinary(),
            $this->artisanBinary(),
            'queue:work',
            $connection,
            '--once',
            "--name={$options->name}",
            "--queue={$queue}",
            "--backoff={$options->backoff}",
            "--memory={$options->memory}",
            "--sleep={$options->sleep}",
            "--tries={$options->maxTries}",
            $options->force ? '--force' : null,
        ], function ($value) {
            return ! is_null($value);
        });
    }

    /**
     * Run the given process.
     *
     * @param  \Symfony\Component\Process\Process  $process
     * @param  int  $memory
     * @return void
     */
    public function runProcess(Process $process, $memory)
    {
        $process->run(function ($type, $line) {
            $this->handleWorkerOutput($type, $line);
        });

        // Once we have run the job we'll go check if the memory limit has been exceeded
        // for the script. If it has, we will kill this script so the process manager
        // will restart this with a clean slate of memory automatically on exiting.
        if ($this->memoryExceeded($memory)) {
            $this->stop();
        }
    }

    /**
     * Handle output from the worker process.
     *
     * @param  int  $type
     * @param  string  $line
     * @return void
     */
    protected function handleWorkerOutput($type, $line)
    {
        if (isset($this->outputHandler)) {
            call_user_func($this->outputHandler, $type, $line);
        }
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param  int  $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @return never
     */
    public function stop()
    {
        exit;
    }

    /**
     * Set the output handler callback.
     *
     * @param  \Closure  $outputHandler
     * @return void
     */
    public function setOutputHandler(Closure $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }
}
