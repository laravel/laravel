<?php

namespace Illuminate\Database\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Prohibitable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'db:wipe')]
class WipeCommand extends Command
{
    use ConfirmableTrait, Prohibitable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:wipe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables, views, and types';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->isProhibited() ||
            ! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $database = $this->input->getOption('database');

        if ($this->option('drop-views')) {
            $this->dropAllViews($database);

            $this->components->info('Dropped all views successfully.');
        }

        $this->dropAllTables($database);

        $this->components->info('Dropped all tables successfully.');

        if ($this->option('drop-types')) {
            $this->dropAllTypes($database);

            $this->components->info('Dropped all types successfully.');
        }

        $this->flushDatabaseConnection($database);

        return 0;
    }

    /**
     * Drop all of the database tables.
     *
     * @param  string  $database
     * @return void
     */
    protected function dropAllTables($database)
    {
        $this->laravel['db']->connection($database)
            ->getSchemaBuilder()
            ->dropAllTables();
    }

    /**
     * Drop all of the database views.
     *
     * @param  string  $database
     * @return void
     */
    protected function dropAllViews($database)
    {
        $this->laravel['db']->connection($database)
            ->getSchemaBuilder()
            ->dropAllViews();
    }

    /**
     * Drop all of the database types.
     *
     * @param  string  $database
     * @return void
     */
    protected function dropAllTypes($database)
    {
        $this->laravel['db']->connection($database)
            ->getSchemaBuilder()
            ->dropAllTypes();
    }

    /**
     * Flush the given database connection.
     *
     * @param  string  $database
     * @return void
     */
    protected function flushDatabaseConnection($database)
    {
        $this->laravel['db']->connection($database)->disconnect();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            ['drop-views', null, InputOption::VALUE_NONE, 'Drop all tables and views'],
            ['drop-types', null, InputOption::VALUE_NONE, 'Drop all tables and types (Postgres only)'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }
}
