<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Queue\Listener;
use Illuminate\Queue\ListenerOptions;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:listen')]
class ListenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'queue:listen
                            {connection? : The name of connection}
                            {--name=default : The name of the worker}
                            {--delay=0 : The number of seconds to delay failed jobs (Deprecated)}
                            {--backoff=0 : The number of seconds to wait before retrying a job that encountered an uncaught exception}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--queue= : The queue to listen on}
                            {--sleep=3 : The number of seconds to sleep when no job is available}
                            {--rest=0 : The number of seconds to rest between jobs}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=1 : The number of times to attempt a job before logging it failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to a given queue';

    /**
     * The queue listener instance.
     *
     * @var \Illuminate\Queue\Listener
     */
    protected $listener;

    /**
     * Create a new queue listen command.
     *
     * @param  \Illuminate\Queue\Listener  $listener
     */
    public function __construct(Listener $listener)
    {
        parent::__construct();

        $this->setOutputHandler($this->listener = $listener);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queue = $this->getQueue(
            $connection = $this->input->getArgument('connection')
        );

        $this->components->info(sprintf('Processing jobs from the [%s] %s.', $queue, (new Stringable('queue'))->plural(explode(',', $queue))));

        $this->listener->listen(
            $connection, $queue, $this->gatherOptions()
        );
    }

    /**
     * Get the name of the queue connection to listen on.
     *
     * @param  string  $connection
     * @return string
     */
    protected function getQueue($connection)
    {
        $connection = $connection ?: $this->laravel['config']['queue.default'];

        return $this->input->getOption('queue') ?: $this->laravel['config']->get(
            "queue.connections.{$connection}.queue", 'default'
        );
    }

    /**
     * Get the listener options for the command.
     *
     * @return \Illuminate\Queue\ListenerOptions
     */
    protected function gatherOptions()
    {
        $backoff = $this->hasOption('backoff')
            ? $this->option('backoff')
            : $this->option('delay');

        return new ListenerOptions(
            name: $this->option('name'),
            environment: $this->option('env'),
            backoff: $backoff,
            memory: $this->option('memory'),
            timeout: $this->option('timeout'),
            sleep: $this->option('sleep'),
            rest: $this->option('rest'),
            maxTries: $this->option('tries'),
            force: $this->option('force')
        );
    }

    /**
     * Set the options on the queue listener.
     *
     * @param  \Illuminate\Queue\Listener  $listener
     * @return void
     */
    protected function setOutputHandler(Listener $listener)
    {
        $listener->setOutputHandler(function ($type, $line) {
            $this->output->write($line);
        });
    }
}
