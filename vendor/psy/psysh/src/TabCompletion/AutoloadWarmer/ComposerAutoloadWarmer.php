<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion\AutoloadWarmer;

use Psy\Shell;

/**
 * Composer autoload warmer.
 *
 * Loads classes from Composer's autoload configuration. By default, loads
 * application classes but excludes vendor packages and test files to balance
 * startup time with completion quality.
 *
 * Example configuration:
 *
 *     // Enable autoload warming with default settings
 *     $config->setWarmAutoload(true);
 *
 *     // Disable autoload warming (default)
 *     $config->setWarmAutoload(false);
 *
 *     // Configure ComposerAutoloadWarmer options
 *     $config->setWarmAutoload([
 *         'includeVendor'     => true,                   // Include all vendor packages
 *         'includeTests'      => false,                  // Exclude test namespaces
 *         'includeNamespaces' => ['App\\', 'Lib\\'],     // Only these namespaces
 *         'excludeNamespaces' => ['App\\Legacy\\'],      // Exclude specific namespaces
 *     ]);
 *
 *     // Include specific vendor packages only
 *     $config->setWarmAutoload([
 *         'includeVendorNamespaces' => ['Symfony\\', 'Doctrine\\'],
 *     ]);
 *
 *     // Include all vendor except specific packages
 *     $config->setWarmAutoload([
 *         'includeVendor' => true,
 *         'excludeVendorNamespaces' => ['Symfony\\Debug\\'],
 *     ]);
 *
 *     // Use custom warmers only
 *     $config->setWarmAutoload([
 *         'warmers' => [$myCustomWarmer],
 *     ]);
 *
 *     // Combine custom warmers with configured ComposerAutoloadWarmer
 *     $config->setWarmAutoload([
 *         'warmers'                 => [$myCustomWarmer],
 *         'includeVendorNamespaces' => ['Symfony\\'],
 *     ]);
 */
class ComposerAutoloadWarmer implements AutoloadWarmerInterface
{
    private bool $includeVendor;
    private bool $includeTests;
    private array $includeNamespaces;
    private array $excludeNamespaces;
    private array $includeVendorNamespaces;
    private array $excludeVendorNamespaces;
    private ?string $vendorDir = null;
    private ?string $pharPrefix = null;

    private const KNOWN_BAD_NAMESPACES = [
        'Psy\\Readline\\Hoa\\',
        // Autoloading Composer classes breaks Composer autoloading :grimacing:
        'Composer\\',
        // DI is an optional Symfony Console dependency; prevent explosion
        'Symfony\\Component\\Console\\DependencyInjection\\',
    ];

    /**
     * PsySH's php-scoper prefix pattern for PHAR builds.
     *
     * Vendor dependencies in the PHAR are prefixed with "_Psy<hash>\" to avoid
     * conflicts with user dependencies. The hash is randomly generated per build.
     */
    private const PHAR_SCOPED_PREFIX_PATTERN = '/^_Psy[a-f0-9]+\\\\/';

    /**
     * @param array       $config    Configuration options
     * @param string|null $vendorDir Optional vendor directory path (auto-detected if not provided)
     */
    public function __construct(array $config = [], ?string $vendorDir = null)
    {
        $hasVendorFilters = isset($config['includeVendorNamespaces']) || isset($config['excludeVendorNamespaces']);

        // Validate conflicting config
        if ($hasVendorFilters && isset($config['includeVendor']) && $config['includeVendor'] === false) {
            throw new \InvalidArgumentException('Cannot use includeVendorNamespaces or excludeVendorNamespaces when includeVendor is false');
        }

        // Vendor namespace filters imply includeVendor: true
        $this->includeVendor = $config['includeVendor'] ?? $hasVendorFilters;
        $this->includeTests = $config['includeTests'] ?? false;
        $this->includeNamespaces = $this->normalizeNamespaces($config['includeNamespaces'] ?? []);
        $this->excludeNamespaces = $this->normalizeNamespaces($config['excludeNamespaces'] ?? []);
        $this->includeVendorNamespaces = $this->normalizeNamespaces($config['includeVendorNamespaces'] ?? []);
        $this->excludeVendorNamespaces = $this->normalizeNamespaces($config['excludeVendorNamespaces'] ?? []);

        // Cache PHAR prefix to avoid repeated Phar::running() calls
        if (Shell::isPhar()) {
            $runningPhar = \Phar::running(false);
            $this->pharPrefix = 'phar://'.$runningPhar.'/';
        }

        $vendorDir = $vendorDir ?? $this->findVendorDir();
        if ($vendorDir !== null) {
            $resolvedVendorDir = \realpath($vendorDir);
            if ($resolvedVendorDir !== false) {
                $this->vendorDir = \str_replace('\\', '/', $resolvedVendorDir);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function warm(): int
    {
        // Get count of already-loaded classes before we start
        $beforeCount = \count(\get_declared_classes()) +
                       \count(\get_declared_interfaces()) +
                       \count(\get_declared_traits());

        $classes = $this->getClassNames();
        foreach ($classes as $class) {
            try {
                // Skip if already loaded (check without autoloading first)
                if (
                    \class_exists($class, false) ||
                    \interface_exists($class, false) ||
                    \trait_exists($class, false)
                ) {
                    continue;
                }

                // Try to load the class/interface/trait
                // The autoload parameter (true) will trigger autoloading
                \class_exists($class, true) ||
                \interface_exists($class, true) ||
                \trait_exists($class, true);
            } catch (\Throwable $e) {
                // Ignore classes that fail to load
                // This is expected for classes with missing dependencies, etc.
            }
        }

        // Return the number of newly loaded classes
        $afterCount = \count(\get_declared_classes()) +
                      \count(\get_declared_interfaces()) +
                      \count(\get_declared_traits());

        return $afterCount - $beforeCount;
    }

    /**
     * Discover classes from available sources.
     *
     * Uses two complementary strategies:
     * 1. ClassLoader's classmap (from optimized autoload or registered classes)
     * 2. ClassMapGenerator to scan PSR-4 directories (if available, safe, no side effects)
     *
     * Both strategies are attempted and results are combined, since ClassLoader
     * may have an optimized classmap while ClassMapGenerator can discover new
     * classes added during development.
     *
     * @internal This method is exported for testability and is not part of the public API
     *
     * @return string[] Fully-qualified class names
     */
    public function getClassNames(): array
    {
        $autoloadMap = $this->getAutoloadClassMap();
        $generatedMap = $this->generateClassMap();

        return $this->classesFromClassMap(\array_merge($autoloadMap, $generatedMap));
    }

    /**
     * Get class map from the registered Composer ClassLoader, if available.
     *
     * This map is populated by running `composer dump-autoload`
     *
     * @return array Map of class name => file path
     */
    private function getAutoloadClassMap(): array
    {
        // If we found a project vendor dir, try to register their autoloader (if it hasn't been already)
        // Skip if vendor dir is inside a PHAR (don't re-require the PHAR's autoloader)
        if ($this->vendorDir !== null && \substr($this->vendorDir, 0, 7) !== 'phar://') {
            $projectAutoload = $this->vendorDir.'/autoload.php';
            if (\file_exists($projectAutoload)) {
                try {
                    require_once $projectAutoload;
                } catch (\Throwable $e) {
                    // Ignore autoloader errors
                }
            }
        }

        foreach (\spl_autoload_functions() as $autoloader) {
            if (!\is_array($autoloader)) {
                continue;
            }

            $loader = $autoloader[0] ?? null;
            if (!\is_object($loader) || \get_class($loader) !== 'Composer\Autoload\ClassLoader') {
                continue;
            }

            // Check if this loader contains scoped classes (indicates it's from the PHAR)
            if (\method_exists($loader, 'getClassMap')) {
                $classMap = $loader->getClassMap();
                foreach (\array_keys($classMap) as $class) {
                    if (\preg_match(self::PHAR_SCOPED_PREFIX_PATTERN, $class)) {
                        continue 2; // Skip to next autoloader
                    }
                }
            }

            // If we have an explicit vendor dir, check if this loader serves that directory
            if ($this->vendorDir !== null) {
                $hasTargetPaths = false;

                if (\method_exists($loader, 'getPrefixesPsr4')) {
                    $prefixes = $loader->getPrefixesPsr4();
                    foreach ($prefixes as $namespace => $paths) {
                        foreach ($paths as $path) {
                            if (\strpos($path, $this->vendorDir.'/') === 0) {
                                $hasTargetPaths = true;
                                break 2;
                            }
                        }
                    }
                }

                if (!$hasTargetPaths) {
                    continue;
                }
            }

            if (\method_exists($loader, 'getClassMap')) {
                return $loader->getClassMap();
            }

            return [];
        }

        return [];
    }

    /**
     * Scan autoload directories using a Composer ClassMapGenerator, if available.
     *
     * @return array Map of class name => file path
     */
    private function generateClassMap(): array
    {
        if ($this->vendorDir === null || !\class_exists('Composer\\ClassMapGenerator\\ClassMapGenerator', true)) {
            return [];
        }

        // Get PSR-4 mappings from Composer
        $psr4File = $this->vendorDir.'/composer/autoload_psr4.php';
        if (!\file_exists($psr4File)) {
            return [];
        }

        try {
            $psr4Map = require $psr4File;
            if (!\is_array($psr4Map)) {
                return [];
            }

            $generator = new \Composer\ClassMapGenerator\ClassMapGenerator();

            foreach ($psr4Map as $prefix => $paths) {
                foreach ($paths as $path) {
                    if (!\is_dir($path) || $this->shouldSkipPath($path)) {
                        continue;
                    }

                    try {
                        $generator->scanPaths($path);
                    } catch (\Throwable $e) {
                        // Ignore errors (permissions, malformed files, etc.)
                    }
                }
            }

            return $generator->getClassMap()->getMap();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Find the vendor directory by checking registered autoloaders, falling
     * back to filesystem search.
     *
     * @return string|null
     */
    private function findVendorDir(): ?string
    {
        // When running the PsySH PHAR, skip autoloader detection. It will just return the internal
        // vendor directory.
        if ($this->pharPrefix !== null) {
            // Try to find from autoloader
            foreach (\spl_autoload_functions() as $autoloader) {
                if (!\is_array($autoloader)) {
                    continue;
                }

                $loader = $autoloader[0] ?? null;
                if (!\is_object($loader)) {
                    continue;
                }

                try {
                    $reflection = new \ReflectionClass($loader);
                    $loaderPath = $reflection->getFileName();
                    $normalizedPath = \str_replace('\\', '/', $loaderPath);

                    // Skip any other PHAR-based autoloaders
                    if (\strpos($normalizedPath, 'phar://') === 0) {
                        continue;
                    }

                    // ClassLoader is typically at vendor/composer/ClassLoader.php
                    if (\strpos($normalizedPath, '/vendor/composer/') !== false) {
                        return \dirname($loaderPath, 2);
                    }
                } catch (\Throwable $e) {
                    // Ignore and try next autoloader
                }
            }
        }

        // Walk up the directory tree from cwd looking for vendor directory
        $dir = \getcwd();
        $root = \dirname($dir);

        while ($dir !== $root) {
            $vendorDir = $dir.'/vendor';
            if (\is_dir($vendorDir) && \file_exists($vendorDir.'/composer/autoload_psr4.php')) {
                return $vendorDir;
            }

            $root = $dir;
            $dir = \dirname($dir);
        }

        return null;
    }

    /**
     * Normalize namespace prefixes.
     *
     * Removes leading backslash and ensures trailing backslash.
     *
     * @param string[] $namespaces
     *
     * @return string[]
     */
    private function normalizeNamespaces(array $namespaces): array
    {
        return \array_map(fn ($namespace) => \trim($namespace, '\\').'\\', $namespaces);
    }

    /**
     * Check if a path should be skipped based on configuration.
     *
     * @param string $path File or directory path
     *
     * @return bool True if the path should be skipped
     */
    private function shouldSkipPath(string $path): bool
    {
        $normalizedPath = \str_replace('\\', '/', $path);

        // Skip paths from PsySH's own PHAR; these are PsySH's bundled dependencies, not user dependencies
        if ($this->isPathFromPhar($normalizedPath)) {
            return true;
        }

        // Check if path is under vendor directory
        if (!$this->includeVendor && $this->vendorDir !== null) {
            // Resolve relative paths like "vendor/composer/../../src/Cache.php" before comparing
            $resolvedPath = \realpath($path);
            if ($resolvedPath === false) {
                // File doesn't exist, permissions, etc. /shrug
                return true;
            }

            $resolvedPath = \str_replace('\\', '/', $resolvedPath);
            if (\strpos($resolvedPath, $this->vendorDir.'/') === 0) {
                return true;
            }
        }

        // Check test paths
        if (!$this->includeTests) {
            $patterns = ['/test/', '/tests/', '/spec/', '/specs/'];
            foreach ($patterns as $pattern) {
                if (\stripos($normalizedPath, $pattern) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get classes from class map, filtered based on configured namespace rules and excluded paths.
     *
     * @param array $classMap Map of class name => file path
     *
     * @return string[]
     */
    private function classesFromClassMap(array $classMap): array
    {
        // First filter the map by path
        $classMap = \array_filter($classMap, function ($path) {
            return !$this->shouldSkipPath($path);
        });

        $classes = \array_keys($classMap);

        // Then filter by namespace
        return \array_values(
            \array_filter($classes, function ($class) use ($classMap) {
                // Exclude PsySH's scoped PHAR dependencies (e.g., _Psy3684f4474398\Symfony\...)
                if (\preg_match(self::PHAR_SCOPED_PREFIX_PATTERN, $class)) {
                    return false;
                }

                // Hardcode excluding known-bad classes
                foreach (self::KNOWN_BAD_NAMESPACES as $namespace) {
                    if (\stripos($class, $namespace) === 0) {
                        return false;
                    }
                }

                $isVendorClass = $this->isVendorClass($class, $classMap);

                // Apply vendor-specific exclude filters
                if ($isVendorClass && !empty($this->excludeVendorNamespaces)) {
                    foreach ($this->excludeVendorNamespaces as $namespace) {
                        if (\stripos($class, $namespace) === 0) {
                            return false;
                        }
                    }
                }

                // Apply general exclude filters
                foreach ($this->excludeNamespaces as $namespace) {
                    if (\stripos($class, $namespace) === 0) {
                        return false;
                    }
                }

                // Apply vendor-specific include filters
                if ($isVendorClass && !empty($this->includeVendorNamespaces)) {
                    foreach ($this->includeVendorNamespaces as $namespace) {
                        if (\stripos($class, $namespace) === 0) {
                            return true;
                        }
                    }

                    return false; // Vendor class doesn't match vendor filters
                }

                // Apply general include filters
                if (!empty($this->includeNamespaces)) {
                    foreach ($this->includeNamespaces as $namespace) {
                        if (\stripos($class, $namespace) === 0) {
                            return true;
                        }
                    }

                    return false;
                }

                // No include filters provided, and didn't match exclude filters
                return true;
            }),
        );
    }

    /**
     * Check if a class is from the vendor directory.
     *
     * @param string $class    Class name
     * @param array  $classMap Map of class name => file path
     *
     * @return bool
     */
    private function isVendorClass(string $class, array $classMap): bool
    {
        if ($this->vendorDir === null || !isset($classMap[$class])) {
            return false;
        }

        $path = $classMap[$class];

        // Resolve relative paths like "vendor/composer/../../src/Cache.php" before comparing
        // This ensures consistency with shouldSkipPath() logic
        $resolvedPath = \realpath($path);
        if ($resolvedPath === false) {
            // If realpath fails, fall back to raw path comparison
            // This matches the behavior in shouldSkipPath()
            $normalizedPath = \str_replace('\\', '/', $path);

            return \strpos($normalizedPath, $this->vendorDir.'/') === 0;
        }

        $normalizedPath = \str_replace('\\', '/', $resolvedPath);

        return \strpos($normalizedPath, $this->vendorDir.'/') === 0;
    }

    /**
     * Check if a path is from PsySH's own PHAR.
     *
     * @param string $path File path to check
     *
     * @return bool True if the path is from PsySH's PHAR
     */
    private function isPathFromPhar(string $path): bool
    {
        if ($this->pharPrefix === null || \strpos($path, 'phar://') !== 0) {
            return false;
        }

        return \strpos($path, $this->pharPrefix) === 0;
    }
}
