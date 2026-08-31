<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Grid;
use Laravel\Prompts\Output\BufferedConsoleOutput;
use Symfony\Component\Console\Helper\Table as SymfonyTable;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;

class GridRenderer extends Renderer
{
    use Concerns\InteractsWithStrings;

    protected int $minWidth = 60;

    /**
     * Render the grid.
     */
    public function __invoke(Grid $grid): string
    {
        if (empty($grid->items)) {
            return $this;
        }

        $maxWidth = $grid->maxWidth - 2;
        $cellWidth = max(array_map(fn ($item) => mb_strwidth($this->stripEscapeSequences($item)), $grid->items)) + 4;
        $maxColumns = max(1, (int) floor(($maxWidth - 1) / ($cellWidth + 1)));
        $columnCount = max(1, $this->balancedColumnCount(count($grid->items), $maxColumns));

        $rows = $this->buildRowsWithSeparators($grid->items, $columnCount);

        $tableStyle = (new TableStyle)
            ->setHorizontalBorderChars('─')
            ->setVerticalBorderChars('│', '│')
            ->setCellRowFormat('<fg=default>%s</>')
            ->setCrossingChars('┼', '', '', '', '┤', '┘', '┴', '└', '├', '┌', '┬', '┐');

        $buffered = new BufferedConsoleOutput;

        (new SymfonyTable($buffered))
            ->setRows($rows)
            ->setStyle($tableStyle)
            ->render();

        foreach (explode(PHP_EOL, trim($buffered->content(), PHP_EOL)) as $line) {
            $this->line(' '.$line);
        }

        return $this;
    }

    /**
     * Calculate a balanced column count for even row distribution.
     */
    protected function balancedColumnCount(int $itemCount, int $maxColumns): int
    {
        if ($itemCount <= $maxColumns) {
            return $itemCount;
        }

        for ($cols = $maxColumns; $cols >= 1; $cols--) {
            $remainder = $itemCount % $cols;

            if ($remainder === 0 || $remainder >= (int) ceil($cols / 2)) {
                return $cols;
            }
        }

        return $maxColumns;
    }

    /**
     * Build rows with separators between them.
     *
     * @param  array<int, string>  $items
     * @param  int<1, max>  $columnCount
     * @return array<int, array<int, string>|TableSeparator>
     */
    protected function buildRowsWithSeparators(array $items, int $columnCount): array
    {
        $chunks = array_chunk($items, $columnCount);
        $rows = [];

        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                $rows[] = new TableSeparator;
            }

            $rows[] = array_pad($chunk, $columnCount, '');
        }

        return $rows;
    }
}
