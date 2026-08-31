<?php

namespace Illuminate\Support;

use RuntimeException;

class MultipleItemsFoundException extends RuntimeException
{
    /**
     * The number of items found.
     *
     * @var int
     */
    public $count;

    /**
     * Create a new exception instance.
     *
     * @param  int  $count
     * @param  int  $code
     * @param  \Throwable|null  $previous
     */
    public function __construct($count, $code = 0, $previous = null)
    {
        $this->count = $count;

        parent::__construct("$count items were found.", $code, $previous);
    }

    /**
     * Get the number of items found.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
