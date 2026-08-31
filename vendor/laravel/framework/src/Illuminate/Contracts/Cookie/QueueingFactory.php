<?php

namespace Illuminate\Contracts\Cookie;

interface QueueingFactory extends Factory
{
    /**
     * Queue a cookie to send with the next response.
     *
     * @param  mixed  ...$parameters
     * @return void
     */
    public function queue(...$parameters);

    /**
     * Remove a cookie from the queue.
     *
     * @param  string  $name
     * @param  string|null  $path
     * @return void
     */
    public function unqueue($name, $path = null);

    /**
     * Get the cookies which have been queued for the next request.
     *
     * @return array
     */
    public function getQueuedCookies();
}
