<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\InteractsWithTime;

class ArrayStore extends TaggableStore implements LockProvider
{
    use InteractsWithTime, RetrievesMultipleKeys;

    /**
     * The array of stored values.
     *
     * @var array<string, array{value: mixed, expiresAt: float}>
     */
    protected $storage = [];

    /**
     * The array of locks.
     *
     * @var array<string, array{owner: ?string, expiresAt: ?\Illuminate\Support\Carbon}>
     */
    public $locks = [];

    /**
     * Indicates if values are serialized within the store.
     *
     * @var bool
     */
    protected $serializesValues;

    /**
     * The classes that should be allowed during unserialization.
     *
     * @var array|bool|null
     */
    protected $serializableClasses;

    /**
     * Create a new Array store.
     *
     * @param  bool  $serializesValues
     * @param  array|bool|null  $serializableClasses
     */
    public function __construct($serializesValues = false, $serializableClasses = null)
    {
        $this->serializesValues = $serializesValues;
        $this->serializableClasses = $serializableClasses;
    }

    /**
     * Get all of the cached values and their expiration times.
     *
     * @param  bool  $unserialize
     * @return array<string, array{value: mixed, expiresAt: float}>
     */
    public function all($unserialize = true)
    {
        if ($unserialize === false || $this->serializesValues === false) {
            return $this->storage;
        }

        $storage = [];

        foreach ($this->storage as $key => $data) {
            $storage[$key] = [
                'value' => $this->unserialize($data['value']),
                'expiresAt' => $data['expiresAt'],
            ];
        }

        return $storage;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        if (! isset($this->storage[$key])) {
            return;
        }

        $item = $this->storage[$key];

        $expiresAt = $item['expiresAt'] ?? 0;

        if ($expiresAt !== 0 && (Carbon::now()->getPreciseTimestamp(3) / 1000) >= $expiresAt) {
            $this->forget($key);

            return;
        }

        return $this->serializesValues ? $this->unserialize($item['value']) : $item['value'];
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
        $this->storage[$key] = [
            'value' => $this->serializesValues ? serialize($value) : $value,
            'expiresAt' => $this->calculateExpiration($seconds),
        ];

        return true;
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
        if (! is_null($existing = $this->get($key))) {
            return tap(((int) $existing) + $value, function ($incremented) use ($key) {
                $value = $this->serializesValues ? serialize($incremented) : $incremented;

                $this->storage[$key]['value'] = $value;
            });
        }

        $this->forever($key, $value);

        return $value;
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
        return $this->increment($key, $value * -1);
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
        return $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        if (array_key_exists($key, $this->storage)) {
            unset($this->storage[$key]);

            return true;
        }

        return false;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->storage = [];

        return true;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return '';
    }

    /**
     * Get the expiration time of the key.
     *
     * @param  int  $seconds
     * @return float
     */
    protected function calculateExpiration($seconds)
    {
        return $this->toTimestamp($seconds);
    }

    /**
     * Get the UNIX timestamp, with milliseconds, for the given number of seconds in the future.
     *
     * @param  int  $seconds
     * @return float
     */
    protected function toTimestamp($seconds)
    {
        return $seconds > 0 ? (Carbon::now()->getPreciseTimestamp(3) / 1000) + $seconds : 0;
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
        return new ArrayLock($this, $name, $seconds, $owner);
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
     * Unserialize the given value.
     *
     * @param  string  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if ($this->serializableClasses !== null) {
            return unserialize($value, ['allowed_classes' => $this->serializableClasses]);
        }

        return unserialize($value);
    }
}
