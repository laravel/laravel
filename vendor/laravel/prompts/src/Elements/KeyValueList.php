<?php

namespace Laravel\Prompts\Elements;

class KeyValueList implements ElementContract
{
    /**
     * @param  array<string, string>  $items
     */
    public function __construct(public readonly array $items)
    {
        //
    }
}
