<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Input;

use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Readline\Interactive\Layout\SoftWrapCalculator;

/**
 * Soft-wrapped visual row cursor navigation.
 */
class VisualNavigationPolicy
{
    /**
     * Move cursor by soft-wrapped rows on the current logical line.
     *
     * @return int|null New cursor position, or null if movement is not possible
     */
    public function moveByRows(string $text, int $cursor, int $deltaRows, int $terminalWidth, int $promptWidth): ?int
    {
        if ($deltaRows === 0) {
            return null;
        }

        $calculator = new SoftWrapCalculator($terminalWidth);
        $promptWidth = \max(0, $promptWidth);

        [$lineStart, $lineEnd] = $this->getLineBounds($text, $cursor);
        $lineText = \mb_substr($text, $lineStart, $lineEnd - $lineStart);

        $cursorInLine = \max(0, \min(\mb_strlen($lineText), $cursor - $lineStart));
        $textBeforeCursor = \mb_substr($lineText, 0, $cursorInLine);

        $currentAbsoluteColumn = $promptWidth + DisplayString::width($textBeforeCursor) + 1;
        $lineEndAbsoluteColumn = $promptWidth + DisplayString::width($lineText) + 1;

        $currentRow = $calculator->rowOffsetForAbsoluteColumn($currentAbsoluteColumn);
        $targetRow = $currentRow + $deltaRows;
        $maxRow = $calculator->rowOffsetForAbsoluteColumn($lineEndAbsoluteColumn);

        if ($targetRow < 0 || $targetRow > $maxRow) {
            return null;
        }

        $targetAbsoluteColumn = $currentAbsoluteColumn + ($deltaRows * $calculator->getTerminalWidth());
        $targetAbsoluteColumn = \max($promptWidth + 1, \min($lineEndAbsoluteColumn, $targetAbsoluteColumn));
        $targetTextWidth = \max(0, $targetAbsoluteColumn - $promptWidth - 1);

        $targetOffset = DisplayString::offsetForWidth($lineText, $targetTextWidth);

        return $lineStart + $targetOffset;
    }

    /**
     * Get current logical line bounds as [start, end) offsets.
     *
     * @return array{int, int}
     */
    private function getLineBounds(string $text, int $cursor): array
    {
        $beforeCursor = \mb_substr($text, 0, $cursor);
        $lineStartPos = \mb_strrpos($beforeCursor, "\n");
        $lineStart = $lineStartPos === false ? 0 : $lineStartPos + 1;

        $lineEndPos = \mb_strpos($text, "\n", $lineStart);
        $lineEnd = $lineEndPos === false ? \mb_strlen($text) : $lineEndPos;

        return [$lineStart, $lineEnd];
    }
}
