<?php

namespace Illuminate\Notifications\Console;

use Illuminate\Console\MigrationGeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:notifications-table', aliases: ['notifications:table'])]
class NotificationTableCommand extends MigrationGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:notifications-table';

    /**
     * The console command name aliases.
     *
     * @var array
     */
    protected $aliases = ['notifications:table'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the notifications table';

    /**
     * Get the migration table name.
     *
     * @return string
     */
    protected function migrationTableName()
    {
        return 'notifications';
    }

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    protected function migrationStubFile()
    {
        return __DIR__.'/stubs/notifications.stub';
    }
}
