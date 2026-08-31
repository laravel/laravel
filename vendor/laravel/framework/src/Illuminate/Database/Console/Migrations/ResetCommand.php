<?php

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Prohibitable;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'migrate:reset')]
class ResetCommand extends BaseCommand
{
    use ConfirmableTrait, Prohibitable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database migrations';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Create a new migration rollback command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

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

        return $this->migrator->usingConnection($this->option('database'), function () {
            // First, we'll make sure that the migration table actually exists before we
            // start trying to rollback and re-run all of the migrations. If it's not
            // present we'll just bail out with an info message for the developers.
            if (! $this->migrator->repositoryExists()) {
                return $this->components->warn('Migration table not found.');
            }

            $this->migrator->setOutput($this->output)->reset(
                $this->getMigrationPaths(), $this->option('pretend')
            );
        });
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

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],

            ['path', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) to the migrations files to be executed'],

            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],

            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run'],
        ];
    }
}
