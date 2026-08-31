<?php

namespace Illuminate\Database\Schema;

use Exception;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Pdo\Mysql;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class MySqlSchemaState extends SchemaState
{
    /**
     * Dump the database's schema into a file.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $path
     * @return void
     */
    public function dump(Connection $connection, $path)
    {
        $this->executeDumpProcess($this->makeProcess(
            $this->baseDumpCommand().' --routines --result-file="${:LARAVEL_LOAD_PATH}" --no-data'
        ), $this->output, array_merge($this->baseVariables($this->connection->getConfig()), [
            'LARAVEL_LOAD_PATH' => $path,
        ]));

        $this->removeAutoIncrementingState($path);

        if ($this->hasMigrationTable()) {
            $this->appendMigrationData($path);
        }
    }

    /**
     * Remove the auto-incrementing state from the given schema dump.
     *
     * @param  string  $path
     * @return void
     */
    protected function removeAutoIncrementingState(string $path)
    {
        $this->files->put($path, preg_replace(
            '/\s+AUTO_INCREMENT=[0-9]+/iu',
            '',
            $this->files->get($path)
        ));
    }

    /**
     * Append the migration data to the schema dump.
     *
     * @param  string  $path
     * @return void
     */
    protected function appendMigrationData(string $path)
    {
        $process = $this->executeDumpProcess($this->makeProcess(
            $this->baseDumpCommand().' '.$this->getMigrationTable().' --no-create-info --skip-extended-insert --skip-routines --compact --complete-insert'
        ), null, array_merge($this->baseVariables($this->connection->getConfig()), [
            //
        ]));

        $this->files->append($path, $process->getOutput());
    }

    /**
     * Load the given schema file into the database.
     *
     * @param  string  $path
     * @return void
     */
    public function load($path)
    {
        $versionInfo = $this->detectClientVersion();

        $command = 'mysql '.$this->connectionString($versionInfo).' --database="${:LARAVEL_LOAD_DATABASE}" < "${:LARAVEL_LOAD_PATH}"';

        $process = $this->makeProcess($command)->setTimeout(null);

        $process->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [
            'LARAVEL_LOAD_PATH' => $path,
        ]));
    }

    /**
     * Get the base dump command arguments for MySQL as a string.
     *
     * @return string
     */
    protected function baseDumpCommand()
    {
        $versionInfo = $this->detectClientVersion();

        $command = 'mysqldump '.$this->connectionString($versionInfo).' --no-tablespaces --skip-add-locks --skip-comments --skip-set-charset --tz-utc --column-statistics=0';

        if (! $this->connection->isMaria()) {
            $command .= ' --set-gtid-purged=OFF';
        }

        return $command.' "${:LARAVEL_LOAD_DATABASE}"';
    }

    /**
     * Generate a basic connection string (--socket, --host, --port, --user, --password) for the database.
     *
     * @param  array{version: string, isMariaDb: bool}  $versionInfo
     * @return string
     */
    protected function connectionString(array $versionInfo)
    {
        $value = ' --user="${:LARAVEL_LOAD_USER}" --password="${:LARAVEL_LOAD_PASSWORD}"';

        $config = $this->connection->getConfig();

        $value .= $config['unix_socket'] ?? false
            ? ' --socket="${:LARAVEL_LOAD_SOCKET}"'
            : ' --host="${:LARAVEL_LOAD_HOST}" --port="${:LARAVEL_LOAD_PORT}"';

        if (isset($config['options'][Mysql::ATTR_SSL_CA])) {
            $value .= ' --ssl-ca="${:LARAVEL_LOAD_SSL_CA}"';
        }

        if (isset($config['options'][Mysql::ATTR_SSL_CERT])) {
            $value .= ' --ssl-cert="${:LARAVEL_LOAD_SSL_CERT}"';
        }

        if (isset($config['options'][Mysql::ATTR_SSL_KEY])) {
            $value .= ' --ssl-key="${:LARAVEL_LOAD_SSL_KEY}"';
        }

        /** @phpstan-ignore classConstant.notFound */
        if (($config['options'][Mysql::ATTR_SSL_VERIFY_SERVER_CERT] ?? null) === false) {
            if (version_compare($versionInfo['version'], '5.7.11', '>=') && ! $versionInfo['isMariaDb']) {
                $value .= ' --ssl-mode=DISABLED';
            } else {
                $value .= ' --ssl=off';
            }
        }

        return $value;
    }

    /**
     * Get the base variables for a dump / load command.
     *
     * @param  array  $config
     * @return array
     */
    protected function baseVariables(array $config)
    {
        $config['host'] ??= '';

        return [
            'LARAVEL_LOAD_SOCKET' => $config['unix_socket'] ?? '',
            'LARAVEL_LOAD_HOST' => is_array($config['host']) ? $config['host'][0] : $config['host'],
            'LARAVEL_LOAD_PORT' => $config['port'] ?? '',
            'LARAVEL_LOAD_USER' => $config['username'],
            'LARAVEL_LOAD_PASSWORD' => $config['password'] ?? '',
            'LARAVEL_LOAD_DATABASE' => $config['database'],
            'LARAVEL_LOAD_SSL_CA' => $config['options'][Mysql::ATTR_SSL_CA] ?? '',
            'LARAVEL_LOAD_SSL_CERT' => $config['options'][Mysql::ATTR_SSL_CERT] ?? '',
            'LARAVEL_LOAD_SSL_KEY' => $config['options'][Mysql::ATTR_SSL_KEY] ?? '',
        ];
    }

    /**
     * Execute the given dump process.
     *
     * @param  \Symfony\Component\Process\Process  $process
     * @param  callable  $output
     * @param  array  $variables
     * @param  int  $depth
     * @return \Symfony\Component\Process\Process
     */
    protected function executeDumpProcess(Process $process, $output, array $variables, int $depth = 0)
    {
        if ($depth > 30) {
            throw new Exception('Dump execution exceeded maximum depth of 30.');
        }

        try {
            $process->setTimeout(null)->mustRun($output, $variables);
        } catch (Exception $e) {
            if (Str::contains($e->getMessage(), ['column-statistics', 'column_statistics'])) {
                return $this->executeDumpProcess(Process::fromShellCommandLine(
                    str_replace(' --column-statistics=0', '', $process->getCommandLine())
                ), $output, $variables, $depth + 1);
            }

            if (str_contains($e->getMessage(), 'set-gtid-purged')) {
                return $this->executeDumpProcess(Process::fromShellCommandLine(
                    str_replace(' --set-gtid-purged=OFF', '', $process->getCommandLine())
                ), $output, $variables, $depth + 1);
            }

            throw $e;
        }

        return $process;
    }

    /**
     * Detect the MySQL client version.
     *
     * @return array{version: string, isMariaDb: bool}
     */
    protected function detectClientVersion(): array
    {
        [$version, $isMariaDb] = ['8.0.0', false];

        try {
            $versionOutput = $this->makeProcess('mysql --version')->mustRun()->getOutput();

            if (preg_match('/(\d+\.\d+\.\d+)/', $versionOutput, $matches)) {
                $version = $matches[1];
            }

            $isMariaDb = stripos($versionOutput, 'mariadb') !== false;
        } catch (ProcessFailedException) {
        }

        return [
            'version' => $version,
            'isMariaDb' => $isMariaDb,
        ];
    }
}
