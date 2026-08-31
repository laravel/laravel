<?php

namespace Illuminate\Http\Resources\JsonApi\Exceptions;

use RuntimeException;

class ResourceIdentificationException extends RuntimeException
{
    /**
     * Create an exception indicating we were unable to determine the resource ID for the given resource.
     *
     * @param  mixed  $resource
     * @return self
     */
    public static function attemptingToDetermineIdFor($resource)
    {
        $resourceType = is_object($resource) ? $resource::class : gettype($resource);

        return new self(sprintf(
            'Unable to resolve resource object ID for [%s].', $resourceType
        ));
    }

    /**
     * Create an exception indicating we were unable to determine the resource type for the given resource.
     *
     * @param  mixed  $resource
     * @return self
     */
    public static function attemptingToDetermineTypeFor($resource)
    {
        $resourceType = is_object($resource) ? $resource::class : gettype($resource);

        return new self(sprintf(
            'Unable to resolve resource object type for [%s].', $resourceType
        ));
    }
}
