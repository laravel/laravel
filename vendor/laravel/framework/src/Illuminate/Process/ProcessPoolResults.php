<?php

namespace Illuminate\Process;

use ArrayAccess;
use Illuminate\Support\Collection;

class ProcessPoolResults implements ArrayAccess
{
    /**
     * The results of the processes.
     *
     * @var array
     */
    protected $results = [];

    /**
     * Create a new process pool result set.
     *
     * @param  array  $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * Determine if all of the processes in the pool were successful.
     *
     * @return bool
     */
    public function successful()
    {
        return $this->collect()->every(fn ($p) => $p->successful());
    }

    /**
     * Determine if any of the processes in the pool failed.
     *
     * @return bool
     */
    public function failed()
    {
        return ! $this->successful();
    }

    /**
     * Get the results as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect()
    {
        return new Collection($this->results);
    }

    /**
     * Determine if the given array offset exists.
     *
     * @param  int  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->results[$offset]);
    }

    /**
     * Get the result at the given offset.
     *
     * @param  int  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->results[$offset];
    }

    /**
     * Set the result at the given offset.
     *
     * @param  int  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->results[$offset] = $value;
    }

    /**
     * Unset the result at the given offset.
     *
     * @param  int  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->results[$offset]);
    }
}
