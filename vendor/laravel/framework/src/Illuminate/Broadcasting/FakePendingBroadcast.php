<?php

namespace Illuminate\Broadcasting;

class FakePendingBroadcast extends PendingBroadcast
{
    /**
     * Create a new pending broadcast instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Broadcast the event using a specific broadcaster.
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function via($connection = null)
    {
        return $this;
    }

    /**
     * Broadcast the event to everyone except the current user.
     *
     * @return $this
     */
    public function toOthers()
    {
        return $this;
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        //
    }
}
