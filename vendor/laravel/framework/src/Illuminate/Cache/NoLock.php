<?php

namespace Illuminate\Cache;

class NoLock extends Lock
{
    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        return true;
    }

    /**
     * Attempt to refresh the lock for the given number of seconds.
     *
     * @param  int|null  $seconds
     * @return bool
     */
    public function refresh($seconds = null)
    {
        return true;
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release()
    {
        return true;
    }

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease()
    {
        //
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return mixed
     */
    protected function getCurrentOwner()
    {
        return $this->owner;
    }
}
