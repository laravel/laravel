<?php

declare(strict_types=1);

namespace League\Flysystem;

use LogicException;

class UnableToMountFilesystem extends LogicException implements FilesystemException
{
    /**
     * @param mixed $key
     */
    public static function becauseTheKeyIsNotValid($key): UnableToMountFilesystem
    {
        return new UnableToMountFilesystem(
            'Unable to mount filesystem, key was invalid. String expected, received: ' . gettype($key)
        );
    }

    /**
     * @param mixed $filesystem
     */
    public static function becauseTheFilesystemWasNotValid($filesystem): UnableToMountFilesystem
    {
        $received = is_object($filesystem) ? get_class($filesystem) : gettype($filesystem);

        return new UnableToMountFilesystem(
            'Unable to mount filesystem, filesystem was invalid. Instance of ' . FilesystemOperator::class . ' expected, received: ' . $received
        );
    }
}
