<?php

namespace Illuminate\Database;

use Illuminate\Contracts\Database\ConcurrencyErrorDetector as ConcurrencyErrorDetectorContract;
use Illuminate\Support\Str;
use PDOException;
use Throwable;

class ConcurrencyErrorDetector implements ConcurrencyErrorDetectorContract
{
    /**
     * Determine if the given exception was caused by a concurrency error such as a deadlock or serialization failure.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    public function causedByConcurrencyError(Throwable $e): bool
    {
        if ($e instanceof PDOException && ($e->getCode() === 40001 || $e->getCode() === '40001')) {
            return true;
        }

        $message = $e->getMessage();

        return Str::contains($message, [
            'Deadlock found when trying to get lock',
            'deadlock detected',
            'The database file is locked',
            'database is locked',
            'database table is locked',
            'A table in the database is locked',
            'has been chosen as the deadlock victim',
            'Lock wait timeout exceeded; try restarting transaction',
            'WSREP detected deadlock/conflict and aborted the transaction. Try restarting the transaction',
            'Record has changed since last read in table',
        ]);
    }
}
