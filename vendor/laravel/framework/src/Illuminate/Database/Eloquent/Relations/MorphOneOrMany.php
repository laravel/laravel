<?php

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TResult
 *
 * @extends \Illuminate\Database\Eloquent\Relations\HasOneOrMany<TRelatedModel, TDeclaringModel, TResult>
 */
abstract class MorphOneOrMany extends HasOneOrMany
{
    /**
     * The foreign key type for the relationship.
     *
     * @var string
     */
    protected $morphType;

    /**
     * The class name of the parent model.
     *
     * @var class-string<TRelatedModel>
     */
    protected $morphClass;

    /**
     * Create a new morph one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     */
    public function __construct(Builder $query, Model $parent, $type, $id, $localKey)
    {
        $this->morphType = $type;

        $this->morphClass = $parent->getMorphClass();

        parent::__construct($query, $parent, $id, $localKey);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->getRelationQuery()->where($this->morphType, $this->morphClass);

            parent::addConstraints();
        }
    }

    /** @inheritDoc */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);

        $this->getRelationQuery()->where($this->morphType, $this->morphClass);
    }

    /**
     * Create a new instance of the related model. Allow mass-assignment.
     *
     * @param  array  $attributes
     * @return TRelatedModel
     */
    public function forceCreate(array $attributes = [])
    {
        $attributes[$this->getForeignKeyName()] = $this->getParentKey();
        $attributes[$this->getMorphType()] = $this->morphClass;

        return $this->applyInverseRelationToModel($this->related->forceCreate($attributes));
    }

    /**
     * Set the foreign ID and type for creating a related model.
     *
     * @param  TRelatedModel  $model
     * @return void
     */
    protected function setForeignAttributesForCreate(Model $model)
    {
        $model->{$this->getForeignKeyName()} = $this->getParentKey();

        $model->{$this->getMorphType()} = $this->morphClass;

        foreach ($this->getQuery()->pendingAttributes as $key => $value) {
            $attributes ??= $model->getAttributes();

            if (! array_key_exists($key, $attributes)) {
                $model->setAttribute($key, $value);
            }
        }

        $this->applyInverseRelationToModel($model);
    }

    /**
     * Insert new records or update the existing ones.
     *
     * @param  array  $values
     * @param  array|string  $uniqueBy
     * @param  array|null  $update
     * @return int
     */
    public function upsert(array $values, $uniqueBy, $update = null)
    {
        if (! empty($values) && ! is_array(array_first($values))) {
            $values = [$values];
        }

        foreach ($values as $key => $value) {
            $values[$key][$this->getMorphType()] = $this->getMorphClass();
        }

        return parent::upsert($values, $uniqueBy, $update);
    }

    /** @inheritDoc */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)->where(
            $query->qualifyColumn($this->getMorphType()), $this->morphClass
        );
    }

    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getQualifiedMorphType()
    {
        return $this->morphType;
    }

    /**
     * Get the plain morph type name without the table.
     *
     * @return string
     */
    public function getMorphType()
    {
        return last(explode('.', $this->morphType));
    }

    /**
     * Get the class name of the parent model.
     *
     * @return class-string<TRelatedModel>
     */
    public function getMorphClass()
    {
        return $this->morphClass;
    }

    /**
     * Get the possible inverse relations for the parent model.
     *
     * @return array<non-empty-string>
     */
    protected function getPossibleInverseRelations(): array
    {
        return array_unique([
            Str::beforeLast($this->getMorphType(), '_type'),
            ...parent::getPossibleInverseRelations(),
        ]);
    }
}
