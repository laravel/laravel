<?php

declare(strict_types=1);

namespace League\Flysystem;

class FileAttributes implements StorageAttributes
{
    use ProxyArrayAccessToProperties;
    private string $type = StorageAttributes::TYPE_FILE;

    public function __construct(
        private string $path,
        private ?int $fileSize = null,
        private ?string $visibility = null,
        private ?int $lastModified = null,
        private ?string $mimeType = null,
        private array $extraMetadata = []
    ) {
        $this->path = ltrim($this->path, '/');
    }

    public function type(): string
    {
        return $this->type;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function fileSize(): ?int
    {
        return $this->fileSize;
    }

    public function visibility(): ?string
    {
        return $this->visibility;
    }

    public function lastModified(): ?int
    {
        return $this->lastModified;
    }

    public function mimeType(): ?string
    {
        return $this->mimeType;
    }

    public function extraMetadata(): array
    {
        return $this->extraMetadata;
    }

    public function isFile(): bool
    {
        return true;
    }

    public function isDir(): bool
    {
        return false;
    }

    public function withPath(string $path): self
    {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public static function fromArray(array $attributes): self
    {
        return new FileAttributes(
            $attributes[StorageAttributes::ATTRIBUTE_PATH],
            $attributes[StorageAttributes::ATTRIBUTE_FILE_SIZE] ?? null,
            $attributes[StorageAttributes::ATTRIBUTE_VISIBILITY] ?? null,
            $attributes[StorageAttributes::ATTRIBUTE_LAST_MODIFIED] ?? null,
            $attributes[StorageAttributes::ATTRIBUTE_MIME_TYPE] ?? null,
            $attributes[StorageAttributes::ATTRIBUTE_EXTRA_METADATA] ?? []
        );
    }

    public function jsonSerialize(): array
    {
        return [
            StorageAttributes::ATTRIBUTE_TYPE => self::TYPE_FILE,
            StorageAttributes::ATTRIBUTE_PATH => $this->path,
            StorageAttributes::ATTRIBUTE_FILE_SIZE => $this->fileSize,
            StorageAttributes::ATTRIBUTE_VISIBILITY => $this->visibility,
            StorageAttributes::ATTRIBUTE_LAST_MODIFIED => $this->lastModified,
            StorageAttributes::ATTRIBUTE_MIME_TYPE => $this->mimeType,
            StorageAttributes::ATTRIBUTE_EXTRA_METADATA => $this->extraMetadata,
        ];
    }
}
