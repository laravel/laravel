<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use ReflectionClass;

/**
 * @template TCollection of \Illuminate\Database\Eloquent\Collection
 */
trait HasCollection
{
    /**
     * The resolved collection class names by model.
     *
     * @var array<class-string<static>, class-string<TCollection>>
     */
    protected static array $resolvedCollectionClasses = [];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array<array-key, \Illuminate\Database\Eloquent\Model>  $models
     * @return TCollection
     */
    public function newCollection(array $models = [])
    {
        static::$resolvedCollectionClasses[static::class] ??= ($this->resolveCollectionFromAttribute() ?? static::$collectionClass);

        $collection = new static::$resolvedCollectionClasses[static::class]($models);

        if (Model::isAutomaticallyEagerLoadingRelationships()) {
            $collection->withRelationshipAutoloading();
        }

        return $collection;
    }

    /**
     * Resolve the collection class name from the CollectedBy attribute.
     *
     * @return class-string<TCollection>|null
     */
    public function resolveCollectionFromAttribute()
    {
        $reflectionClass = new ReflectionClass(static::class);

        $attributes = $reflectionClass->getAttributes(CollectedBy::class);

        if (! isset($attributes[0]) || ! isset($attributes[0]->getArguments()[0])) {
            return;
        }

        return $attributes[0]->getArguments()[0];
    }
}
