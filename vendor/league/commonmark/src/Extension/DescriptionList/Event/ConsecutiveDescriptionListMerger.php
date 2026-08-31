<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\DescriptionList\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\DescriptionList\Node\DescriptionList;
use League\CommonMark\Node\NodeIterator;

final class ConsecutiveDescriptionListMerger
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        foreach ($event->getDocument()->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof DescriptionList) {
                continue;
            }

            if (! ($prev = $node->previous()) instanceof DescriptionList) {
                continue;
            }

            // There's another description list behind this one; merge the current one into that
            foreach ($node->children() as $child) {
                $prev->appendChild($child);
            }

            $node->detach();
        }
    }
}
