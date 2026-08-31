<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function array_keys;
use function is_file;
use function realpath;
use function str_contains;
use function str_starts_with;

final class Filter
{
    /**
     * @var array<string,true>
     */
    private array $files = [];

    /**
     * @var array<string,bool>
     */
    private array $isFileCache = [];

    /**
     * @param list<string> $filenames
     */
    public function includeFiles(array $filenames): void
    {
        foreach ($filenames as $filename) {
            $this->includeFile($filename);
        }
    }

    public function includeFile(string $filename): void
    {
        $filename = realpath($filename);

        if (!$filename) {
            return;
        }

        $this->files[$filename] = true;
    }

    public function isFile(string $filename): bool
    {
        if (isset($this->isFileCache[$filename])) {
            return $this->isFileCache[$filename];
        }

        if ($filename === '-' ||
            str_starts_with($filename, 'vfs://') ||
            str_contains($filename, 'xdebug://debug-eval') ||
            str_contains($filename, 'eval()\'d code') ||
            str_contains($filename, 'runtime-created function') ||
            str_contains($filename, 'runkit created function') ||
            str_contains($filename, 'assert code') ||
            str_contains($filename, 'regexp code') ||
            str_contains($filename, 'Standard input code')) {
            $isFile = false;
        } else {
            $isFile = is_file($filename);
        }

        $this->isFileCache[$filename] = $isFile;

        return $isFile;
    }

    public function isExcluded(string $filename): bool
    {
        return !isset($this->files[$filename]) || !$this->isFile($filename);
    }

    /**
     * @return list<string>
     */
    public function files(): array
    {
        return array_keys($this->files);
    }

    public function isEmpty(): bool
    {
        return empty($this->files);
    }
}
