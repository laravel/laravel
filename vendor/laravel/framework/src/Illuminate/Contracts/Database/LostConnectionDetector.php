<?php

namespace Illuminate\Contracts\Database;

use Throwable;

interface LostConnectionDetector
{
    /**
     * Determine if the given exception was caused by a lost connection.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    public function causedByLostConnection(Throwable $e): bool;
}
