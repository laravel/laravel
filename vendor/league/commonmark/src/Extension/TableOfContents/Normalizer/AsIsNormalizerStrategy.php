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

namespace League\CommonMark\Extension\TableOfContents\Normalizer;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;

final class AsIsNormalizerStrategy implements NormalizerStrategyInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ListBlock $parentListBlock;

    /** @psalm-readonly-allow-private-mutation */
    private int $parentLevel = 1;

    /** @psalm-readonly-allow-private-mutation */
    private ?ListItem $lastListItem = null;

    public function __construct(TableOfContents $toc)
    {
        $this->parentListBlock = $toc;
    }

    public function addItem(int $level, ListItem $listItemToAdd): void
    {
        while ($level > $this->parentLevel) {
            // Descend downwards, creating new ListBlocks if needed, until we reach the correct depth
            if ($this->lastListItem === null) {
                $this->lastListItem = new ListItem($this->parentListBlock->getListData());
                $this->parentListBlock->appendChild($this->lastListItem);
            }

            $newListBlock = new ListBlock($this->parentListBlock->getListData());
            $newListBlock->setStartLine($listItemToAdd->getStartLine());
            $newListBlock->setEndLine($listItemToAdd->getEndLine());
            $this->lastListItem->appendChild($newListBlock);
            $this->parentListBlock = $newListBlock;
            $this->lastListItem    = null;

            $this->parentLevel++;
        }

        while ($level < $this->parentLevel) {
            // Search upwards for the previous parent list block
            $search = $this->parentListBlock;
            while ($search = $search->parent()) {
                if ($search instanceof ListBlock) {
                    $this->parentListBlock = $search;
                    break;
                }
            }

            $this->parentLevel--;
        }

        $this->parentListBlock->appendChild($listItemToAdd);

        $this->lastListItem = $listItemToAdd;
    }
}
