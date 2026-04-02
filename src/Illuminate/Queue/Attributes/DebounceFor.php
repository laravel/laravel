<?php

namespace Illuminate\Queue\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DebounceFor
{
    /**
     * Create a new attribute instance.
     */
    public function __construct(public int $debounceFor)
    {
        //
    }
}
