<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Reference\Reference;

final class NumberFootnotesListener
{
    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document     = $event->getDocument();
        $nextCounter  = 1;
        $usedLabels   = [];
        $usedCounters = [];

        foreach ($document->iterator() as $node) {
            if (! $node instanceof FootnoteRef) {
                continue;
            }

            $existingReference   = $node->getReference();
            $label               = $existingReference->getLabel();
            $counter             = $nextCounter;
            $canIncrementCounter = true;

            if (\array_key_exists($label, $usedLabels)) {
                /*
                 * Reference is used again, we need to point
                 * to the same footnote. But with a different ID
                 */
                $counter             = $usedCounters[$label];
                $label              .= '__' . ++$usedLabels[$label];
                $canIncrementCounter = false;
            }

            // rewrite reference title to use a numeric link
            $newReference = new Reference(
                $label,
                $existingReference->getDestination(),
                (string) $counter
            );

            // Override reference with numeric link
            $node->setReference($newReference);
            $document->getReferenceMap()->add($newReference);

            /*
             * Store created references in document for
             * creating FootnoteBackrefs
             */
            $document->data->append($existingReference->getDestination(), $newReference);

            $usedLabels[$label]   = 1;
            $usedCounters[$label] = $nextCounter;

            if ($canIncrementCounter) {
                $nextCounter++;
            }
        }
    }
}
