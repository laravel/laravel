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

use Psy\Output\Theme;
use Psy\Readline\Interactive\Helper\CommandHighlighter;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Readline\Interactive\Suggestion\SuggestionResult;
use Psy\Readline\Interactive\Terminal;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Frame-buffered renderer for interactive readline.
 *
 * Orchestrates the input frame widget and an optional overlay widget into
 * a single Frame, then diffs the frame against the previously displayed
 * one to emit the minimum ANSI required to reconcile the terminal.
 *
 * Input-related public methods forward to the InputFrameWidget so existing
 * callers (Readline) don't need to know about the widget.
 */
class FrameRenderer
{
    private Terminal $terminal;
    private OverlayViewport $viewport;
    private LineMetrics $lineMetrics;
    private InputFrameWidget $inputWidget;

    private Frame $previousFrame;
    private ?WidgetInterface $overlay = null;

    private ?int $lastTerminalWidth = null;
    private ?int $lastTerminalHeight = null;

    public function __construct(Terminal $terminal, OverlayViewport $viewport, ?Theme $theme = null)
    {
        $this->terminal = $terminal;
        $this->viewport = $viewport;
        $this->lineMetrics = new LineMetrics($terminal);
        $this->inputWidget = new InputFrameWidget($terminal, $this->lineMetrics, $theme);
        $this->previousFrame = Frame::empty();
    }

    public function getCommandHighlighter(): CommandHighlighter
    {
        return $this->inputWidget->getCommandHighlighter();
    }

    public function setTheme(Theme $theme): void
    {
        $this->inputWidget->setTheme($theme);
    }

    public function setUseSyntaxHighlighting(bool $enabled): void
    {
        $this->inputWidget->setUseSyntaxHighlighting($enabled);
    }

    public function isCompactInputFrame(): bool
    {
        return $this->inputWidget->isCompact();
    }

    public function setErrorMode(bool $error): void
    {
        $this->inputWidget->setErrorMode($error);
    }

    public function getInputFrameOuterRowCount(): int
    {
        return $this->inputWidget->getOuterRowCount();
    }

    public function getPromptWidthForCurrentLine(Buffer $buffer): int
    {
        return $this->inputWidget->getPromptWidthForCurrentLine($buffer);
    }

    public function addHistoryLines(string $text, bool $isCommand = false): void
    {
        $this->inputWidget->addHistoryLines($text, $isCommand);
    }

    public function clearHistoryLines(): void
    {
        $this->inputWidget->clearHistoryLines();
    }

    /**
     * Set the widget rendered below the input, or null to clear it.
     */
    public function setOverlay(?WidgetInterface $widget): void
    {
        $this->overlay = $widget;
    }

    /**
     * Access the shared LineMetrics instance used by Pager (and any
     * future widget that needs wrap-aware row math against the same
     * cache the renderer uses).
     */
    public function getLineMetrics(): LineMetrics
    {
        return $this->lineMetrics;
    }

    /**
     * Render the full frame (input + overlay) to the terminal.
     */
    public function render(Buffer $buffer, ?SuggestionResult $suggestion, ?string $historySearchTerm = null, bool $isCommand = false): void
    {
        $this->inputWidget->setRenderState($buffer, $suggestion, $historySearchTerm, $isCommand, $this->overlay !== null);

        $frame = new Frame([], 0, 0);
        $inputArea = new Area($this->lineMetrics->getTerminalWidth(), \PHP_INT_MAX);
        $inputRows = $this->inputWidget->render($frame, $inputArea);

        $this->viewport->setInputRowCount($inputRows);
        $this->renderOverlayInto($frame);

        $this->syncFrame($frame);
        $this->terminal->flush();
    }

    /**
     * Render a single widget that occupies the full terminal: no input
     * frame, no overlay. Used by Pager (and any future full-screen mode).
     *
     * The widget is given the entire terminal area and is expected to set
     * its own cursor position on the frame.
     */
    public function renderFullScreenWidget(WidgetInterface $widget): void
    {
        $frame = new Frame([], 0, 0);
        $area = new Area(
            $this->lineMetrics->getTerminalWidth(),
            \max(1, $this->terminal->getHeight()),
        );
        $widget->render($frame, $area);

        $this->syncFrame($frame);
        $this->terminal->flush();
    }

    /**
     * Render the search UI: preview in the input frame, search field + results below.
     *
     * @param string $previewText  Text to show in the input frame as a preview
     * @param string $searchPrompt The search field line (cursor goes here)
     */
    public function renderSearchFrame(string $previewText, string $searchPrompt): void
    {
        // Show a concise, truncated preview on the prompt line.
        $promptLine = $this->inputWidget->getPromptForLine(0);
        if ($previewText !== '') {
            $collapsed = History::collapseToSingleLine($previewText);
            $promptWidth = $this->inputWidget->getPromptWidthForLine(0, $this->terminal->getFormatter());
            $maxWidth = $this->lineMetrics->getTerminalWidth() - $promptWidth;
            $truncated = DisplayString::truncate($collapsed, $maxWidth, true);
            $promptLine .= OutputFormatter::escape($truncated);
        }

        $inputLines = $this->inputWidget->wrapContentInFrame([$promptLine]);

        $inputRowCount = $this->lineMetrics->frameRowCount($inputLines);
        $searchPromptRows = $this->lineMetrics->lineRowCount($searchPrompt);
        $this->viewport->setInputRowCount($inputRowCount + $searchPromptRows);

        // The cursor sits on the search prompt line, which is immediately after
        // the input frame, so the overlay (rendered after the search prompt)
        // does not affect cursor row math.
        $calculator = $this->lineMetrics->softWrap();
        $absoluteColumn = DisplayString::widthWithoutAnsi($searchPrompt) + 1;
        $searchLineRow = $this->lineMetrics->rowOffsetBeforeLine($inputLines, \count($inputLines));
        $cursorRow = $searchLineRow + $calculator->rowOffsetForAbsoluteColumn($absoluteColumn);
        $cursorColumn = $calculator->normalizeColumn($absoluteColumn);

        $frame = new Frame($inputLines, $cursorRow, $cursorColumn);
        $frame->appendLine($searchPrompt);
        $this->renderOverlayInto($frame);

        $this->syncFrame($frame);
        $this->terminal->flush();
    }

    /**
     * Reset renderer state (call when starting a new readline session).
     */
    public function reset(): void
    {
        $this->previousFrame = Frame::empty();
        $this->overlay = null;
        $this->inputWidget->reset();
        $this->lastTerminalWidth = null;
        $this->lastTerminalHeight = null;
    }

    /**
     * Render the current overlay widget (if any) onto the given frame.
     */
    private function renderOverlayInto(Frame $frame): void
    {
        if ($this->overlay === null) {
            return;
        }

        $area = new Area($this->lineMetrics->getTerminalWidth(), $this->viewport->getAvailableRows(false));
        $this->overlay->render($frame, $area);
    }

    /**
     * Sync a new frame to the terminal.
     */
    private function syncFrame(Frame $newFrame): void
    {
        $newLines = $newFrame->getLines();
        $cursorRow = $newFrame->getCursorRow();
        $cursorColumn = $newFrame->getCursorColumn();

        $terminalWidth = $this->lineMetrics->getTerminalWidth();
        $terminalHeight = \max(1, $this->terminal->getHeight());
        $sizeChanged = $this->lastTerminalWidth !== null && (
            $terminalWidth !== $this->lastTerminalWidth || $terminalHeight !== $this->lastTerminalHeight
        );
        $dirty = $this->terminal->isDirty();

        $oldLines = ($sizeChanged || $dirty) ? [] : $this->previousFrame->getLines();
        $firstChangedLine = $this->findFirstChangedLine($oldLines, $newLines);

        // Wrapped row tracking is invalid after row-affecting out-of-band writes/resizes,
        // or if we can no longer trust the previous frame contents.
        $currentRow = ($sizeChanged || $this->terminal->isCursorRowUnknown())
            ? 0
            : $this->previousFrame->getCursorRow();

        $this->terminal->beginFrameRender();

        try {
            if ($firstChangedLine !== null) {
                $oldStartRow = $this->lineMetrics->rowOffsetBeforeLine($oldLines, $firstChangedLine);
                $newStartRow = $this->lineMetrics->rowOffsetBeforeLine($newLines, $firstChangedLine);

                $currentRow = $this->moveCursorToRow($currentRow, $oldStartRow);

                $this->terminal->write("\r");
                $this->terminal->clearToEndOfScreen();

                $suffix = \array_slice($newLines, $firstChangedLine);
                foreach ($suffix as $i => $line) {
                    if ($i > 0) {
                        $this->terminal->write("\n");
                    }
                    $this->terminal->write($line);
                }

                $currentRow = empty($suffix)
                    ? $newStartRow
                    : $newStartRow + $this->lineMetrics->frameRowCount($suffix) - 1;
            }

            $currentRow = $this->moveCursorToRow($currentRow, $cursorRow);

            $this->terminal->moveCursorToColumn($cursorColumn);

            $this->previousFrame = $newFrame;
            $this->lastTerminalWidth = $terminalWidth;
            $this->lastTerminalHeight = $terminalHeight;
        } finally {
            $this->terminal->endFrameRender();
        }
    }

    /**
     * Find the first logical line that differs between two frames.
     *
     * @param string[] $oldFrame
     * @param string[] $newFrame
     */
    private function findFirstChangedLine(array $oldFrame, array $newFrame): ?int
    {
        $oldCount = \count($oldFrame);
        $newCount = \count($newFrame);
        $sharedCount = \min($oldCount, $newCount);

        for ($i = 0; $i < $sharedCount; $i++) {
            if ($oldFrame[$i] !== $newFrame[$i]) {
                return $i;
            }
        }

        if ($oldCount !== $newCount) {
            return $sharedCount;
        }

        return null;
    }

    /**
     * Move cursor vertically between wrapped frame rows.
     */
    private function moveCursorToRow(int $currentRow, int $targetRow): int
    {
        if ($targetRow < $currentRow) {
            $this->terminal->moveCursorUp($currentRow - $targetRow);
        } elseif ($targetRow > $currentRow) {
            $this->terminal->moveCursorDown($targetRow - $currentRow);
        }

        return $targetRow;
    }
}
