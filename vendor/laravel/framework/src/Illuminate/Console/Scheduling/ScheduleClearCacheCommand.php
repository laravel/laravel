<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'schedule:clear-cache')]
class ScheduleClearCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'schedule:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the cached mutex files created by scheduler';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function handle(Schedule $schedule)
    {
        $mutexCleared = false;

        foreach ($schedule->events($this->laravel) as $event) {
            if ($event->mutex->exists($event)) {
                $this->components->info(sprintf('Deleting mutex for [%s]', $event->command));

                $event->mutex->forget($event);

                $mutexCleared = true;
            }
        }

        if (! $mutexCleared) {
            $this->components->info('No mutex files were found.');
        }
    }
}
