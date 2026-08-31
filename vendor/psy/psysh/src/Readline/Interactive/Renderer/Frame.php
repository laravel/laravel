<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Renderer;

/**
 * The visible state of an interactive readline render pass.
 *
 * Holds the vertical stack of pre-styled logical lines plus the desired
 * cursor position (in wrapped frame coordinates). FrameRenderer diffs the
 * previous Frame against the next one to produce minimal terminal updates.
 */
class Frame
{
    /** @var string[] */
    private array $lines;

    private int $cursorRow;
    private int $cursorColumn;

    /**
     * @param string[] $lines        Pre-styled logical lines (OutputFormatter tags or raw ANSI)
     * @param int      $cursorRow    Desired cursor row in wrapped frame coordinates (0-indexed)
     * @param int      $cursorColumn Desired terminal column for the cursor (1-indexed)
     */
    public function __construct(array $lines, int $cursorRow, int $cursorColumn)
    {
        $this->lines = $lines;
        $this->cursorRow = $cursorRow;
        $this->cursorColumn = $cursorColumn;
    }

    /**
     * An empty frame with the cursor at the origin.
     */
    public static function empty(): self
    {
        return new self([], 0, 0);
    }

    /**
     * Append a pre-styled logical line to this frame.
     */
    public function appendLine(string $line): void
    {
        $this->lines[] = $line;
    }

    /**
     * Update the desired cursor position.
     */
    public function setCursor(int $row, int $column): void
    {
        $this->cursorRow = $row;
        $this->cursorColumn = $column;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getCursorRow(): int
    {
        return $this->cursorRow;
    }

    public function getCursorColumn(): int
    {
        return $this->cursorColumn;
    }
}
