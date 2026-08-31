<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Bus\Queueable;

class BroadcastMessage
{
    use Queueable;

    /**
     * The data for the notification.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param  array  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Set the message data.
     *
     * @param  array  $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }
}
