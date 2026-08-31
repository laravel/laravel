<?php

namespace Illuminate\Http\Resources\JsonApi;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AnonymousResourceCollection extends \Illuminate\Http\Resources\Json\AnonymousResourceCollection
{
    use Concerns\ResolvesJsonApiRequest;

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    #[\Override]
    public function with($request)
    {
        return array_filter([
            'included' => $this->collection
                ->map(fn ($resource) => $resource->resolveIncludedResourceObjects($request))
                ->flatten(depth: 1)
                ->uniqueStrict('_uniqueKey')
                ->map(fn ($included) => Arr::except($included, ['_uniqueKey']))
                ->values()
                ->all(),
            ...($implementation = JsonApiResource::$jsonApiInformation)
                ? ['jsonapi' => $implementation]
                : [],
        ]);
    }

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    #[\Override]
    public function toAttributes(Request $request)
    {
        return $this->collection
            ->map(fn ($resource) => $resource->resolveResourceData($request))
            ->all();
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    #[\Override]
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->header('Content-Type', 'application/vnd.api+json');
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[\Override]
    public function toResponse($request)
    {
        return parent::toResponse($this->resolveJsonApiRequestFrom($request));
    }

    /**
     * Resolve the HTTP request instance from container.
     *
     * @return \Illuminate\Http\Resources\JsonApi\SparseRequest
     */
    #[\Override]
    protected function resolveRequestFromContainer()
    {
        return $this->resolveJsonApiRequestFrom(Container::getInstance()->make('request'));
    }
}
