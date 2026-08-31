<?php

namespace Illuminate\Cache;

use Illuminate\Cache\Events\CacheFlushed;
use Illuminate\Cache\Events\CacheFlushing;
use Illuminate\Contracts\Cache\Store;

class TaggedCache extends Repository
{
    use RetrievesMultipleKeys {
        putMany as putManyAlias;
    }

    /**
     * The tag set instance.
     *
     * @var \Illuminate\Cache\TagSet
     */
    protected $tags;

    /**
     * Create a new tagged cache instance.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @param  \Illuminate\Cache\TagSet  $tags
     */
    public function __construct(Store $store, TagSet $tags)
    {
        parent::__construct($store);

        $this->tags = $tags;
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int|null  $ttl
     * @return bool
     */
    public function putMany(array $values, $ttl = null)
    {
        if ($ttl === null) {
            return $this->putManyForever($values);
        }

        return $this->putManyAlias($values, $ttl);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->store->increment($this->itemKey($key), $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->store->decrement($this->itemKey($key), $value);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->event(new CacheFlushing($this->getName()));

        $this->tags->reset();

        $this->event(new CacheFlushed($this->getName()));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function itemKey($key)
    {
        return $this->taggedItemKey($key);
    }

    /**
     * Get a fully-qualified key for a tagged item.
     *
     * @param  string  $key
     * @return string
     */
    public function taggedItemKey($key)
    {
        return sha1($this->tags->getNamespace()).':'.$key;
    }

    /**
     * Fire an event for this cache instance.
     *
     * @param  object  $event
     * @return void
     */
    protected function event($event)
    {
        if (method_exists($event, 'setTags')) {
            $event->setTags($this->tags->getNames());
        }

        parent::event($event);
    }

    /**
     * Get the tag set instance.
     *
     * @return \Illuminate\Cache\TagSet
     */
    public function getTags()
    {
        return $this->tags;
    }
}
