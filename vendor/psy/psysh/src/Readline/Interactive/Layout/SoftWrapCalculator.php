<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Layout;

/**
 * Terminal soft-wrap geometry calculations.
 */
class SoftWrapCalculator
{
    private int $terminalWidth;

    public function __construct(int $terminalWidth)
    {
        $this->terminalWidth = \max(1, $terminalWidth);
    }

    public function getTerminalWidth(): int
    {
        return $this->terminalWidth;
    }

    /**
     * Normalize an absolute terminal column (1-indexed) into visible column.
     */
    public function normalizeColumn(int $absoluteColumn): int
    {
        $offset = \max(0, $absoluteColumn - 1);

        return ($offset % $this->terminalWidth) + 1;
    }

    /**
     * Get wrapped row offset for an absolute terminal column (1-indexed).
     */
    public function rowOffsetForAbsoluteColumn(int $absoluteColumn): int
    {
        return \intdiv(\max(0, $absoluteColumn - 1), $this->terminalWidth);
    }

    /**
     * Count wrapped rows occupied by text with this display width.
     *
     * Zero-width text still occupies one row.
     */
    public function rowCountForDisplayWidth(int $displayWidth): int
    {
        if ($displayWidth <= 0) {
            return 1;
        }

        return \intdiv($displayWidth - 1, $this->terminalWidth) + 1;
    }
}
