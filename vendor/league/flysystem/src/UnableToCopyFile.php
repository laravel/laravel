<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToCopyFile extends RuntimeException implements FilesystemOperationFailed
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $destination;

    public function source(): string
    {
        return $this->source;
    }

    public function destination(): string
    {
        return $this->destination;
    }

    public static function fromLocationTo(
        string $sourcePath,
        string $destinationPath,
        ?Throwable $previous = null
    ): UnableToCopyFile {
        $e = new static("Unable to copy file from $sourcePath to $destinationPath", 0 , $previous);
        $e->source = $sourcePath;
        $e->destination = $destinationPath;

        return $e;
    }

    public static function sourceAndDestinationAreTheSame(string $source, string $destination): UnableToCopyFile
    {
        return UnableToCopyFile::because('Source and destination are the same', $source, $destination);
    }

    public static function because(string $reason, string $sourcePath, string $destinationPath): UnableToCopyFile
    {
        $e = new static("Unable to copy file from $sourcePath to $destinationPath, because $reason");
        $e->source = $sourcePath;
        $e->destination = $destinationPath;

        return $e;
    }

    public function operation(): string
    {
        return FilesystemOperationFailed::OPERATION_COPY;
    }
}
