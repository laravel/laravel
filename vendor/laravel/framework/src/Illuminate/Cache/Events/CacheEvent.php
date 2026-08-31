<?php

namespace Illuminate\Cache\Events;

abstract class CacheEvent
{
    /**
     * The name of the cache store.
     *
     * @var string|null
     */
    public $storeName;

    /**
     * The key of the event.
     *
     * @var string
     */
    public $key;

    /**
     * The tags that were assigned to the key.
     *
     * @var array
     */
    public $tags;

    /**
     * Create a new event instance.
     *
     * @param  string|null  $storeName
     * @param  string  $key
     * @param  array  $tags
     */
    public function __construct($storeName, $key, array $tags = [])
    {
        $this->storeName = $storeName;
        $this->key = $key;
        $this->tags = $tags;
    }

    /**
     * Set the tags for the cache event.
     *
     * @param  array  $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
}
