<?php

namespace Illuminate\Cache;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use DateTimeInterface;
use Illuminate\Cache\Events\CacheFlushed;
use Illuminate\Cache\Events\CacheFlushFailed;
use Illuminate\Cache\Events\CacheFlushing;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\ForgettingKey;
use Illuminate\Cache\Events\KeyForgetFailed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWriteFailed;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Cache\Events\RetrievingKey;
use Illuminate\Cache\Events\RetrievingManyKeys;
use Illuminate\Cache\Events\WritingKey;
use Illuminate\Cache\Events\WritingManyKeys;
use Illuminate\Cache\Limiters\ConcurrencyLimiterBuilder;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;

use function Illuminate\Support\defer;
use function Illuminate\Support\enum_value;

/**
 * @mixin \Illuminate\Contracts\Cache\Store
 */
class Repository implements ArrayAccess, CacheContract
{
    use InteractsWithTime, Macroable {
        __call as macroCall;
    }

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Store
     */
    protected $store;

    /**
     * The event dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The default number of seconds to store items.
     *
     * @var int|null
     */
    protected $default = 3600;

    /**
     * The cache store configuration options.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create a new cache repository instance.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @param  array  $config
     */
    public function __construct(Store $store, array $config = [])
    {
        $this->store = $store;
        $this->config = $config;
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param  \UnitEnum|array|string  $key
     * @return bool
     */
    public function has($key): bool
    {
        return ! is_null($this->get($key));
    }

    /**
     * Determine if an item doesn't exist in the cache.
     *
     * @param  \UnitEnum|string  $key
     * @return bool
     */
    public function missing($key)
    {
        return ! $this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  \UnitEnum|array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null): mixed
    {
        if (is_array($key)) {
            return $this->many($key);
        }

        $key = enum_value($key);

        $this->event(new RetrievingKey($this->getName(), $key));

        $value = $this->store->get($this->itemKey($key));

        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $this->event(new CacheMissed($this->getName(), $key));

            $value = value($default);
        } else {
            $this->event(new CacheHit($this->getName(), $key, $value));
        }

        return $value;
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
        $this->event(new RetrievingManyKeys($this->getName(), $keys));

        $values = $this->store->many((new Collection($keys))
            ->map(fn ($value, $key) => is_string($key) ? $key : enum_value($value))
            ->values()
            ->all()
        );

        return (new Collection($values))
            ->map(fn ($value, $key) => $this->handleManyResult($keys, $key, $value))
            ->all();
    }

    /**
     * {@inheritdoc}
     *
     * @return iterable
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $defaults = [];

        foreach ($keys as $key) {
            $defaults[enum_value($key)] = $default;
        }

        return $this->many($defaults);
    }

    /**
     * Handle a result for the "many" method.
     *
     * @param  array  $keys
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function handleManyResult($keys, $key, $value)
    {
        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $this->event(new CacheMissed($this->getName(), $key));

            return (isset($keys[$key]) && ! array_is_list($keys)) ? value($keys[$key]) : null;
        }

        // If we found a valid value we will fire the "hit" event and return the value
        // back from this function. The "hit" event gives developers an opportunity
        // to listen for every possible cache "hit" throughout this applications.
        $this->event(new CacheHit($this->getName(), $key, $value));

        return $value;
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param  \UnitEnum|array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return tap($this->get($key, $default), function () use ($key) {
            $this->forget($key);
        });
    }

    /**
     * Retrieve a string item from the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  (\Closure():(string|null))|string|null  $default
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function string($key, $default = null): string
    {
        $value = $this->get($key, $default);

        if (! is_string($value)) {
            throw new InvalidArgumentException(
                sprintf('Cache value for key [%s] must be a string, %s given.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Retrieve an integer item from the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  (\Closure():(int|null))|int|null  $default
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function integer($key, $default = null): int
    {
        $value = $this->get($key, $default);

        if (is_int($value)) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }

        throw new InvalidArgumentException(
            sprintf('Cache value for key [%s] must be an integer, %s given.', $key, gettype($value))
        );
    }

    /**
     * Retrieve a float item from the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  (\Closure():(float|null))|float|null  $default
     * @return float
     *
     * @throws \InvalidArgumentException
     */
    public function float($key, $default = null): float
    {
        $value = $this->get($key, $default);

        if (is_float($value)) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return (float) $value;
        }

        throw new InvalidArgumentException(
            sprintf('Cache value for key [%s] must be a float, %s given.', $key, gettype($value))
        );
    }

    /**
     * Retrieve a boolean item from the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  (\Closure():(bool|null))|bool|null  $default
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function boolean($key, $default = null): bool
    {
        $value = $this->get($key, $default);

        if (! is_bool($value)) {
            throw new InvalidArgumentException(
                sprintf('Cache value for key [%s] must be a boolean, %s given.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Retrieve an array item from the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  (\Closure():(array<array-key, mixed>|null))|array<array-key, mixed>|null  $default
     * @return array<array-key, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function array($key, $default = null): array
    {
        $value = $this->get($key, $default);

        if (! is_array($value)) {
            throw new InvalidArgumentException(
                sprintf('Cache value for key [%s] must be an array, %s given.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Store an item in the cache.
     *
     * @param  \UnitEnum|array|string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function put($key, $value, $ttl = null)
    {
        if (is_array($key)) {
            return $this->putMany($key, $value);
        }

        $key = enum_value($key);

        if ($ttl === null) {
            return $this->forever($key, $value);
        }

        $seconds = $this->getSeconds($ttl);

        if ($seconds <= 0) {
            return $this->forget($key);
        }

        $this->event(new WritingKey($this->getName(), $key, $value, $seconds));

        $result = $this->store->put($this->itemKey($key), $value, $seconds);

        if ($result) {
            $this->event(new KeyWritten($this->getName(), $key, $value, $seconds));
        } else {
            $this->event(new KeyWriteFailed($this->getName(), $key, $value, $seconds));
        }

        return $result;
    }

    /**
     * Store an item in the cache.
     *
     * @param  \UnitEnum|array|string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        return $this->put($key, $value, $ttl);
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function putMany(array $values, $ttl = null)
    {
        if ($ttl === null) {
            return $this->putManyForever($values);
        }

        $seconds = $this->getSeconds($ttl);

        if ($seconds <= 0) {
            return $this->deleteMultiple(array_keys($values));
        }

        $this->event(new WritingManyKeys($this->getName(), array_keys($values), array_values($values), $seconds));

        $result = $this->store->putMany($values, $seconds);

        foreach ($values as $key => $value) {
            if ($result) {
                $this->event(new KeyWritten($this->getName(), $key, $value, $seconds));
            } else {
                $this->event(new KeyWriteFailed($this->getName(), $key, $value, $seconds));
            }
        }

        return $result;
    }

    /**
     * Store multiple items in the cache indefinitely.
     *
     * @param  array  $values
     * @return bool
     */
    protected function putManyForever(array $values)
    {
        $result = true;

        foreach ($values as $key => $value) {
            if (! $this->forever($key, $value)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return $this->putMany(is_array($values) ? $values : iterator_to_array($values), $ttl);
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param  \UnitEnum|array|string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function add($key, $value, $ttl = null)
    {
        $key = enum_value($key);

        $seconds = null;

        if ($ttl !== null) {
            $seconds = $this->getSeconds($ttl);

            if ($seconds <= 0) {
                return false;
            }

            // If the store has an "add" method we will call the method on the store so it
            // has a chance to override this logic. Some drivers better support the way
            // this operation should work with a total "atomic" implementation of it.
            if (method_exists($this->store, 'add')) {
                return $this->store->add(
                    $this->itemKey($key), $value, $seconds
                );
            }
        }

        // If the value did not exist in the cache, we will put the value in the cache
        // so it exists for subsequent requests. Then, we will return true so it is
        // easy to know if the value gets added. Otherwise, we will return false.
        if (is_null($this->get($key))) {
            return $this->put($key, $value, $seconds);
        }

        return false;
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->store->increment(enum_value($key), $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->store->decrement(enum_value($key), $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        $key = enum_value($key);

        $this->event(new WritingKey($this->getName(), $key, $value));

        $result = $this->store->forever($this->itemKey($key), $value);

        if ($result) {
            $this->event(new KeyWritten($this->getName(), $key, $value));
        } else {
            $this->event(new KeyWriteFailed($this->getName(), $key, $value));
        }

        return $result;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @template TCacheValue
     *
     * @param  \UnitEnum|string  $key
     * @param  \Closure|\DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  \Closure(): TCacheValue  $callback
     * @return TCacheValue
     */
    public function remember($key, $ttl, Closure $callback)
    {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately and if
        // not we will execute the given Closure and cache the result of that for a
        // given number of seconds so it's available for all subsequent requests.
        if (! is_null($value)) {
            return $value;
        }

        $value = $callback();

        $this->put($key, $value, value($ttl, $value));

        return $value;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @template TCacheValue
     *
     * @param  \UnitEnum|string  $key
     * @param  \Closure(): TCacheValue  $callback
     * @return TCacheValue
     */
    public function sear($key, Closure $callback)
    {
        return $this->rememberForever($key, $callback);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @template TCacheValue
     *
     * @param  \UnitEnum|string  $key
     * @param  \Closure(): TCacheValue  $callback
     * @return TCacheValue
     */
    public function rememberForever($key, Closure $callback)
    {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately
        // and if not we will execute the given Closure and cache the result
        // of that forever so it is available for all subsequent requests.
        if (! is_null($value)) {
            return $value;
        }

        $this->forever($key, $value = $callback());

        return $value;
    }

    /**
     * Retrieve an item from the cache by key, refreshing it in the background if it is stale.
     *
     * @template TCacheValue
     *
     * @param  \UnitEnum|string  $key
     * @param  array{ 0: \DateTimeInterface|\DateInterval|int, 1: \DateTimeInterface|\DateInterval|int }  $ttl
     * @param  (callable(): TCacheValue)  $callback
     * @param  array{ seconds?: int, owner?: string }|null  $lock
     * @param  bool  $alwaysDefer
     * @return TCacheValue
     */
    public function flexible($key, $ttl, $callback, $lock = null, $alwaysDefer = false)
    {
        $key = enum_value($key);

        [
            $key => $value,
            "illuminate:cache:flexible:created:{$key}" => $created,
        ] = $this->many([$key, "illuminate:cache:flexible:created:{$key}"]);

        if (in_array(null, [$value, $created], true)) {
            return tap(value($callback), fn ($value) => $this->putMany([
                $key => $value,
                "illuminate:cache:flexible:created:{$key}" => Carbon::now()->getTimestamp(),
            ], $ttl[1]));
        }

        if (($created + $this->getSeconds($ttl[0])) > Carbon::now()->getTimestamp()) {
            return $value;
        }

        $refresh = function () use ($key, $ttl, $callback, $lock, $created) {
            $this->store->lock(
                "illuminate:cache:flexible:lock:{$key}",
                $lock['seconds'] ?? 0,
                $lock['owner'] ?? null,
            )->get(function () use ($key, $callback, $created, $ttl) {
                if ($created !== $this->get("illuminate:cache:flexible:created:{$key}")) {
                    return;
                }

                $this->putMany([
                    $key => value($callback),
                    "illuminate:cache:flexible:created:{$key}" => Carbon::now()->getTimestamp(),
                ], $ttl[1]);
            });
        };

        defer($refresh, "illuminate:cache:flexible:{$key}", $alwaysDefer);

        return $value;
    }

    /**
     * Execute a callback while holding an atomic lock on a cache mutex to prevent overlapping calls.
     *
     * @template TReturn
     *
     * @param  \UnitEnum|string  $key
     * @param  callable(): TReturn  $callback
     * @param  int  $lockFor
     * @param  int  $waitFor
     * @param  string|null  $owner
     * @return TReturn
     *
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     */
    public function withoutOverlapping($key, callable $callback, $lockFor = 0, $waitFor = 10, $owner = null)
    {
        return $this->store->lock(enum_value($key), $lockFor, $owner)->block($waitFor, $callback);
    }

    /**
     * Funnel a callback for a maximum number of simultaneous executions.
     *
     * @param  \UnitEnum|string  $name
     * @return \Illuminate\Cache\Limiters\ConcurrencyLimiterBuilder
     */
    public function funnel($name)
    {
        if (! $this->store instanceof LockProvider) {
            throw new BadMethodCallException('This cache store does not support locks.');
        }

        return new ConcurrencyLimiterBuilder($this, enum_value($name));
    }

    /**
     * Remove an item from the cache.
     *
     * @param  \UnitEnum|array|string  $key
     * @return bool
     */
    public function forget($key)
    {
        $key = enum_value($key);

        $this->event(new ForgettingKey($this->getName(), $key));

        return tap($this->store->forget($this->itemKey($key)), function ($result) use ($key) {
            if ($result) {
                $this->event(new KeyForgotten($this->getName(), $key));
            } else {
                $this->event(new KeyForgetFailed($this->getName(), $key));
            }
        });
    }

    /**
     * Remove an item from the cache.
     *
     * @param  \UnitEnum|array|string  $key
     * @return bool
     */
    public function delete($key): bool
    {
        return $this->forget($key);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        $result = true;

        foreach ($keys as $key) {
            if (! $this->forget($key)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->event(new CacheFlushing($this->getName()));

        $result = $this->store->flush();

        if ($result) {
            $this->event(new CacheFlushed($this->getName()));
        } else {
            $this->event(new CacheFlushFailed($this->getName()));
        }

        return $result;
    }

    /**
     * Begin executing a new tags operation if the store supports it.
     *
     * @param  mixed  $names
     * @return \Illuminate\Cache\TaggedCache
     *
     * @throws \BadMethodCallException
     */
    public function tags($names)
    {
        if (! $this->supportsTags()) {
            throw new BadMethodCallException('This cache store does not support tagging.');
        }

        $cache = $this->store->tags(is_array($names) ? $names : func_get_args());

        $cache->config = $this->config;

        if (! is_null($this->events)) {
            $cache->setEventDispatcher($this->events);
        }

        return $cache->setDefaultCacheTime($this->default);
    }

    /**
     * Format the key for a cache item.
     *
     * @param  string  $key
     * @return string
     */
    protected function itemKey($key)
    {
        return $key;
    }

    /**
     * Calculate the number of seconds for the given TTL.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $ttl
     * @return int
     */
    protected function getSeconds($ttl)
    {
        $duration = $this->parseDateInterval($ttl);

        if ($duration instanceof DateTimeInterface) {
            $duration = (int) ceil(
                Carbon::now()->diffInMilliseconds($duration, false) / 1000
            );
        }

        return (int) ($duration > 0 ? $duration : 0);
    }

    /**
     * Get the name of the cache store.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->config['store'] ?? null;
    }

    /**
     * Determine if the current store supports tags.
     *
     * @return bool
     */
    public function supportsTags()
    {
        return method_exists($this->store, 'tags');
    }

    /**
     * Get the default cache time.
     *
     * @return int|null
     */
    public function getDefaultCacheTime()
    {
        return $this->default;
    }

    /**
     * Set the default cache time in seconds.
     *
     * @param  int|null  $seconds
     * @return $this
     */
    public function setDefaultCacheTime($seconds)
    {
        $this->default = $seconds;

        return $this;
    }

    /**
     * Get the cache store implementation.
     *
     * @return \Illuminate\Contracts\Cache\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set the cache store implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @return static
     */
    public function setStore($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Fire an event for this cache instance.
     *
     * @param  object|string  $event
     * @return void
     */
    protected function event($event)
    {
        $this->events?->dispatch($event);
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getEventDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function setEventDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Determine if a cached value exists.
     *
     * @param  \UnitEnum|string  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  \UnitEnum|string  $key
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Store an item in the cache for the default time.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->put($key, $value, $this->default);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  \UnitEnum|string  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->forget($key);
    }

    /**
     * Handle dynamic calls into macros or pass missing methods to the store.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->store->$method(...$parameters);
    }

    /**
     * Clone cache repository instance.
     *
     * @return void
     */
    public function __clone()
    {
        $this->store = clone $this->store;
    }
}
