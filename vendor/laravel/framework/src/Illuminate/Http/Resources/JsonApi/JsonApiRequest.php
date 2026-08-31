<?php

namespace Illuminate\Http\Resources\JsonApi;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class JsonApiRequest extends Request
{
    /**
     * Cached sparse fieldset.
     */
    protected ?array $cachedSparseFields = null;

    /**
     * Cached sparse included.
     */
    protected ?array $cachedSparseIncluded = null;

    /**
     * Get the request's included fields.
     */
    public function sparseFields(string $key): array
    {
        if (is_null($this->cachedSparseFields)) {
            $this->cachedSparseFields = (new Collection($this->array('fields')))
                ->transform(fn ($fieldsets) => empty($fieldsets) ? [] : explode(',', $fieldsets))
                ->all();
        }

        return $this->cachedSparseFields[$key] ?? [];
    }

    /**
     * Get the request's included relationships.
     */
    public function sparseIncluded(?string $key = null): ?array
    {
        if (is_null($this->cachedSparseIncluded)) {
            $included = (string) $this->string('include', '');

            $this->cachedSparseIncluded = (new Collection(empty($included) ? [] : explode(',', $included)))
                ->transform(function ($item) {
                    $with = null;

                    if (str_contains($item, '.')) {
                        [$relation, $with] = explode('.', $item, 2);
                    } else {
                        $relation = $item;
                    }

                    return ['relation' => $relation, 'with' => $with];
                })->mapToGroups(fn ($item) => [$item['relation'] => $item['with']])
                ->toArray();
        }

        if (is_null($key)) {
            return array_keys($this->cachedSparseIncluded);
        }

        return transform($this->cachedSparseIncluded[$key] ?? null, function ($value) {
            return Collection::wrap($value)
                ->transform(function ($item) {
                    $item = implode('.', Arr::take(explode('.', $item), JsonApiResource::$maxRelationshipDepth - 1));

                    return ! empty($item) ? $item : null;
                })->filter()->all();
        }) ?? [];
    }
}
