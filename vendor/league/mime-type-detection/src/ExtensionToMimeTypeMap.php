<?php

declare(strict_types=1);

namespace League\MimeTypeDetection;

interface ExtensionToMimeTypeMap
{
    public function lookupMimeType(string $extension): ?string;
}
