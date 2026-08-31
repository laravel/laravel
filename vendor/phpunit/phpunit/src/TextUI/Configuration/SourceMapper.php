<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

use function file_get_contents;
use function file_put_contents;
use function is_array;
use function realpath;
use function serialize;
use function unserialize;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use SplObjectStorage;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class SourceMapper
{
    /**
     * @var ?SplObjectStorage<Source, array<non-empty-string, true>>
     */
    private static ?SplObjectStorage $files = null;

    public static function saveTo(string $path, Source $source): bool
    {
        $map = (new self)->map($source);

        return file_put_contents($path, serialize($map)) !== false;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function loadFrom(string $path, Source $source): void
    {
        $content = file_get_contents($path);

        if ($content === false) {
            return;
        }

        $map = unserialize($content, ['allowed_classes' => false]);

        if (!is_array($map)) {
            return;
        }

        if (self::$files === null) {
            self::$files = new SplObjectStorage;
        }

        /** @phpstan-ignore offsetAssign.valueType */
        self::$files[$source] = $map;
    }

    /**
     * @return array<non-empty-string, true>
     */
    public function map(Source $source): array
    {
        if (self::$files === null) {
            self::$files = new SplObjectStorage;
        }

        if (isset(self::$files[$source])) {
            return self::$files[$source];
        }

        $files = [];

        $directories = $this->aggregateDirectories($source->includeDirectories());

        foreach ($directories as $path => [$prefixes, $suffixes]) {
            foreach ((new FileIteratorFacade)->getFilesAsArray($path, $suffixes, $prefixes) as $file) {
                $file = realpath($file);

                if (!$file) {
                    continue;
                }

                $files[$file] = true;
            }
        }

        foreach ($source->includeFiles() as $file) {
            $file = realpath($file->path());

            if (!$file) {
                continue;
            }

            $files[$file] = true;
        }

        $directories = $this->aggregateDirectories($source->excludeDirectories());

        foreach ($directories as $path => [$prefixes, $suffixes]) {
            foreach ((new FileIteratorFacade)->getFilesAsArray($path, $suffixes, $prefixes) as $file) {
                $file = realpath($file);

                if (!$file) {
                    continue;
                }

                if (!isset($files[$file])) {
                    continue;
                }

                unset($files[$file]);
            }
        }

        foreach ($source->excludeFiles() as $file) {
            $file = realpath($file->path());

            if (!$file) {
                continue;
            }

            if (!isset($files[$file])) {
                continue;
            }

            unset($files[$file]);
        }

        self::$files[$source] = $files;

        return $files;
    }

    /**
     * @return array<string,array{list<string>,list<string>}>
     */
    private function aggregateDirectories(FilterDirectoryCollection $directories): array
    {
        $aggregated = [];

        foreach ($directories as $directory) {
            if (!isset($aggregated[$directory->path()])) {
                $aggregated[$directory->path()] = [
                    0 => [],
                    1 => [],
                ];
            }

            $prefix = $directory->prefix();

            if ($prefix !== '') {
                $aggregated[$directory->path()][0][] = $prefix;
            }

            $suffix = $directory->suffix();

            if ($suffix !== '') {
                $aggregated[$directory->path()][1][] = $suffix;
            }
        }

        return $aggregated;
    }
}
