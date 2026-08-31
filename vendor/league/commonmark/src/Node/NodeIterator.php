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

namespace League\CommonMark\Node;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * @implements \IteratorAggregate<int, Node>
 */
final class NodeIterator implements \IteratorAggregate
{
    public const FLAG_BLOCKS_ONLY = 1;

    private Node $node;
    private bool $blocksOnly;

    public function __construct(Node $node, int $flags = 0)
    {
        $this->node       = $node;
        $this->blocksOnly = ($flags & self::FLAG_BLOCKS_ONLY) === self::FLAG_BLOCKS_ONLY;
    }

    /**
     * @return \Generator<int, Node>
     */
    public function getIterator(): \Generator
    {
        $stack = [$this->node];
        $index = 0;

        while ($stack) {
            $node = \array_pop($stack);

            yield $index++ => $node;

            // Push all children onto the stack in reverse order
            $child = $node->lastChild();
            while ($child !== null) {
                if (! $this->blocksOnly || $child instanceof AbstractBlock) {
                    $stack[] = $child;
                }

                $child = $child->previous();
            }
        }
    }
}
