<?php

declare(strict_types=1);

namespace League\Flysystem\Local;

use League\MimeTypeDetection\MimeTypeDetector;
use function in_array;

class FallbackMimeTypeDetector implements MimeTypeDetector
{
    private const INCONCLUSIVE_MIME_TYPES = [
        'application/x-empty',
        'text/plain',
        'text/x-asm',
        'application/octet-stream',
        'inode/x-empty',
    ];

    public function __construct(
        private MimeTypeDetector $detector,
        private array $inconclusiveMimetypes = self::INCONCLUSIVE_MIME_TYPES,
        private bool $useInconclusiveMimeTypeFallback = false,
    ) {
    }

    public function detectMimeType(string $path, $contents): ?string
    {
        return $this->detector->detectMimeType($path, $contents);
    }

    public function detectMimeTypeFromBuffer(string $contents): ?string
    {
        return $this->detector->detectMimeTypeFromBuffer($contents);
    }

    public function detectMimeTypeFromPath(string $path): ?string
    {
        return $this->detector->detectMimeTypeFromPath($path);
    }

    public function detectMimeTypeFromFile(string $path): ?string
    {
        $mimeType = $this->detector->detectMimeTypeFromFile($path);

        if ($mimeType !== null && ! in_array($mimeType, $this->inconclusiveMimetypes)) {
            return $mimeType;
        }

        return $this->detector->detectMimeTypeFromPath($path) ?? ($this->useInconclusiveMimeTypeFallback ? $mimeType : null);
    }
}
