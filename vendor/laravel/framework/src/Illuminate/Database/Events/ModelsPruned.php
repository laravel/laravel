<?php

namespace Illuminate\Database\Events;

class ModelsPruned
{
    /**
     * Create a new event instance.
     *
     * @param  string  $model  The class name of the model that was pruned.
     * @param  int  $count  The number of pruned records.
     */
    public function __construct(
        public $model,
        public $count,
    ) {
    }
}
