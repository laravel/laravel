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

namespace League\CommonMark\Node\Block;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Node\Node;

/**
 * Block-level element
 *
 * @method parent() ?AbstractBlock
 */
abstract class AbstractBlock extends Node
{
    protected ?int $startLine = null;

    protected ?int $endLine = null;

    protected function setParent(?Node $node = null): void
    {
        if ($node && ! $node instanceof self) {
            throw new InvalidArgumentException('Parent of block must also be block (cannot be inline)');
        }

        parent::setParent($node);
    }

    public function setStartLine(?int $startLine): void
    {
        $this->startLine = $startLine;
        if ($this->endLine === null) {
            $this->endLine = $startLine;
        }
    }

    public function getStartLine(): ?int
    {
        return $this->startLine;
    }

    public function setEndLine(?int $endLine): void
    {
        $this->endLine = $endLine;
    }

    public function getEndLine(): ?int
    {
        return $this->endLine;
    }
}
