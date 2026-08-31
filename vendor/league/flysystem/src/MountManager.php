<?php

declare(strict_types=1);

namespace League\Flysystem;

use DateTimeInterface;
use Throwable;

use function compact;
use function method_exists;
use function sprintf;

class MountManager implements FilesystemOperator
{
    /**
     * @var array<string, FilesystemOperator>
     */
    private $filesystems = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * MountManager constructor.
     *
     * @param array<string,FilesystemOperator> $filesystems
     */
    public function __construct(array $filesystems = [], array $config = [])
    {
        $this->mountFilesystems($filesystems);
        $this->config = new Config($config);
    }

    /**
     * It is not recommended to mount filesystems after creation because interacting
     * with the Mount Manager becomes unpredictable. Use this as an escape hatch.
     */
    public function dangerouslyMountFilesystems(string $key, FilesystemOperator $filesystem): void
    {
        $this->mountFilesystem($key, $filesystem);
    }

    /**
     * @param array<string,FilesystemOperator> $filesystems
     */
    public function extend(array $filesystems, array $config = []): MountManager
    {
        $clone = clone $this;
        $clone->config = $this->config->extend($config);
        $clone->mountFilesystems($filesystems);

        return $clone;
    }

    public function fileExists(string $location): bool
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->fileExists($path);
        } catch (Throwable $exception) {
            throw UnableToCheckFileExistence::forLocation($location, $exception);
        }
    }

    public function has(string $location): bool
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->fileExists($path) || $filesystem->directoryExists($path);
        } catch (Throwable $exception) {
            throw UnableToCheckExistence::forLocation($location, $exception);
        }
    }

    public function directoryExists(string $location): bool
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->directoryExists($path);
        } catch (Throwable $exception) {
            throw UnableToCheckDirectoryExistence::forLocation($location, $exception);
        }
    }

    public function read(string $location): string
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->read($path);
        } catch (UnableToReadFile $exception) {
            throw UnableToReadFile::fromLocation($location, $exception->reason(), $exception);
        }
    }

    public function readStream(string $location)
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->readStream($path);
        } catch (UnableToReadFile $exception) {
            throw UnableToReadFile::fromLocation($location, $exception->reason(), $exception);
        }
    }

    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path, $mountIdentifier] = $this->determineFilesystemAndPath($location);

        return
            $filesystem
                ->listContents($path, $deep)
                ->map(
                    function (StorageAttributes $attributes) use ($mountIdentifier) {
                        return $attributes->withPath(sprintf('%s://%s', $mountIdentifier, $attributes->path()));
                    }
                );
    }

    public function lastModified(string $location): int
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->lastModified($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::lastModified($location, $exception->reason(), $exception);
        }
    }

    public function fileSize(string $location): int
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->fileSize($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::fileSize($location, $exception->reason(), $exception);
        }
    }

    public function mimeType(string $location): string
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->mimeType($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::mimeType($location, $exception->reason(), $exception);
        }
    }

    public function visibility(string $path): string
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $location] = $this->determineFilesystemAndPath($path);

        try {
            return $filesystem->visibility($location);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::visibility($path, $exception->reason(), $exception);
        }
    }

    public function write(string $location, string $contents, array $config = []): void
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->write($path, $contents, $this->config->extend($config)->toArray());
        } catch (UnableToWriteFile $exception) {
            throw UnableToWriteFile::atLocation($location, $exception->reason(), $exception);
        }
    }

    public function writeStream(string $location, $contents, array $config = []): void
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);
        $filesystem->writeStream($path, $contents, $this->config->extend($config)->toArray());
    }

    public function setVisibility(string $path, string $visibility): void
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($path);
        $filesystem->setVisibility($path, $visibility);
    }

    public function delete(string $location): void
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->delete($path);
        } catch (UnableToDeleteFile $exception) {
            throw UnableToDeleteFile::atLocation($location, $exception->reason(), $exception);
        }
    }

    public function deleteDirectory(string $location): void
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->deleteDirectory($path);
        } catch (UnableToDeleteDirectory $exception) {
            throw UnableToDeleteDirectory::atLocation($location, $exception->reason(), $exception);
        }
    }

    public function createDirectory(string $location, array $config = []): void
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->createDirectory($path, $this->config->extend($config)->toArray());
        } catch (UnableToCreateDirectory $exception) {
            throw UnableToCreateDirectory::dueToFailure($location, $exception);
        }
    }

    public function move(string $source, string $destination, array $config = []): void
    {
        /** @var FilesystemOperator $sourceFilesystem */
        /* @var FilesystemOperator $destinationFilesystem */
        [$sourceFilesystem, $sourcePath] = $this->determineFilesystemAndPath($source);
        [$destinationFilesystem, $destinationPath] = $this->determineFilesystemAndPath($destination);

        $sourceFilesystem === $destinationFilesystem ? $this->moveInTheSameFilesystem(
            $sourceFilesystem,
            $sourcePath,
            $destinationPath,
            $source,
            $destination,
            $config,
        ) : $this->moveAcrossFilesystems($source, $destination, $config);
    }

    public function copy(string $source, string $destination, array $config = []): void
    {
        /** @var FilesystemOperator $sourceFilesystem */
        /* @var FilesystemOperator $destinationFilesystem */
        [$sourceFilesystem, $sourcePath] = $this->determineFilesystemAndPath($source);
        [$destinationFilesystem, $destinationPath] = $this->determineFilesystemAndPath($destination);

        $sourceFilesystem === $destinationFilesystem ? $this->copyInSameFilesystem(
            $sourceFilesystem,
            $sourcePath,
            $destinationPath,
            $source,
            $destination,
            $config,
        ) : $this->copyAcrossFilesystem(
            $sourceFilesystem,
            $sourcePath,
            $destinationFilesystem,
            $destinationPath,
            $source,
            $destination,
            $config,
        );
    }

    public function publicUrl(string $path, array $config = []): string
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($path);

        if ( ! method_exists($filesystem, 'publicUrl')) {
            throw new UnableToGeneratePublicUrl(sprintf('%s does not support generating public urls.', $filesystem::class), $path);
        }

        return $filesystem->publicUrl($path, $config);
    }

    public function temporaryUrl(string $path, DateTimeInterface $expiresAt, array $config = []): string
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($path);

        if ( ! method_exists($filesystem, 'temporaryUrl')) {
            throw new UnableToGenerateTemporaryUrl(sprintf('%s does not support generating public urls.', $filesystem::class), $path);
        }

        return $filesystem->temporaryUrl($path, $expiresAt, $this->config->extend($config)->toArray());
    }

    public function checksum(string $path, array $config = []): string
    {
        /** @var FilesystemOperator $filesystem */
        [$filesystem, $path] = $this->determineFilesystemAndPath($path);

        if ( ! method_exists($filesystem, 'checksum')) {
            throw new UnableToProvideChecksum(sprintf('%s does not support providing checksums.', $filesystem::class), $path);
        }

        return $filesystem->checksum($path, $this->config->extend($config)->toArray());
    }

    private function mountFilesystems(array $filesystems): void
    {
        foreach ($filesystems as $key => $filesystem) {
            $this->guardAgainstInvalidMount($key, $filesystem);
            /* @var string $key */
            /* @var FilesystemOperator $filesystem */
            $this->mountFilesystem($key, $filesystem);
        }
    }

    private function guardAgainstInvalidMount(mixed $key, mixed $filesystem): void
    {
        if ( ! is_string($key)) {
            throw UnableToMountFilesystem::becauseTheKeyIsNotValid($key);
        }

        if ( ! $filesystem instanceof FilesystemOperator) {
            throw UnableToMountFilesystem::becauseTheFilesystemWasNotValid($filesystem);
        }
    }

    private function mountFilesystem(string $key, FilesystemOperator $filesystem): void
    {
        $this->filesystems[$key] = $filesystem;
    }

    /**
     * @param string $path
     *
     * @return array{0:FilesystemOperator, 1:string, 2:string}
     */
    private function determineFilesystemAndPath(string $path): array
    {
        if (strpos($path, '://') < 1) {
            throw UnableToResolveFilesystemMount::becauseTheSeparatorIsMissing($path);
        }

        /** @var string $mountIdentifier */
        /** @var string $mountPath */
        [$mountIdentifier, $mountPath] = explode('://', $path, 2);

        if ( ! array_key_exists($mountIdentifier, $this->filesystems)) {
            throw UnableToResolveFilesystemMount::becauseTheMountWasNotRegistered($mountIdentifier);
        }

        return [$this->filesystems[$mountIdentifier], $mountPath, $mountIdentifier];
    }

    private function copyInSameFilesystem(
        FilesystemOperator $sourceFilesystem,
        string $sourcePath,
        string $destinationPath,
        string $source,
        string $destination,
        array $config,
    ): void {
        try {
            $sourceFilesystem->copy($sourcePath, $destinationPath, $this->config->extend($config)->toArray());
        } catch (UnableToCopyFile $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function copyAcrossFilesystem(
        FilesystemOperator $sourceFilesystem,
        string $sourcePath,
        FilesystemOperator $destinationFilesystem,
        string $destinationPath,
        string $source,
        string $destination,
        array $config,
    ): void {
        $config = $this->config->extend($config);
        $retainVisibility = (bool) $config->get(Config::OPTION_RETAIN_VISIBILITY, true);
        $visibility = $config->get(Config::OPTION_VISIBILITY);

        try {
            if ($visibility == null && $retainVisibility) {
                $visibility = $sourceFilesystem->visibility($sourcePath);
                $config = $config->extend(compact('visibility'));
            }

            $stream = $sourceFilesystem->readStream($sourcePath);
            $destinationFilesystem->writeStream($destinationPath, $stream, $config->toArray());
        } catch (UnableToRetrieveMetadata | UnableToReadFile | UnableToWriteFile $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function moveInTheSameFilesystem(
        FilesystemOperator $sourceFilesystem,
        string $sourcePath,
        string $destinationPath,
        string $source,
        string $destination,
        array $config,
    ): void {
        try {
            $sourceFilesystem->move($sourcePath, $destinationPath, $this->config->extend($config)->toArray());
        } catch (UnableToMoveFile $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function moveAcrossFilesystems(string $source, string $destination, array $config = []): void
    {
        try {
            $this->copy($source, $destination, $config);
            $this->delete($source);
        } catch (UnableToCopyFile | UnableToDeleteFile $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }
}
