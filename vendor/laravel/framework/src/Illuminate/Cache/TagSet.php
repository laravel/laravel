<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\Store;

class TagSet
{
    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Store
     */
    protected $store;

    /**
     * The tag names.
     *
     * @var array
     */
    protected $names = [];

    /**
     * Create a new TagSet instance.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @param  array  $names
     */
    public function __construct(Store $store, array $names = [])
    {
        $this->store = $store;
        $this->names = $names;
    }

    /**
     * Reset all tags in the set.
     *
     * @return void
     */
    public function reset()
    {
        array_walk($this->names, $this->resetTag(...));
    }

    /**
     * Reset the tag and return the new tag identifier.
     *
     * @param  string  $name
     * @return string
     */
    public function resetTag($name)
    {
        $this->store->forever($this->tagKey($name), $id = str_replace('.', '', uniqid('', true)));

        return $id;
    }

    /**
     * Flush all the tags in the set.
     *
     * @return void
     */
    public function flush()
    {
        array_walk($this->names, $this->flushTag(...));
    }

    /**
     * Flush the tag from the cache.
     *
     * @param  string  $name
     */
    public function flushTag($name)
    {
        $this->store->forget($this->tagKey($name));
    }

    /**
     * Get a unique namespace that changes when any of the tags are flushed.
     *
     * @return string
     */
    public function getNamespace()
    {
        return implode('|', $this->tagIds());
    }

    /**
     * Get an array of tag identifiers for all of the tags in the set.
     *
     * @return array
     */
    protected function tagIds()
    {
        return array_map($this->tagId(...), $this->names);
    }

    /**
     * Get the unique tag identifier for a given tag.
     *
     * @param  string  $name
     * @return string
     */
    public function tagId($name)
    {
        return $this->store->get($this->tagKey($name)) ?: $this->resetTag($name);
    }

    /**
     * Get the tag identifier key for a given tag.
     *
     * @param  string  $name
     * @return string
     */
    public function tagKey($name)
    {
        return 'tag:'.$name.':key';
    }

    /**
     * Get all of the tag names in the set.
     *
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }
}
