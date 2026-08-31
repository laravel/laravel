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

/**
 * Tab completion menu, rendered as a compact columnar list.
 *
 * Honors the area height; when not expanded, also caps at half the
 * terminal height to keep the menu from dominating the screen.
 *
 * Always emits a leading blank row to visually separate the menu from
 * the input above it.
 */
class CompletionMenuWidget implements WidgetInterface
{
    private Terminal $terminal;
    /** @var string[] */
    private array $items;
    private int $selectedIndex;
    private int $scrollOffset;
    private bool $expanded;

    /**
     * @param string[] $items
     */
    public function __construct(
        Terminal $terminal,
        array $items,
        int $selectedIndex = -1,
        int $scrollOffset = 0,
        bool $expanded = false
    ) {
        $this->terminal = $terminal;
        $this->items = $items;
        $this->selectedIndex = $selectedIndex;
        $this->scrollOffset = $scrollOffset;
        $this->expanded = $expanded;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Frame $frame, Area $area): int
    {
        $frame->appendLine('');

        if (empty($this->items)) {
            $frame->appendLine($this->terminal->format('  <whisper>(no matches)</whisper>'));

            return 2;
        }

        $items = \array_map([History::class, 'collapseToSingleLine'], \array_values($this->items));
        $layout = self::doCalculateLayout($this->terminal->getWidth(), $items);
        $totalRows = $layout['rows'];
        $columns = $layout['columns'];
        $columnWidths = $layout['columnWidths'];
        $count = \count($items);

        $maxRows = $this->resolveMaxRows($area);
        $needsTruncation = $totalRows > $maxRows;
        $visibleRows = $needsTruncation ? \max(1, $maxRows - 1) : $totalRows;

        $startRow = $needsTruncation ? $this->scrollOffset : 0;
        $endRow = \min($totalRows, $startRow + $visibleRows);

        $formatter = $this->terminal->getFormatter();
        $highlightStyle = $formatter->isDecorated() && $formatter->hasStyle('input_highlight')
            ? $formatter->getStyle('input_highlight')
            : null;
        $hasSelection = $this->selectedIndex >= 0;

        $consumed = 1; // counts the leading blank line

        for ($row = $startRow; $row < $endRow; $row++) {
            $line = '   ';
            for ($col = 0; $col < $columns; $col++) {
                $index = $row + $col * $totalRows;
                if ($index >= $count) {
                    continue;
                }

                $colWidth = $columnWidths[$col];
                $item = DisplayString::truncate($items[$index], $colWidth, true);
                $itemWidth = DisplayString::width($item);
                $padding = \max(0, $colWidth - $itemWidth);

                if ($hasSelection && $index === $this->selectedIndex) {
                    $highlighted = $item.\str_repeat(' ', $padding);
                    $line .= $highlightStyle ? $highlightStyle->apply($highlighted) : $highlighted;
                } else {
                    $line .= $item.\str_repeat(' ', $padding);
                }

                if ($col < $columns - 1) {
                    $line .= '  ';
                }
            }
            $frame->appendLine($line);
            $consumed++;
        }

        if ($needsTruncation) {
            $frame->appendLine($this->renderStatusLine($startRow, $endRow, $totalRows));
            $consumed++;
        }

        return $consumed;
    }

    /**
     * Calculate the row/column structure for a set of items.
     *
     * Exposed for callers that need totals before render time (scroll math).
     *
     * @param string[] $items
     *
     * @return array{rows: int, columns: int, columnWidths: int[]}
     */
    public static function calculateLayout(Terminal $terminal, array $items): array
    {
        return self::doCalculateLayout(
            $terminal->getWidth(),
            \array_map([History::class, 'collapseToSingleLine'], $items),
        );
    }

    /**
     * Resolve the menu's row budget (items + status line) within the area.
     *
     * The compact cap (when not expanded) limits the full overlay, including
     * the leading blank line, to half the terminal height. One row is then
     * reserved for the leading blank, leaving the remainder for the menu.
     */
    private function resolveMaxRows(Area $area): int
    {
        $totalBudget = $area->getHeight();

        if (!$this->expanded) {
            $halfTerminal = (int) \floor($this->terminal->getHeight() / 2);
            $totalBudget = \min($totalBudget, $halfTerminal);
        }

        return \max(1, $totalBudget - 1);
    }

    /**
     * Render the status line for truncated menus.
     */
    private function renderStatusLine(int $startRow, int $endRow, int $totalRows): string
    {
        if (!$this->expanded && $startRow === 0) {
            $remaining = $totalRows - $endRow;
            $prefix = $this->terminal->useUnicode() ? '…' : '...';
            $text = \sprintf('%sand %d more rows', $prefix, $remaining);
        } else {
            $text = \sprintf('rows %d to %d of %d', $startRow + 1, $endRow, $totalRows);
        }

        return $this->terminal->format('   <whisper>'.$text.'</whisper>');
    }

    /**
     * @param string[] $items Display-ready (single-line) items
     *
     * @return array{rows: int, columns: int, columnWidths: int[]}
     */
    private static function doCalculateLayout(int $maxWidth, array $items): array
    {
        $widths = \array_map([DisplayString::class, 'width'], $items);
        $count = \count($items);

        // Naive guess based on the widest item.
        $columns = \max(1, \intdiv($maxWidth, \max($widths) + 2));
        $columnWidths = self::calculateColumnWidths($widths, $count, $columns);
        $maxColumns = \min($count, $columns + 5);

        // Try a few wider layouts; prefer the most columns that still fit.
        for ($try = $columns + 1; $try <= $maxColumns; $try++) {
            $candidate = self::calculateColumnWidths($widths, $count, $try);
            if (\array_sum($candidate) + ($try - 1) * 2 + 3 <= $maxWidth) {
                $columns = $try;
                $columnWidths = $candidate;
            }
        }

        // Cap single-column width so wide items don't soft-wrap.
        if ($columns === 1) {
            $columnWidths[0] = \min($columnWidths[0], $maxWidth - 3);
        }

        return [
            'rows'         => self::calculateRowCount($count, $columns),
            'columns'      => $columns,
            'columnWidths' => $columnWidths,
        ];
    }

    /**
     * @param int[] $widths Pre-computed item widths
     *
     * @return int[] Width of each column
     */
    private static function calculateColumnWidths(array $widths, int $count, int $columns): array
    {
        $rows = self::calculateRowCount($count, $columns);
        $columnWidths = \array_fill(0, $columns, 0);
        for ($i = 0; $i < $count; $i++) {
            $col = \intdiv($i, $rows);
            $columnWidths[$col] = \max($columnWidths[$col], $widths[$i]);
        }

        return $columnWidths;
    }

    private static function calculateRowCount(int $itemCount, int $columns): int
    {
        return \intdiv($itemCount + $columns - 1, $columns);
    }
}
