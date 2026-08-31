<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;

class UnableToResolveFilesystemMount extends RuntimeException implements FilesystemException
{
    public static function becauseTheSeparatorIsMissing(string $path): UnableToResolveFilesystemMount
    {
        return new UnableToResolveFilesystemMount("Unable to resolve the filesystem mount because the path ($path) is missing a separator (://).");
    }

    public static function becauseTheMountWasNotRegistered(string $mountIdentifier): UnableToResolveFilesystemMount
    {
        return new UnableToResolveFilesystemMount("Unable to resolve the filesystem mount because the mount ($mountIdentifier) was not registered.");
    }
}
