<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type CodeUnitFunctionType from \SebastianBergmann\CodeCoverage\StaticAnalysis\CodeUnitFindingVisitor
 * @phpstan-import-type CodeUnitMethodType from \SebastianBergmann\CodeCoverage\StaticAnalysis\CodeUnitFindingVisitor
 * @phpstan-import-type CodeUnitClassType from \SebastianBergmann\CodeCoverage\StaticAnalysis\CodeUnitFindingVisitor
 * @phpstan-import-type CodeUnitTraitType from \SebastianBergmann\CodeCoverage\StaticAnalysis\CodeUnitFindingVisitor
 *
 * @phpstan-type LinesOfCodeType = array{
 *     linesOfCode: int,
 *     commentLinesOfCode: int,
 *     nonCommentLinesOfCode: int
 * }
 * @phpstan-type LinesType = array<int, int>
 */
interface FileAnalyser
{
    /**
     * @return array<string, CodeUnitClassType>
     */
    public function classesIn(string $filename): array;

    /**
     * @return array<string, CodeUnitTraitType>
     */
    public function traitsIn(string $filename): array;

    /**
     * @return array<string, CodeUnitFunctionType>
     */
    public function functionsIn(string $filename): array;

    /**
     * @return LinesOfCodeType
     */
    public function linesOfCodeFor(string $filename): array;

    /**
     * @return LinesType
     */
    public function executableLinesIn(string $filename): array;

    /**
     * @return LinesType
     */
    public function ignoredLinesFor(string $filename): array;
}
