<?php

namespace Illuminate\Database\Console;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'db:show')]
class ShowCommand extends DatabaseInspectionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:show {--database= : The database connection}
                {--json : Output the database information as JSON}
                {--counts : Show the table row count <bg=red;options=bold> Note: This can be slow on large databases </>}
                {--views : Show the database views <bg=red;options=bold> Note: This can be slow on large databases </>}
                {--types : Show the user defined types}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display information about the given database';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $connections
     * @return int
     */
    public function handle(ConnectionResolverInterface $connections)
    {
        $connection = $connections->connection($database = $this->input->getOption('database'));

        $schema = $connection->getSchemaBuilder();

        $data = [
            'platform' => [
                'config' => $this->getConfigFromDatabase($database),
                'name' => $connection->getDriverTitle(),
                'connection' => $connection->getName(),
                'version' => $connection->getServerVersion(),
                'open_connections' => $connection->threadCount(),
            ],
            'tables' => $this->tables($connection, $schema),
        ];

        if ($this->option('views')) {
            $data['views'] = $this->views($connection, $schema);
        }

        if ($this->option('types')) {
            $data['types'] = $this->types($connection, $schema);
        }

        $this->display($data);

        return 0;
    }

    /**
     * Get information regarding the tables within the database.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Database\Schema\Builder  $schema
     * @return \Illuminate\Support\Collection
     */
    protected function tables(ConnectionInterface $connection, Builder $schema)
    {
        return (new Collection($schema->getTables()))->map(fn ($table) => [
            'table' => $table['name'],
            'schema' => $table['schema'],
            'schema_qualified_name' => $table['schema_qualified_name'],
            'size' => $table['size'],
            'rows' => $this->option('counts')
                ? $connection->withoutTablePrefix(fn ($connection) => $connection->table($table['schema_qualified_name'])->count())
                : null,
            'engine' => $table['engine'],
            'collation' => $table['collation'],
            'comment' => $table['comment'],
        ]);
    }

    /**
     * Get information regarding the views within the database.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Database\Schema\Builder  $schema
     * @return \Illuminate\Support\Collection
     */
    protected function views(ConnectionInterface $connection, Builder $schema)
    {
        return (new Collection($schema->getViews()))
            ->map(fn ($view) => [
                'view' => $view['name'],
                'schema' => $view['schema'],
                'rows' => $connection->withoutTablePrefix(fn ($connection) => $connection->table($view['schema_qualified_name'])->count()),
            ]);
    }

    /**
     * Get information regarding the user-defined types within the database.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Database\Schema\Builder  $schema
     * @return \Illuminate\Support\Collection
     */
    protected function types(ConnectionInterface $connection, Builder $schema)
    {
        return (new Collection($schema->getTypes()))
            ->map(fn ($type) => [
                'name' => $type['name'],
                'schema' => $type['schema'],
                'type' => $type['type'],
                'category' => $type['category'],
            ]);
    }

    /**
     * Render the database information.
     *
     * @param  array  $data
     * @return void
     */
    protected function display(array $data)
    {
        $this->option('json') ? $this->displayJson($data) : $this->displayForCli($data);
    }

    /**
     * Render the database information as JSON.
     *
     * @param  array  $data
     * @return void
     */
    protected function displayJson(array $data)
    {
        $this->output->writeln(json_encode($data));
    }

    /**
     * Render the database information formatted for the CLI.
     *
     * @param  array  $data
     * @return void
     */
    protected function displayForCli(array $data)
    {
        $platform = $data['platform'];
        $tables = $data['tables'];
        $views = $data['views'] ?? null;
        $types = $data['types'] ?? null;

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>'.$platform['name'].'</>', $platform['version']);
        $this->components->twoColumnDetail('Connection', $platform['connection']);
        $this->components->twoColumnDetail('Database', Arr::get($platform['config'], 'database'));
        $this->components->twoColumnDetail('Host', Arr::get($platform['config'], 'host'));
        $this->components->twoColumnDetail('Port', Arr::get($platform['config'], 'port'));
        $this->components->twoColumnDetail('Username', Arr::get($platform['config'], 'username'));
        $this->components->twoColumnDetail('URL', Arr::get($platform['config'], 'url'));
        $this->components->twoColumnDetail('Open Connections', $platform['open_connections']);
        $this->components->twoColumnDetail('Tables', $tables->count());

        if ($tableSizeSum = $tables->sum('size')) {
            $this->components->twoColumnDetail('Total Size', Number::fileSize($tableSizeSum, 2));
        }

        $this->newLine();

        if ($tables->isNotEmpty()) {
            $hasSchema = ! is_null($tables->first()['schema']);

            $this->components->twoColumnDetail(
                ($hasSchema ? '<fg=green;options=bold>Schema</> <fg=gray;options=bold>/</> ' : '').'<fg=green;options=bold>Table</>',
                'Size'.($this->option('counts') ? ' <fg=gray;options=bold>/</> <fg=yellow;options=bold>Rows</>' : '')
            );

            $tables->each(function ($table) {
                $tableSize = is_null($table['size']) ? null : Number::fileSize($table['size'], 2);

                $this->components->twoColumnDetail(
                    ($table['schema'] ? $table['schema'].' <fg=gray;options=bold>/</> ' : '').$table['table'].($this->output->isVerbose() ? ' <fg=gray>'.$table['engine'].'</>' : null),
                    ($tableSize ?? '—').($this->option('counts') ? ' <fg=gray;options=bold>/</> <fg=yellow;options=bold>'.Number::format($table['rows']).'</>' : '')
                );

                if ($this->output->isVerbose()) {
                    if ($table['comment']) {
                        $this->components->bulletList([
                            $table['comment'],
                        ]);
                    }
                }
            });

            $this->newLine();
        }

        if ($views && $views->isNotEmpty()) {
            $hasSchema = ! is_null($views->first()['schema']);

            $this->components->twoColumnDetail(
                ($hasSchema ? '<fg=green;options=bold>Schema</> <fg=gray;options=bold>/</> ' : '').'<fg=green;options=bold>View</>',
                '<fg=green;options=bold>Rows</>'
            );

            $views->each(fn ($view) => $this->components->twoColumnDetail(
                ($view['schema'] ? $view['schema'].' <fg=gray;options=bold>/</> ' : '').$view['view'],
                Number::format($view['rows'])
            ));

            $this->newLine();
        }

        if ($types && $types->isNotEmpty()) {
            $hasSchema = ! is_null($types->first()['schema']);

            $this->components->twoColumnDetail(
                ($hasSchema ? '<fg=green;options=bold>Schema</> <fg=gray;options=bold>/</> ' : '').'<fg=green;options=bold>Type</>',
                '<fg=green;options=bold>Type</> <fg=gray;options=bold>/</> <fg=green;options=bold>Category</>'
            );

            $types->each(fn ($type) => $this->components->twoColumnDetail(
                ($type['schema'] ? $type['schema'].' <fg=gray;options=bold>/</> ' : '').$type['name'],
                $type['type'].' <fg=gray;options=bold>/</> '.$type['category']
            ));

            $this->newLine();
        }
    }
}
