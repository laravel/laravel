<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the commands that should be registered by default.
     *
     * @return array
     */
    protected function getCommands()
    {
        return array_merge(parent::getCommands(), [
            \App\Console\Commands\SafeMigrate::class,
            \App\Console\Commands\DatabaseBackup::class,
            \App\Console\Commands\SeedDemoData::class,
        ]);
    }
}
