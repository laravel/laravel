<?php

namespace Illuminate\Database\Eloquent\Factories;

use Illuminate\Support\Arr;

class CrossJoinSequence extends Sequence
{
    /**
     * Create a new cross join sequence instance.
     *
     * @param  array  ...$sequences
     */
    public function __construct(...$sequences)
    {
        $crossJoined = array_map(
            function ($a) {
                return array_merge(...$a);
            },
            Arr::crossJoin(...$sequences),
        );

        parent::__construct(...$crossJoined);
    }
}
