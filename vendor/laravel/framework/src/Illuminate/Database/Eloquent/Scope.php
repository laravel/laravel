<?php

namespace Illuminate\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $builder
     * @param  TModel  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
