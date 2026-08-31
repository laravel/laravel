<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Factory as QueueManager;
use Illuminate\Queue\Console\Concerns\ParsesQueue;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:resume', aliases: ['queue:continue'])]
class ResumeCommand extends Command
{
    use ParsesQueue;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'queue:resume {queue : The name of the queue that should resume processing}';

    /**
     * The console command name aliases.
     *
     * @var list<string>
     */
    protected $aliases = ['queue:continue'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resume job processing for a paused queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(QueueManager $manager)
    {
        [$connection, $queue] = $this->parseQueue($this->argument('queue'));

        $manager->resume($connection, $queue);

        $this->components->info("Job processing on queue [{$connection}:{$queue}] has been resumed.");

        return 0;
    }
}
