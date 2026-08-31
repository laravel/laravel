<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToListContents extends RuntimeException implements FilesystemOperationFailed
{
    public static function atLocation(string $location, bool $deep, Throwable $previous): UnableToListContents
    {
        $message = "Unable to list contents for '$location', " . ($deep ? 'deep' : 'shallow') . " listing\n\n"
            . 'Reason: ' . $previous->getMessage();

        return new UnableToListContents($message, 0, $previous);
    }

    public function operation(): string
    {
        return self::OPERATION_LIST_CONTENTS;
    }
}
