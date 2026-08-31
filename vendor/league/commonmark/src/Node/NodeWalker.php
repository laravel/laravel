<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Node;

use League\CommonMark\Node\Block\AbstractBlock;

final class NodeWalker
{
    /** @psalm-readonly */
    private Node $root;

    /** @psalm-readonly-allow-private-mutation */
    private ?Node $current = null;

    /** @psalm-readonly-allow-private-mutation */
    private bool $entering;

    public function __construct(Node $root)
    {
        $this->root     = $root;
        $this->current  = $this->root;
        $this->entering = true;
    }

    /**
     * Returns an event which contains node and entering flag
     * (entering is true when we enter a Node from a parent or sibling,
     * and false when we reenter it from child)
     */
    public function next(): ?NodeWalkerEvent
    {
        $current  = $this->current;
        $entering = $this->entering;
        if ($current === null) {
            return null;
        }

        if ($entering && ($current instanceof AbstractBlock || $current->hasChildren())) {
            if ($current->firstChild()) {
                $this->current  = $current->firstChild();
                $this->entering = true;
            } else {
                $this->entering = false;
            }
        } elseif ($current === $this->root) {
            $this->current = null;
        } elseif ($current->next() === null) {
            $this->current  = $current->parent();
            $this->entering = false;
        } else {
            $this->current  = $current->next();
            $this->entering = true;
        }

        return new NodeWalkerEvent($current, $entering);
    }

    /**
     * Resets the iterator to resume at the specified node
     */
    public function resumeAt(Node $node, bool $entering = true): void
    {
        $this->current  = $node;
        $this->entering = $entering;
    }
}
