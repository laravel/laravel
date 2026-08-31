<?php

namespace Illuminate\Filesystem;

if (! function_exists('Illuminate\Filesystem\join_paths')) {
    /**
     * Join the given paths together.
     *
     * @param  string|null  $basePath
     * @param  string  ...$paths
     */
    function join_paths($basePath, ...$paths): string
    {
        foreach ($paths as $index => $path) {
            if (empty($path) && $path !== '0') {
                unset($paths[$index]);
            } else {
                $paths[$index] = DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR);
            }
        }

        return $basePath.implode('', $paths);
    }
}
