<?php

declare(strict_types=1);

namespace League\Flysystem\UnixVisibility;

use League\Flysystem\PortableVisibilityGuard;
use League\Flysystem\Visibility;

class PortableVisibilityConverter implements VisibilityConverter
{
    public function __construct(
        private int $filePublic = 0644,
        private int $filePrivate = 0600,
        private int $directoryPublic = 0755,
        private int $directoryPrivate = 0700,
        private string $defaultForDirectories = Visibility::PRIVATE
    ) {
    }

    public function forFile(string $visibility): int
    {
        PortableVisibilityGuard::guardAgainstInvalidInput($visibility);

        return $visibility === Visibility::PUBLIC
            ? $this->filePublic
            : $this->filePrivate;
    }

    public function forDirectory(string $visibility): int
    {
        PortableVisibilityGuard::guardAgainstInvalidInput($visibility);

        return $visibility === Visibility::PUBLIC
            ? $this->directoryPublic
            : $this->directoryPrivate;
    }

    public function inverseForFile(int $visibility): string
    {
        if ($visibility === $this->filePublic) {
            return Visibility::PUBLIC;
        } elseif ($visibility === $this->filePrivate) {
            return Visibility::PRIVATE;
        }

        return Visibility::PUBLIC; // default
    }

    public function inverseForDirectory(int $visibility): string
    {
        if ($visibility === $this->directoryPublic) {
            return Visibility::PUBLIC;
        } elseif ($visibility === $this->directoryPrivate) {
            return Visibility::PRIVATE;
        }

        return Visibility::PUBLIC; // default
    }

    public function defaultForDirectories(): int
    {
        return $this->defaultForDirectories === Visibility::PUBLIC ? $this->directoryPublic : $this->directoryPrivate;
    }

    /**
     * @param array<mixed>  $permissionMap
     */
    public static function fromArray(array $permissionMap, string $defaultForDirectories = Visibility::PRIVATE): PortableVisibilityConverter
    {
        return new PortableVisibilityConverter(
            $permissionMap['file']['public'] ?? 0644,
            $permissionMap['file']['private'] ?? 0600,
            $permissionMap['dir']['public'] ?? 0755,
            $permissionMap['dir']['private'] ?? 0700,
            $defaultForDirectories
        );
    }
}
