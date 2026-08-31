<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report;

use const DIRECTORY_SEPARATOR;
use function basename;
use function count;
use function preg_match;
use function range;
use function str_replace;
use function time;
use DOMImplementation;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException;
use SebastianBergmann\CodeCoverage\Node\File;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Util\Xml;

final class Cobertura
{
    /**
     * @param null|non-empty-string $target
     *
     * @throws WriteOperationFailedException
     */
    public function process(CodeCoverage $coverage, ?string $target = null): string
    {
        $time = (string) time();

        $report = $coverage->getReport();

        $implementation = new DOMImplementation;

        $documentType = $implementation->createDocumentType(
            'coverage',
            '',
            'http://cobertura.sourceforge.net/xml/coverage-04.dtd',
        );

        $document             = $implementation->createDocument('', '', $documentType);
        $document->xmlVersion = '1.0';
        $document->encoding   = 'UTF-8';

        $coverageElement = $document->createElement('coverage');

        $linesValid   = $report->numberOfExecutableLines();
        $linesCovered = $report->numberOfExecutedLines();
        $lineRate     = $linesValid === 0 ? 0 : ($linesCovered / $linesValid);
        $coverageElement->setAttribute('line-rate', (string) $lineRate);

        $branchesValid   = $report->numberOfExecutableBranches();
        $branchesCovered = $report->numberOfExecutedBranches();
        $branchRate      = $branchesValid === 0 ? 0 : ($branchesCovered / $branchesValid);
        $coverageElement->setAttribute('branch-rate', (string) $branchRate);

        $coverageElement->setAttribute('lines-covered', (string) $report->numberOfExecutedLines());
        $coverageElement->setAttribute('lines-valid', (string) $report->numberOfExecutableLines());
        $coverageElement->setAttribute('branches-covered', (string) $report->numberOfExecutedBranches());
        $coverageElement->setAttribute('branches-valid', (string) $report->numberOfExecutableBranches());
        $coverageElement->setAttribute('complexity', '');
        $coverageElement->setAttribute('version', '0.4');
        $coverageElement->setAttribute('timestamp', $time);

        $document->appendChild($coverageElement);

        $sourcesElement = $document->createElement('sources');
        $coverageElement->appendChild($sourcesElement);

        $sourceElement = $document->createElement('source', $report->pathAsString());
        $sourcesElement->appendChild($sourceElement);

        $packagesElement = $document->createElement('packages');
        $coverageElement->appendChild($packagesElement);

        $complexity = 0;

        foreach ($report as $item) {
            if (!$item instanceof File) {
                continue;
            }

            $packageElement    = $document->createElement('package');
            $packageComplexity = 0;

            $packageElement->setAttribute('name', str_replace($report->pathAsString() . DIRECTORY_SEPARATOR, '', $item->pathAsString()));

            $linesValid   = $item->numberOfExecutableLines();
            $linesCovered = $item->numberOfExecutedLines();
            $lineRate     = $linesValid === 0 ? 0 : ($linesCovered / $linesValid);

            $packageElement->setAttribute('line-rate', (string) $lineRate);

            $branchesValid   = $item->numberOfExecutableBranches();
            $branchesCovered = $item->numberOfExecutedBranches();
            $branchRate      = $branchesValid === 0 ? 0 : ($branchesCovered / $branchesValid);

            $packageElement->setAttribute('branch-rate', (string) $branchRate);

            $packageElement->setAttribute('complexity', '');
            $packagesElement->appendChild($packageElement);

            $classesElement = $document->createElement('classes');

            $packageElement->appendChild($classesElement);

            $classes      = $item->classesAndTraits();
            $coverageData = $item->lineCoverageData();

            foreach ($classes as $className => $class) {
                $complexity        += $class['ccn'];
                $packageComplexity += $class['ccn'];

                $linesValid   = $class['executableLines'];
                $linesCovered = $class['executedLines'];
                $lineRate     = $linesValid === 0 ? 0 : ($linesCovered / $linesValid);

                $branchesValid   = $class['executableBranches'];
                $branchesCovered = $class['executedBranches'];
                $branchRate      = $branchesValid === 0 ? 0 : ($branchesCovered / $branchesValid);

                $classElement = $document->createElement('class');

                $classElement->setAttribute('name', $className);
                $classElement->setAttribute('filename', str_replace($report->pathAsString() . DIRECTORY_SEPARATOR, '', $item->pathAsString()));
                $classElement->setAttribute('line-rate', (string) $lineRate);
                $classElement->setAttribute('branch-rate', (string) $branchRate);
                $classElement->setAttribute('complexity', (string) $class['ccn']);

                $classesElement->appendChild($classElement);

                $methodsElement = $document->createElement('methods');

                $classElement->appendChild($methodsElement);

                $classLinesElement = $document->createElement('lines');

                $classElement->appendChild($classLinesElement);

                foreach ($class['methods'] as $methodName => $method) {
                    if ($method['executableLines'] === 0) {
                        continue;
                    }

                    preg_match("/\((.*?)\)/", $method['signature'], $signature);

                    $linesValid   = $method['executableLines'];
                    $linesCovered = $method['executedLines'];
                    $lineRate     = $linesValid === 0 ? 0 : ($linesCovered / $linesValid);

                    $branchesValid   = $method['executableBranches'];
                    $branchesCovered = $method['executedBranches'];
                    $branchRate      = $branchesValid === 0 ? 0 : ($branchesCovered / $branchesValid);

                    $methodElement = $document->createElement('method');

                    $methodElement->setAttribute('name', $methodName);
                    $methodElement->setAttribute('signature', $signature[1]);
                    $methodElement->setAttribute('line-rate', (string) $lineRate);
                    $methodElement->setAttribute('branch-rate', (string) $branchRate);
                    $methodElement->setAttribute('complexity', (string) $method['ccn']);

                    $methodLinesElement = $document->createElement('lines');

                    $methodElement->appendChild($methodLinesElement);

                    foreach (range($method['startLine'], $method['endLine']) as $line) {
                        if (!isset($coverageData[$line])) {
                            continue;
                        }
                        $methodLineElement = $document->createElement('line');

                        $methodLineElement->setAttribute('number', (string) $line);
                        $methodLineElement->setAttribute('hits', (string) count($coverageData[$line]));

                        $methodLinesElement->appendChild($methodLineElement);

                        $classLineElement = $methodLineElement->cloneNode();

                        $classLinesElement->appendChild($classLineElement);
                    }

                    $methodsElement->appendChild($methodElement);
                }
            }

            if ($item->numberOfFunctions() === 0) {
                $packageElement->setAttribute('complexity', (string) $packageComplexity);

                continue;
            }

            $functionsComplexity      = 0;
            $functionsLinesValid      = 0;
            $functionsLinesCovered    = 0;
            $functionsBranchesValid   = 0;
            $functionsBranchesCovered = 0;

            $classElement = $document->createElement('class');
            $classElement->setAttribute('name', basename($item->pathAsString()));
            $classElement->setAttribute('filename', str_replace($report->pathAsString() . DIRECTORY_SEPARATOR, '', $item->pathAsString()));

            $methodsElement = $document->createElement('methods');

            $classElement->appendChild($methodsElement);

            $classLinesElement = $document->createElement('lines');

            $classElement->appendChild($classLinesElement);

            $functions = $item->functions();

            foreach ($functions as $functionName => $function) {
                if ($function['executableLines'] === 0) {
                    continue;
                }

                $complexity          += $function['ccn'];
                $packageComplexity   += $function['ccn'];
                $functionsComplexity += $function['ccn'];

                $linesValid   = $function['executableLines'];
                $linesCovered = $function['executedLines'];
                $lineRate     = $linesValid === 0 ? 0 : ($linesCovered / $linesValid);

                $functionsLinesValid   += $linesValid;
                $functionsLinesCovered += $linesCovered;

                $branchesValid   = $function['executableBranches'];
                $branchesCovered = $function['executedBranches'];
                $branchRate      = $branchesValid === 0 ? 0 : ($branchesCovered / $branchesValid);

                $functionsBranchesValid   += $branchesValid;
                $functionsBranchesCovered += $branchesValid;

                $methodElement = $document->createElement('method');

                $methodElement->setAttribute('name', $functionName);
                $methodElement->setAttribute('signature', $function['signature']);
                $methodElement->setAttribute('line-rate', (string) $lineRate);
                $methodElement->setAttribute('branch-rate', (string) $branchRate);
                $methodElement->setAttribute('complexity', (string) $function['ccn']);

                $methodLinesElement = $document->createElement('lines');

                $methodElement->appendChild($methodLinesElement);

                foreach (range($function['startLine'], $function['endLine']) as $line) {
                    if (!isset($coverageData[$line])) {
                        continue;
                    }
                    $methodLineElement = $document->createElement('line');

                    $methodLineElement->setAttribute('number', (string) $line);
                    $methodLineElement->setAttribute('hits', (string) count($coverageData[$line]));

                    $methodLinesElement->appendChild($methodLineElement);

                    $classLineElement = $methodLineElement->cloneNode();

                    $classLinesElement->appendChild($classLineElement);
                }

                $methodsElement->appendChild($methodElement);
            }

            $packageElement->setAttribute('complexity', (string) $packageComplexity);

            if ($functionsLinesValid === 0) {
                continue;
            }

            $lineRate   = $functionsLinesCovered / $functionsLinesValid;
            $branchRate = $functionsBranchesValid === 0 ? 0 : ($functionsBranchesCovered / $functionsBranchesValid);

            $classElement->setAttribute('line-rate', (string) $lineRate);
            $classElement->setAttribute('branch-rate', (string) $branchRate);
            $classElement->setAttribute('complexity', (string) $functionsComplexity);

            $classesElement->appendChild($classElement);
        }

        $coverageElement->setAttribute('complexity', (string) $complexity);

        $buffer = Xml::asString($document);

        if ($target !== null) {
            Filesystem::write($target, $buffer);
        }

        return $buffer;
    }
}
