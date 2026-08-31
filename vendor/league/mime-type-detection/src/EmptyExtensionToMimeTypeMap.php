<?php

declare(strict_types=1);

namespace League\MimeTypeDetection;

class EmptyExtensionToMimeTypeMap implements ExtensionToMimeTypeMap
{
    public function lookupMimeType(string $extension): ?string
    {
        return null;
    }
}
