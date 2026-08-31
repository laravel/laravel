<?php

namespace Illuminate\Console\Scheduling;

use Exception;
use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Sleep;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'schedule:run')]
class ScheduleRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:run {--whisper : Do not output message indicating that no jobs were ready to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduled commands';

    /**
     * The schedule instance.
     *
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * The 24 hour timestamp this scheduler command started running.
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $startedAt;

    /**
     * Check if any events ran.
     *
     * @var bool
     */
    protected $eventsRan = false;

    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * The exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $handler;

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The PHP binary used by the command.
     *
     * @var string
     */
    protected $phpBinary;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->startedAt = Date::now();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $handler
     * @return void
     */
    public function handle(Schedule $schedule, Dispatcher $dispatcher, Cache $cache, ExceptionHandler $handler)
    {
        $this->schedule = $schedule;
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
        $this->handler = $handler;
        $this->phpBinary = Application::phpBinary();

        $events = $this->schedule->dueEvents($this->laravel);

        if ($events->contains->isRepeatable()) {
            $this->clearInterruptSignal();
        }

        foreach ($events as $event) {
            if (! $event->filtersPass($this->laravel)) {
                $this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

                continue;
            }

            if (! $this->eventsRan) {
                $this->newLine();
            }

            if ($event->onOneServer) {
                $this->runSingleServerEvent($event);
            } else {
                $this->runEvent($event);
            }

            $this->eventsRan = true;
        }

        if ($events->contains->isRepeatable()) {
            $this->repeatEvents($events->filter->isRepeatable());
        }

        if (! $this->eventsRan) {
            if (! $this->option('whisper')) {
                $this->components->info('No scheduled commands are ready to run.');
            }
        } else {
            $this->newLine();
        }
    }

    /**
     * Run the given single server event.
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runSingleServerEvent($event)
    {
        if ($this->schedule->serverShouldRun($event, $this->startedAt)) {
            $this->runEvent($event);
        } else {
            $this->components->info(sprintf(
                'Skipping [%s] because the command already ran on another server.', $event->getSummaryForDisplay()
            ));
        }
    }

    /**
     * Run the given event.
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runEvent($event)
    {
        $summary = $event->getSummaryForDisplay();

        $command = $event instanceof CallbackEvent
            ? $summary
            : trim(str_replace($this->phpBinary, '', $event->command));

        $description = sprintf(
            '<fg=gray>%s</> Running [%s]%s',
            Carbon::now()->format('Y-m-d H:i:s'),
            $command,
            $event->runInBackground ? ' in background' : '',
        );

        $this->components->task($description, function () use ($event) {
            $this->dispatcher->dispatch(new ScheduledTaskStarting($event));

            $start = microtime(true);

            try {
                $event->run($this->laravel);

                $this->dispatcher->dispatch(new ScheduledTaskFinished(
                    $event,
                    round(microtime(true) - $start, 2)
                ));

                $this->eventsRan = true;

                if ($event->exitCode != 0 && ! $event->runInBackground) {
                    throw new Exception("Scheduled command [{$event->command}] failed with exit code [{$event->exitCode}].");
                }
            } catch (Throwable $e) {
                $this->dispatcher->dispatch(new ScheduledTaskFailed($event, $e));

                $this->handler->report($e);
            }

            return $event->exitCode == 0;
        });

        if (! $event instanceof CallbackEvent) {
            $this->components->bulletList([
                $event->getSummaryForDisplay(),
            ]);
        }
    }

    /**
     * Run the given repeating events.
     *
     * @param  \Illuminate\Support\Collection<\Illuminate\Console\Scheduling\Event>  $events
     * @return void
     */
    protected function repeatEvents($events)
    {
        $hasEnteredMaintenanceMode = false;

        while (Date::now()->lte($this->startedAt->endOfMinute())) {
            foreach ($events as $event) {
                if ($this->shouldInterrupt()) {
                    return;
                }

                if (! $event->shouldRepeatNow()) {
                    continue;
                }

                $hasEnteredMaintenanceMode = $hasEnteredMaintenanceMode || $this->laravel->isDownForMaintenance();

                if ($hasEnteredMaintenanceMode && ! $event->runsInMaintenanceMode()) {
                    continue;
                }

                if (! $event->filtersPass($this->laravel)) {
                    $this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

                    continue;
                }

                if ($event->onOneServer) {
                    $this->runSingleServerEvent($event);
                } else {
                    $this->runEvent($event);
                }

                $this->eventsRan = true;
            }

            Sleep::usleep(100_000);
        }
    }

    /**
     * Determine if the schedule run should be interrupted.
     *
     * @return bool
     */
    protected function shouldInterrupt()
    {
        return $this->cache->get('illuminate:schedule:interrupt', false);
    }

    /**
     * Ensure the interrupt signal is cleared.
     *
     * @return void
     */
    protected function clearInterruptSignal()
    {
        $this->cache->forget('illuminate:schedule:interrupt');
    }
}
