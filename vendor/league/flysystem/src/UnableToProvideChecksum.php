<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToProvideChecksum extends RuntimeException implements FilesystemException
{
    public function __construct(string $reason, string $path, ?Throwable $previous = null)
    {
        parent::__construct("Unable to get checksum for $path: $reason", 0, $previous);
    }
}
