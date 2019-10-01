<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Backoffice\RoleAddCommand::class,
        Commands\Backoffice\RolePermissionAddCommand::class,
        Commands\Backoffice\UserAddCommand::class,
        Commands\Backoffice\UserPermissionAddCommand::class,
        Commands\Backoffice\UserRoleAddCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * This is where you may define all of your Closure based console
     * commands. Each Closure is bound to a command instance allowing a
     * simple approach to interacting with each command's IO methods.
     */
    protected function commands()
    {
        $this->command('inspire', function () {
            /* @var \Illuminate\Console\Command $this */
            $this->comment(\Illuminate\Foundation\Inspiring::quote());
        })->describe('Display an inspiring quote');
    }
}
