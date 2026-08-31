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

if (\PHP_VERSION_ID < 80400 && \extension_loaded('pdo_firebird')) {
    class Firebird extends \PDO
    {
        public const ATTR_DATE_FORMAT = \PDO::FB_ATTR_DATE_FORMAT;
        public const ATTR_TIME_FORMAT = \PDO::FB_ATTR_TIME_FORMAT;
        public const ATTR_TIMESTAMP_FORMAT = \PDO::FB_ATTR_TIMESTAMP_FORMAT;

        public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
        {
            parent::__construct($dsn, $username, $password, $options);

            if ('firebird' !== $driver = $this->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
                throw new \PDOException(\sprintf('Pdo\Firebird::__construct() cannot be used for connecting to the "%s" driver', $driver));
            }
        }

        public static function connect(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null): self
        {
            try {
                return new self($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw preg_match('/^Pdo\\\\Firebird::__construct\(\) cannot be used for connecting to the "([a-z]+)" driver/', $e->getMessage(), $matches) ? new \PDOException(\sprintf('Pdo\Firebird::connect() cannot be used for connecting to the "%s" driver', $matches[1])) : $e;
            }
        }
    }
}
