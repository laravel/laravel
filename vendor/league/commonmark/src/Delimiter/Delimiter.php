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

namespace League\CommonMark\Delimiter;

use League\CommonMark\Node\Inline\AbstractStringContainer;

final class Delimiter implements DelimiterInterface
{
    /** @psalm-readonly */
    private string $char;

    /** @psalm-readonly-allow-private-mutation */
    private int $length;

    /** @psalm-readonly */
    private int $originalLength;

    /** @psalm-readonly */
    private AbstractStringContainer $inlineNode;

    /** @psalm-readonly-allow-private-mutation */
    private ?DelimiterInterface $previous = null;

    /** @psalm-readonly-allow-private-mutation */
    private ?DelimiterInterface $next = null;

    /** @psalm-readonly */
    private bool $canOpen;

    /** @psalm-readonly */
    private bool $canClose;

    /** @psalm-readonly-allow-private-mutation */
    private bool $active;

    /** @psalm-readonly */
    private ?int $index = null;

    public function __construct(string $char, int $numDelims, AbstractStringContainer $node, bool $canOpen, bool $canClose, ?int $index = null)
    {
        $this->char           = $char;
        $this->length         = $numDelims;
        $this->originalLength = $numDelims;
        $this->inlineNode     = $node;
        $this->canOpen        = $canOpen;
        $this->canClose       = $canClose;
        $this->active         = true;
        $this->index          = $index;
    }

    public function canClose(): bool
    {
        return $this->canClose;
    }

    public function canOpen(): bool
    {
        return $this->canOpen;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getChar(): string
    {
        return $this->char;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function getNext(): ?DelimiterInterface
    {
        return $this->next;
    }

    public function setNext(?DelimiterInterface $next): void
    {
        $this->next = $next;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getOriginalLength(): int
    {
        return $this->originalLength;
    }

    public function getInlineNode(): AbstractStringContainer
    {
        return $this->inlineNode;
    }

    public function getPrevious(): ?DelimiterInterface
    {
        return $this->previous;
    }

    public function setPrevious(?DelimiterInterface $previous): void
    {
        $this->previous = $previous;
    }
}
