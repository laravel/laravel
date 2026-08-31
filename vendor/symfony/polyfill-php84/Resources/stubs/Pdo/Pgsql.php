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

if (\PHP_VERSION_ID < 80400 && \extension_loaded('pdo_pgsql')) {
    class Pgsql extends \PDO
    {
        public const ATTR_DISABLE_PREPARES = \PDO::PGSQL_ATTR_DISABLE_PREPARES;

        public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
        {
            parent::__construct($dsn, $username, $password, $options);

            if ('pgsql' !== $driver = $this->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
                throw new \PDOException(\sprintf('Pdo\Pgsql::__construct() cannot be used for connecting to the "%s" driver', $driver));
            }
        }

        public static function connect(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null): self
        {
            try {
                return new self($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw preg_match('/^Pdo\\\\Pgsql::__construct\(\) cannot be used for connecting to the "([a-z]+)" driver/', $e->getMessage(), $matches) ? new \PDOException(\sprintf('Pdo\Pgsql::connect() cannot be used for connecting to the "%s" driver', $matches[1])) : $e;
            }
        }

        public function copyFromArray(string $tableName, array $rows, string $separator = "\t", string $nullAs = '\\\\N', ?string $fields = null): bool
        {
            return $this->pgsqlCopyFromArray($tableName, $rows, $separator, $nullAs, $fields);
        }

        public function copyFromFile(string $tableName, string $filename, string $separator = "\t", string $nullAs = '\\\\N', ?string $fields = null): bool
        {
            return $this->pgsqlCopyFromFile($tableName, $filename, $separator, $nullAs, $fields);
        }

        /**
         * @return array|false
         */
        public function copyToArray(string $tableName, string $separator = "\t", string $nullAs = '\\\\N', ?string $fields = null)
        {
            return $this->pgsqlCopyToArray($tableName, $separator, $nullAs, $fields);
        }

        public function copyToFile(string $tableName, string $filename, string $separator = "\t", string $nullAs = '\\\\N', ?string $fields = null): bool
        {
            return $this->pgsqlCopyToFile($tableName, $filename, $separator, $nullAs, $fields);
        }

        /**
         * @return array|false
         */
        public function getNotify(int $fetchMode = \PDO::FETCH_DEFAULT, int $timeoutMilliseconds = 0)
        {
            return $this->pgsqlGetNotify($fetchMode, $timeoutMilliseconds);
        }

        public function getPid(): int
        {
            return $this->pgsqlGetPid();
        }

        /**
         * @return string|false
         */
        public function lobCreate()
        {
            return $this->pgsqlLOBCreate();
        }

        /**
         * @return resource|false
         */
        public function lobOpen(string $oid, string $mode = 'rb')
        {
            return $this->pgsqlLOBOpen($oid, $mode);
        }

        public function lobUnlink(string $oid): bool
        {
            return $this->pgsqlLOBUnlink($oid);
        }
    }
}
