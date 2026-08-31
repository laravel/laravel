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

use DOMElement;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class CoverageHtmlToReport extends LogToReportMigration
{
    protected function forType(): string
    {
        return 'coverage-html';
    }

    protected function toReportFormat(DOMElement $logNode): DOMElement
    {
        $html = $logNode->ownerDocument->createElement('html');
        $html->setAttribute('outputDirectory', $logNode->getAttribute('target'));

        $this->migrateAttributes($logNode, $html, ['lowUpperBound', 'highLowerBound']);

        return $html;
    }
}
