<?php

namespace Illuminate\Session\Console;

use Illuminate\Console\MigrationGeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

use function Illuminate\Filesystem\join_paths;

#[AsCommand(name: 'make:session-table', aliases: ['session:table'])]
class SessionTableCommand extends MigrationGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:session-table';

    /**
     * The console command name aliases.
     *
     * @var array
     */
    protected $aliases = ['session:table'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the session database table';

    /**
     * Get the migration table name.
     *
     * @return string
     */
    protected function migrationTableName()
    {
        return 'sessions';
    }

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    protected function migrationStubFile()
    {
        return __DIR__.'/stubs/database.stub';
    }

    /**
     * Determine whether a migration for the table already exists.
     *
     * @param  string  $table
     * @return bool
     */
    protected function migrationExists($table)
    {
        foreach ([
            join_paths($this->laravel->databasePath('migrations'), '*_*_*_*_create_'.$table.'_table.php'),
            join_paths($this->laravel->databasePath('migrations'), '0001_01_01_000000_create_users_table.php'),
        ] as $path) {
            if (count($this->files->glob($path)) !== 0) {
                return true;
            }
        }

        return false;
    }
}
