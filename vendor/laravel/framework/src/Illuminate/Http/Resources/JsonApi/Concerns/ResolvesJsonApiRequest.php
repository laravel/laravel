<?php

namespace Illuminate\Http\Resources\JsonApi\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiRequest;

trait ResolvesJsonApiRequest
{
    /**
     * Resolve a JSON API request instance from the given HTTP request.
     *
     * @return \Illuminate\Http\Resources\JsonApi\JsonApiRequest
     */
    protected function resolveJsonApiRequestFrom(Request $request)
    {
        return $request instanceof JsonApiRequest
            ? $request
            : JsonApiRequest::createFrom($request);
    }
}
