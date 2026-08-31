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

use Psy\Readline\Interactive\Terminal;

/**
 * Calculates available terminal space for overlays rendered below the input.
 *
 * Tracks how many terminal rows the input currently occupies and provides
 * the remaining rows available for overlay content (tab completion menus, etc.).
 */
class OverlayViewport
{
    private Terminal $terminal;

    /** How many terminal rows the current input (prompt + buffer) occupies. */
    private int $inputRowCount = 1;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * Update how many terminal rows the input currently occupies.
     *
     * Called by FrameRenderer after each render.
     */
    public function setInputRowCount(int $rows): void
    {
        $this->inputRowCount = \max(1, $rows);
    }

    /**
     * Get the maximum number of rows available for overlay content.
     *
     * Subtracts the input height from the terminal height, reserving
     * one row for breathing room. In compact mode, also caps at half
     * the terminal height.
     *
     * @param bool $compact If true, use at most half the terminal height
     */
    public function getAvailableRows(bool $compact = false): int
    {
        $terminalHeight = $this->terminal->getHeight();

        // Reserve 1 row for status line / breathing room
        $available = $terminalHeight - $this->inputRowCount - 1;

        if ($compact) {
            $halfTerminal = (int) \floor($terminalHeight / 2);
            $available = \min($available, $halfTerminal);
        }

        return \max(1, $available);
    }
}
