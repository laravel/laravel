<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdo;

if (\PHP_VERSION_ID < 80400 && \extension_loaded('pdo_sqlite')) {
    class Sqlite extends \PDO
    {
        public const ATTR_EXTENDED_RESULT_CODES = \PHP_VERSION_ID >= 70400 ? \PDO::SQLITE_ATTR_EXTENDED_RESULT_CODES : 1002;
        public const ATTR_OPEN_FLAGS = \PHP_VERSION_ID >= 70300 ? \PDO::SQLITE_ATTR_OPEN_FLAGS : 1000;
        public const ATTR_READONLY_STATEMENT = \PHP_VERSION_ID >= 70400 ? \PDO::SQLITE_ATTR_READONLY_STATEMENT : 1001;
        public const DETERMINISTIC = \PDO::SQLITE_DETERMINISTIC;
        public const OPEN_READONLY = \PHP_VERSION_ID >= 70300 ? \PDO::SQLITE_OPEN_READONLY : 1;
        public const OPEN_READWRITE = \PHP_VERSION_ID >= 70300 ? \PDO::SQLITE_OPEN_READWRITE : 2;
        public const OPEN_CREATE = \PHP_VERSION_ID >= 70300 ? \PDO::SQLITE_OPEN_CREATE : 4;

        public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
        {
            parent::__construct($dsn, $username, $password, $options);

            if ('sqlite' !== $driver = $this->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
                throw new \PDOException(\sprintf('Pdo\Sqlite::__construct() cannot be used for connecting to the "%s" driver', $driver));
            }
        }

        public static function connect(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null): self
        {
            try {
                return new self($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw preg_match('/^Pdo\\\\Sqlite::__construct\(\) cannot be used for connecting to the "([a-z]+)" driver/', $e->getMessage(), $matches) ? new \PDOException(\sprintf('Pdo\Sqlite::connect() cannot be used for connecting to the "%s" driver', $matches[1])) : $e;
            }
        }

        public function createAggregate(string $name, callable $step, callable $finalize, int $numArgs = -1): bool
        {
            return $this->sqliteCreateAggregate($name, $step, $finalize, $numArgs);
        }

        public function createCollation(string $name, callable $callback): bool
        {
            return $this->sqliteCreateCollation($name, $callback);
        }

        public function createFunction(string $function_name, callable $callback, int $num_args = -1, int $flags = 0): bool
        {
            return $this->sqliteCreateFunction($function_name, $callback, $num_args, $flags);
        }
    }
}
