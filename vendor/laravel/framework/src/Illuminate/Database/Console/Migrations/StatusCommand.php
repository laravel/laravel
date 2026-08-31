<?php

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'migrate:status')]
class StatusCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each migration';

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
     * @return int|null
     */
    public function handle()
    {
        return $this->migrator->usingConnection($this->option('database'), function () {
            if (! $this->migrator->repositoryExists()) {
                $this->components->error('Migration table not found.');

                return 1;
            }

            $ran = $this->migrator->getRepository()->getRan();

            $batches = $this->migrator->getRepository()->getMigrationBatches();

            $migrations = $this->getStatusFor($ran, $batches)
                ->when($this->option('pending') !== false, fn ($collection) => $collection->filter(function ($migration) {
                    return (new Stringable($migration[1]))->contains('Pending');
                }));

            if (count($migrations) > 0) {
                $this->newLine();

                $this->components->twoColumnDetail('<fg=gray>Migration name</>', '<fg=gray>Batch / Status</>');

                $migrations
                    ->each(
                        fn ($migration) => $this->components->twoColumnDetail($migration[0], $migration[1])
                    );

                $this->newLine();
            } elseif ($this->option('pending') !== false) {
                $this->components->info('No pending migrations');
            } else {
                $this->components->info('No migrations found');
            }

            if ($this->option('pending') && $migrations->some(fn ($m) => (new Stringable($m[1]))->contains('Pending'))) {
                return $this->option('pending');
            }
        });
    }

    /**
     * Get the status for the given run migrations.
     *
     * @param  array  $ran
     * @param  array  $batches
     * @return \Illuminate\Support\Collection
     */
    protected function getStatusFor(array $ran, array $batches)
    {
        return (new Collection($this->getAllMigrationFiles()))
            ->map(function ($migration) use ($ran, $batches) {
                $migrationName = $this->migrator->getMigrationName($migration);

                $status = in_array($migrationName, $ran)
                    ? '<fg=green;options=bold>Ran</>'
                    : '<fg=yellow;options=bold>Pending</>';

                if (in_array($migrationName, $ran)) {
                    $status = '['.$batches[$migrationName].'] '.$status;
                }

                return [$migrationName, $status];
            });
    }

    /**
     * Get an array of all of the migration files.
     *
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        return $this->migrator->getMigrationFiles($this->getMigrationPaths());
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
            ['pending', null, InputOption::VALUE_OPTIONAL, 'Only list pending migrations', false],
            ['path', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) to the migrations files to use'],
            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],
        ];
    }
}
