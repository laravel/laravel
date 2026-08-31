<?php

namespace Laravel\Prompts\Elements;

class NumberedList implements ElementContract
{
    /**
     * @param  array<int, string>  $items
     */
    public function __construct(
        public readonly array $items,
        public readonly bool $spaced = false,
    ) {
        //
    }
}
