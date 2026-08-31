<?php

namespace Illuminate\Cache;

use Illuminate\Database\Connection;
use Illuminate\Database\DetectsConcurrencyErrors;
use Illuminate\Database\QueryException;
use Throwable;

class DatabaseLock extends Lock
{
    use DetectsConcurrencyErrors;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table;

    /**
     * The prune probability odds.
     *
     * @var array{int, int}|null
     */
    protected $lottery;

    /**
     * The default number of seconds that a lock should be held.
     *
     * @var int
     */
    protected $defaultTimeoutInSeconds;

    /**
     * Create a new lock instance.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     * @param  string  $name
     * @param  int  $seconds
     * @param  string|null  $owner
     * @param  array{int, int}|null  $lottery
     * @param  int  $defaultTimeoutInSeconds
     */
    public function __construct(Connection $connection, $table, $name, $seconds, $owner = null, $lottery = [2, 100], $defaultTimeoutInSeconds = 86400)
    {
        parent::__construct($name, $seconds, $owner);

        $this->connection = $connection;
        $this->table = $table;
        $this->lottery = $lottery;
        $this->defaultTimeoutInSeconds = $defaultTimeoutInSeconds;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     *
     * @throws \Throwable
     */
    public function acquire()
    {
        try {
            $this->connection->table($this->table)->insert([
                'key' => $this->name,
                'owner' => $this->owner,
                'expiration' => $this->expiresAt(),
            ]);

            $acquired = true;
        } catch (QueryException) {
            $updated = $this->connection->table($this->table)
                ->where('key', $this->name)
                ->where(function ($query) {
                    return $query->where('owner', $this->owner)->orWhere('expiration', '<=', $this->currentTime());
                })->update([
                    'owner' => $this->owner,
                    'expiration' => $this->expiresAt(),
                ]);

            $acquired = $updated >= 1;
        }

        if (count($this->lottery ?? []) === 2 && random_int(1, $this->lottery[1]) <= $this->lottery[0]) {
            $this->pruneExpiredLocks();
        }

        return $acquired;
    }

    /**
     * Attempt to refresh the lock for the given number of seconds.
     *
     * @param  int|null  $seconds
     * @return bool
     */
    public function refresh($seconds = null)
    {
        $seconds ??= $this->seconds;

        return $this->connection->table($this->table)
            ->where('key', $this->name)
            ->where('owner', $this->owner)
            ->where('expiration', '>', $this->currentTime())
            ->update(['expiration' => $this->expiresAt($seconds)]) >= 1;
    }

    /**
     * Get the UNIX timestamp indicating when the lock should expire.
     *
     * @return int
     */
    protected function expiresAt($seconds = null)
    {
        $seconds ??= $this->seconds;

        $lockTimeout = $seconds > 0 ? $seconds : $this->defaultTimeoutInSeconds;

        return $this->currentTime() + $lockTimeout;
    }

    /**
     * Release the lock.
     *
     * @return bool
     *
     * @throws \Throwable
     */
    public function release()
    {
        if ($this->isOwnedByCurrentProcess()) {
            try {
                $this->connection->table($this->table)
                    ->where('key', $this->name)
                    ->where('owner', $this->owner)
                    ->delete();

                return true;
            } catch (Throwable $e) {
                if ($this->causedByConcurrencyError($e)) {
                    return true;
                }

                throw $e;
            }
        }

        return false;
    }

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease()
    {
        $this->connection->table($this->table)
            ->where('key', $this->name)
            ->delete();
    }

    /**
     * Deletes locks that are past expiration.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function pruneExpiredLocks()
    {
        try {
            $this->connection->table($this->table)
                ->where('expiration', '<=', $this->currentTime())
                ->delete();
        } catch (Throwable $e) {
            if (! $this->causedByConcurrencyError($e)) {
                throw $e;
            }
        }
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string|null
     */
    protected function getCurrentOwner()
    {
        return $this->connection->table($this->table)->where('key', $this->name)->first()?->owner;
    }

    /**
     * Get the name of the database connection being used to manage the lock.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection->getName();
    }
}
