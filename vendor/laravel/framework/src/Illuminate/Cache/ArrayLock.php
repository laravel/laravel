<?php

namespace Illuminate\Cache;

use Illuminate\Support\Carbon;

class ArrayLock extends Lock
{
    /**
     * The parent array cache store.
     *
     * @var \Illuminate\Cache\ArrayStore
     */
    protected $store;

    /**
     * Create a new lock instance.
     *
     * @param  \Illuminate\Cache\ArrayStore  $store
     * @param  string  $name
     * @param  int  $seconds
     * @param  string|null  $owner
     */
    public function __construct($store, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);

        $this->store = $store;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        $expiration = $this->store->locks[$this->name]['expiresAt'] ?? Carbon::now()->addSecond();

        if ($this->exists() && $expiration->isFuture()) {
            return false;
        }

        $this->store->locks[$this->name] = [
            'owner' => $this->owner,
            'expiresAt' => $this->seconds === 0 ? null : Carbon::now()->addSeconds($this->seconds),
        ];

        return true;
    }

    /**
     * Determine if the current lock exists.
     *
     * @return bool
     */
    protected function exists()
    {
        return isset($this->store->locks[$this->name]);
    }

    /**
     * Attempt to refresh the lock for the given number of seconds.
     *
     * @param  int|null  $seconds
     * @return bool
     */
    public function refresh($seconds = null)
    {
        if (! $this->isOwnedByCurrentProcess()) {
            return false;
        }

        $expiresAt = $this->store->locks[$this->name]['expiresAt'];

        if ($expiresAt && ! $expiresAt->isFuture()) {
            return false;
        }

        $seconds ??= $this->seconds;

        $this->store->locks[$this->name]['expiresAt'] = $seconds === 0 ? null : Carbon::now()->addSeconds($seconds);

        return true;
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release()
    {
        if (! $this->exists()) {
            return false;
        }

        if (! $this->isOwnedByCurrentProcess()) {
            return false;
        }

        $this->forceRelease();

        return true;
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string|null
     */
    protected function getCurrentOwner()
    {
        if (! $this->exists()) {
            return null;
        }

        return $this->store->locks[$this->name]['owner'];
    }

    /**
     * Releases this lock regardless of ownership.
     *
     * @return void
     */
    public function forceRelease()
    {
        unset($this->store->locks[$this->name]);
    }
}
