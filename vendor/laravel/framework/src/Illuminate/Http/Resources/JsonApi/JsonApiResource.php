<?php

namespace Illuminate\Http\Resources\JsonApi;

use BadMethodCallException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class JsonApiResource extends JsonResource
{
    use Concerns\ResolvesJsonApiElements,
        Concerns\ResolvesJsonApiRequest;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'data';

    /**
     * The resource's "version" for JSON:API.
     *
     * @var array{version?: string, ext?: array, profile?: array, meta?: array}
     */
    public static $jsonApiInformation = [];

    /**
     * The resource's "links" for JSON:API.
     */
    protected array $jsonApiLinks = [];

    /**
     * The resource's "meta" for JSON:API.
     */
    protected array $jsonApiMeta = [];

    /**
     * Set the JSON:API version for the request.
     *
     * @return void
     */
    public static function configure(?string $version = null, array $ext = [], array $profile = [], array $meta = [])
    {
        static::$jsonApiInformation = array_filter([
            'version' => $version,
            'ext' => $ext,
            'profile' => $profile,
            'meta' => $meta,
        ]);
    }

    /**
     * Get the resource's ID.
     *
     * @return string|null
     */
    public function toId(Request $request)
    {
        return null;
    }

    /**
     * Get the resource's type.
     *
     * @return string|null
     */
    public function toType(Request $request)
    {
        return null;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Arrayable|\JsonSerializable|array
     */
    #[\Override]
    public function toAttributes(Request $request)
    {
        if (property_exists($this, 'attributes')) {
            return $this->attributes;
        }

        return $this->toArray($request);
    }

    /**
     * Get the resource's relationships.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Arrayable|array
     */
    public function toRelationships(Request $request)
    {
        if (property_exists($this, 'relationships')) {
            return $this->relationships;
        }

        return [];
    }

    /**
     * Get the resource's links.
     *
     * @return array
     */
    public function toLinks(Request $request)
    {
        return $this->jsonApiLinks;
    }

    /**
     * Get the resource's meta information.
     *
     * @return array
     */
    public function toMeta(Request $request)
    {
        return $this->jsonApiMeta;
    }

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
            'included' => $this->resolveIncludedResourceObjects($request)
                ->uniqueStrict('_uniqueKey')
                ->map(fn ($included) => Arr::except($included, ['_uniqueKey']))
                ->values()
                ->all(),
            ...($implementation = static::$jsonApiInformation)
                ? ['jsonapi' => $implementation]
                : [],
        ]);
    }

    /**
     * Resolve the resource to an array.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     */
    #[\Override]
    public function resolve($request = null)
    {
        return [
            'data' => $this->resolveResourceData($this->resolveJsonApiRequestFrom($request ?? $this->resolveRequestFromContainer())),
        ];
    }

    /**
     * Resolve the resource data to an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    #[\Override]
    public function resolveResourceData(Request $request)
    {
        return $this->resolveResourceObject($request);
    }

    /**
     * Customize the outgoing response for the resource.
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
     * @return \Illuminate\Http\Resources\JsonApi\JsonApiRequest
     */
    #[\Override]
    protected function resolveRequestFromContainer()
    {
        return $this->resolveJsonApiRequestFrom(parent::resolveRequestFromContainer());
    }

    /**
     * Create a new resource collection instance.
     *
     * @param  mixed  $resource
     * @return \Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection
     */
    #[\Override]
    protected static function newCollection($resource)
    {
        return new AnonymousResourceCollection($resource, static::class);
    }

    /**
     * Set the string that should wrap the outer-most resource array.
     *
     * @param  string  $value
     * @return never
     *
     * @throws \RuntimeException
     */
    #[\Override]
    public static function wrap($value)
    {
        throw new BadMethodCallException(sprintf('Using %s() method is not allowed.', __METHOD__));
    }

    /**
     * Disable wrapping of the outer-most resource array.
     *
     * @return never
     */
    #[\Override]
    public static function withoutWrapping()
    {
        throw new BadMethodCallException(sprintf('Using %s() method is not allowed.', __METHOD__));
    }

    /**
     * Flush the resource's global state.
     *
     * @return void
     */
    #[\Override]
    public static function flushState()
    {
        parent::flushState();

        static::$jsonApiInformation = [];
        static::$maxRelationshipDepth = 3;
    }
}
