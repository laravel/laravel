<?php

namespace Illuminate\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LogicException;
use ReflectionClass;
use Traversable;

trait CollectsResources
{
    /**
     * Map the given collection resource into its individual resources.
     *
     * @param  mixed  $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if ($resource instanceof MissingValue) {
            return $resource;
        }

        if (is_array($resource)) {
            $resource = new Collection($resource);
        }

        $collects = $this->collects();

        $this->collection = $collects && ! $resource->first() instanceof $collects
            ? $resource->mapInto($collects)
            : $resource->toBase();

        return ($resource instanceof AbstractPaginator || $resource instanceof AbstractCursorPaginator)
            ? $resource->setCollection($this->collection)
            : $this->collection;
    }

    /**
     * Get the resource that this resource collects.
     *
     * @return class-string<\Illuminate\Http\Resources\Json\JsonResource>|null
     *
     * @throws \LogicException
     */
    protected function collects()
    {
        $collects = null;

        if ($this->collects) {
            $collects = $this->collects;
        } elseif (str_ends_with(class_basename($this), 'Collection') &&
            (class_exists($class = Str::replaceLast('Collection', '', get_class($this))) ||
             class_exists($class = Str::replaceLast('Collection', 'Resource', get_class($this))))) {
            $collects = $class;
        }

        if (! $collects || is_a($collects, JsonResource::class, true)) {
            return $collects;
        }

        throw new LogicException('Resource collections must collect instances of '.JsonResource::class.'.');
    }

    /**
     * Get the JSON serialization options that should be applied to the resource response.
     *
     * @return int
     *
     * @throws \ReflectionException
     */
    public function jsonOptions()
    {
        $collects = $this->collects();

        if (! $collects) {
            return 0;
        }

        return (new ReflectionClass($collects))
            ->newInstanceWithoutConstructor()
            ->jsonOptions();
    }

    /**
     * Get an iterator for the resource collection.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return $this->collection->getIterator();
    }
}
