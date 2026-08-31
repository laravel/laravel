<?php

namespace Illuminate\Support\Traits;

use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Attributes\UseResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use LogicException;
use ReflectionClass;

trait TransformsToResourceCollection
{
    /**
     * Create a new resource collection instance for the given resource.
     *
     * @param  class-string<\Illuminate\Http\Resources\Json\JsonResource>|null  $resourceClass
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     *
     * @throws \Throwable
     */
    public function toResourceCollection(?string $resourceClass = null): ResourceCollection
    {
        if ($resourceClass === null) {
            return $this->guessResourceCollection();
        }

        return $resourceClass::collection($this);
    }

    /**
     * Guess the resource collection for the items.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     *
     * @throws \Throwable
     */
    protected function guessResourceCollection(): ResourceCollection
    {
        if ($this->isEmpty()) {
            return new ResourceCollection($this);
        }

        $model = $this->items[0] ?? null;

        throw_unless(is_object($model), LogicException::class, 'Resource collection guesser expects the collection to contain objects.');

        /** @var class-string<Model> $className */
        $className = get_class($model);

        throw_unless(method_exists($className, 'guessResourceName'), LogicException::class, sprintf('Expected class %s to implement guessResourceName method. Make sure the model uses the TransformsToResource trait.', $className));

        $useResourceCollection = $this->resolveResourceCollectionFromAttribute($className);

        if ($useResourceCollection !== null && class_exists($useResourceCollection)) {
            return new $useResourceCollection($this);
        }

        $useResource = $this->resolveResourceFromAttribute($className);

        if ($useResource !== null && class_exists($useResource)) {
            return $useResource::collection($this);
        }

        $resourceClasses = $className::guessResourceName();

        foreach ($resourceClasses as $resourceClass) {
            $resourceCollection = $resourceClass.'Collection';

            if (is_string($resourceCollection) && class_exists($resourceCollection)) {
                return new $resourceCollection($this);
            }
        }

        foreach ($resourceClasses as $resourceClass) {
            if (is_string($resourceClass) && class_exists($resourceClass)) {
                return $resourceClass::collection($this);
            }
        }

        throw new LogicException(sprintf('Failed to find resource class for model [%s].', $className));
    }

    /**
     * Get the resource class from the class attribute.
     *
     * @param  class-string<\Illuminate\Http\Resources\Json\JsonResource>  $class
     * @return class-string<*>|null
     */
    protected function resolveResourceFromAttribute(string $class): ?string
    {
        if (! class_exists($class)) {
            return null;
        }

        $attributes = (new ReflectionClass($class))->getAttributes(UseResource::class);

        return $attributes !== []
            ? $attributes[0]->newInstance()->class
            : null;
    }

    /**
     * Get the resource collection class from the class attribute.
     *
     * @param  class-string<\Illuminate\Http\Resources\Json\ResourceCollection>  $class
     * @return class-string<*>|null
     */
    protected function resolveResourceCollectionFromAttribute(string $class): ?string
    {
        if (! class_exists($class)) {
            return null;
        }

        $attributes = (new ReflectionClass($class))->getAttributes(UseResourceCollection::class);

        return $attributes !== []
            ? $attributes[0]->newInstance()->class
            : null;
    }
}
