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

use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Readline\Interactive\Layout\SoftWrapCalculator;
use Psy\Readline\Interactive\Terminal;

/**
 * Soft-wrap math and line-row caching, shared by the renderer and any
 * widgets that need to reason about wrapped-row geometry.
 *
 * Holds a single SoftWrapCalculator keyed by terminal width and a cache
 * of per-line wrapped-row counts; both are invalidated automatically when
 * the terminal width changes.
 */
class LineMetrics
{
    private Terminal $terminal;
    private ?SoftWrapCalculator $softWrapCalculator = null;
    private ?int $cachedWidth = null;

    /** @var array<string, int> Cached row counts keyed by line content. */
    private array $rowCache = [];

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    public function getTerminalWidth(): int
    {
        return \max(1, $this->terminal->getWidth());
    }

    public function softWrap(): SoftWrapCalculator
    {
        $width = $this->getTerminalWidth();
        if ($this->softWrapCalculator === null || $this->cachedWidth !== $width) {
            $this->softWrapCalculator = new SoftWrapCalculator($width);
            $this->cachedWidth = $width;
            $this->rowCache = [];
        }

        return $this->softWrapCalculator;
    }

    /**
     * Count wrapped terminal rows for a single logical line.
     */
    public function lineRowCount(string $line): int
    {
        // Ask for the calculator first so it can invalidate the row cache if
        // the terminal width has changed since the last call.
        $softWrap = $this->softWrap();

        if (isset($this->rowCache[$line])) {
            return $this->rowCache[$line];
        }

        $width = DisplayString::widthWithoutAnsi($line);
        $rows = $softWrap->rowCountForDisplayWidth($width);
        $this->rowCache[$line] = $rows;

        return $rows;
    }

    /**
     * Total wrapped rows occupied by a list of lines.
     *
     * @param string[] $lines
     */
    public function frameRowCount(array $lines): int
    {
        $rows = 0;
        foreach ($lines as $line) {
            $rows += $this->lineRowCount($line);
        }

        return \max(1, $rows);
    }

    /**
     * Wrapped rows occupied by lines before the given logical line index.
     *
     * @param string[] $lines
     */
    public function rowOffsetBeforeLine(array $lines, int $lineIndex): int
    {
        $rows = 0;
        $lineIndex = \max(0, \min($lineIndex, \count($lines)));

        for ($i = 0; $i < $lineIndex; $i++) {
            $rows += $this->lineRowCount($lines[$i]);
        }

        return $rows;
    }
}
