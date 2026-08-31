<?php

namespace Illuminate\Http\Resources\Json;

use Illuminate\Support\Arr;

class PaginatedResourceResponse extends ResourceResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return tap(response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                array_merge_recursive(
                    $this->paginationInformation($request),
                    $this->resource->with($request),
                    $this->resource->additional
                )
            ),
            $this->calculateStatus(),
            [],
            $this->resource->jsonOptions()
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource->map(function ($item) {
                if (is_array($item)) {
                    return Arr::get($item, 'resource');
                } elseif (is_object($item)) {
                    return $item->resource ?? null;
                }

                return null;
            });

            $this->resource->withResponse($request, $response);
        });
    }

    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->resource->toArray();

        $default = [
            'links' => $this->paginationLinks($paginated),
            'meta' => $this->meta($paginated),
        ];

        if (method_exists($this->resource, 'paginationInformation') ||
            $this->resource->hasMacro('paginationInformation')) {
            return $this->resource->paginationInformation($request, $paginated, $default);
        }

        return $default;
    }

    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated)
    {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the metadata for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }
}
