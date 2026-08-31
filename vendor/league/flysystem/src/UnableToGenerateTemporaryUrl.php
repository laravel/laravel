<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToGenerateTemporaryUrl extends RuntimeException implements FilesystemException
{
    public function __construct(string $reason, string $path, ?Throwable $previous = null)
    {
        parent::__construct("Unable to generate temporary url for $path: $reason", 0, $previous);
    }

    public static function dueToError(string $path, Throwable $exception): static
    {
        return new static($exception->getMessage(), $path, $exception);
    }

    public static function noGeneratorConfigured(string $path, string $extraReason = ''): static
    {
        return new static('No generator was configured ' . $extraReason, $path);
    }
}
