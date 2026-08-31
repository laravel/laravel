<?php

namespace Illuminate\Database\Eloquent\Concerns;

use Illuminate\Support\Str;

trait HasUuids
{
    use HasUniqueStringIds;

    /**
     * Generate a new unique key for the model.
     *
     * @return string
     */
    public function newUniqueId()
    {
        return (string) Str::uuid7();
    }

    /**
     * Determine if given key is valid.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isValidUniqueId($value): bool
    {
        return Str::isUuid($value);
    }
}
