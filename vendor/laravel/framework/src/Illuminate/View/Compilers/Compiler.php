<?php

namespace Illuminate\View\Compilers;

use ErrorException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class Compiler
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The cache path for the compiled views.
     *
     * @var string
     */
    protected $cachePath;

    /**
     * The base path that should be removed from paths before hashing.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Determines if compiled views should be cached.
     *
     * @var bool
     */
    protected $shouldCache;

    /**
     * The compiled view file extension.
     *
     * @var string
     */
    protected $compiledExtension = 'php';

    /**
     * Indicates if view cache timestamps should be checked.
     *
     * @var bool
     */
    protected $shouldCheckTimestamps;

    /**
     * Create a new compiler instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $cachePath
     * @param  string  $basePath
     * @param  bool  $shouldCache
     * @param  string  $compiledExtension
     * @param  bool  $shouldCheckTimestamps
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Filesystem $files,
        $cachePath,
        $basePath = '',
        $shouldCache = true,
        $compiledExtension = 'php',
        $shouldCheckTimestamps = true,
    ) {
        if (! $cachePath) {
            throw new InvalidArgumentException('Please provide a valid cache path.');
        }

        $this->files = $files;
        $this->cachePath = $cachePath;
        $this->basePath = $basePath;
        $this->shouldCache = $shouldCache;
        $this->compiledExtension = $compiledExtension;
        $this->shouldCheckTimestamps = $shouldCheckTimestamps;
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string  $path
     * @return string
     */
    public function getCompiledPath($path)
    {
        return $this->cachePath.'/'.hash('xxh128', 'v2'.Str::after($path, $this->basePath)).'.'.$this->compiledExtension;
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param  string  $path
     * @return bool
     *
     * @throws \ErrorException
     */
    public function isExpired($path)
    {
        if (! $this->shouldCache) {
            return true;
        }

        $compiled = $this->getCompiledPath($path);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (! $this->files->exists($compiled)) {
            return true;
        }

        if (! $this->shouldCheckTimestamps) {
            return false;
        }

        try {
            return $this->files->lastModified($path) >=
                $this->files->lastModified($compiled);
        } catch (ErrorException $exception) {
            if (! $this->files->exists($compiled)) {
                return true;
            }

            throw $exception;
        }
    }

    /**
     * Create the compiled file directory if necessary.
     *
     * @param  string  $path
     * @return void
     */
    protected function ensureCompiledDirectoryExists($path)
    {
        if (! $this->files->exists(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}
