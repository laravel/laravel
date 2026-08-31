<?php

namespace Illuminate\Database\Concerns;

trait ParsesSearchPath
{
    /**
     * Parse the Postgres "search_path" configuration value into an array.
     *
     * @param  string|array|null  $searchPath
     * @return array
     */
    protected function parseSearchPath($searchPath)
    {
        if (is_string($searchPath)) {
            preg_match_all('/[^\s,"\']+/', $searchPath, $matches);

            $searchPath = $matches[0];
        }

        return array_map(function ($schema) {
            return trim($schema, '\'"');
        }, $searchPath ?? []);
    }
}
