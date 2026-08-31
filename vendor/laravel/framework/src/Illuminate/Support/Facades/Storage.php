<?php

namespace Illuminate\Support\Facades;

use Illuminate\Filesystem\Filesystem;

use function Illuminate\Support\enum_value;

/**
 * @method static \Illuminate\Contracts\Filesystem\Filesystem drive(string|null $name = null)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem disk(\UnitEnum|string|null $name = null)
 * @method static \Illuminate\Contracts\Filesystem\Cloud cloud()
 * @method static \Illuminate\Contracts\Filesystem\Filesystem build(string|array $config)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem createLocalDriver(array $config, string $name = 'local')
 * @method static \Illuminate\Contracts\Filesystem\Filesystem createFtpDriver(array $config)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem createSftpDriver(array $config)
 * @method static \Illuminate\Contracts\Filesystem\Cloud createS3Driver(array $config)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem createScopedDriver(array $config)
 * @method static \Illuminate\Filesystem\FilesystemManager set(string $name, mixed $disk)
 * @method static string getDefaultDriver()
 * @method static string getDefaultCloudDriver()
 * @method static \Illuminate\Filesystem\FilesystemManager forgetDisk(array|string $disk)
 * @method static void purge(string|null $name = null)
 * @method static \Illuminate\Filesystem\FilesystemManager extend(string $driver, \Closure $callback)
 * @method static \Illuminate\Filesystem\FilesystemManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static string path(string $path)
 * @method static bool exists(string $path)
 * @method static string|null get(string $path)
 * @method static resource|null readStream(string $path)
 * @method static bool put(string $path, \Psr\Http\Message\StreamInterface|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|resource $contents, mixed $options = [])
 * @method static string|false putFile(\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $path, \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null $file = null, mixed $options = [])
 * @method static string|false putFileAs(\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $path, \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null $file, string|array|null $name = null, mixed $options = [])
 * @method static bool writeStream(string $path, resource $resource, array $options = [])
 * @method static string getVisibility(string $path)
 * @method static bool setVisibility(string $path, string $visibility)
 * @method static bool prepend(string $path, string $data)
 * @method static bool append(string $path, string $data)
 * @method static bool delete(string|array $paths)
 * @method static bool copy(string $from, string $to)
 * @method static bool move(string $from, string $to)
 * @method static int size(string $path)
 * @method static int lastModified(string $path)
 * @method static array files(string|null $directory = null, bool $recursive = false)
 * @method static array allFiles(string|null $directory = null)
 * @method static array directories(string|null $directory = null, bool $recursive = false)
 * @method static array allDirectories(string|null $directory = null)
 * @method static bool makeDirectory(string $path)
 * @method static bool deleteDirectory(string $directory)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertExists(string|array $path, string|null $content = null)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertCount(string $path, int $count, bool $recursive = false)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertMissing(string|array $path)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertDirectoryEmpty(string $path)
 * @method static bool missing(string $path)
 * @method static bool fileExists(string $path)
 * @method static bool fileMissing(string $path)
 * @method static bool directoryExists(string $path)
 * @method static bool directoryMissing(string $path)
 * @method static array|null json(string $path, int $flags = 0)
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse response(string $path, string|null $name = null, array $headers = [], string|null $disposition = 'inline')
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse serve(\Illuminate\Http\Request $request, string $path, string|null $name = null, array $headers = [])
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse download(string $path, string|null $name = null, array $headers = [])
 * @method static string|false checksum(string $path, array $options = [])
 * @method static string|false mimeType(string $path)
 * @method static string url(string $path)
 * @method static bool providesTemporaryUrls()
 * @method static bool providesTemporaryUploadUrls()
 * @method static string temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = [])
 * @method static array temporaryUploadUrl(string $path, \DateTimeInterface $expiration, array $options = [])
 * @method static \League\Flysystem\FilesystemOperator getDriver()
 * @method static \League\Flysystem\FilesystemAdapter getAdapter()
 * @method static array getConfig()
 * @method static void serveUsing(\Closure $callback)
 * @method static void buildTemporaryUrlsUsing(\Closure $callback)
 * @method static void buildTemporaryUploadUrlsUsing(\Closure $callback)
 * @method static \Illuminate\Filesystem\FilesystemAdapter|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Filesystem\FilesystemAdapter|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static bool has(string $location)
 * @method static string read(string $location)
 * @method static \League\Flysystem\DirectoryListing listContents(string $location, bool $deep = false)
 * @method static int fileSize(string $path)
 * @method static string visibility(string $path)
 * @method static void write(string $location, string $contents, array $config = [])
 * @method static void createDirectory(string $location, array $config = [])
 *
 * @see \Illuminate\Filesystem\FilesystemManager
 */
class Storage extends Facade
{
    /**
     * Replace the given disk with a local testing disk.
     *
     * @param  \UnitEnum|string|null  $disk
     * @param  array  $config
     * @return \Illuminate\Filesystem\LocalFilesystemAdapter
     */
    public static function fake($disk = null, array $config = [])
    {
        $root = self::getRootPath($disk = enum_value($disk) ?: static::$app['config']->get('filesystems.default'));

        if ($token = ParallelTesting::token()) {
            $root = "{$root}_test_{$token}";
        }

        (new Filesystem)->cleanDirectory($root);

        static::set($disk, $fake = static::createLocalDriver(
            self::buildDiskConfiguration($disk, $config, root: $root)
        ));

        return tap($fake, function ($fake) {
            $fake->buildTemporaryUrlsUsing(function ($path, $expiration) {
                return URL::to($path.'?expiration='.$expiration->getTimestamp());
            });

            $fake->buildTemporaryUploadUrlsUsing(function ($path, $expiration) {
                return ['url' => URL::to($path.'?expiration='.$expiration->getTimestamp()), 'headers' => []];
            });
        });
    }

    /**
     * Replace the given disk with a persistent local testing disk.
     *
     * @param  \UnitEnum|string|null  $disk
     * @param  array  $config
     * @return \Illuminate\Filesystem\LocalFilesystemAdapter
     */
    public static function persistentFake($disk = null, array $config = [])
    {
        $disk = enum_value($disk) ?: static::$app['config']->get('filesystems.default');

        static::set($disk, $fake = static::createLocalDriver(
            self::buildDiskConfiguration($disk, $config, root: self::getRootPath($disk))
        ));

        return $fake;
    }

    /**
     * Get the root path of the given disk.
     *
     * @param  string  $disk
     * @return string
     */
    protected static function getRootPath(string $disk): string
    {
        return storage_path('framework/testing/disks/'.$disk);
    }

    /**
     * Assemble the configuration of the given disk.
     *
     * @param  string  $disk
     * @param  array  $config
     * @param  string  $root
     * @return array
     */
    protected static function buildDiskConfiguration(string $disk, array $config, string $root): array
    {
        $originalConfig = static::$app['config']["filesystems.disks.{$disk}"] ?? [];

        return array_merge([
            'throw' => $originalConfig['throw'] ?? false],
            $config,
            ['root' => $root]
        );
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filesystem';
    }
}
