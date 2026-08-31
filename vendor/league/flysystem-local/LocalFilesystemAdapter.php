<?php

declare(strict_types=1);

namespace League\Flysystem\Local;

use const DIRECTORY_SEPARATOR;
use const LOCK_EX;
use DirectoryIterator;
use FilesystemIterator;
use Generator;
use League\Flysystem\ChecksumProvider;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\SymbolicLinkEncountered;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToProvideChecksum;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;
use function chmod;
use function clearstatcache;
use function dirname;
use function error_clear_last;
use function error_get_last;
use function file_exists;
use function file_put_contents;
use function hash_file;
use function is_dir;
use function is_file;
use function mkdir;
use function rename;

class LocalFilesystemAdapter implements FilesystemAdapter, ChecksumProvider
{
    /**
     * @var int
     */
    public const SKIP_LINKS = 0001;

    /**
     * @var int
     */
    public const DISALLOW_LINKS = 0002;

    private PathPrefixer $prefixer;
    private VisibilityConverter $visibility;
    private MimeTypeDetector $mimeTypeDetector;
    private string $rootLocation;

    /**
     * @var bool
     */
    private $rootLocationIsSetup = false;

    public function __construct(
        string $location,
        ?VisibilityConverter $visibility = null,
        private int $writeFlags = LOCK_EX,
        private int $linkHandling = self::DISALLOW_LINKS,
        ?MimeTypeDetector $mimeTypeDetector = null,
        bool $lazyRootCreation = false,
        bool $useInconclusiveMimeTypeFallback = false,
    ) {
        $this->prefixer = new PathPrefixer($location, DIRECTORY_SEPARATOR);
        $visibility ??= new PortableVisibilityConverter();
        $this->visibility = $visibility;
        $this->rootLocation = $location;
        $this->mimeTypeDetector = $mimeTypeDetector ?? new FallbackMimeTypeDetector(
            detector: new FinfoMimeTypeDetector(),
            useInconclusiveMimeTypeFallback: $useInconclusiveMimeTypeFallback,
        );

        if ( ! $lazyRootCreation) {
            $this->ensureRootDirectoryExists();
        }
    }

    private function ensureRootDirectoryExists(): void
    {
        if ($this->rootLocationIsSetup) {
            return;
        }

        $this->ensureDirectoryExists($this->rootLocation, $this->visibility->defaultForDirectories());
        $this->rootLocationIsSetup = true;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->writeToFile($path, $contents, $config);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->writeToFile($path, $contents, $config);
    }

    /**
     * @param resource|string $contents
     */
    private function writeToFile(string $path, $contents, Config $config): void
    {
        $prefixedLocation = $this->prefixer->prefixPath($path);
        $this->ensureRootDirectoryExists();
        $this->ensureDirectoryExists(
            dirname($prefixedLocation),
            $this->resolveDirectoryVisibility($config->get(Config::OPTION_DIRECTORY_VISIBILITY))
        );
        error_clear_last();

        if (@file_put_contents($prefixedLocation, $contents, $this->writeFlags) === false) {
            throw UnableToWriteFile::atLocation($path, error_get_last()['message'] ?? '');
        }

        if ($visibility = $config->get(Config::OPTION_VISIBILITY)) {
            $this->setVisibility($path, (string) $visibility);
        }
    }

    public function delete(string $path): void
    {
        $location = $this->prefixer->prefixPath($path);

        if ( ! file_exists($location)) {
            return;
        }

        error_clear_last();

        if ( ! @unlink($location)) {
            throw UnableToDeleteFile::atLocation($location, error_get_last()['message'] ?? '');
        }
    }

    public function deleteDirectory(string $prefix): void
    {
        $location = $this->prefixer->prefixPath($prefix);

        if ( ! is_dir($location)) {
            return;
        }

        $contents = $this->listDirectoryRecursively($location, RecursiveIteratorIterator::CHILD_FIRST);

        /** @var SplFileInfo $file */
        foreach ($contents as $file) {
            if ( ! $this->deleteFileInfoObject($file)) {
                throw UnableToDeleteDirectory::atLocation($prefix, "Unable to delete file at " . $file->getPathname());
            }
        }

        unset($contents);

        if ( ! @rmdir($location)) {
            throw UnableToDeleteDirectory::atLocation($prefix, error_get_last()['message'] ?? '');
        }
    }

    private function listDirectoryRecursively(
        string $path,
        int $mode = RecursiveIteratorIterator::SELF_FIRST
    ): Generator {
        if ( ! is_dir($path)) {
            return;
        }

        yield from new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            $mode
        );
    }

    protected function deleteFileInfoObject(SplFileInfo $file): bool
    {
        switch ($file->getType()) {
            case 'dir':
                return @rmdir((string) $file->getRealPath());
            case 'link':
                return @unlink((string) $file->getPathname());
            default:
                return @unlink((string) $file->getRealPath());
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $location = $this->prefixer->prefixPath($path);

        if ( ! is_dir($location)) {
            return;
        }

        /** @var SplFileInfo[] $iterator */
        $iterator = $deep ? $this->listDirectoryRecursively($location) : $this->listDirectory($location);

        foreach ($iterator as $fileInfo) {
            $pathName = $fileInfo->getPathname();

            try {
                if ($fileInfo->isLink()) {
                    if ($this->linkHandling & self::SKIP_LINKS) {
                        continue;
                    }
                    throw SymbolicLinkEncountered::atLocation($pathName);
                }

                $path = $this->prefixer->stripPrefix($pathName);
                $lastModified = $fileInfo->getMTime();
                $isDirectory = $fileInfo->isDir();
                $permissions = octdec(substr(sprintf('%o', $fileInfo->getPerms()), -4));
                $visibility = $isDirectory ? $this->visibility->inverseForDirectory($permissions) : $this->visibility->inverseForFile($permissions);

                yield $isDirectory ? new DirectoryAttributes(str_replace('\\', '/', $path), $visibility, $lastModified) : new FileAttributes(
                    str_replace('\\', '/', $path),
                    $fileInfo->getSize(),
                    $visibility,
                    $lastModified
                );
            } catch (Throwable $exception) {
                if (file_exists($pathName)) {
                    throw $exception;
                }
            }
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $sourcePath = $this->prefixer->prefixPath($source);
        $destinationPath = $this->prefixer->prefixPath($destination);

        $this->ensureRootDirectoryExists();
        $this->ensureDirectoryExists(
            dirname($destinationPath),
            $this->resolveDirectoryVisibility($config->get(Config::OPTION_DIRECTORY_VISIBILITY))
        );

        error_clear_last();
        if ( ! @rename($sourcePath, $destinationPath)) {
            throw UnableToMoveFile::because(error_get_last()['message'] ?? 'unknown reason', $source, $destination);
        }

        if ($visibility = $config->get(Config::OPTION_VISIBILITY)) {
            $this->setVisibility($destination, (string) $visibility);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $sourcePath = $this->prefixer->prefixPath($source);
        $destinationPath = $this->prefixer->prefixPath($destination);
        $this->ensureRootDirectoryExists();
        $this->ensureDirectoryExists(
            dirname($destinationPath),
            $this->resolveDirectoryVisibility($config->get(Config::OPTION_DIRECTORY_VISIBILITY))
        );

        error_clear_last();
        if ($sourcePath !== $destinationPath && ! @copy($sourcePath, $destinationPath)) {
            throw UnableToCopyFile::because(error_get_last()['message'] ?? 'unknown', $source, $destination);
        }

        $visibility = $config->get(
            Config::OPTION_VISIBILITY,
            $config->get(Config::OPTION_RETAIN_VISIBILITY, true)
                ? $this->visibility($source)->visibility()
                : null,
        );

        if ($visibility) {
            $this->setVisibility($destination, (string) $visibility);
        }
    }

    public function read(string $path): string
    {
        $location = $this->prefixer->prefixPath($path);
        error_clear_last();
        $contents = @file_get_contents($location);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, error_get_last()['message'] ?? '');
        }

        return $contents;
    }

    public function readStream(string $path)
    {
        $location = $this->prefixer->prefixPath($path);
        error_clear_last();
        $contents = @fopen($location, 'rb');

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, error_get_last()['message'] ?? '');
        }

        return $contents;
    }

    protected function ensureDirectoryExists(string $dirname, int $visibility): void
    {
        if (is_dir($dirname)) {
            return;
        }

        error_clear_last();

        if ( ! @mkdir($dirname, $visibility, true)) {
            $mkdirError = error_get_last();
        }

        clearstatcache(true, $dirname);

        if ( ! is_dir($dirname)) {
            $errorMessage = isset($mkdirError['message']) ? $mkdirError['message'] : '';

            throw UnableToCreateDirectory::atLocation($dirname, $errorMessage);
        }
    }

    public function fileExists(string $location): bool
    {
        $location = $this->prefixer->prefixPath($location);
        clearstatcache();
        return is_file($location);
    }

    public function directoryExists(string $location): bool
    {
        $location = $this->prefixer->prefixPath($location);
        clearstatcache();
        return is_dir($location);
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->ensureRootDirectoryExists();
        $location = $this->prefixer->prefixPath($path);
        $visibility = $config->get(Config::OPTION_VISIBILITY, $config->get(Config::OPTION_DIRECTORY_VISIBILITY));
        $permissions = $this->resolveDirectoryVisibility($visibility);

        if (is_dir($location)) {
            $this->setPermissions($location, $permissions);

            return;
        }

        error_clear_last();

        if ( ! @mkdir($location, $permissions, true)) {
            throw UnableToCreateDirectory::atLocation($path, error_get_last()['message'] ?? '');
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $path = $this->prefixer->prefixPath($path);
        $visibility = is_dir($path) ? $this->visibility->forDirectory($visibility) : $this->visibility->forFile(
            $visibility
        );

        $this->setPermissions($path, $visibility);
    }

    public function visibility(string $path): FileAttributes
    {
        $location = $this->prefixer->prefixPath($path);
        clearstatcache(false, $location);
        error_clear_last();
        $fileperms = @fileperms($location);

        if ($fileperms === false) {
            throw UnableToRetrieveMetadata::visibility($path, error_get_last()['message'] ?? '');
        }

        $permissions = $fileperms & 0777;
        $visibility = $this->visibility->inverseForFile($permissions);

        return new FileAttributes($path, null, $visibility);
    }

    private function resolveDirectoryVisibility(?string $visibility): int
    {
        return $visibility === null ? $this->visibility->defaultForDirectories() : $this->visibility->forDirectory(
            $visibility
        );
    }

    public function mimeType(string $path): FileAttributes
    {
        $location = $this->prefixer->prefixPath($path);
        error_clear_last();

        if ( ! is_file($location)) {
            throw UnableToRetrieveMetadata::mimeType($location, 'No such file exists.');
        }

        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($location);

        if ($mimeType === null) {
            throw UnableToRetrieveMetadata::mimeType($path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($path, null, null, null, $mimeType);
    }

    public function lastModified(string $path): FileAttributes
    {
        $location = $this->prefixer->prefixPath($path);
        clearstatcache();
        error_clear_last();
        $lastModified = @filemtime($location);

        if ($lastModified === false) {
            throw UnableToRetrieveMetadata::lastModified($path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($path, null, null, $lastModified);
    }

    public function fileSize(string $path): FileAttributes
    {
        $location = $this->prefixer->prefixPath($path);
        clearstatcache();
        error_clear_last();

        if (is_file($location) && ($fileSize = @filesize($location)) !== false) {
            return new FileAttributes($path, $fileSize);
        }

        throw UnableToRetrieveMetadata::fileSize($path, error_get_last()['message'] ?? '');
    }

    public function checksum(string $path, Config $config): string
    {
        $algo = $config->get('checksum_algo', 'md5');
        $location = $this->prefixer->prefixPath($path);
        error_clear_last();
        $checksum = @hash_file($algo, $location);

        if ($checksum === false) {
            throw new UnableToProvideChecksum(error_get_last()['message'] ?? '', $path);
        }

        return $checksum;
    }

    private function listDirectory(string $location): Generator
    {
        $iterator = new DirectoryIterator($location);

        foreach ($iterator as $item) {
            if ($item->isDot()) {
                continue;
            }

            yield $item;
        }
    }

    private function setPermissions(string $location, int $visibility): void
    {
        error_clear_last();
        if ( ! @chmod($location, $visibility)) {
            $extraMessage = error_get_last()['message'] ?? '';
            throw UnableToSetVisibility::atLocation($this->prefixer->stripPrefix($location), $extraMessage);
        }
    }
}
