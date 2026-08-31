<?php

namespace Laravel\Pail\Guards;

use RuntimeException;

class EnsurePcntlIsAvailable
{
    /**
     * Checks if the pcntl extension is available.
     */
    public static function check(): void
    {
        if (! function_exists('pcntl_fork')) {
            throw new RuntimeException('The [pcntl] extension is required to run Pail.');
        }
    }
}
