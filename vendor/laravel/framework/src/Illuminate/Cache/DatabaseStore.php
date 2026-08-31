<?php

namespace Illuminate\Cache;

use Closure;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\QueryException;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;

class DatabaseStore implements LockProvider, Store
{
    use InteractsWithTime;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The database connection instance that should be used to manage locks.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $lockConnection;

    /**
     * The name of the cache table.
     *
     * @var string
     */
    protected $table;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The name of the cache locks table.
     *
     * @var string
     */
    protected $lockTable;

    /**
     * An array representation of the lock lottery odds.
     *
     * @var array
     */
    protected $lockLottery;

    /**
     * The default number of seconds that a lock should be held.
     *
     * @var int
     */
    protected $defaultLockTimeoutInSeconds;

    /**
     * The classes that should be allowed during unserialization.
     *
     * @var array|bool|null
     */
    protected $serializableClasses;

    /**
     * Create a new database store.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  string  $table
     * @param  string  $prefix
     * @param  string  $lockTable
     * @param  array  $lockLottery
     * @param  int  $defaultLockTimeoutInSeconds
     * @param  array|bool|null  $serializableClasses
     */
    public function __construct(
        ConnectionInterface $connection,
        $table,
        $prefix = '',
        $lockTable = 'cache_locks',
        $lockLottery = [2, 100],
        $defaultLockTimeoutInSeconds = 86400,
        $serializableClasses = null,
    ) {
        $this->table = $table;
        $this->prefix = $prefix;
        $this->connection = $connection;
        $this->lockTable = $lockTable;
        $this->lockLottery = $lockLottery;
        $this->defaultLockTimeoutInSeconds = $defaultLockTimeoutInSeconds;
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
        return $this->many([$key])[$key];
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @return array
     */
    public function many(array $keys)
    {
        if (count($keys) === 0) {
            return [];
        }

        $results = array_fill_keys($keys, null);

        // First we will retrieve all of the items from the cache using their keys and
        // the prefix value. Then we will need to iterate through each of the items
        // and convert them to an object when they are currently in array format.
        $values = $this->table()
            ->whereIn('key', array_map(function ($key) {
                return $this->prefix.$key;
            }, $keys))
            ->get()
            ->map(function ($value) {
                return is_array($value) ? (object) $value : $value;
            });

        $currentTime = $this->currentTime();

        // If this cache expiration date is past the current time, we will remove this
        // item from the cache. Then we will return a null value since the cache is
        // expired. We will use "Carbon" to make this comparison with the column.
        [$values, $expired] = $values->partition(function ($cache) use ($currentTime) {
            return $cache->expiration > $currentTime;
        });

        if ($expired->isNotEmpty()) {
            $this->forgetManyIfExpired($expired->pluck('key')->all(), prefixed: true);
        }

        return Arr::map($results, function ($value, $key) use ($values) {
            if ($cache = $values->firstWhere('key', $this->prefix.$key)) {
                return $this->unserialize($cache->value);
            }

            return $value;
        });
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
        return $this->putMany([$key => $value], $seconds);
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
        $serializedValues = [];

        $expiration = $this->getTime() + $seconds;

        foreach ($values as $key => $value) {
            $serializedValues[] = [
                'key' => $this->prefix.$key,
                'value' => $this->serialize($value),
                'expiration' => $expiration,
            ];
        }

        return $this->table()->upsert($serializedValues, 'key') > 0;
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
        if (! is_null($this->get($key))) {
            return false;
        }

        $key = $this->prefix.$key;
        $value = $this->serialize($value);
        $expiration = $this->getTime() + $seconds;

        if (! $this->getConnection() instanceof SqlServerConnection) {
            return $this->table()->insertOrIgnore(compact('key', 'value', 'expiration')) > 0;
        }

        try {
            return $this->table()->insert(compact('key', 'value', 'expiration'));
        } catch (QueryException) {
            // ...
        }

        return false;
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  int  $value
     * @return int|false
     */
    public function increment($key, $value = 1)
    {
        return $this->incrementOrDecrement($key, $value, function ($current, $value) {
            return $current + $value;
        });
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  int  $value
     * @return int|false
     */
    public function decrement($key, $value = 1)
    {
        return $this->incrementOrDecrement($key, $value, function ($current, $value) {
            return $current - $value;
        });
    }

    /**
     * Increment or decrement an item in the cache.
     *
     * @param  string  $key
     * @param  int|float  $value
     * @param  \Closure  $callback
     * @return int|false
     */
    protected function incrementOrDecrement($key, $value, Closure $callback)
    {
        return $this->connection->transaction(function () use ($key, $value, $callback) {
            $prefixed = $this->prefix.$key;

            $cache = $this->table()->where('key', $prefixed)
                ->lockForUpdate()->first();

            // If there is no value in the cache, we will return false here. Otherwise the
            // value will be decrypted and we will proceed with this function to either
            // increment or decrement this value based on the given action callbacks.
            if (is_null($cache)) {
                return false;
            }

            $cache = is_array($cache) ? (object) $cache : $cache;

            $current = $this->unserialize($cache->value);

            // Here we'll call this callback function that was given to the function which
            // is used to either increment or decrement the function. We use a callback
            // so we do not have to recreate all this logic in each of the functions.
            $new = $callback((int) $current, $value);

            if (! is_numeric($current)) {
                return false;
            }

            // Here we will update the values in the table. We will also encrypt the value
            // since database cache values are encrypted by default with secure storage
            // that can't be easily read. We will return the new value after storing.
            $this->table()->where('key', $prefixed)->update([
                'value' => $this->serialize($new),
            ]);

            return $new;
        });
    }

    /**
     * Get the current system time.
     *
     * @return int
     */
    protected function getTime()
    {
        return $this->currentTime();
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
        return $this->put($key, $value, 315360000);
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
        return new DatabaseLock(
            $this->lockConnection ?? $this->connection,
            $this->lockTable,
            $this->prefix.$name,
            $seconds,
            $owner,
            $this->lockLottery,
            $this->defaultLockTimeoutInSeconds
        );
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
        return $this->forgetMany([$key]);
    }

    /**
     * Remove an item from the cache if it is expired.
     *
     * @param  string  $key
     * @return bool
     */
    public function forgetIfExpired($key)
    {
        return $this->forgetManyIfExpired([$key]);
    }

    /**
     * Remove all items from the cache.
     *
     * @param  array  $keys
     * @return bool
     */
    protected function forgetMany(array $keys)
    {
        $this->table()->whereIn('key', (new Collection($keys))->flatMap(fn ($key) => [
            $this->prefix.$key,
            "{$this->prefix}illuminate:cache:flexible:created:{$key}",
        ])->all())->delete();

        return true;
    }

    /**
     * Remove all expired items from the given set from the cache.
     *
     * @param  array  $keys
     * @param  bool  $prefixed
     * @return bool
     */
    protected function forgetManyIfExpired(array $keys, bool $prefixed = false)
    {
        $this->table()
            ->whereIn('key', (new Collection($keys))->flatMap(fn ($key) => $prefixed ? [
                $key,
                $this->prefix.'illuminate:cache:flexible:created:'.Str::chopStart($key, $this->prefix),
            ] : [
                "{$this->prefix}{$key}",
                "{$this->prefix}illuminate:cache:flexible:created:{$key}",
            ])->all())
            ->where('expiration', '<=', $this->getTime())
            ->delete();

        return true;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->table()->delete();

        return true;
    }

    /**
     * Get a query builder for the cache table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the underlying database connection.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the underlying database connection.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Get the connection used to manage locks.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getLockConnection()
    {
        return $this->lockConnection;
    }

    /**
     * Specify the connection that should be used to manage locks.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @return $this
     */
    public function setLockConnection($connection)
    {
        $this->lockConnection = $connection;

        return $this;
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
     * Serialize the given value.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function serialize($value)
    {
        $result = serialize($value);

        if (($this->connection instanceof PostgresConnection ||
             $this->connection instanceof SQLiteConnection) &&
            str_contains($result, "\0")) {
            $result = base64_encode($result);
        }

        return $result;
    }

    /**
     * Unserialize the given value.
     *
     * @param  string  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if (($this->connection instanceof PostgresConnection ||
             $this->connection instanceof SQLiteConnection) &&
            ! Str::contains($value, [':', ';'])) {
            $value = base64_decode($value);
        }

        if ($this->serializableClasses !== null) {
            return unserialize($value, ['allowed_classes' => $this->serializableClasses]);
        }

        return unserialize($value);
    }
}
