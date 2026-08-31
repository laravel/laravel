<?php

declare(strict_types=1);

namespace League\Flysystem;

interface FilesystemWriter
{
    /**
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function write(string $location, string $contents, array $config = []): void;

    /**
     * @param mixed $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function writeStream(string $location, $contents, array $config = []): void;

    /**
     * @throws UnableToSetVisibility
     * @throws FilesystemException
     */
    public function setVisibility(string $path, string $visibility): void;

    /**
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     */
    public function delete(string $location): void;

    /**
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory(string $location): void;

    /**
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public function createDirectory(string $location, array $config = []): void;

    /**
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move(string $source, string $destination, array $config = []): void;

    /**
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy(string $source, string $destination, array $config = []): void;
}
