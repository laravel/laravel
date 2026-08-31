<?php

namespace Illuminate\View;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class FileViewFinder implements ViewFinderInterface
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The array of active view paths.
     *
     * @var string[]
     */
    protected $paths;

    /**
     * The array of views that have been located.
     *
     * @var array<string, string>
     */
    protected $views = [];

    /**
     * The namespace to file path hints.
     *
     * @var array<string, array>
     */
    protected $hints = [];

    /**
     * Register a view extension with the finder.
     *
     * @var string[]
     */
    protected $extensions = ['blade.php', 'php', 'css', 'html'];

    /**
     * Create a new file view loader instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string[]  $paths
     * @param  string[]|null  $extensions
     */
    public function __construct(Filesystem $files, array $paths, ?array $extensions = null)
    {
        $this->files = $files;
        $this->paths = array_map($this->resolvePath(...), $paths);

        if (isset($extensions)) {
            $this->extensions = $extensions;
        }
    }

    /**
     * Get the fully-qualified location of the view.
     *
     * @param  string  $name
     * @return string
     */
    public function find($name)
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        [$namespace, $view] = $this->parseNamespaceSegments($name);

        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Get the segments of a template with a named path.
     *
     * @param  string  $name
     * @return string[]
     *
     * @throws \InvalidArgumentException
     */
    protected function parseNamespaceSegments($name)
    {
        $segments = explode(static::HINT_PATH_DELIMITER, $name);

        if (count($segments) !== 2) {
            throw new InvalidArgumentException("View [{$name}] has an invalid name.");
        }

        if (! isset($this->hints[$segments[0]])) {
            throw new InvalidArgumentException("No hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param  string  $name
     * @param  string|string[]  $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findInPaths($name, $paths)
    {
        foreach ((array) $paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                $viewPath = $path.'/'.$file;

                if (strlen($viewPath) < (PHP_MAXPATHLEN - 1) && $this->files->exists($viewPath)) {
                    return $viewPath;
                }
            }
        }

        throw new InvalidArgumentException("View [{$name}] not found.");
    }

    /**
     * Get an array of possible view files.
     *
     * @param  string  $name
     * @return string[]
     */
    protected function getPossibleViewFiles($name)
    {
        return array_map(fn ($extension) => str_replace('.', '/', $name).'.'.$extension, $this->extensions);
    }

    /**
     * Add a location to the finder.
     *
     * @param  string  $location
     * @return void
     */
    public function addLocation($location)
    {
        $this->paths[] = $this->resolvePath($location);
    }

    /**
     * Prepend a location to the finder.
     *
     * @param  string  $location
     * @return void
     */
    public function prependLocation($location)
    {
        array_unshift($this->paths, $this->resolvePath($location));
    }

    /**
     * Resolve the path.
     *
     * @param  string  $path
     * @return string
     */
    protected function resolvePath($path)
    {
        return realpath($path) ?: $path;
    }

    /**
     * Add a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|string[]  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|string[]  $hints
     * @return void
     */
    public function prependNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param  string  $namespace
     * @param  string|string[]  $hints
     * @return void
     */
    public function replaceNamespace($namespace, $hints)
    {
        $this->hints[$namespace] = (array) $hints;
    }

    /**
     * Register an extension with the view finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension($extension)
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }

    /**
     * Returns whether or not the view name has any hint information.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasHintInformation($name)
    {
        return strpos($name, static::HINT_PATH_DELIMITER) > 0;
    }

    /**
     * Flush the cache of located views.
     *
     * @return void
     */
    public function flush()
    {
        $this->views = [];
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Set the active view paths.
     *
     * @param  string[]  $paths
     * @return $this
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * Get the active view paths.
     *
     * @return string[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Get the views that have been located.
     *
     * @return array<string, string>
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Get the namespace to file path hints.
     *
     * @return array<string, array>
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Get registered extensions.
     *
     * @return string[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
