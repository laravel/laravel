<?php

namespace Illuminate\Database\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ConfigurationUrlParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use UnexpectedValueException;

#[AsCommand(name: 'db')]
class DbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db {connection? : The database connection that should be used}
               {--read : Connect to the read connection}
               {--write : Connect to the write connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a new database CLI session';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = $this->getConnection();

        if (! isset($connection['host']) && $connection['driver'] !== 'sqlite') {
            $this->components->error('No host specified for this database connection.');
            $this->line('  Use the <options=bold>[--read]</> and <options=bold>[--write]</> options to specify a read or write connection.');
            $this->newLine();

            return Command::FAILURE;
        }

        try {
            (new Process(
                array_merge([$command = $this->getCommand($connection)], $this->commandArguments($connection)),
                null,
                $this->commandEnvironment($connection)
            ))->setTimeout(null)->setTty(true)->mustRun(function ($type, $buffer) {
                $this->output->write($buffer);
            });
        } catch (ProcessFailedException $e) {
            throw_unless($e->getProcess()->getExitCode() === 127, $e);

            $this->error("{$command} not found in path.");

            return Command::FAILURE;
        }

        return 0;
    }

    /**
     * Get the database connection configuration.
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public function getConnection()
    {
        $connection = $this->laravel['config']['database.connections.'.
            (($db = $this->argument('connection')) ?? $this->laravel['config']['database.default'])
        ];

        if (empty($connection)) {
            throw new UnexpectedValueException("Invalid database connection [{$db}].");
        }

        if (! empty($connection['url'])) {
            $connection = (new ConfigurationUrlParser)->parseConfiguration($connection);
        }

        if ($this->option('read')) {
            if (is_array($connection['read']['host'])) {
                $connection['read']['host'] = $connection['read']['host'][0];
            }

            $connection = array_merge($connection, $connection['read']);
        } elseif ($this->option('write')) {
            if (is_array($connection['write']['host'])) {
                $connection['write']['host'] = $connection['write']['host'][0];
            }

            $connection = array_merge($connection, $connection['write']);
        }

        return $connection;
    }

    /**
     * Get the arguments for the database client command.
     *
     * @param  array  $connection
     * @return array
     */
    public function commandArguments(array $connection)
    {
        $driver = ucfirst($connection['driver']);

        return $this->{"get{$driver}Arguments"}($connection);
    }

    /**
     * Get the environment variables for the database client command.
     *
     * @param  array  $connection
     * @return array|null
     */
    public function commandEnvironment(array $connection)
    {
        $driver = ucfirst($connection['driver']);

        if (method_exists($this, "get{$driver}Environment")) {
            return $this->{"get{$driver}Environment"}($connection);
        }

        return null;
    }

    /**
     * Get the database client command to run.
     *
     * @param  array  $connection
     * @return string
     */
    public function getCommand(array $connection)
    {
        return [
            'mysql' => 'mysql',
            'mariadb' => 'mariadb',
            'pgsql' => 'psql',
            'sqlite' => 'sqlite3',
            'sqlsrv' => 'sqlcmd',
        ][$connection['driver']];
    }

    /**
     * Get the arguments for the MySQL CLI.
     *
     * @param  array  $connection
     * @return array
     */
    protected function getMysqlArguments(array $connection)
    {
        $optionalArguments = [
            'password' => '--password='.$connection['password'],
            'unix_socket' => '--socket='.($connection['unix_socket'] ?? ''),
            'charset' => '--default-character-set='.($connection['charset'] ?? ''),
        ];

        if (! $connection['password']) {
            unset($optionalArguments['password']);
        }

        return array_merge([
            '--host='.$connection['host'],
            '--port='.$connection['port'],
            '--user='.$connection['username'],
        ], $this->getOptionalArguments($optionalArguments, $connection), [$connection['database']]);
    }

    /**
     * Get the arguments for the MariaDB CLI.
     *
     * @param  array  $connection
     * @return array
     */
    protected function getMariaDbArguments(array $connection)
    {
        return $this->getMysqlArguments($connection);
    }

    /**
     * Get the arguments for the Postgres CLI.
     *
     * @param  array  $connection
     * @return array
     */
    protected function getPgsqlArguments(array $connection)
    {
        return [$connection['database']];
    }

    /**
     * Get the arguments for the SQLite CLI.
     *
     * @param  array  $connection
     * @return array
     */
    protected function getSqliteArguments(array $connection)
    {
        return [$connection['database']];
    }

    /**
     * Get the arguments for the SQL Server CLI.
     *
     * @param  array  $connection
     * @return array
     */
    protected function getSqlsrvArguments(array $connection)
    {
        return array_merge(...$this->getOptionalArguments([
            'database' => ['-d', $connection['database']],
            'username' => ['-U', $connection['username']],
            'password' => ['-P', $connection['password']],
            'host' => ['-S', 'tcp:'.$connection['host']
                        .($connection['port'] ? ','.$connection['port'] : ''), ],
            'trust_server_certificate' => ['-C'],
        ], $connection));
    }

    /**
     * Get the environment variables for the Postgres CLI.
     *
     * @param  array  $connection
     * @return array|null
     */
    protected function getPgsqlEnvironment(array $connection)
    {
        return array_merge(...$this->getOptionalArguments([
            'username' => ['PGUSER' => $connection['username']],
            'host' => ['PGHOST' => $connection['host']],
            'port' => ['PGPORT' => $connection['port']],
            'password' => ['PGPASSWORD' => $connection['password']],
        ], $connection));
    }

    /**
     * Get the optional arguments based on the connection configuration.
     *
     * @param  array  $args
     * @param  array  $connection
     * @return array
     */
    protected function getOptionalArguments(array $args, array $connection)
    {
        return array_values(array_filter($args, function ($key) use ($connection) {
            return ! empty($connection[$key]);
        }, ARRAY_FILTER_USE_KEY));
    }
}
