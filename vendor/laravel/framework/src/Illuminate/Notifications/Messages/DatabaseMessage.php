<?php

namespace Illuminate\Notifications\Messages;

class DatabaseMessage
{
    /**
     * The data that should be stored with the notification.
     *
     * @var array
     */
    public $data = [];

    /**
     * Create a new database message.
     *
     * @param  array  $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
}
