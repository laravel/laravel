<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Redis\Connections\PredisClusterConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class RedisStore extends TaggableStore implements LockProvider
{
    use RetrievesMultipleKeys {
        many as private manyAlias;
        putMany as private putManyAlias;
    }

    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Contracts\Redis\Factory
     */
    protected $redis;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The Redis connection instance that should be used to manage locks.
     *
     * @var string
     */
    protected $connection;

    /**
     * The name of the connection that should be used for locks.
     *
     * @var string
     */
    protected $lockConnection;

    /**
     * The classes that should be allowed during unserialization.
     *
     * @var array|bool|null
     */
    protected $serializableClasses;

    /**
     * Create a new Redis store.
     *
     * @param  \Illuminate\Contracts\Redis\Factory  $redis
     * @param  string  $prefix
     * @param  string  $connection
     * @param  array|bool|null  $serializableClasses
     */
    public function __construct(Redis $redis, $prefix = '', $connection = 'default', $serializableClasses = null)
    {
        $this->redis = $redis;
        $this->setPrefix($prefix);
        $this->setConnection($connection);
        $this->serializableClasses = $serializableClasses;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $connection = $this->connection();

        $value = $connection->get($this->prefix.$key);

        return ! is_null($value) ? $this->connectionAwareUnserialize($value, $connection) : null;
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        if (count($keys) === 0) {
            return [];
        }

        $results = [];

        $connection = $this->connection();

        // PredisClusterConnection does not support reading multiple values if the keys hash differently...
        if ($connection instanceof PredisClusterConnection) {
            return $this->manyAlias($keys);
        }

        $values = $connection->mget(array_map(function ($key) {
            return $this->prefix.$key;
        }, $keys));

        foreach ($values as $index => $value) {
            $results[$keys[$index]] = ! is_null($value) ? $this->connectionAwareUnserialize($value, $connection) : null;
        }

        return $results;
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        $connection = $this->connection();

        return (bool) $connection->setex(
            $this->prefix.$key, (int) max(1, $seconds), $this->connectionAwareSerialize($value, $connection)
        );
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds)
    {
        $connection = $this->connection();

        // Cluster connections do not support writing multiple values if the keys hash differently...
        if ($connection instanceof PhpRedisClusterConnection ||
            $connection instanceof PredisClusterConnection) {
            return $this->putManyAlias($values, $seconds);
        }

        $serializedValues = [];

        foreach ($values as $key => $value) {
            $serializedValues[$this->prefix.$key] = $this->connectionAwareSerialize($value, $connection);
        }

        $connection->multi();

        $manyResult = null;

        foreach ($serializedValues as $key => $value) {
            $result = (bool) $connection->setex(
                $key, (int) max(1, $seconds), $value
            );

            $manyResult = is_null($manyResult) ? $result : $result && $manyResult;
        }

        $connection->exec();

        return $manyResult ?: false;
    }

    /**
     * Store an item in the cache if the key doesn't exist.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function add($key, $value, $seconds)
    {
        $connection = $this->connection();

        return (bool) $connection->eval(
            LuaScripts::add(), 1, $this->prefix.$key, $this->pack($value, $connection), (int) max(1, $seconds)
        );
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int
     */
    public function increment($key, $value = 1)
    {
        return $this->connection()->incrby($this->prefix.$key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int
     */
    public function decrement($key, $value = 1)
    {
        return $this->connection()->decrby($this->prefix.$key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        $connection = $this->connection();

        return (bool) $connection->set($this->prefix.$key, $this->connectionAwareSerialize($value, $connection));
    }

    /**
     * Get a lock instance.
     *
     * @param  string  $name
     * @param  int  $seconds
     * @param  string|null  $owner
     * @return \Illuminate\Contracts\Cache\Lock
     */
    public function lock($name, $seconds = 0, $owner = null)
    {
        $lockName = $this->prefix.$name;

        $lockConnection = $this->lockConnection();

        if ($lockConnection instanceof PhpRedisConnection) {
            return new PhpRedisLock($lockConnection, $lockName, $seconds, $owner);
        }

        return new RedisLock($lockConnection, $lockName, $seconds, $owner);
    }

    /**
     * Restore a lock instance using the owner identifier.
     *
     * @param  string  $name
     * @param  string  $owner
     * @return \Illuminate\Contracts\Cache\Lock
     */
    public function restoreLock($name, $owner)
    {
        return $this->lock($name, 0, $owner);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        return (bool) $this->connection()->del($this->prefix.$key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->connection()->flushdb();

        return true;
    }

    /**
     * Remove all expired tag set entries.
     *
     * @return void
     */
    public function flushStaleTags()
    {
        foreach ($this->currentTags()->chunk(1000) as $tags) {
            $this->tags($tags->all())->flushStale();
        }
    }

    /**
     * Begin executing a new tags operation.
     *
     * @param  mixed  $names
     * @return \Illuminate\Cache\RedisTaggedCache
     */
    public function tags($names)
    {
        return new RedisTaggedCache(
            $this, new RedisTagSet($this, is_array($names) ? $names : func_get_args())
        );
    }

    /**
     * Get a collection of all of the cache tags currently being used.
     *
     * @param  int  $chunkSize
     * @return \Illuminate\Support\LazyCollection
     */
    protected function currentTags($chunkSize = 1000)
    {
        $connection = $this->connection();

        // Connections can have a global prefix...
        $connectionPrefix = match (true) {
            $connection instanceof PhpRedisConnection => $connection->_prefix(''),
            $connection instanceof PredisConnection => $connection->getOptions()->prefix ?: '',
            default => '',
        };

        $defaultCursorValue = match (true) {
            $connection instanceof PhpRedisConnection && version_compare(phpversion('redis'), '6.1.0', '>=') => null,
            default => '0',
        };

        $prefix = $connectionPrefix.$this->getPrefix();

        return (new LazyCollection(function () use ($connection, $chunkSize, $prefix, $defaultCursorValue) {
            $cursor = $defaultCursorValue;

            do {
                $scanResult = $connection->scan(
                    $cursor,
                    ['match' => $prefix.'tag:*:entries', 'count' => $chunkSize]
                );

                if (! is_array($scanResult)) {
                    break;
                }

                [$cursor, $tagsChunk] = $scanResult;

                if (! is_array($tagsChunk)) {
                    break;
                }

                $tagsChunk = array_unique($tagsChunk);

                if (empty($tagsChunk)) {
                    continue;
                }

                foreach ($tagsChunk as $tag) {
                    yield $tag;
                }
            } while (((string) $cursor) !== $defaultCursorValue);
        }))->map(fn (string $tagKey) => Str::match('/^'.preg_quote($prefix, '/').'tag:(.*):entries$/', $tagKey));
    }

    /**
     * Get the Redis connection instance.
     *
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection()
    {
        return $this->redis->connection($this->connection);
    }

    /**
     * Get the Redis connection instance that should be used to manage locks.
     *
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function lockConnection()
    {
        return $this->redis->connection($this->lockConnection ?? $this->connection);
    }

    /**
     * Specify the name of the connection that should be used to store data.
     *
     * @param  string  $connection
     * @return void
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Specify the name of the connection that should be used to manage locks.
     *
     * @param  string  $connection
     * @return $this
     */
    public function setLockConnection($connection)
    {
        $this->lockConnection = $connection;

        return $this;
    }

    /**
     * Get the Redis database instance.
     *
     * @return \Illuminate\Contracts\Redis\Factory
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Prepare a value to be used with the Redis cache store when used by eval scripts.
     *
     * @param  mixed  $value
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     * @return mixed
     */
    protected function pack($value, $connection)
    {
        if ($connection instanceof PhpRedisConnection) {
            if ($connection->serialized()) {
                return $connection->pack([$value])[0];
            }

            if ($connection->compressed()) {
                return $connection->pack([$this->serialize($value)])[0];
            }
        }

        return $this->serialize($value);
    }

    /**
     * Serialize the value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function serialize($value)
    {
        return $this->shouldBeStoredWithoutSerialization($value) ? $value : serialize($value);
    }

    /**
     * Determine if the given value should be stored as plain value.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function shouldBeStoredWithoutSerialization($value): bool
    {
        return is_numeric($value) && is_finite($value);
    }

    /**
     * Unserialize the value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        if ($this->serializableClasses !== null) {
            return unserialize($value, ['allowed_classes' => $this->serializableClasses]);
        }

        return unserialize($value);
    }

    /**
     * Handle connection specific considerations when a value needs to be serialized.
     *
     * @param  mixed  $value
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     * @return mixed
     */
    protected function connectionAwareSerialize($value, $connection)
    {
        if ($connection instanceof PhpRedisConnection && $connection->serialized()) {
            return $value;
        }

        return $this->serialize($value);
    }

    /**
     * Handle connection specific considerations when a value needs to be unserialized.
     *
     * @param  mixed  $value
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     * @return mixed
     */
    protected function connectionAwareUnserialize($value, $connection)
    {
        if ($connection instanceof PhpRedisConnection && $connection->serialized()) {
            return $value;
        }

        return $this->unserialize($value);
    }
}
