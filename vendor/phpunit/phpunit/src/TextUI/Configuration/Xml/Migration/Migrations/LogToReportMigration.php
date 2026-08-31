<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use function sprintf;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class LogToReportMigration implements Migration
{
    /**
     * @throws MigrationException
     */
    public function migrate(DOMDocument $document): void
    {
        $coverage = $document->getElementsByTagName('coverage')->item(0);

        if (!$coverage instanceof DOMElement) {
            throw new MigrationException('Unexpected state - No coverage element');
        }

        $logNode = $this->findLogNode($document);

        if ($logNode === null) {
            return;
        }

        $reportChild = $this->toReportFormat($logNode);

        $report = $coverage->getElementsByTagName('report')->item(0);

        if ($report === null) {
            $report = $coverage->appendChild($document->createElement('report'));
        }

        $report->appendChild($reportChild);
        $logNode->parentNode->removeChild($logNode);
    }

    /**
     * @param list<non-empty-string> $attributes
     */
    protected function migrateAttributes(DOMElement $src, DOMElement $dest, array $attributes): void
    {
        foreach ($attributes as $attr) {
            if (!$src->hasAttribute($attr)) {
                continue;
            }

            $dest->setAttribute($attr, $src->getAttribute($attr));
            $src->removeAttribute($attr);
        }
    }

    abstract protected function forType(): string;

    abstract protected function toReportFormat(DOMElement $logNode): DOMElement;

    private function findLogNode(DOMDocument $document): ?DOMElement
    {
        $xpath = new DOMXPath($document);

        $logNode = $xpath->query(
            sprintf(
                '//logging/log[@type="%s"]',
                $this->forType(),
            ),
        );

        assert($logNode !== false);

        $logNode = $logNode->item(0);

        if (!$logNode instanceof DOMElement) {
            return null;
        }

        return $logNode;
    }
}
