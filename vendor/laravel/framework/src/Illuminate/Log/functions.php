<?php

namespace Illuminate\Log;

use Psr\Log\LoggerInterface;

if (! function_exists('Illuminate\Log\log')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string|null  $message
     * @param  array  $context
     * @return ($message is null ? \Psr\Log\LoggerInterface: null)
     */
    function log($message = null, array $context = []): ?LoggerInterface
    {
        return logger($message, $context);
    }
}
