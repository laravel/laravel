<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToDeleteDirectory extends RuntimeException implements FilesystemOperationFailed
{
    /**
     * @var string
     */
    private $location = '';

    /**
     * @var string
     */
    private $reason;

    public static function atLocation(
        string $location,
        string $reason = '',
        ?Throwable $previous = null
    ): UnableToDeleteDirectory {
        $e = new static(rtrim("Unable to delete directory located at: {$location}. {$reason}"), 0, $previous);
        $e->location = $location;
        $e->reason = $reason;

        return $e;
    }

    public function operation(): string
    {
        return FilesystemOperationFailed::OPERATION_DELETE_DIRECTORY;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function location(): string
    {
        return $this->location;
    }
}
