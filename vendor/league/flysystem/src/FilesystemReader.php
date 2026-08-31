<?php

declare(strict_types=1);

namespace League\Flysystem;

use DateTimeInterface;

/**
 * This interface contains everything to read from and inspect
 * a filesystem. All methods containing are non-destructive.
 *
 * @method string publicUrl(string $path, array $config = []) Will be added in 4.0
 * @method string temporaryUrl(string $path, DateTimeInterface $expiresAt, array $config = []) Will be added in 4.0
 * @method string checksum(string $path, array $config = []) Will be added in 4.0
 */
interface FilesystemReader
{
    public const LIST_SHALLOW = false;
    public const LIST_DEEP = true;

    /**
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function fileExists(string $location): bool;

    /**
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function directoryExists(string $location): bool;

    /**
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function has(string $location): bool;

    /**
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read(string $location): string;

    /**
     * @return resource
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function readStream(string $location);

    /**
     * @return DirectoryListing<StorageAttributes>
     *
     * @throws FilesystemException
     * @throws UnableToListContents
     */
    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function lastModified(string $path): int;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function fileSize(string $path): int;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function mimeType(string $path): string;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function visibility(string $path): string;
}
