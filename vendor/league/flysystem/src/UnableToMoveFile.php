<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToMoveFile extends RuntimeException implements FilesystemOperationFailed
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $destination;

    public static function sourceAndDestinationAreTheSame(string $source, string $destination): UnableToMoveFile
    {
        return UnableToMoveFile::because('Source and destination are the same', $source, $destination);
    }

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
    ): UnableToMoveFile {
        $message = $previous?->getMessage() ?? "Unable to move file from $sourcePath to $destinationPath";
        $e = new static($message, 0, $previous);
        $e->source = $sourcePath;
        $e->destination = $destinationPath;

        return $e;
    }

    public static function because(
        string $reason,
        string $sourcePath,
        string $destinationPath,
    ): UnableToMoveFile {
        $message = "Unable to move file from $sourcePath to $destinationPath, because $reason";
        $e = new static($message);
        $e->source = $sourcePath;
        $e->destination = $destinationPath;

        return $e;
    }

    public function operation(): string
    {
        return FilesystemOperationFailed::OPERATION_MOVE;
    }
}
