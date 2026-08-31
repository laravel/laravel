<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Parser;

use League\CommonMark\Extension\Attributes\Node\Attributes;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

final class AttributesBlockContinueParser extends AbstractBlockContinueParser
{
    private Attributes $block;

    private AbstractBlock $container;

    private bool $hasSubsequentLine = false;

    /**
     * @param array<string, mixed> $attributes The attributes identified by the block start parser
     * @param AbstractBlock        $container  The node we were in when these attributes were discovered
     */
    public function __construct(array $attributes, AbstractBlock $container)
    {
        $this->block = new Attributes($attributes);

        $this->container = $container;
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        $this->hasSubsequentLine = true;

        $cursor->advanceToNextNonSpaceOrTab();

        // Does this next line also have attributes?
        $attributes = AttributesHelper::parseAttributes($cursor);
        $cursor->advanceToNextNonSpaceOrTab();
        if ($cursor->isAtEnd() && $attributes !== []) {
            // It does! Merge them into what we parsed previously
            $this->block->setAttributes(AttributesHelper::mergeAttributes(
                $this->block->getAttributes(),
                $attributes
            ));

            // Tell the core parser we've consumed everything
            return BlockContinue::at($cursor);
        }

        // Okay, so there are no attributes on the next line
        // If this next line is blank we know we can't target the next node, it must be a previous one
        if ($cursor->isBlank()) {
            $this->block->setTarget(Attributes::TARGET_PREVIOUS);
        }

        return BlockContinue::none();
    }

    public function closeBlock(): void
    {
        // Attributes appearing at the very end of the document won't have any last lines to check
        // so we can make that determination here
        if (! $this->hasSubsequentLine) {
            $this->block->setTarget(Attributes::TARGET_PREVIOUS);
        }

        // We know this block must apply to the "previous" block, but that could be a sibling or parent,
        // so we check the containing block to see which one it might be.
        if ($this->block->getTarget() === Attributes::TARGET_PREVIOUS && $this->block->parent() === $this->container) {
            $this->block->setTarget(Attributes::TARGET_PARENT);
        }
    }
}
