<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-file-iterator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\FileIterator;

use const DIRECTORY_SEPARATOR;
use const GLOB_ONLYDIR;
use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function glob;
use function is_dir;
use function is_string;
use function realpath;
use function sort;
use function str_ends_with;
use function stripos;
use function substr;
use AppendIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-file-iterator
 */
final class Factory
{
    /**
     * @param list<non-empty-string>|non-empty-string $paths
     * @param list<non-empty-string>|string           $suffixes
     * @param list<non-empty-string>|string           $prefixes
     * @param list<non-empty-string>                  $exclude
     *
     * @phpstan-ignore missingType.generics
     */
    public function getFileIterator(array|string $paths, array|string $suffixes = '', array|string $prefixes = '', array $exclude = []): AppendIterator
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        $paths   = $this->resolveWildcards($paths);
        $exclude = $this->resolveWildcards($exclude);

        if (is_string($prefixes)) {
            if ($prefixes !== '') {
                $prefixes = [$prefixes];
            } else {
                $prefixes = [];
            }
        }

        if (is_string($suffixes)) {
            if ($suffixes !== '') {
                $suffixes = [$suffixes];
            } else {
                $suffixes = [];
            }
        }

        $iterator = new AppendIterator;

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $iterator->append(
                    new Iterator(
                        $path,
                        new RecursiveIteratorIterator(
                            new ExcludeIterator(
                                new RecursiveDirectoryIterator($path, FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::SKIP_DOTS),
                                $exclude,
                            ),
                        ),
                        $suffixes,
                        $prefixes,
                    ),
                );
            }
        }

        return $iterator;
    }

    /**
     * @param list<non-empty-string> $paths
     *
     * @return list<non-empty-string>
     */
    private function resolveWildcards(array $paths): array
    {
        $_paths = [[]];

        foreach ($paths as $path) {
            $pathEndsWithDirectorySeparator = str_ends_with($path, '/') || str_ends_with($path, DIRECTORY_SEPARATOR);

            if ($locals = $this->globstar($path)) {
                $_paths[] = array_map(
                    static function (string $local) use ($pathEndsWithDirectorySeparator): string|false
                    {
                        $realPath = realpath($local);

                        if ($realPath !== false && $pathEndsWithDirectorySeparator && is_dir($realPath)) {
                            return $realPath . DIRECTORY_SEPARATOR;
                        }

                        return $realPath;
                    },
                    $locals,
                );
            } else {
                // @codeCoverageIgnoreStart
                $realPath = realpath($path);

                if ($realPath !== false && $pathEndsWithDirectorySeparator && is_dir($realPath)) {
                    $_paths[] = [$realPath . DIRECTORY_SEPARATOR];
                } else {
                    $_paths[] = [$realPath];
                }
                // @codeCoverageIgnoreEnd
            }
        }

        return array_values(array_filter(array_merge(...$_paths)));
    }

    /**
     * @see https://gist.github.com/funkjedi/3feee27d873ae2297b8e2370a7082aad
     *
     * @return list<string>
     */
    private function globstar(string $pattern): array
    {
        if (stripos($pattern, '**') === false) {
            $files = glob($pattern, GLOB_ONLYDIR);
        } else {
            $position    = stripos($pattern, '**');
            $rootPattern = substr($pattern, 0, $position - 1);
            $restPattern = substr($pattern, $position + 2);

            $patterns = [$rootPattern . $restPattern];
            $rootPattern .= '/*';

            while ($directories = glob($rootPattern, GLOB_ONLYDIR)) {
                $rootPattern .= '/*';

                foreach ($directories as $directory) {
                    $patterns[] = $directory . $restPattern;
                }
            }

            $files = [];

            foreach ($patterns as $_pattern) {
                $files = array_merge($files, $this->globstar($_pattern));
            }
        }

        if ($files !== false) {
            $files = array_unique($files);

            sort($files);

            return $files;
        }

        // @codeCoverageIgnoreStart
        return [];
        // @codeCoverageIgnoreEnd
    }
}
