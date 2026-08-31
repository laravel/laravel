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

use Dflydev\DotAccessData\Data;
use League\CommonMark\Exception\InvalidArgumentException;

abstract class Node
{
    /** @psalm-readonly */
    public Data $data;

    /** @psalm-readonly-allow-private-mutation */
    protected int $depth = 0;

    /** @psalm-readonly-allow-private-mutation */
    protected ?Node $parent = null;

    /** @psalm-readonly-allow-private-mutation */
    protected ?Node $previous = null;

    /** @psalm-readonly-allow-private-mutation */
    protected ?Node $next = null;

    /** @psalm-readonly-allow-private-mutation */
    protected ?Node $firstChild = null;

    /** @psalm-readonly-allow-private-mutation */
    protected ?Node $lastChild = null;

    public function __construct()
    {
        $this->data = new Data([
            'attributes' => [],
        ]);
    }

    public function previous(): ?Node
    {
        return $this->previous;
    }

    public function next(): ?Node
    {
        return $this->next;
    }

    public function parent(): ?Node
    {
        return $this->parent;
    }

    protected function setParent(?Node $node = null): void
    {
        $this->parent = $node;
        $this->depth  = $node === null ? 0 : $node->depth + 1;
    }

    /**
     * Inserts the $sibling node after $this
     */
    public function insertAfter(Node $sibling): void
    {
        $sibling->detach();
        $sibling->next = $this->next;

        if ($sibling->next) {
            $sibling->next->previous = $sibling;
        }

        $sibling->previous = $this;
        $this->next        = $sibling;
        $sibling->setParent($this->parent);

        if (! $sibling->next && $sibling->parent) {
            $sibling->parent->lastChild = $sibling;
        }
    }

    /**
     * Inserts the $sibling node before $this
     */
    public function insertBefore(Node $sibling): void
    {
        $sibling->detach();
        $sibling->previous = $this->previous;

        if ($sibling->previous) {
            $sibling->previous->next = $sibling;
        }

        $sibling->next  = $this;
        $this->previous = $sibling;
        $sibling->setParent($this->parent);

        if (! $sibling->previous && $sibling->parent) {
            $sibling->parent->firstChild = $sibling;
        }
    }

    public function replaceWith(Node $replacement): void
    {
        $replacement->detach();
        $this->insertAfter($replacement);
        $this->detach();
    }

    public function detach(): void
    {
        if ($this->previous) {
            $this->previous->next = $this->next;
        } elseif ($this->parent) {
            $this->parent->firstChild = $this->next;
        }

        if ($this->next) {
            $this->next->previous = $this->previous;
        } elseif ($this->parent) {
            $this->parent->lastChild = $this->previous;
        }

        $this->parent   = null;
        $this->next     = null;
        $this->previous = null;
        $this->depth    = 0;
    }

    public function hasChildren(): bool
    {
        return $this->firstChild !== null;
    }

    public function firstChild(): ?Node
    {
        return $this->firstChild;
    }

    public function lastChild(): ?Node
    {
        return $this->lastChild;
    }

    /**
     * @return Node[]
     */
    public function children(): iterable
    {
        $children = [];
        for ($current = $this->firstChild; $current !== null; $current = $current->next) {
            $children[] = $current;
        }

        return $children;
    }

    public function appendChild(Node $child): void
    {
        if ($this->lastChild) {
            $this->lastChild->insertAfter($child);
        } else {
            $child->detach();
            $child->setParent($this);
            $this->lastChild = $this->firstChild = $child;
        }
    }

    /**
     * Adds $child as the very first child of $this
     */
    public function prependChild(Node $child): void
    {
        if ($this->firstChild) {
            $this->firstChild->insertBefore($child);
        } else {
            $child->detach();
            $child->setParent($this);
            $this->lastChild = $this->firstChild = $child;
        }
    }

    /**
     * Detaches all child nodes of given node
     */
    public function detachChildren(): void
    {
        foreach ($this->children() as $children) {
            $children->setParent(null);
        }

        $this->firstChild = $this->lastChild = null;
    }

    /**
     * Replace all children of given node with collection of another
     *
     * @param iterable<Node> $children
     */
    public function replaceChildren(iterable $children): void
    {
        $this->detachChildren();
        foreach ($children as $item) {
            $this->appendChild($item);
        }
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function walker(): NodeWalker
    {
        return new NodeWalker($this);
    }

    public function iterator(int $flags = 0): NodeIterator
    {
        return new NodeIterator($this, $flags);
    }

    /**
     * Clone the current node and its children
     *
     * WARNING: This is a recursive function and should not be called on deeply-nested node trees!
     */
    public function __clone()
    {
        // Cloned nodes are detached from their parents, siblings, and children
        $this->parent   = null;
        $this->previous = null;
        $this->next     = null;
        // But save a copy of the children since we'll need that in a moment
        $children = $this->children();
        $this->detachChildren();

        // The original children get cloned and re-added
        foreach ($children as $child) {
            $this->appendChild(clone $child);
        }
    }

    public static function assertInstanceOf(Node $node): void
    {
        if (! $node instanceof static) {
            throw new InvalidArgumentException(\sprintf('Incompatible node type: expected %s, got %s', static::class, \get_class($node)));
        }
    }
}
