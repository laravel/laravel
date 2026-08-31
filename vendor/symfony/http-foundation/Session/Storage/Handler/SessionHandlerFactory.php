<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Tools\DsnParser;
use Relay\Relay;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SessionHandlerFactory
{
    public static function createHandler(object|string $connection, array $options = []): AbstractSessionHandler
    {
        if ($query = \is_string($connection) ? parse_url($connection) : false) {
            parse_str($query['query'] ?? '', $query);

            if (($options['ttl'] ?? null) instanceof \Closure) {
                $query['ttl'] = $options['ttl'];
            }
        }
        $options = ($query ?: []) + $options;

        switch (true) {
            case $connection instanceof \Redis:
            case $connection instanceof Relay:
            case $connection instanceof \RedisArray:
            case $connection instanceof \RedisCluster:
            case $connection instanceof \Predis\ClientInterface:
                return new RedisSessionHandler($connection);

            case $connection instanceof \Memcached:
                return new MemcachedSessionHandler($connection);

            case $connection instanceof \PDO:
                return new PdoSessionHandler($connection);

            case !\is_string($connection):
                throw new \InvalidArgumentException(\sprintf('Unsupported Connection: "%s".', get_debug_type($connection)));
            case str_starts_with($connection, 'file://'):
                $savePath = substr($connection, 7);

                return new StrictSessionHandler(new NativeFileSessionHandler('' === $savePath ? null : $savePath));

            case str_starts_with($connection, 'redis:'):
            case str_starts_with($connection, 'rediss:'):
            case str_starts_with($connection, 'valkey:'):
            case str_starts_with($connection, 'valkeys:'):
            case str_starts_with($connection, 'memcached:'):
                if (!class_exists(AbstractAdapter::class)) {
                    throw new \InvalidArgumentException('Unsupported Redis or Memcached DSN. Try running "composer require symfony/cache".');
                }
                $handlerClass = str_starts_with($connection, 'memcached:') ? MemcachedSessionHandler::class : RedisSessionHandler::class;
                $connection = preg_replace('/([?&])prefix=[^&]*+&?/', '\1', $connection);
                $connection = AbstractAdapter::createConnection($connection, ['lazy' => true]);

                return new $handlerClass($connection, array_intersect_key($options, ['prefix' => 1, 'ttl' => 1]));

            case str_starts_with($connection, 'pdo_oci://'):
                if (!class_exists(DriverManager::class)) {
                    throw new \InvalidArgumentException('Unsupported PDO OCI DSN. Try running "composer require doctrine/dbal".');
                }
                $connection[3] = '-';
                $params = (new DsnParser())->parse($connection);
                $config = new Configuration();
                $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());

                $connection = DriverManager::getConnection($params, $config)->getNativeConnection();
                // no break;

            case str_starts_with($connection, 'mssql://'):
            case str_starts_with($connection, 'mysql://'):
            case str_starts_with($connection, 'mysql2://'):
            case str_starts_with($connection, 'pgsql://'):
            case str_starts_with($connection, 'postgres://'):
            case str_starts_with($connection, 'postgresql://'):
            case str_starts_with($connection, 'sqlsrv://'):
            case str_starts_with($connection, 'sqlite://'):
            case str_starts_with($connection, 'sqlite3://'):
                return new PdoSessionHandler($connection, $options);
        }

        throw new \InvalidArgumentException(\sprintf('Unsupported Connection: "%s".', $connection));
    }
}
