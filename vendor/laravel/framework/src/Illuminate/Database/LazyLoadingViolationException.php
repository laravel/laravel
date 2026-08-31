<?php

namespace Illuminate\Database;

use RuntimeException;

class LazyLoadingViolationException extends RuntimeException
{
    /**
     * The name of the affected Eloquent model.
     *
     * @var string
     */
    public $model;

    /**
     * The name of the relation.
     *
     * @var string
     */
    public $relation;

    /**
     * Create a new exception instance.
     *
     * @param  object  $model
     * @param  string  $relation
     */
    public function __construct($model, $relation)
    {
        $class = get_class($model);

        parent::__construct("Attempted to lazy load [{$relation}] on model [{$class}] but lazy loading is disabled.");

        $this->model = $class;
        $this->relation = $relation;
    }
}
