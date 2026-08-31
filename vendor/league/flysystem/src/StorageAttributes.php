<?php

declare(strict_types=1);

namespace League\Flysystem;

use ArrayAccess;
use JsonSerializable;

interface StorageAttributes extends JsonSerializable, ArrayAccess
{
    public const ATTRIBUTE_PATH = 'path';
    public const ATTRIBUTE_TYPE = 'type';
    public const ATTRIBUTE_FILE_SIZE = 'file_size';
    public const ATTRIBUTE_VISIBILITY = 'visibility';
    public const ATTRIBUTE_LAST_MODIFIED = 'last_modified';
    public const ATTRIBUTE_MIME_TYPE = 'mime_type';
    public const ATTRIBUTE_EXTRA_METADATA = 'extra_metadata';

    public const TYPE_FILE = 'file';
    public const TYPE_DIRECTORY = 'dir';

    public function path(): string;

    public function type(): string;

    public function visibility(): ?string;

    public function lastModified(): ?int;

    public static function fromArray(array $attributes): StorageAttributes;

    public function isFile(): bool;

    public function isDir(): bool;

    public function withPath(string $path): StorageAttributes;

    public function extraMetadata(): array;
}
