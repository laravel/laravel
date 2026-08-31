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

namespace League\CommonMark\Extension\CommonMark\Parser\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

final class ListItemParser extends AbstractBlockContinueParser
{
    /** @psalm-readonly */
    private ListItem $block;

    public function __construct(ListData $listData)
    {
        $this->block = new ListItem($listData);
    }

    public function getBlock(): ListItem
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return ! $childBlock instanceof ListItem;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->isBlank()) {
            if ($this->block->firstChild() === null) {
                // Blank line after empty list item
                return BlockContinue::none();
            }

            $cursor->advanceToNextNonSpaceOrTab();

            return BlockContinue::at($cursor);
        }

        $contentIndent = $this->block->getListData()->markerOffset + $this->getBlock()->getListData()->padding;
        if ($cursor->getIndent() >= $contentIndent) {
            $cursor->advanceBy($contentIndent, true);

            return BlockContinue::at($cursor);
        }

        // Note: We'll hit this case for lazy continuation lines, they will get added later.
        return BlockContinue::none();
    }

    public function closeBlock(): void
    {
        if (($lastChild = $this->block->lastChild()) instanceof AbstractBlock) {
            $this->block->setEndLine($lastChild->getEndLine());
        } else {
            // Empty list item
            $this->block->setEndLine($this->block->getStartLine());
        }
    }
}
