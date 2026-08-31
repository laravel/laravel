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

namespace League\CommonMark\Delimiter;

use League\CommonMark\Node\Node;

final class Bracket
{
    private Node $node;
    private ?Bracket $previous;
    private bool $hasNext = false;
    private int $position;
    private bool $image;
    private bool $active = true;

    public function __construct(Node $node, ?Bracket $previous, int $position, bool $image)
    {
        $this->node     = $node;
        $this->previous = $previous;
        $this->position = $position;
        $this->image    = $image;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function getPrevious(): ?Bracket
    {
        return $this->previous;
    }

    public function hasNext(): bool
    {
        return $this->hasNext;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isImage(): bool
    {
        return $this->image;
    }

    /**
     * Only valid in the context of non-images (links)
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @internal
     */
    public function setHasNext(bool $hasNext): void
    {
        $this->hasNext = $hasNext;
    }

    /**
     * @internal
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
