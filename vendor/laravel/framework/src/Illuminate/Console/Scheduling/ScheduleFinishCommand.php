<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Console\Command;
use Illuminate\Console\Events\ScheduledBackgroundTaskFinished;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'schedule:finish')]
class ScheduleFinishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'schedule:finish {id} {code=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle the completion of a scheduled command';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function handle(Schedule $schedule)
    {
        (new Collection($schedule->events()))
            ->filter(fn ($value) => $value->mutexName() == $this->argument('id'))
            ->each(function ($event) {
                $event->finish($this->laravel, $this->argument('code'));

                $this->laravel->make(Dispatcher::class)->dispatch(new ScheduledBackgroundTaskFinished($event));
            });
    }
}
