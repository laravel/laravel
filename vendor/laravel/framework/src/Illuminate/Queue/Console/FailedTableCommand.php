<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\MigrationGeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

use function Illuminate\Filesystem\join_paths;

#[AsCommand(name: 'make:queue-failed-table', aliases: ['queue:failed-table'])]
class FailedTableCommand extends MigrationGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:queue-failed-table';

    /**
     * The console command name aliases.
     *
     * @var array
     */
    protected $aliases = ['queue:failed-table'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the failed queue jobs database table';

    /**
     * Get the migration table name.
     *
     * @return string
     */
    protected function migrationTableName()
    {
        return $this->laravel['config']['queue.failed.table'];
    }

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    protected function migrationStubFile()
    {
        return __DIR__.'/stubs/failed_jobs.stub';
    }

    /**
     * Determine whether a migration for the table already exists.
     *
     * @param  string  $table
     * @return bool
     */
    protected function migrationExists($table)
    {
        if ($table !== 'failed_jobs') {
            return parent::migrationExists($table);
        }

        foreach ([
            join_paths($this->laravel->databasePath('migrations'), '*_*_*_*_create_'.$table.'_table.php'),
            join_paths($this->laravel->databasePath('migrations'), '0001_01_01_000002_create_jobs_table.php'),
        ] as $path) {
            if (count($this->files->glob($path)) !== 0) {
                return true;
            }
        }

        return false;
    }
}
