<?php

namespace Illuminate\Cache;

use Aws\DynamoDb\DynamoDbClient;
use Closure;
use Illuminate\Contracts\Cache\Factory as FactoryContract;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Mockery;
use Mockery\LegacyMockInterface;

/**
 * @mixin \Illuminate\Cache\Repository
 * @mixin \Illuminate\Contracts\Cache\LockProvider
 */
class CacheManager implements FactoryContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved cache stores.
     *
     * @var array
     */
    protected $stores = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new Cache manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a cache store instance by name, wrapped in a repository.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function store($name = null)
    {
        $name = $name ?? $this->getDefaultDriver();

        return $this->stores[$name] ??= $this->resolve($name);
    }

    /**
     * Get a cache driver instance.
     *
     * @param  string|null  $driver
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function driver($driver = null)
    {
        return $this->store($driver);
    }

    /**
     * Get a memoized cache driver instance.
     *
     * @param  string|null  $driver
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function memo($driver = null)
    {
        $driver = $driver ?? $this->getDefaultDriver();

        $bindingKey = "cache.__memoized:{$driver}";

        $isSpy = isset($this->app['cache']) && $this->app['cache'] instanceof LegacyMockInterface;

        $this->app->scopedIf($bindingKey, function () use ($driver, $isSpy) {
            $repository = $this->repository(
                new MemoizedStore($driver, $this->store($driver)), ['events' => false]
            );

            return $isSpy ? Mockery::spy($repository) : $repository;
        });

        return $this->app->make($bindingKey);
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Cache\Repository
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        $config = Arr::add($config, 'store', $name);

        return $this->build($config);
    }

    /**
     * Build a cache repository with the given configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     *
     * @throws \InvalidArgumentException
     */
    public function build(array $config)
    {
        $config = Arr::add($config, 'store', $config['name'] ?? 'ondemand');

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create an instance of the APC cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createApcDriver(array $config)
    {
        $prefix = $this->getPrefix($config);

        return $this->repository(new ApcStore(new ApcWrapper, $prefix), $config);
    }

    /**
     * Create an instance of the array cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createArrayDriver(array $config)
    {
        return $this->repository(new ArrayStore(
            $config['serialize'] ?? false,
            $this->getSerializableClasses($config),
        ), $config);
    }

    /**
     * Create an instance of the database cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createDatabaseDriver(array $config)
    {
        $connection = $this->app['db']->connection($config['connection'] ?? null);

        $store = new DatabaseStore(
            $connection,
            $config['table'],
            $this->getPrefix($config),
            $config['lock_table'] ?? 'cache_locks',
            $config['lock_lottery'] ?? [2, 100],
            $config['lock_timeout'] ?? 86400,
            $this->getSerializableClasses($config),
        );

        return $this->repository(
            $store->setLockConnection(
                $this->app['db']->connection($config['lock_connection'] ?? $config['connection'] ?? null)
            ),
            $config
        );
    }

    /**
     * Create an instance of the DynamoDB cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createDynamodbDriver(array $config)
    {
        $client = $this->newDynamodbClient($config);

        return $this->repository(
            new DynamoDbStore(
                $client,
                $config['table'],
                $config['attributes']['key'] ?? 'key',
                $config['attributes']['value'] ?? 'value',
                $config['attributes']['expiration'] ?? 'expires_at',
                $this->getPrefix($config),
                $this->getSerializableClasses($config),
            ),
            $config
        );
    }

    /**
     * Create new DynamoDb Client instance.
     *
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    protected function newDynamodbClient(array $config)
    {
        $dynamoConfig = [
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => $config['endpoint'] ?? null,
        ];

        if (! empty($config['key']) && ! empty($config['secret'])) {
            $dynamoConfig['credentials'] = Arr::only(
                $config, ['key', 'secret']
            );

            if (! empty($config['token'])) {
                $dynamoConfig['credentials']['token'] = $config['token'];
            }
        }

        return new DynamoDbClient($dynamoConfig);
    }

    /**
     * Create an instance of the failover cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createFailoverDriver(array $config)
    {
        return $this->repository(new FailoverStore(
            $this,
            $this->app->make(DispatcherContract::class),
            $config['stores']
        ), ['events' => false, ...$config]);
    }

    /**
     * Create an instance of the file cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createFileDriver(array $config)
    {
        return $this->repository(
            (new FileStore(
                $this->app['files'],
                $config['path'],
                $config['permission'] ?? null,
                $this->getSerializableClasses($config),
            ))
                ->setLockDirectory($config['lock_path'] ?? null),
            $config
        );
    }

    /**
     * Create an instance of the Memcached cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createMemcachedDriver(array $config)
    {
        $prefix = $this->getPrefix($config);

        $memcached = $this->app['memcached.connector']->connect(
            $config['servers'],
            $config['persistent_id'] ?? null,
            $config['options'] ?? [],
            array_filter($config['sasl'] ?? [])
        );

        return $this->repository(new MemcachedStore($memcached, $prefix), $config);
    }

    /**
     * Create an instance of the Null cache driver.
     *
     * @return \Illuminate\Cache\Repository
     */
    protected function createNullDriver()
    {
        return $this->repository(new NullStore, []);
    }

    /**
     * Create an instance of the Redis cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createRedisDriver(array $config)
    {
        $redis = $this->app['redis'];

        $connection = $config['connection'] ?? 'default';

        $store = new RedisStore(
            $redis,
            $this->getPrefix($config),
            $connection,
            $this->getSerializableClasses($config),
        );

        return $this->repository(
            $store->setLockConnection($config['lock_connection'] ?? $connection),
            $config
        );
    }

    /**
     * Create an instance of the session cache driver.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    protected function createSessionDriver(array $config)
    {
        return $this->repository(
            new SessionStore(
                $this->getSession(),
                $config['key'] ?? '_cache',
            ),
            $config
        );
    }

    /**
     * Get the session store implementation.
     *
     * @return \Illuminate\Contracts\Session\Session
     *
     * @throws \InvalidArgumentException
     */
    protected function getSession()
    {
        $session = $this->app['session'] ?? null;

        if (! $session) {
            throw new InvalidArgumentException('Session store requires session manager to be available in container.');
        }

        return $session;
    }

    /**
     * Create a new cache repository with the given implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    public function repository(Store $store, array $config = [])
    {
        return tap(new Repository($store, Arr::only($config, ['store'])), function ($repository) use ($config) {
            if ($config['events'] ?? true) {
                $this->setEventDispatcher($repository);
            }
        });
    }

    /**
     * Set the event dispatcher on the given repository instance.
     *
     * @param  \Illuminate\Cache\Repository  $repository
     * @return void
     */
    protected function setEventDispatcher(Repository $repository)
    {
        if (! $this->app->bound(DispatcherContract::class)) {
            return;
        }

        $repository->setEventDispatcher(
            $this->app[DispatcherContract::class]
        );
    }

    /**
     * Re-set the event dispatcher on all resolved cache repositories.
     *
     * @return void
     */
    public function refreshEventDispatcher()
    {
        array_map($this->setEventDispatcher(...), $this->stores);
    }

    /**
     * Get the cache prefix.
     *
     * @param  array  $config
     * @return string
     */
    protected function getPrefix(array $config)
    {
        return $config['prefix'] ?? $this->app['config']['cache.prefix'];
    }

    /**
     * Get the classes that should be allowed during unserialization.
     *
     * @param  array  $config
     * @return array|bool|null
     */
    protected function getSerializableClasses(array $config)
    {
        return $this->app['config']['cache.serializable_classes'] ?? null;
    }

    /**
     * Get the cache connection configuration.
     *
     * @param  string  $name
     * @return array|null
     */
    protected function getConfig($name)
    {
        return $name !== 'null'
            ? $this->app['config']["cache.stores.{$name}"]
            : ['driver' => 'null'];
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['cache.default'] ?? 'null';
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['cache.default'] = $name;
    }

    /**
     * Unset the given driver instances.
     *
     * @param  array|string|null  $name
     * @return $this
     */
    public function forgetDriver($name = null)
    {
        $name ??= $this->getDefaultDriver();

        foreach ((array) $name as $cacheName) {
            if (isset($this->stores[$cacheName])) {
                unset($this->stores[$cacheName]);
            }
        }

        return $this;
    }

    /**
     * Disconnect the given driver and remove from local cache.
     *
     * @param  string|null  $name
     * @return void
     */
    public function purge($name = null)
    {
        $name ??= $this->getDefaultDriver();

        unset($this->stores[$name]);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     *
     * @param-closure-this  $this  $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Set the application instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}
