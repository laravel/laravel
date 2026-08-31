<?php

namespace Illuminate\Cache\Events;

class CacheHit extends CacheEvent
{
    /**
     * The value that was retrieved.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new event instance.
     *
     * @param  string|null  $storeName
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $tags
     */
    public function __construct($storeName, $key, $value, array $tags = [])
    {
        parent::__construct($storeName, $key, $tags);

        $this->value = $value;
    }
}
