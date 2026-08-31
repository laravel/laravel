<?php

namespace Laravel\Tinker;

use Illuminate\Support\Str;
use Psy\Shell;

class ClassAliasAutoloader
{
    /**
     * The shell instance.
     *
     * @var \Psy\Shell
     */
    protected $shell;

    /**
     * All of the discovered classes.
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Path to the vendor directory.
     *
     * @var string
     */
    protected $vendorPath;

    /**
     * Explicitly included namespaces/classes.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $includedAliases;

    /**
     * Excluded namespaces/classes.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $excludedAliases;

    /**
     * Register a new alias loader instance.
     *
     * @param  \Psy\Shell  $shell
     * @param  string  $classMapPath
     * @param  array  $includedAliases
     * @param  array  $excludedAliases
     * @return static
     */
    public static function register(Shell $shell, $classMapPath, array $includedAliases = [], array $excludedAliases = [])
    {
        return tap(new static($shell, $classMapPath, $includedAliases, $excludedAliases), function ($loader) {
            spl_autoload_register([$loader, 'aliasClass']);
        });
    }

    /**
     * Create a new alias loader instance.
     *
     * @param  \Psy\Shell  $shell
     * @param  string  $classMapPath
     * @param  array  $includedAliases
     * @param  array  $excludedAliases
     * @return void
     */
    public function __construct(Shell $shell, $classMapPath, array $includedAliases = [], array $excludedAliases = [])
    {
        $this->shell = $shell;
        $this->vendorPath = dirname(dirname($classMapPath));
        $this->includedAliases = collect($includedAliases);
        $this->excludedAliases = collect($excludedAliases);

        $classes = require $classMapPath;

        foreach ($classes as $class => $path) {
            if (! $this->isAliasable($class, $path)) {
                continue;
            }

            $name = class_basename($class);

            if (! isset($this->classes[$name])) {
                $this->classes[$name] = $class;
            }
        }
    }

    /**
     * Find the closest class by name.
     *
     * @param  string  $class
     * @return void
     */
    public function aliasClass($class)
    {
        if (Str::contains($class, '\\')) {
            return;
        }

        $fullName = $this->classes[$class] ?? false;

        if ($fullName) {
            $this->shell->writeStdout("[!] Aliasing '{$class}' to '{$fullName}' for this Tinker session.\n");

            class_alias($fullName, $class);
        }
    }

    /**
     * Unregister the alias loader instance.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'aliasClass']);
    }

    /**
     * Handle the destruction of the instance.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->unregister();
    }

    /**
     * Whether a class may be aliased.
     *
     * @param  string  $class
     * @param  string  $path
     */
    public function isAliasable($class, $path)
    {
        if (! Str::contains($class, '\\')) {
            return false;
        }

        if ($this->includedAliases->contains(function ($alias) use ($class) {
            return Str::startsWith($class, $alias);
        })) {
            return true;
        }

        if (Str::startsWith($path, $this->vendorPath)) {
            return false;
        }

        if ($this->excludedAliases->contains(function ($alias) use ($class) {
            return Str::startsWith($class, $alias);
        })) {
            return false;
        }

        return true;
    }
}
