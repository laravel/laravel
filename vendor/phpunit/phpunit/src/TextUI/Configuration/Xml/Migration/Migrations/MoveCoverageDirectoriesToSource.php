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
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MoveCoverageDirectoriesToSource implements Migration
{
    /**
     * @throws MigrationException
     */
    public function migrate(DOMDocument $document): void
    {
        $source = $document->getElementsByTagName('source')->item(0);

        if ($source !== null) {
            return;
        }

        $coverage = $document->getElementsByTagName('coverage')->item(0);

        if ($coverage === null) {
            return;
        }

        $root = $document->documentElement;

        assert($root instanceof DOMElement);

        $source = $document->createElement('source');
        $root->appendChild($source);

        $xpath = new DOMXPath($document);

        foreach (['include', 'exclude'] as $element) {
            $nodes = $xpath->query('//coverage/' . $element);

            assert($nodes !== false);

            foreach (SnapshotNodeList::fromNodeList($nodes) as $node) {
                $source->appendChild($node);
            }
        }

        if ($coverage->childElementCount !== 0) {
            return;
        }

        assert($coverage->parentNode !== null);

        $coverage->parentNode->removeChild($coverage);
    }
}
