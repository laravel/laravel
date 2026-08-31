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

use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Readline\Interactive\Terminal;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Pager content viewport + status line.
 *
 * Renders a slice of pre-styled lines starting at `scrollOffset`. Lines
 * are emitted in full and may wrap across multiple visual rows; the
 * widget uses LineMetrics to budget against the area's visual row height
 * so the status line lands at a predictable position. Keeping wrap
 * behavior consistent during paging and after exit means scrollback
 * matches what the user saw on screen.
 */
class PagerWidget implements WidgetInterface
{
    private Terminal $terminal;
    private LineMetrics $lineMetrics;
    /** @var string[] */
    private array $lines;
    private int $scrollOffset;
    private string $searchQuery;
    private bool $searchInputActive;
    private ?string $hint;
    private int $matchCount;
    private int $currentMatchIndex;

    /**
     * @param string[]    $lines             Pre-styled content lines (no embedded newlines)
     * @param int         $scrollOffset      Index of the first visible line
     * @param string      $searchQuery       Active search query (empty = no search)
     * @param bool        $searchInputActive True while user is typing the query
     * @param string|null $hint              Optional hint shown when no search is active
     * @param int         $matchCount        Number of matching lines (0 if no search)
     * @param int         $currentMatchIndex 0-indexed position within matches (-1 if no current)
     */
    public function __construct(
        Terminal $terminal,
        LineMetrics $lineMetrics,
        array $lines,
        int $scrollOffset = 0,
        string $searchQuery = '',
        bool $searchInputActive = false,
        ?string $hint = null,
        int $matchCount = 0,
        int $currentMatchIndex = -1
    ) {
        $this->terminal = $terminal;
        $this->lineMetrics = $lineMetrics;
        $this->lines = $lines;
        $this->scrollOffset = $scrollOffset;
        $this->searchQuery = $searchQuery;
        $this->searchInputActive = $searchInputActive;
        $this->hint = $hint;
        $this->matchCount = $matchCount;
        $this->currentMatchIndex = $currentMatchIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Frame $frame, Area $area): int
    {
        $totalLines = \count($this->lines);
        $viewportRows = \max(1, $area->getHeight() - 1); // reserve last row for status

        $rowsUsed = 0;
        $i = $this->scrollOffset;
        $endLine = $i;
        $partialTail = false;

        while ($i < $totalLines && $rowsUsed < $viewportRows) {
            $line = $this->lines[$i];
            if ($this->searchQuery !== '') {
                $line = $this->highlightQuery($line);
            }

            $lineRows = $this->lineMetrics->lineRowCount($line);
            if ($lineRows > $viewportRows - $rowsUsed) {
                // If a single logical line is taller than the viewport, show
                // a viewport-sized preview instead of dropping it entirely.
                if ($rowsUsed === 0) {
                    $frame->appendLine($this->truncateLineToRows($line, $viewportRows));
                    $rowsUsed = $viewportRows;
                    $i++;
                    $endLine = $i;
                    // The line was truncated; there is more below the
                    // viewport.
                    $partialTail = true;
                }

                // Otherwise stop before the next wrapped line so the status
                // row stays anchored at the bottom.
                break;
            }

            $frame->appendLine($line);
            $rowsUsed += $lineRows;
            $i++;
            $endLine = $i;
        }

        // Pad with blank rows so the status sits at the same terminal row
        // regardless of how much content fits.
        while ($rowsUsed < $viewportRows) {
            $frame->appendLine('');
            $rowsUsed++;
        }

        $frame->appendLine($this->renderStatusLine($area->getWidth(), $this->scrollOffset, $endLine, $totalLines, $partialTail));

        return $viewportRows + 1;
    }

    private function truncateLineToRows(string $line, int $rows): string
    {
        $maxWidth = \max(1, $this->lineMetrics->getTerminalWidth() * $rows);
        $plainLine = DisplayString::stripAnsi($line);

        return DisplayString::truncate($plainLine, $maxWidth, true);
    }

    private function renderStatusLine(int $width, int $startRow, int $endRow, int $totalLines, bool $partialTail = false): string
    {
        if ($totalLines === 0) {
            $right = '0/0';
        } else {
            $percent = (int) \floor(($endRow / $totalLines) * 100);
            if ($partialTail) {
                // The last visible line was truncated; there's more of it
                // below the viewport. Cap at 99% so we don't claim "done"
                // and append an ellipsis to signal more content.
                $percent = \min($percent, 99);
                $right = \sprintf('%d/%d%s%d%%%s', $endRow, $totalLines, $this->separator(), $percent, $this->ellipsis());
            } else {
                $right = \sprintf('%d/%d%s%d%%', $endRow, $totalLines, $this->separator(), $percent);
            }
        }

        if ($this->searchInputActive) {
            $left = \sprintf('search: %s', $this->searchQuery);
        } elseif ($this->searchQuery !== '' && $this->matchCount > 0) {
            $left = \sprintf(
                '/%s%s%d/%d matches',
                $this->searchQuery,
                $this->separator(),
                $this->currentMatchIndex + 1,
                $this->matchCount,
            );
        } elseif ($this->searchQuery !== '') {
            $left = \sprintf('/%s%s(no matches)', $this->searchQuery, $this->separator());
        } else {
            $left = $this->hint ?? \sprintf('j/k scroll%s/ search%sq quit', $this->separator(), $this->separator());
        }

        $leftWidth = DisplayString::widthWithoutAnsi($left);
        $rightWidth = DisplayString::widthWithoutAnsi($right);
        $padding = \max(1, $width - $leftWidth - $rightWidth);

        $line = OutputFormatter::escape($left).\str_repeat(' ', $padding).OutputFormatter::escape($right);

        return $this->terminal->format('<whisper>'.$line.'</whisper>');
    }

    /**
     * Highlight occurrences of the active query in a styled line.
     *
     * If the line already contains ANSI escape sequences (i.e. it's
     * pre-styled output), skip highlighting; running a regex over
     * the styled text can match characters inside escape sequences and
     * corrupt them.
     */
    private function highlightQuery(string $line): string
    {
        if ($this->searchQuery === '') {
            return $line;
        }

        if (\strpos($line, "\033") !== false) {
            return $line;
        }

        $formatter = $this->terminal->getFormatter();
        if (!$formatter->isDecorated() || !$formatter->hasStyle('input_highlight')) {
            return $line;
        }

        if (!\mb_check_encoding($this->searchQuery, 'UTF-8')) {
            return $line;
        }

        $style = $formatter->getStyle('input_highlight');
        $caseSensitive = History::isSearchCaseSensitive($this->searchQuery);
        $pattern = '/'.\preg_quote($this->searchQuery, '/').'/u'.($caseSensitive ? '' : 'i');
        $highlighted = \preg_replace_callback($pattern, fn (array $m) => $style->apply($m[0]), $line);

        return $highlighted ?? $line;
    }

    private function separator(): string
    {
        return $this->terminal->useUnicode() ? ' · ' : ' | ';
    }

    private function ellipsis(): string
    {
        return $this->terminal->useUnicode() ? '…' : '...';
    }
}
