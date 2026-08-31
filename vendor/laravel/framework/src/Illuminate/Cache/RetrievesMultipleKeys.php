<?php

namespace Illuminate\Cache;

use Illuminate\Support\Collection;

trait RetrievesMultipleKeys
{
    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        $return = [];

        $keys = (new Collection($keys))
            ->mapWithKeys(fn ($value, $key) => [is_string($key) ? $key : $value => is_string($key) ? $value : null])
            ->all();

        foreach ($keys as $key => $default) {
            /** @phpstan-ignore arguments.count (some clients don't accept a default) */
            $return[$key] = $this->get($key, $default);
        }

        return $return;
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds)
    {
        $manyResult = null;

        foreach ($values as $key => $value) {
            $result = $this->put($key, $value, $seconds);

            $manyResult = is_null($manyResult) ? $result : $result && $manyResult;
        }

        return $manyResult ?: false;
    }
}
