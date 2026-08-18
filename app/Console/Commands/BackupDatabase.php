<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'mealflow:backup {--keep=14 : Days of backups to retain}';
    protected $description = 'Create a timestamped database backup and prune backups older than --keep days';

    public function handle(): int
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);
        $stamp = now()->format('Y-m-d_His');

        if ($connection === 'sqlite') {
            $source = $config['database'];
            if (! is_file($source)) {
                $this->error("SQLite database file not found at [$source]. Nothing to back up.");
                return self::FAILURE;
            }
            $dest = "$dir/backup-$stamp.sqlite";
            File::copy($source, $dest);
        } elseif (in_array($connection, ['mysql', 'mariadb'], true)) {
            $dest = "$dir/backup-$stamp.sql";
            $process = new Process([
                'mysqldump', '-h', (string) $config['host'], '-P', (string) $config['port'],
                '-u', (string) $config['username'], '--password='.$config['password'], $config['database'],
            ]);
            $process->setTimeout(300);
            $process->run();
            if (! $process->isSuccessful()) {
                $this->error('mysqldump failed: '.$process->getErrorOutput());
                return self::FAILURE;
            }
            File::put($dest, $process->getOutput());
        } else {
            $this->error("Automated backup is not implemented for the [$connection] connection.");
            return self::FAILURE;
        }

        $this->info("Backup written to $dest");

        $keepDays = (int) $this->option('keep');
        $cutoff = now()->subDays($keepDays)->timestamp;
        foreach (File::files($dir) as $file) {
            if (str_starts_with($file->getFilename(), 'backup-') && $file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
            }
        }

        return self::SUCCESS;
    }
}
