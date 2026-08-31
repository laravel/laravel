<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Xml;

use DOMDocument;
use DOMElement;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
class File
{
    private readonly DOMDocument $dom;
    private readonly DOMElement $contextNode;

    public function __construct(DOMElement $context)
    {
        $this->dom         = $context->ownerDocument;
        $this->contextNode = $context;
    }

    public function totals(): Totals
    {
        $totalsContainer = $this->contextNode->firstChild;

        if (!$totalsContainer) {
            $totalsContainer = $this->contextNode->appendChild(
                $this->dom->createElementNS(
                    'https://schema.phpunit.de/coverage/1.0',
                    'totals',
                ),
            );
        }

        return new Totals($totalsContainer);
    }

    public function lineCoverage(string $line): Coverage
    {
        $coverage = $this->contextNode->getElementsByTagNameNS(
            'https://schema.phpunit.de/coverage/1.0',
            'coverage',
        )->item(0);

        if (!$coverage) {
            $coverage = $this->contextNode->appendChild(
                $this->dom->createElementNS(
                    'https://schema.phpunit.de/coverage/1.0',
                    'coverage',
                ),
            );
        }

        $lineNode = $coverage->appendChild(
            $this->dom->createElementNS(
                'https://schema.phpunit.de/coverage/1.0',
                'line',
            ),
        );

        return new Coverage($lineNode, $line);
    }

    protected function contextNode(): DOMElement
    {
        return $this->contextNode;
    }

    protected function dom(): DOMDocument
    {
        return $this->dom;
    }
}
