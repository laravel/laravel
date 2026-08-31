<?php

namespace Illuminate\Filesystem;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Filesystem\Cloud as CloudFilesystemContract;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use League\Flysystem\FilesystemAdapter as FlysystemAdapter;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\StorageAttributes;
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
use League\Flysystem\Visibility;
use PHPUnit\Framework\Assert as PHPUnit;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @mixin \League\Flysystem\FilesystemOperator
 */
class FilesystemAdapter implements CloudFilesystemContract
{
    use Conditionable;
    use Macroable {
        __call as macroCall;
    }

    /**
     * The Flysystem filesystem implementation.
     *
     * @var \League\Flysystem\FilesystemOperator
     */
    protected $driver;

    /**
     * The Flysystem adapter implementation.
     *
     * @var \League\Flysystem\FilesystemAdapter
     */
    protected $adapter;

    /**
     * The filesystem configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The Flysystem PathPrefixer instance.
     *
     * @var \League\Flysystem\PathPrefixer
     */
    protected $prefixer;

    /**
     * The file server callback.
     *
     * @var \Closure|null
     */
    protected $serveCallback;

    /**
     * The temporary URL builder callback.
     *
     * @var \Closure|null
     */
    protected $temporaryUrlCallback;

    /**
     * The temporary upload URL builder callback.
     *
     * @var \Closure|null
     */
    protected $temporaryUploadUrlCallback;

    /**
     * Create a new filesystem adapter instance.
     *
     * @param  \League\Flysystem\FilesystemOperator  $driver
     * @param  \League\Flysystem\FilesystemAdapter  $adapter
     * @param  array  $config
     */
    public function __construct(FilesystemOperator $driver, FlysystemAdapter $adapter, array $config = [])
    {
        $this->driver = $driver;
        $this->adapter = $adapter;
        $this->config = $config;
        $separator = $config['directory_separator'] ?? DIRECTORY_SEPARATOR;

        $this->prefixer = new PathPrefixer($config['root'] ?? '', $separator);

        if (isset($config['prefix'])) {
            $this->prefixer = new PathPrefixer($this->prefixer->prefixPath($config['prefix']), $separator);
        }
    }

    /**
     * Assert that the given file or directory exists.
     *
     * @param  string|array  $path
     * @param  string|null  $content
     * @return $this
     */
    public function assertExists($path, $content = null)
    {
        clearstatcache();

        $paths = Arr::wrap($path);

        foreach ($paths as $path) {
            PHPUnit::assertTrue(
                $this->exists($path), "Unable to find a file or directory at path [{$path}]."
            );

            if (! is_null($content)) {
                $actual = $this->get($path);

                PHPUnit::assertSame(
                    $content,
                    $actual,
                    "File or directory [{$path}] was found, but content [{$actual}] does not match [{$content}]."
                );
            }
        }

        return $this;
    }

    /**
     * Assert that the number of files in path equals the expected count.
     *
     * @param  string  $path
     * @param  int  $count
     * @param  bool  $recursive
     * @return $this
     */
    public function assertCount($path, $count, $recursive = false)
    {
        clearstatcache();

        $actual = count($this->files($path, $recursive));

        PHPUnit::assertEquals(
            $count, $actual, "Expected [{$count}] files at [{$path}], but found [{$actual}]."
        );

        return $this;
    }

    /**
     * Assert that the given file or directory does not exist.
     *
     * @param  string|array  $path
     * @return $this
     */
    public function assertMissing($path)
    {
        clearstatcache();

        $paths = Arr::wrap($path);

        foreach ($paths as $path) {
            PHPUnit::assertFalse(
                $this->exists($path), "Found unexpected file or directory at path [{$path}]."
            );
        }

        return $this;
    }

    /**
     * Assert that the given directory is empty.
     *
     * @param  string  $path
     * @return $this
     */
    public function assertDirectoryEmpty($path)
    {
        PHPUnit::assertEmpty(
            $this->allFiles($path), "Directory [{$path}] is not empty."
        );

        return $this;
    }

    /**
     * Determine if a file or directory exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return $this->driver->has($path);
    }

    /**
     * Determine if a file or directory is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public function missing($path)
    {
        return ! $this->exists($path);
    }

    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function fileExists($path)
    {
        return $this->driver->fileExists($path);
    }

    /**
     * Determine if a file is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public function fileMissing($path)
    {
        return ! $this->fileExists($path);
    }

    /**
     * Determine if a directory exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function directoryExists($path)
    {
        return $this->driver->directoryExists($path);
    }

    /**
     * Determine if a directory is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public function directoryMissing($path)
    {
        return ! $this->directoryExists($path);
    }

    /**
     * Get the full path to the file that exists at the given relative path.
     *
     * @param  string  $path
     * @return string
     */
    public function path($path)
    {
        return $this->prefixer->prefixPath($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @return string|null
     */
    public function get($path)
    {
        try {
            return $this->driver->read($path);
        } catch (UnableToReadFile $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);
        }
    }

    /**
     * Get the contents of a file as decoded JSON.
     *
     * @param  string  $path
     * @param  int  $flags
     * @return array|null
     */
    public function json($path, $flags = 0)
    {
        $content = $this->get($path);

        return is_null($content) ? null : json_decode($content, true, 512, $flags);
    }

    /**
     * Create a streamed response for a given file.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $headers
     * @param  string|null  $disposition
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * @throws UnableToRetrieveMetadata
     */
    public function response($path, $name = null, array $headers = [], $disposition = 'inline')
    {
        $response = new StreamedResponse;

        $headers['Content-Type'] ??= $this->mimeType($path);
        $headers['Content-Length'] ??= $this->size($path);

        if (! array_key_exists('Content-Disposition', $headers)) {
            $filename = $name ?? basename($path);

            $disposition = $response->headers->makeDisposition(
                $disposition, $filename, $this->fallbackName($filename)
            );

            $headers['Content-Disposition'] = $disposition;
        }

        $response->headers->replace($headers);

        $response->setCallback(function () use ($path) {
            $stream = $this->readStream($path);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * Create a streamed download response for a given file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * @throws UnableToRetrieveMetadata
     */
    public function serve(Request $request, $path, $name = null, array $headers = [])
    {
        return isset($this->serveCallback)
            ? call_user_func($this->serveCallback, $request, $path, $headers)
            : $this->response($path, $name, $headers);
    }

    /**
     * Create a streamed download response for a given file.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * @throws UnableToRetrieveMetadata
     */
    public function download($path, $name = null, array $headers = [])
    {
        return $this->response($path, $name, $headers, 'attachment');
    }

    /**
     * Convert the string to ASCII characters that are equivalent to the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function fallbackName($name)
    {
        return str_replace('%', '', Str::ascii($name));
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  \Psr\Http\Message\StreamInterface|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|resource  $contents
     * @param  mixed  $options
     * @return string|bool
     */
    public function put($path, $contents, $options = [])
    {
        $options = is_string($options)
            ? ['visibility' => $options]
            : (array) $options;

        // If the given contents is actually a file or uploaded file instance than we will
        // automatically store the file using a stream. This provides a convenient path
        // for the developer to store streams without managing them manually in code.
        if ($contents instanceof File ||
            $contents instanceof UploadedFile) {
            return $this->putFile($path, $contents, $options);
        }

        try {
            if ($contents instanceof StreamInterface) {
                $this->driver->writeStream($path, $contents->detach(), $options);

                return true;
            }

            is_resource($contents)
                ? $this->driver->writeStream($path, $contents, $options)
                : $this->driver->write($path, $contents, $options);
        } catch (UnableToWriteFile|UnableToSetVisibility $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Store the uploaded file on the disk.
     *
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string  $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null  $file
     * @param  mixed  $options
     * @return string|false
     */
    public function putFile($path, $file = null, $options = [])
    {
        if (is_null($file) || is_array($file)) {
            [$path, $file, $options] = ['', $path, $file ?? []];
        }

        $file = is_string($file) ? new File($file) : $file;

        return $this->putFileAs($path, $file, $file->hashName(), $options);
    }

    /**
     * Store the uploaded file on the disk with a given name.
     *
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string  $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null  $file
     * @param  string|array|null  $name
     * @param  mixed  $options
     * @return string|false
     */
    public function putFileAs($path, $file, $name = null, $options = [])
    {
        if (is_null($name) || is_array($name)) {
            [$path, $file, $name, $options] = ['', $path, $file, $name ?? []];
        }

        $stream = fopen(is_string($file) ? $file : $file->getRealPath(), 'r');

        // Next, we will format the path of the file and store the file using a stream since
        // they provide better performance than alternatives. Once we write the file this
        // stream will get closed automatically by us so the developer doesn't have to.
        $result = $this->put(
            $path = trim($path.'/'.$name, '/'), $stream, $options
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result ? $path : false;
    }

    /**
     * Get the visibility for the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function getVisibility($path)
    {
        if ($this->driver->visibility($path) == Visibility::PUBLIC) {
            return FilesystemContract::VISIBILITY_PUBLIC;
        }

        return FilesystemContract::VISIBILITY_PRIVATE;
    }

    /**
     * Set the visibility for the given path.
     *
     * @param  string  $path
     * @param  string  $visibility
     * @return bool
     */
    public function setVisibility($path, $visibility)
    {
        try {
            $this->driver->setVisibility($path, $this->parseVisibility($visibility));
        } catch (UnableToSetVisibility $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @param  string  $separator
     * @return bool
     */
    public function prepend($path, $data, $separator = PHP_EOL)
    {
        if ($this->fileExists($path)) {
            return $this->put($path, $data.$separator.$this->get($path));
        }

        return $this->put($path, $data);
    }

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @param  string  $separator
     * @return bool
     */
    public function append($path, $data, $separator = PHP_EOL)
    {
        if ($this->fileExists($path)) {
            return $this->put($path, $this->get($path).$separator.$data);
        }

        return $this->put($path, $data);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                $this->driver->delete($path);
            } catch (UnableToDeleteFile $e) {
                throw_if($this->throwsExceptions(), $e);

                $this->report($e);

                $success = false;
            }
        }

        return $success;
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copy($from, $to)
    {
        try {
            $this->driver->copy($from, $to);
        } catch (UnableToCopyFile $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function move($from, $to)
    {
        try {
            $this->driver->move($from, $to);
        } catch (UnableToMoveFile $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path)
    {
        return $this->driver->fileSize($path);
    }

    /**
     * Get the checksum for a file.
     *
     * @return string|false
     *
     * @throws UnableToProvideChecksum
     */
    public function checksum(string $path, array $options = [])
    {
        try {
            return $this->driver->checksum($path, $options);
        } catch (UnableToProvideChecksum $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|false
     *
     * @throws UnableToRetrieveMetadata
     */
    public function mimeType($path)
    {
        try {
            return $this->driver->mimeType($path);
        } catch (UnableToRetrieveMetadata $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);
        }

        return false;
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path)
    {
        return $this->driver->lastModified($path);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        try {
            return $this->driver->readStream($path);
        } catch (UnableToReadFile $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, array $options = [])
    {
        try {
            $this->driver->writeStream($path, $resource, $options);
        } catch (UnableToWriteFile|UnableToSetVisibility $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \RuntimeException
     */
    public function url($path)
    {
        if (isset($this->config['prefix'])) {
            $path = $this->concatPathToUrl($this->config['prefix'], $path);
        }

        $adapter = $this->adapter;

        if (method_exists($adapter, 'getUrl')) {
            return $adapter->getUrl($path);
        } elseif (method_exists($this->driver, 'getUrl')) {
            return $this->driver->getUrl($path);
        } elseif ($adapter instanceof FtpAdapter || $adapter instanceof SftpAdapter) {
            return $this->getFtpUrl($path);
        } elseif ($adapter instanceof LocalAdapter) {
            return $this->getLocalUrl($path);
        } else {
            throw new RuntimeException('This driver does not support retrieving URLs.');
        }
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getFtpUrl($path)
    {
        return isset($this->config['url'])
            ? $this->concatPathToUrl($this->config['url'], $path)
            : $path;
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getLocalUrl($path)
    {
        // If an explicit base URL has been set on the disk configuration then we will use
        // it as the base URL instead of the default path. This allows the developer to
        // have full control over the base path for this filesystem's generated URLs.
        if (isset($this->config['url'])) {
            return $this->concatPathToUrl($this->config['url'], $path);
        }

        $path = '/storage/'.$path;

        // If the path contains "storage/public", it probably means the developer is using
        // the default disk to generate the path instead of the "public" disk like they
        // are really supposed to use. We will remove the public from this path here.
        if (str_contains($path, '/storage/public/')) {
            return Str::replaceFirst('/public/', '/', $path);
        }

        return $path;
    }

    /**
     * Determine if temporary URLs can be generated.
     *
     * @return bool
     */
    public function providesTemporaryUrls()
    {
        return method_exists($this->adapter, 'getTemporaryUrl') || isset($this->temporaryUrlCallback);
    }

    /**
     * Determine if temporary upload URLs can be generated.
     *
     * @return bool
     */
    public function providesTemporaryUploadUrls()
    {
        return method_exists($this->adapter, 'temporaryUploadUrl') || isset($this->temporaryUploadUrlCallback);
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public function temporaryUrl($path, $expiration, array $options = [])
    {
        if (method_exists($this->adapter, 'getTemporaryUrl')) {
            return $this->adapter->getTemporaryUrl($path, $expiration, $options);
        }

        if ($this->temporaryUrlCallback) {
            return $this->temporaryUrlCallback->bindTo($this, static::class)(
                $path, $expiration, $options
            );
        }

        throw new RuntimeException('This driver does not support creating temporary URLs.');
    }

    /**
     * Get a temporary upload URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return array
     *
     * @throws \RuntimeException
     */
    public function temporaryUploadUrl($path, $expiration, array $options = [])
    {
        if (method_exists($this->adapter, 'temporaryUploadUrl')) {
            return $this->adapter->temporaryUploadUrl($path, $expiration, $options);
        }

        if ($this->temporaryUploadUrlCallback) {
            return $this->temporaryUploadUrlCallback->bindTo($this, static::class)(
                $path, $expiration, $options
            );
        }

        throw new RuntimeException('This driver does not support creating temporary upload URLs.');
    }

    /**
     * Concatenate a path to a URL.
     *
     * @param  string  $url
     * @param  string  $path
     * @return string
     */
    protected function concatPathToUrl($url, $path)
    {
        return rtrim($url, '/').'/'.ltrim($path, '/');
    }

    /**
     * Replace the scheme, host and port of the given UriInterface with values from the given URL.
     *
     * @param  \Psr\Http\Message\UriInterface  $uri
     * @param  string  $url
     * @return \Psr\Http\Message\UriInterface
     */
    protected function replaceBaseUrl($uri, $url)
    {
        $parsed = parse_url($url);

        return $uri
            ->withScheme($parsed['scheme'])
            ->withHost($parsed['host'])
            ->withPort($parsed['port'] ?? null);
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array
     */
    public function files($directory = null, $recursive = false)
    {
        return $this->driver->listContents($directory ?? '', $recursive)
            ->filter(function (StorageAttributes $attributes) {
                return $attributes->isFile();
            })
            ->sortByPath()
            ->map(function (StorageAttributes $attributes) {
                return $attributes->path();
            })
            ->toArray();
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string|null  $directory
     * @return array
     */
    public function allFiles($directory = null)
    {
        return $this->files($directory, true);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array
     */
    public function directories($directory = null, $recursive = false)
    {
        return $this->driver->listContents($directory ?? '', $recursive)
            ->filter(function (StorageAttributes $attributes) {
                return $attributes->isDir();
            })
            ->map(function (StorageAttributes $attributes) {
                return $attributes->path();
            })
            ->toArray();
    }

    /**
     * Get all the directories within a given directory (recursive).
     *
     * @param  string|null  $directory
     * @return array
     */
    public function allDirectories($directory = null)
    {
        return $this->directories($directory, true);
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @return bool
     */
    public function makeDirectory($path)
    {
        try {
            $this->driver->createDirectory($path);
        } catch (UnableToCreateDirectory|UnableToSetVisibility $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Recursively delete a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        try {
            $this->driver->deleteDirectory($directory);
        } catch (UnableToDeleteDirectory $e) {
            throw_if($this->throwsExceptions(), $e);

            $this->report($e);

            return false;
        }

        return true;
    }

    /**
     * Get the Flysystem driver.
     *
     * @return \League\Flysystem\FilesystemOperator
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get the Flysystem adapter.
     *
     * @return \League\Flysystem\FilesystemAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Get the configuration values.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Parse the given visibility value.
     *
     * @param  string|null  $visibility
     * @return string|null
     *
     * @throws \InvalidArgumentException
     */
    protected function parseVisibility($visibility)
    {
        if (is_null($visibility)) {
            return;
        }

        return match ($visibility) {
            FilesystemContract::VISIBILITY_PUBLIC => Visibility::PUBLIC,
            FilesystemContract::VISIBILITY_PRIVATE => Visibility::PRIVATE,
            default => throw new InvalidArgumentException("Unknown visibility: {$visibility}."),
        };
    }

    /**
     * Define a custom callback that generates file download responses.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function serveUsing(Closure $callback)
    {
        $this->serveCallback = $callback;
    }

    /**
     * Define a custom temporary URL builder callback.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function buildTemporaryUrlsUsing(Closure $callback)
    {
        $this->temporaryUrlCallback = $callback;
    }

    /**
     * Define a custom temporary upload URL builder callback.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function buildTemporaryUploadUrlsUsing(Closure $callback)
    {
        $this->temporaryUploadUrlCallback = $callback;
    }

    /**
     * Determine if Flysystem exceptions should be thrown.
     *
     * @return bool
     */
    protected function throwsExceptions(): bool
    {
        return (bool) ($this->config['throw'] ?? false);
    }

    /**
     * Report the exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    protected function report($exception)
    {
        if ($this->shouldReport() && Container::getInstance()->bound(ExceptionHandler::class)) {
            Container::getInstance()->make(ExceptionHandler::class)->report($exception);
        }
    }

    /**
     * Determine if Flysystem exceptions should be reported.
     *
     * @return bool
     */
    protected function shouldReport(): bool
    {
        return (bool) ($this->config['report'] ?? false);
    }

    /**
     * Pass dynamic methods call onto Flysystem.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->driver->{$method}(...$parameters);
    }
}
