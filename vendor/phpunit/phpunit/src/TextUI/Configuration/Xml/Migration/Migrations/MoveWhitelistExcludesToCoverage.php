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
use function in_array;
use DOMDocument;
use DOMElement;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MoveWhitelistExcludesToCoverage implements Migration
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

        $excludeNodes = SnapshotNodeList::fromNodeList($whitelist->getElementsByTagName('exclude'));

        if ($excludeNodes->count() === 0) {
            return;
        }

        $coverage = $document->getElementsByTagName('coverage')->item(0);

        if (!$coverage instanceof DOMElement) {
            throw new MigrationException('Unexpected state - No coverage element');
        }

        $targetExclude = $coverage->getElementsByTagName('exclude')->item(0);

        if ($targetExclude === null) {
            $targetExclude = $coverage->appendChild(
                $document->createElement('exclude'),
            );
        }

        foreach ($excludeNodes as $excludeNode) {
            assert($excludeNode instanceof DOMElement);

            foreach (SnapshotNodeList::fromNodeList($excludeNode->childNodes) as $child) {
                if (!$child instanceof DOMElement || !in_array($child->nodeName, ['directory', 'file'], true)) {
                    continue;
                }

                $targetExclude->appendChild($child);
            }

            if ($excludeNode->getElementsByTagName('*')->count() !== 0) {
                throw new MigrationException('Dangling child elements in exclude found.');
            }

            $whitelist->removeChild($excludeNode);
        }
    }
}
