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

use DOMDocument;
use DOMElement;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MoveWhitelistIncludesToCoverage implements Migration
{
    /**
     * @throws MigrationException
     */
    public function migrate(DOMDocument $document): void
    {
        $whitelist = $document->getElementsByTagName('whitelist')->item(0);

        if ($whitelist === null) {
            return;
        }

        $coverage = $document->getElementsByTagName('coverage')->item(0);

        if (!$coverage instanceof DOMElement) {
            throw new MigrationException('Unexpected state - No coverage element');
        }

        $include = $document->createElement('include');
        $coverage->appendChild($include);

        foreach (SnapshotNodeList::fromNodeList($whitelist->childNodes) as $child) {
            if (!$child instanceof DOMElement) {
                continue;
            }

            if (!($child->nodeName === 'directory' || $child->nodeName === 'file')) {
                continue;
            }

            $include->appendChild($child);
        }
    }
}
