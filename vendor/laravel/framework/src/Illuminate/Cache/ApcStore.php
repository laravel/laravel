<?php

namespace Illuminate\Cache;

class ApcStore extends TaggableStore
{
    use RetrievesMultipleKeys;

    /**
     * The APC wrapper instance.
     *
     * @var \Illuminate\Cache\ApcWrapper
     */
    protected $apc;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new APC store.
     *
     * @param  \Illuminate\Cache\ApcWrapper  $apc
     * @param  string  $prefix
     */
    public function __construct(ApcWrapper $apc, $prefix = '')
    {
        $this->apc = $apc;
        $this->prefix = $prefix;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->apc->get($this->prefix.$key);
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        return $this->apc->put($this->prefix.$key, $value, $seconds);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  int  $value
     * @return int|false
     */
    public function increment($key, $value = 1)
    {
        return $this->apc->increment($this->prefix.$key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  int  $value
     * @return int|false
     */
    public function decrement($key, $value = 1)
    {
        return $this->apc->decrement($this->prefix.$key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->apc->delete($this->prefix.$key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        return $this->apc->flush();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }
}
