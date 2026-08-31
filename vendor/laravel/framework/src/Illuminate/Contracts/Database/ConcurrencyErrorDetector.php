<?php

namespace Illuminate\Contracts\Database;

use Throwable;

interface ConcurrencyErrorDetector
{
    /**
     * Determine if the given exception was caused by a concurrency error such as a deadlock or serialization failure.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    public function causedByConcurrencyError(Throwable $e): bool;
}
