<?php

namespace Illuminate\Translation;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use RuntimeException;

class FileLoader implements Loader
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The default paths for the loader.
     *
     * @var array
     */
    protected $paths;

    /**
     * All of the registered paths to JSON translation files.
     *
     * @var array
     */
    protected $jsonPaths = [];

    /**
     * All of the namespace hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * Create a new file loader instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  array|string  $path
     */
    public function __construct(Filesystem $files, array|string $path)
    {
        $this->files = $files;

        $this->paths = is_string($path) ? [$path] : $path;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadJsonPaths($locale);
        }

        if (is_null($namespace) || $namespace === '*') {
            return $this->loadPaths($this->paths, $locale, $group);
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }

    /**
     * Load a namespaced translation group.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    protected function loadNamespaced($locale, $group, $namespace)
    {
        if (isset($this->hints[$namespace])) {
            $lines = $this->loadPaths([$this->hints[$namespace]], $locale, $group);

            return $this->loadNamespaceOverrides($lines, $locale, $group, $namespace);
        }

        return [];
    }

    /**
     * Load a local namespaced translation group for overrides.
     *
     * @param  array  $lines
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace)
    {
        return (new Collection($this->paths))
            ->reduce(function ($output, $path) use ($locale, $group, $namespace) {
                $file = "{$path}/vendor/{$namespace}/{$locale}/{$group}.php";

                if ($this->files->exists($file)) {
                    $output = array_replace_recursive($output, $this->files->getRequire($file));
                }

                return $output;
            }, $lines);
    }

    /**
     * Load a locale from a given path.
     *
     * @param  array  $paths
     * @param  string  $locale
     * @param  string  $group
     * @return array
     */
    protected function loadPaths(array $paths, $locale, $group)
    {
        return (new Collection($paths))
            ->reduce(function ($output, $path) use ($locale, $group) {
                if ($this->files->exists($full = "{$path}/{$locale}/{$group}.php")) {
                    $output = array_replace_recursive($output, $this->files->getRequire($full));
                }

                return $output;
            }, []);
    }

    /**
     * Load a locale from the given JSON file path.
     *
     * @param  string  $locale
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function loadJsonPaths($locale)
    {
        return (new Collection(array_merge($this->jsonPaths, $this->paths)))
            ->reduce(function ($output, $path) use ($locale) {
                if ($this->files->exists($full = "{$path}/{$locale}.json")) {
                    $decoded = json_decode($this->files->get($full), true);

                    if (is_null($decoded) || json_last_error() !== JSON_ERROR_NONE) {
                        throw new RuntimeException("Translation file [{$full}] contains an invalid JSON structure.");
                    }

                    $output = array_merge($output, $decoded);
                }

                return $output;
            }, []);
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        return $this->hints;
    }

    /**
     * Add a new path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addJsonPath($path)
    {
        $this->jsonPaths[] = $path;
    }

    /**
     * Get an array of all the registered paths to translation files.
     *
     * @return array
     */
    public function paths()
    {
        return $this->paths;
    }

    /**
     * Get an array of all the registered paths to JSON translation files.
     *
     * @return array
     */
    public function jsonPaths()
    {
        return $this->jsonPaths;
    }
}
