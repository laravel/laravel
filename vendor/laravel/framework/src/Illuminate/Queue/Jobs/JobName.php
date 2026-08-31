<?php

namespace Illuminate\Queue\Jobs;

use Illuminate\Support\Str;

class JobName
{
    /**
     * Parse the given job name into a class / method array.
     *
     * @param  string  $job
     * @return array
     */
    public static function parse($job)
    {
        return Str::parseCallback($job, 'fire');
    }

    /**
     * Get the resolved name of the queued job class.
     *
     * @param  string  $name
     * @param  array  $payload
     * @return string
     */
    public static function resolve($name, $payload)
    {
        if (! empty($payload['displayName'])) {
            return $payload['displayName'];
        }

        return $name;
    }

    /**
     * Get the class name for queued job class.
     *
     * @param  string  $name
     * @param  array<string, mixed>  $payload
     * @return string
     */
    public static function resolveClassName($name, $payload)
    {
        if (is_string($payload['data']['commandName'] ?? null)) {
            return $payload['data']['commandName'];
        }

        return $name;
    }
}
