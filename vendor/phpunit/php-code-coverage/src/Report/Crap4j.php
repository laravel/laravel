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

use function date;
use function htmlspecialchars;
use function is_string;
use function round;
use DOMDocument;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException;
use SebastianBergmann\CodeCoverage\Node\File;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Util\Xml;

final class Crap4j
{
    private readonly int $threshold;

    public function __construct(int $threshold = 30)
    {
        $this->threshold = $threshold;
    }

    /**
     * @param null|non-empty-string $target
     * @param null|non-empty-string $name
     *
     * @throws WriteOperationFailedException
     */
    public function process(CodeCoverage $coverage, ?string $target = null, ?string $name = null): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');

        $root = $document->createElement('crap_result');
        $document->appendChild($root);

        $project = $document->createElement('project', is_string($name) ? $name : '');
        $root->appendChild($project);
        $root->appendChild($document->createElement('timestamp', date('Y-m-d H:i:s')));

        $stats       = $document->createElement('stats');
        $methodsNode = $document->createElement('methods');

        $report = $coverage->getReport();
        unset($coverage);

        $fullMethodCount     = 0;
        $fullCrapMethodCount = 0;
        $fullCrapLoad        = 0;
        $fullCrap            = 0;

        foreach ($report as $item) {
            $namespace = 'global';

            if (!$item instanceof File) {
                continue;
            }

            $file = $document->createElement('file');
            $file->setAttribute('name', $item->pathAsString());

            $classes = $item->classesAndTraits();

            foreach ($classes as $className => $class) {
                foreach ($class['methods'] as $methodName => $method) {
                    $crapLoad = $this->crapLoad((float) $method['crap'], $method['ccn'], $method['coverage']);

                    $fullCrap     += $method['crap'];
                    $fullCrapLoad += $crapLoad;
                    $fullMethodCount++;

                    if ($method['crap'] >= $this->threshold) {
                        $fullCrapMethodCount++;
                    }

                    $methodNode = $document->createElement('method');

                    if (!empty($class['namespace'])) {
                        $namespace = $class['namespace'];
                    }

                    $methodNode->appendChild($document->createElement('package', $namespace));
                    $methodNode->appendChild($document->createElement('className', $className));
                    $methodNode->appendChild($document->createElement('methodName', $methodName));
                    $methodNode->appendChild($document->createElement('methodSignature', htmlspecialchars($method['signature'])));
                    $methodNode->appendChild($document->createElement('fullMethod', htmlspecialchars($method['signature'])));
                    $methodNode->appendChild($document->createElement('crap', (string) $this->roundValue((float) $method['crap'])));
                    $methodNode->appendChild($document->createElement('complexity', (string) $method['ccn']));
                    $methodNode->appendChild($document->createElement('coverage', (string) $this->roundValue($method['coverage'])));
                    $methodNode->appendChild($document->createElement('crapLoad', (string) round($crapLoad)));

                    $methodsNode->appendChild($methodNode);
                }
            }
        }

        $stats->appendChild($document->createElement('name', 'Method Crap Stats'));
        $stats->appendChild($document->createElement('methodCount', (string) $fullMethodCount));
        $stats->appendChild($document->createElement('crapMethodCount', (string) $fullCrapMethodCount));
        $stats->appendChild($document->createElement('crapLoad', (string) round($fullCrapLoad)));
        $stats->appendChild($document->createElement('totalCrap', (string) $fullCrap));

        $crapMethodPercent = 0;

        if ($fullMethodCount > 0) {
            $crapMethodPercent = $this->roundValue((100 * $fullCrapMethodCount) / $fullMethodCount);
        }

        $stats->appendChild($document->createElement('crapMethodPercent', (string) $crapMethodPercent));

        $root->appendChild($stats);
        $root->appendChild($methodsNode);

        $buffer = Xml::asString($document);

        if ($target !== null) {
            Filesystem::write($target, $buffer);
        }

        return $buffer;
    }

    private function crapLoad(float $crapValue, int $cyclomaticComplexity, float $coveragePercent): float
    {
        $crapLoad = 0;

        if ($crapValue >= $this->threshold) {
            $crapLoad += $cyclomaticComplexity * (1.0 - $coveragePercent / 100);
            $crapLoad += $cyclomaticComplexity / $this->threshold;
        }

        return $crapLoad;
    }

    private function roundValue(float $value): float
    {
        return round($value, 2);
    }
}
