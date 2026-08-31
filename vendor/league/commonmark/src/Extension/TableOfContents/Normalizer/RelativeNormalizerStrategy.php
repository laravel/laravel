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

final class RelativeNormalizerStrategy implements NormalizerStrategyInterface
{
    /** @psalm-readonly */
    private TableOfContents $toc;

    /**
     * @var array<int, ListItem>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $listItemStack = [];

    public function __construct(TableOfContents $toc)
    {
        $this->toc = $toc;
    }

    public function addItem(int $level, ListItem $listItemToAdd): void
    {
        $previousLevel = \array_key_last($this->listItemStack);

        // Pop the stack if we're too deep
        while ($previousLevel !== null && $level < $previousLevel) {
            \array_pop($this->listItemStack);
            $previousLevel = \array_key_last($this->listItemStack);
        }

        $lastListItem = \end($this->listItemStack);

        // Need to go one level deeper? Add that level
        if ($lastListItem !== false && $level > $previousLevel) {
            $targetListBlock = new ListBlock($lastListItem->getListData());
            $targetListBlock->setStartLine($listItemToAdd->getStartLine());
            $targetListBlock->setEndLine($listItemToAdd->getEndLine());
            $lastListItem->appendChild($targetListBlock);
        // Otherwise we're at the right level
        // If there's no stack we're adding this item directly to the TOC element
        } elseif ($lastListItem === false) {
            $targetListBlock = $this->toc;
        // Otherwise add it to the last list item
        } else {
            $targetListBlock = $lastListItem->parent();
        }

        $targetListBlock->appendChild($listItemToAdd);
        $this->listItemStack[$level] = $listItemToAdd;
    }
}
