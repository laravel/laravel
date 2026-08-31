<?php

namespace Illuminate\Database\Eloquent;

use RuntimeException;

class RelationNotFoundException extends RuntimeException
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
     * @param  string|null  $type
     * @return static
     */
    public static function make($model, $relation, $type = null)
    {
        $class = get_class($model);

        $instance = new static(
            is_null($type)
                ? "Call to undefined relationship [{$relation}] on model [{$class}]."
                : "Call to undefined relationship [{$relation}] on model [{$class}] of type [{$type}].",
        );

        $instance->model = $class;
        $instance->relation = $relation;

        return $instance;
    }
}
