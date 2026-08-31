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

use function array_unique;
use function assert;
use function sort;
use SplFileInfo;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class Facade
{
    /**
     * @param list<non-empty-string>|non-empty-string $paths
     * @param list<non-empty-string>|string           $suffixes
     * @param list<non-empty-string>|string           $prefixes
     * @param list<non-empty-string>                  $exclude
     *
     * @return list<non-empty-string>
     */
    public function getFilesAsArray(array|string $paths, array|string $suffixes = '', array|string $prefixes = '', array $exclude = []): array
    {
        $iterator = (new Factory)->getFileIterator($paths, $suffixes, $prefixes, $exclude);

        $files = [];

        foreach ($iterator as $file) {
            assert($file instanceof SplFileInfo);

            $file = $file->getRealPath();

            if ($file) {
                $files[] = $file;
            }
        }

        $files = array_unique($files);

        sort($files);

        return $files;
    }
}
