<?php

namespace Illuminate\Cache;

use Illuminate\Cache\Events\CacheFlushed;
use Illuminate\Cache\Events\CacheFlushing;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Redis\Connections\PredisClusterConnection;
use Illuminate\Redis\Connections\PredisConnection;

class RedisTaggedCache extends TaggedCache
{
    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function add($key, $value, $ttl = null)
    {
        $seconds = null;

        if ($ttl !== null) {
            $seconds = $this->getSeconds($ttl);

            if ($seconds > 0) {
                $this->tags->addEntry(
                    $this->itemKey($key),
                    $seconds
                );
            }
        }

        return parent::add($key, $value, $ttl);
    }

    /**
     * Store an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function put($key, $value, $ttl = null)
    {
        if (is_null($ttl)) {
            return $this->forever($key, $value);
        }

        $seconds = $this->getSeconds($ttl);

        if ($seconds > 0) {
            $this->tags->addEntry(
                $this->itemKey($key),
                $seconds
            );
        }

        return parent::put($key, $value, $ttl);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        $this->tags->addEntry($this->itemKey($key), updateWhen: 'NX');

        return parent::increment($key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        $this->tags->addEntry($this->itemKey($key), updateWhen: 'NX');

        return parent::decrement($key, $value);
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
        $this->tags->addEntry($this->itemKey($key));

        return parent::forever($key, $value);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $connection = $this->store->connection();

        if ($connection instanceof PredisClusterConnection ||
            $connection instanceof PhpRedisClusterConnection) {
            return $this->flushClusteredConnection();
        }

        $this->event(new CacheFlushing($this->getName()));

        $redisPrefix = match (true) {
            $connection instanceof PhpRedisConnection => $connection->client()->getOption(\Redis::OPT_PREFIX),
            $connection instanceof PredisConnection => $connection->client()->getOptions()->prefix,
        };

        $cachePrefix = $redisPrefix.$this->store->getPrefix();

        $cacheTags = [];

        foreach ($this->tags->getNames() as $name) {
            $cacheTags[] = $cachePrefix.$this->tags->tagId($name);
        }

        $script = <<<'LUA'
            local prefix = table.remove(ARGV, 1)

            for i, key in ipairs(KEYS) do
                redis.call('DEL', key)

                for j, arg in ipairs(ARGV) do
                    local zkey = string.gsub(key, prefix, "")
                    redis.call('ZREM', arg, zkey)
                end
            end
        LUA;

        $entries = $this->tags->entries()
            ->map(fn (string $key) => $this->store->getPrefix().$key)
            ->chunk(1000);

        foreach ($entries as $keysToBeDeleted) {
            $connection->eval(
                $script,
                count($keysToBeDeleted),
                ...$keysToBeDeleted,
                ...[str_replace('-', '%-', $cachePrefix), ...$cacheTags]
            );
        }

        $this->event(new CacheFlushed($this->getName()));

        return true;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    protected function flushClusteredConnection()
    {
        $this->event(new CacheFlushing($this->getName()));

        $this->flushValues();
        $this->tags->flush();

        $this->event(new CacheFlushed($this->getName()));

        return true;
    }

    /**
     * Flush the individual cache entries for the tags.
     *
     * @return void
     */
    protected function flushValues()
    {
        $entries = $this->tags->entries()
            ->map(fn (string $key) => $this->store->getPrefix().$key)
            ->chunk(1000);

        $connection = $this->store->connection();

        foreach ($entries as $cacheKeys) {
            if ($connection instanceof PredisClusterConnection) {
                $connection->pipeline(function ($connection) use ($cacheKeys) {
                    foreach ($cacheKeys as $cacheKey) {
                        $connection->del($cacheKey);
                    }
                });
            } else {
                $connection->del(...$cacheKeys);
            }
        }
    }

    /**
     * Remove all stale reference entries from the tag set.
     *
     * @return bool
     */
    public function flushStale()
    {
        $this->tags->flushStaleEntries();

        return true;
    }
}
