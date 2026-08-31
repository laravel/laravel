<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Baseline;

use function array_fill;
use function array_merge;
use function array_slice;
use function assert;
use function count;
use function explode;
use function implode;
use function str_replace;
use function strpos;
use function substr;
use function trim;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @see Copied from https://github.com/phpstan/phpstan-src/blob/1.10.33/src/File/ParentDirectoryRelativePathHelper.php
 */
final readonly class RelativePathCalculator
{
    /**
     * @var non-empty-string
     */
    private string $baselineDirectory;

    /**
     * @param non-empty-string $baselineDirectory
     */
    public function __construct(string $baselineDirectory)
    {
        $this->baselineDirectory = $baselineDirectory;
    }

    /**
     * @param non-empty-string $filename
     *
     * @return non-empty-string
     */
    public function calculate(string $filename): string
    {
        $result = implode('/', $this->parts($filename));

        assert($result !== '');

        return $result;
    }

    /**
     * @param non-empty-string $filename
     *
     * @return list<non-empty-string>
     */
    public function parts(string $filename): array
    {
        $schemePosition = strpos($filename, '://');

        if ($schemePosition !== false) {
            $filename = substr($filename, $schemePosition + 3);

            assert($filename !== '');
        }

        $parentParts        = explode('/', trim(str_replace('\\', '/', $this->baselineDirectory), '/'));
        $parentPartsCount   = count($parentParts);
        $filenameParts      = explode('/', trim(str_replace('\\', '/', $filename), '/'));
        $filenamePartsCount = count($filenameParts);

        $i = 0;

        for (; $i < $filenamePartsCount; $i++) {
            if ($parentPartsCount < $i + 1) {
                break;
            }

            $parentPath   = implode('/', array_slice($parentParts, 0, $i + 1));
            $filenamePath = implode('/', array_slice($filenameParts, 0, $i + 1));

            if ($parentPath !== $filenamePath) {
                break;
            }
        }

        if ($i === 0) {
            return [$filename];
        }

        $dotsCount = $parentPartsCount - $i;

        assert($dotsCount >= 0);

        return array_merge(array_fill(0, $dotsCount, '..'), array_slice($filenameParts, $i));
    }
}
