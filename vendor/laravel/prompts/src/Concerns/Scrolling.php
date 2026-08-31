<?php

namespace Laravel\Prompts\Concerns;

use Laravel\Prompts\Themes\Contracts\Scrolling as ScrollingRenderer;

trait Scrolling
{
    /**
     * The number of items to display before scrolling.
     */
    public int $scroll;

    /**
     * The index of the highlighted option.
     */
    public ?int $highlighted;

    /**
     * The index of the first visible option.
     */
    public int $firstVisible = 0;

    /**
     * Initialize scrolling.
     */
    protected function initializeScrolling(?int $highlighted = null): void
    {
        $this->highlighted = $highlighted;

        $this->reduceScrollingToFitTerminal();
    }

    /**
     * Reduce the scroll property to fit the terminal height.
     */
    protected function reduceScrollingToFitTerminal(): void
    {
        $reservedLines = ($renderer = $this->getRenderer()) instanceof ScrollingRenderer ? $renderer->reservedLines() : 0;

        $this->scroll = max(1, min($this->scroll, $this->terminal()->lines() - $reservedLines));
    }

    /**
     * Highlight the given index.
     */
    protected function highlight(?int $index): void
    {
        $this->highlighted = $index;

        if ($this->highlighted === null) {
            return;
        }

        if ($this->highlighted < $this->firstVisible) {
            $this->firstVisible = $this->highlighted;
        } elseif ($this->highlighted > $this->firstVisible + $this->scroll - 1) {
            $this->firstVisible = $this->highlighted - $this->scroll + 1;
        }
    }

    /**
     * Highlight the previous entry, or wrap around to the last entry.
     */
    protected function highlightPrevious(int $total, bool $allowNull = false): void
    {
        if ($total === 0) {
            return;
        }

        if ($this->highlighted === null) {
            $this->highlight($total - 1);
        } elseif ($this->highlighted === 0) {
            $this->highlight($allowNull ? null : ($total - 1));
        } else {
            $this->highlight($this->highlighted - 1);
        }
    }

    /**
     * Highlight the next entry, or wrap around to the first entry.
     */
    protected function highlightNext(int $total, bool $allowNull = false): void
    {
        if ($total === 0) {
            return;
        }

        if ($this->highlighted === $total - 1) {
            $this->highlight($allowNull ? null : 0);
        } else {
            $this->highlight(($this->highlighted ?? -1) + 1);
        }
    }

    /**
     * Center the highlighted option.
     */
    protected function scrollToHighlighted(int $total): void
    {
        if ($this->highlighted < $this->scroll) {
            return;
        }

        $remaining = $total - $this->highlighted - 1;
        $halfScroll = (int) floor($this->scroll / 2);
        $endOffset = max(0, $halfScroll - $remaining);

        if ($this->scroll % 2 === 0) {
            $endOffset--;
        }

        $this->firstVisible = $this->highlighted - $halfScroll - $endOffset;
    }
}
