<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

// ✅ Import command
use App\Console\Commands\ScanLocalDisk;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        ScanLocalDisk::class, // ✅ Daftarkan command-mu di sini
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('scan:localdisk')->everyThirtyMinutes();
    }

    protected function commands(): void
    {
        // Opsi lain untuk load otomatis dari direktori Commands (tidak wajib di Laravel 11)
        $this->load(__DIR__.'/Commands');
    }
}
