<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\DataTablePrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;
use Laravel\Prompts\Themes\Default\Concerns\DrawsScrollbars;

class DataTableRenderer extends Renderer implements Scrolling
{
    use DrawsBoxes;
    use DrawsScrollbars;

    /**
     * Render the data table.
     */
    public function __invoke(DataTablePrompt $prompt): string
    {
        $maxWidth = $prompt->terminal()->cols() - 6;

        return match ($prompt->state) {
            'submit' => $this->renderSubmit($prompt, $maxWidth),
            'cancel' => $this->renderCancel($prompt, $maxWidth),
            default => $this->renderActive($prompt, $maxWidth),
        };
    }

    /**
     * Render the submit state.
     */
    protected function renderSubmit(DataTablePrompt $prompt, int $maxWidth): string
    {
        $row = $prompt->selectedRow();
        $display = $row ? $this->truncate(implode(', ', $row), $maxWidth) : '';

        return $this
            ->box(
                $this->dim($this->truncate($prompt->label, $maxWidth)),
                $display,
            );
    }

    /**
     * Render the cancel state.
     */
    protected function renderCancel(DataTablePrompt $prompt, int $maxWidth): string
    {
        $filtered = $prompt->filteredRows();
        $visible = $prompt->visible();

        $numCols = ! empty($prompt->headers)
            ? count($prompt->headers)
            : max(array_map('count', $prompt->rows));

        $widths = $this->computeColumnWidths($prompt->headers, $prompt->rows, $numCols, $maxWidth);
        $innerWidth = array_sum($widths) + ($numCols * 2) + ($numCols - 1) + 2;

        // Top border (red)
        $titleText = $this->truncate($prompt->label, $maxWidth);
        $titleLength = mb_strwidth($this->stripEscapeSequences($titleText));
        $topBorderFill = max(0, $innerWidth - $titleLength - 2);
        $this->line($this->red(' ┌')." {$titleText} ".$this->red(str_repeat('─', $topBorderFill).'┐'));

        // Search line (dimmed, to prevent layout shift)
        $searchContent = $this->renderSearchLine($prompt, $innerWidth - 2);
        $this->line($this->red(' │').' '.$this->dim($this->pad($searchContent, $innerWidth - 2)).' '.$this->red('│'));

        // Column separator
        $this->line(' '.$this->renderBorder('├', '┬', '┤', $widths, 'red'));

        // Header cells (strikethrough + dim)
        if (! empty($prompt->headers)) {
            $headerCells = [];

            foreach ($widths as $i => $w) {
                $header = $prompt->headers[$i] ?? '';
                $text = is_array($header) ? implode(' ', $header) : $header;
                $headerCells[] = $this->dim(' '.$this->pad($this->strikethrough($this->truncate($text, $w)), $w).' ');
            }

            $headerLine = implode($this->red('│'), $headerCells).'  ';
            $this->line($this->red(' │').$this->pad($headerLine, $innerWidth).$this->red('│'));

            $this->line(' '.$this->renderBorder('├', '┼', '┤', $widths, 'red'));
        }

        // Data rows (strikethrough + dim)
        $dataLines = $this->renderDataRows($prompt, $filtered, $visible, $widths, $numCols, $innerWidth, strikethrough: true);

        foreach ($dataLines as $dataLine) {
            $this->line($this->red(' │').$this->pad($dataLine, $innerWidth).$this->red('│'));
        }

        // Bottom border (red)
        $this->line(' '.$this->renderBorder('└', '┴', '┘', $widths, 'red'));

        return $this->error($prompt->cancelMessage);
    }

    /**
     * Render the active/browse/search state.
     */
    protected function renderActive(DataTablePrompt $prompt, int $maxWidth): string
    {
        $filtered = $prompt->filteredRows();
        $total = count($filtered);
        $visible = $prompt->visible();

        $numCols = ! empty($prompt->headers)
            ? count($prompt->headers)
            : max(array_map('count', $prompt->rows));

        // Compute column widths from ALL rows (not filtered) to prevent layout shift when searching
        $widths = $this->computeColumnWidths($prompt->headers, $prompt->rows, $numCols, $maxWidth);

        // Inner width between the outer │ chars:
        // cells (sum of w+2 padding each) + separators (numCols-1) + 2 (scrollbar area)
        $innerWidth = array_sum($widths) + ($numCols * 2) + ($numCols - 1) + 2;

        // Top border: ┌ Title ───┐
        $titleText = $this->cyan($this->truncate($prompt->label, $maxWidth));
        $titleLength = mb_strwidth($this->stripEscapeSequences($titleText));
        $topBorderFill = max(0, $innerWidth - $titleLength - 2);
        $this->line($this->gray(' ┌')." {$titleText} ".$this->gray(str_repeat('─', $topBorderFill).'┐'));

        // Search line: │ / Search              │
        $searchContent = $this->renderSearchLine($prompt, $innerWidth - 2);
        $this->line($this->gray(' │').' '.$this->pad($searchContent, $innerWidth - 2).' '.$this->gray('│'));

        if ($total === 0) {
            // No results: simple box without column separators
            $this->line(' '.$this->renderSimpleBorder('├', '┤', $innerWidth));

            $message = $prompt->searchValue() !== '' ? 'No results found.' : 'No rows.';
            $emptyLine = $this->pad(' '.$this->dim($message), $innerWidth);
            $this->line($this->gray(' │').$this->pad($emptyLine, $innerWidth).$this->gray('│'));

            $this->line(' '.$this->renderSimpleBorder('└', '┘', $innerWidth));
        } else {
            // Column separator: ├──────┬────────┤
            $this->line(' '.$this->renderBorder('├', '┬', '┤', $widths));

            // Header cells: │ Header │ Header   │
            if (! empty($prompt->headers)) {
                $headerCells = [];

                foreach ($widths as $i => $w) {
                    $header = $prompt->headers[$i] ?? '';
                    $text = is_array($header) ? implode(' ', $header) : $header;
                    $headerCells[] = $this->dim(' '.$this->pad($this->truncate($text, $w), $w).' ');
                }

                $headerLine = implode($this->gray('│'), $headerCells).'  ';
                $this->line($this->gray(' │').$this->pad($headerLine, $innerWidth).$this->gray('│'));

                // Header separator: ├──────┼────────┤
                $this->line(' '.$this->renderBorder('├', '┼', '┤', $widths));
            }

            // Data rows
            $dataLines = $this->renderDataRows($prompt, $filtered, $visible, $widths, $numCols, $innerWidth);

            foreach ($dataLines as $dataLine) {
                $this->line($this->gray(' │').$this->pad($dataLine, $innerWidth).$this->gray('│'));
            }

            // Bottom border: └──────┴────────┘
            $this->line(' '.$this->renderBorder('└', '┴', '┘', $widths));

            // Info line below the box (only when not all rows are visible)
            if ($total > $prompt->scroll) {
                $firstRow = $prompt->firstVisible + 1;
                $lastRow = min($prompt->firstVisible + $prompt->scroll, $total);
                $suffix = $prompt->searchValue() !== '' ? ' results' : '';
                $info = $this->dim('  Viewing ').$firstRow.'-'.$lastRow.$this->dim(' of ').$total.$suffix;
                $this->line($info);
            }
        }

        return $this
            ->when(
                $prompt->state === 'error',
                fn () => $this->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),
                fn () => $this->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine(),
                ),
            );
    }

    /**
     * Render a column-aware border line.
     *
     * @param  array<int, int>  $widths
     */
    protected function renderBorder(string $left, string $mid, string $right, array $widths, string $color = 'gray'): string
    {
        $segments = array_map(fn ($w) => str_repeat('─', $w + 2), $widths);

        return $this->{$color}($left.implode($mid, $segments).'──'.$right);
    }

    /**
     * Render a simple border line without column separators.
     */
    protected function renderSimpleBorder(string $left, string $right, int $innerWidth, string $color = 'gray'): string
    {
        return $this->{$color}($left.str_repeat('─', $innerWidth).$right);
    }

    /**
     * Render the search line content.
     */
    protected function renderSearchLine(DataTablePrompt $prompt, int $maxWidth): string
    {
        if ($prompt->state === 'search') {
            return $this->cyan('/').' '.$prompt->searchWithCursor($maxWidth - 4);
        }

        if ($prompt->searchValue() !== '') {
            return $this->dim('/').' '.$prompt->searchValue();
        }

        return $this->dim('/ Search');
    }

    /**
     * Render data rows with scrollbar support.
     *
     * @param  array<int|string, array<int, string>>  $filtered
     * @param  array<int|string, array<int, string>>  $visible
     * @param  array<int, int>  $widths
     * @return array<int, string>
     */
    protected function renderDataRows(DataTablePrompt $prompt, array $filtered, array $visible, array $widths, int $numCols, int $innerWidth, bool $strikethrough = false): array
    {
        $total = count($filtered);

        // Build an empty row template for padding
        $emptyRow = implode($this->gray('│'), array_map(
            fn ($w) => str_repeat(' ', $w + 2),
            $widths,
        )).'  ';

        $highlightedKey = array_keys($filtered)[$prompt->highlighted] ?? null;
        $isSearching = $prompt->state === 'search';
        $fixedHeight = $prompt->scroll;

        // Render all visible logical rows into visual lines, tracking which
        // logical row each visual line belongs to so we can clip intelligently.
        $taggedLines = [];

        foreach ($visible as $key => $row) {
            $isHighlighted = ! $isSearching && ! $strikethrough && $key === $highlightedKey;

            // Split each cell by newlines
            $cellLines = [];
            $maxSubRows = 1;

            foreach ($widths as $i => $w) {
                $text = $row[$i] ?? '';
                $subLines = explode(PHP_EOL, $text);
                $cellLines[$i] = $subLines;
                $maxSubRows = max($maxSubRows, count($subLines));
            }

            // Render each sub-row
            for ($subRow = 0; $subRow < $maxSubRows; $subRow++) {
                $cells = [];

                foreach ($widths as $i => $w) {
                    $text = $cellLines[$i][$subRow] ?? '';
                    $content = ' '.$this->pad($this->truncate($text, $w), $w).' ';

                    if ($strikethrough) {
                        $content = ' '.$this->pad($this->dim($this->strikethrough($this->truncate($text, $w))), $w).' ';
                    } elseif ($isHighlighted) {
                        $content = $this->inverse($content);
                    } elseif ($isSearching) {
                        $content = $this->dim($content);
                    }

                    $cells[] = $content;
                }

                $separator = $isHighlighted ? $this->inverse('│') : $this->gray('│');
                $taggedLines[] = [
                    'line' => implode($separator, $cells).'  ',
                    'highlighted' => $isHighlighted,
                ];
            }
        }

        // Fixed visual height: always exactly `scroll` lines.
        // The highlighted row must be fully visible. If multiline rows cause
        // overflow, clip partial rows at the top or bottom edge.
        $totalVisual = count($taggedLines);

        if ($totalVisual <= $fixedHeight) {
            $dataLines = array_column($taggedLines, 'line');
        } else {
            // Find the highlighted row's visual line range
            $hlStart = null;
            $hlEnd = null;

            foreach ($taggedLines as $i => $tagged) {
                if ($tagged['highlighted']) {
                    $hlStart ??= $i;
                    $hlEnd = $i;
                }
            }

            // Pick a window of fixedHeight lines that includes the full highlighted row.
            // Prefer keeping the highlighted row near the bottom (natural scroll feel).
            if ($hlStart !== null) {
                $startLine = max(0, $hlEnd - $fixedHeight + 1);
                $startLine = min($startLine, $hlStart);
            } else {
                $startLine = 0;
            }

            $startLine = min($startLine, $totalVisual - $fixedHeight);
            $startLine = max(0, $startLine);

            $dataLines = array_column(array_slice($taggedLines, $startLine, $fixedHeight), 'line');
        }

        while (count($dataLines) < $fixedHeight) {
            $dataLines[] = $emptyRow;
        }

        // Apply scrollbar to data lines.
        // We can't use the trait's scrollbar() directly because it compares visual
        // line count against logical row count — multiline rows inflate visual lines
        // beyond $total, causing the scrollbar to disappear. Instead, determine
        // scrollability from logical counts and map the indicator to visual space.
        $shouldScroll = $total > $prompt->scroll;

        if ($shouldScroll) {
            $numVisual = count($dataLines);
            $maxFirst = $total - $prompt->scroll;

            if ($prompt->firstVisible === 0) {
                $visualPos = 0;
            } elseif ($prompt->firstVisible >= $maxFirst) {
                $visualPos = $numVisual - 1;
            } elseif ($numVisual <= 2) {
                $visualPos = -1;
            } else {
                $percent = $prompt->firstVisible / $maxFirst;
                $visualPos = (int) round($percent * ($numVisual - 3)) + 1;
            }

            $dataLines = array_map(fn ($line, $index) => match ($index) {
                $visualPos => preg_replace('/.$/', $this->cyan('┃'), $this->pad($line, $innerWidth)) ?? '',
                default => preg_replace('/.$/', $this->gray('│'), $this->pad($line, $innerWidth)) ?? '',
            }, array_values($dataLines), range(0, $numVisual - 1));
        }

        return $dataLines;
    }

    /**
     * Compute column widths that fit within maxWidth.
     *
     * Columns get their natural (P85) width. Only shrink proportionally
     * if the total exceeds available terminal space.
     *
     * @param  array<int, string|array<int, string>>  $headers
     * @param  array<int|string, array<int, string>>  $allRows
     * @return array<int, int>
     */
    protected function computeColumnWidths(array $headers, array $allRows, int $numCols, int $maxWidth): array
    {
        // Header widths serve as the floor for each column
        $headerWidths = array_fill(0, $numCols, 0);

        foreach ($headers as $i => $header) {
            $headerText = is_array($header) ? implode(' ', $header) : $header;
            $headerWidths[$i] = mb_strwidth($headerText);
        }

        // Collect all cell widths per column (excluding blank cells)
        $columnWidths = array_fill(0, $numCols, []);

        foreach ($allRows as $row) {
            foreach ($row as $i => $cell) {
                $cellMax = 0;
                foreach (explode(PHP_EOL, $cell) as $line) {
                    $cellMax = max($cellMax, mb_strwidth($line));
                }
                if ($cellMax > 0) {
                    $columnWidths[$i][] = $cellMax;
                }
            }
        }

        // Per-column width strategy:
        // - Uniform columns (max <= P90 * 2): use max — all values are reasonable
        // - Outlier columns (max > P90 * 2): use P90 — ignore extreme values
        $natural = array_fill(0, $numCols, 0);

        foreach ($columnWidths as $i => $widths) {
            if (empty($widths)) {
                $natural[$i] = $headerWidths[$i];

                continue;
            }

            sort($widths);
            $p90Index = (int) ceil(count($widths) * 0.90) - 1;
            $p90 = $widths[max(0, $p90Index)];
            $colMax = end($widths);

            $natural[$i] = max($headerWidths[$i], $colMax <= $p90 * 2 ? $colMax : $p90);
        }

        // Available width for cell content:
        // Each column has 1 space padding on each side = 2 per column
        // Columns separated by │ = numCols - 1 separators
        // Scrollbar area = 2 chars on the right
        // Outer frame = 4 chars (` │` left + ` │` right)
        $overhead = ($numCols * 2) + ($numCols - 1) + 2 + 4;
        $available = $maxWidth - $overhead;

        if ($available <= 0) {
            return array_fill(0, $numCols, 1);
        }

        $totalNatural = array_sum($natural);

        // If natural widths fit, use them directly (comfortable width)
        if ($totalNatural <= $available) {
            return $natural;
        }

        // Otherwise, shrink proportionally
        $widths = array_fill(0, $numCols, 0);

        foreach ($natural as $i => $w) {
            $widths[$i] = max($headerWidths[$i], (int) floor($available * $w / $totalNatural));
        }

        // Distribute any remaining pixels from rounding
        $remainder = $available - array_sum($widths);

        if ($remainder > 0) {
            $order = range(0, $numCols - 1);
            usort($order, fn ($a, $b) => $natural[$b] <=> $natural[$a]);

            foreach ($order as $i) {
                if ($remainder <= 0) {
                    break;
                }
                $widths[$i]++;
                $remainder--;
            }
        }

        return $widths;
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     */
    public function reservedLines(): int
    {
        return 10;
    }
}
