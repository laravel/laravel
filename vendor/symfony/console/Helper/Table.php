<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\WrappableOutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides helpers to display a table.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 * @author Max Grigorian <maxakawizard@gmail.com>
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class Table
{
    private const SEPARATOR_TOP = 0;
    private const SEPARATOR_TOP_BOTTOM = 1;
    private const SEPARATOR_MID = 2;
    private const SEPARATOR_BOTTOM = 3;
    private const BORDER_OUTSIDE = 0;
    private const BORDER_INSIDE = 1;
    private const DISPLAY_ORIENTATION_DEFAULT = 'default';
    private const DISPLAY_ORIENTATION_HORIZONTAL = 'horizontal';
    private const DISPLAY_ORIENTATION_VERTICAL = 'vertical';

    private ?string $headerTitle = null;
    private ?string $footerTitle = null;
    private array $headers = [];
    private array $rows = [];
    private array $effectiveColumnWidths = [];
    private int $numberOfColumns;
    private TableStyle $style;
    private array $columnStyles = [];
    private array $columnWidths = [];
    private array $columnMaxWidths = [];
    private bool $rendered = false;
    private string $displayOrientation = self::DISPLAY_ORIENTATION_DEFAULT;

    private static array $styles;

    public function __construct(
        private OutputInterface $output,
    ) {
        self::$styles ??= self::initStyles();

        $this->setStyle('default');
    }

    /**
     * Sets a style definition.
     */
    public static function setStyleDefinition(string $name, TableStyle $style): void
    {
        self::$styles ??= self::initStyles();

        self::$styles[$name] = $style;
    }

    /**
     * Gets a style definition by name.
     */
    public static function getStyleDefinition(string $name): TableStyle
    {
        self::$styles ??= self::initStyles();

        return self::$styles[$name] ?? throw new InvalidArgumentException(\sprintf('Style "%s" is not defined.', $name));
    }

    /**
     * Sets table style.
     *
     * @return $this
     */
    public function setStyle(TableStyle|string $name): static
    {
        $this->style = $this->resolveStyle($name);

        return $this;
    }

    /**
     * Gets the current table style.
     */
    public function getStyle(): TableStyle
    {
        return $this->style;
    }

    /**
     * Sets table column style.
     *
     * @param TableStyle|string $name The style name or a TableStyle instance
     *
     * @return $this
     */
    public function setColumnStyle(int $columnIndex, TableStyle|string $name): static
    {
        $this->columnStyles[$columnIndex] = $this->resolveStyle($name);

        return $this;
    }

    /**
     * Gets the current style for a column.
     *
     * If style was not set, it returns the global table style.
     */
    public function getColumnStyle(int $columnIndex): TableStyle
    {
        return $this->columnStyles[$columnIndex] ?? $this->getStyle();
    }

    /**
     * Sets the minimum width of a column.
     *
     * @return $this
     */
    public function setColumnWidth(int $columnIndex, int $width): static
    {
        $this->columnWidths[$columnIndex] = $width;

        return $this;
    }

    /**
     * Sets the minimum width of all columns.
     *
     * @return $this
     */
    public function setColumnWidths(array $widths): static
    {
        $this->columnWidths = [];
        foreach ($widths as $index => $width) {
            $this->setColumnWidth($index, $width);
        }

        return $this;
    }

    /**
     * Sets the maximum width of a column.
     *
     * Any cell within this column which contents exceeds the specified width will be wrapped into multiple lines, while
     * formatted strings are preserved.
     *
     * @return $this
     */
    public function setColumnMaxWidth(int $columnIndex, int $width): static
    {
        if (!$this->output->getFormatter() instanceof WrappableOutputFormatterInterface) {
            throw new \LogicException(\sprintf('Setting a maximum column width is only supported when using a "%s" formatter, got "%s".', WrappableOutputFormatterInterface::class, get_debug_type($this->output->getFormatter())));
        }

        $this->columnMaxWidths[$columnIndex] = $width;

        return $this;
    }

    /**
     * @return $this
     */
    public function setHeaders(array $headers): static
    {
        $headers = array_values($headers);
        if ($headers && !\is_array($headers[0])) {
            $headers = [$headers];
        }

        $this->headers = $headers;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRows(array $rows): static
    {
        $this->rows = [];

        return $this->addRows($rows);
    }

    /**
     * @return $this
     */
    public function addRows(array $rows): static
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addRow(TableSeparator|array $row): static
    {
        if ($row instanceof TableSeparator) {
            $this->rows[] = $row;

            return $this;
        }

        $this->rows[] = array_values($row);

        return $this;
    }

    /**
     * Adds a row to the table, and re-renders the table.
     *
     * @return $this
     */
    public function appendRow(TableSeparator|array $row): static
    {
        if (!$this->output instanceof ConsoleSectionOutput) {
            throw new RuntimeException(\sprintf('Output should be an instance of "%s" when calling "%s".', ConsoleSectionOutput::class, __METHOD__));
        }

        if ($this->rendered) {
            $this->output->clear($this->calculateRowCount());
        }

        $this->addRow($row);
        $this->render();

        return $this;
    }

    /**
     * @return $this
     */
    public function setRow(int|string $column, array $row): static
    {
        $this->rows[$column] = $row;

        return $this;
    }

    /**
     * @return $this
     */
    public function setHeaderTitle(?string $title): static
    {
        $this->headerTitle = $title;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFooterTitle(?string $title): static
    {
        $this->footerTitle = $title;

        return $this;
    }

    /**
     * @return $this
     */
    public function setHorizontal(bool $horizontal = true): static
    {
        $this->displayOrientation = $horizontal ? self::DISPLAY_ORIENTATION_HORIZONTAL : self::DISPLAY_ORIENTATION_DEFAULT;

        return $this;
    }

    /**
     * @return $this
     */
    public function setVertical(bool $vertical = true): static
    {
        $this->displayOrientation = $vertical ? self::DISPLAY_ORIENTATION_VERTICAL : self::DISPLAY_ORIENTATION_DEFAULT;

        return $this;
    }

    /**
     * Renders table to output.
     *
     * Example:
     *
     *     +---------------+-----------------------+------------------+
     *     | ISBN          | Title                 | Author           |
     *     +---------------+-----------------------+------------------+
     *     | 99921-58-10-7 | Divine Comedy         | Dante Alighieri  |
     *     | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
     *     | 960-425-059-0 | The Lord of the Rings | J. R. R. Tolkien |
     *     +---------------+-----------------------+------------------+
     */
    public function render(): void
    {
        $divider = new TableSeparator();
        $isCellWithColspan = static fn ($cell) => $cell instanceof TableCell && $cell->getColspan() >= 2;

        $horizontal = self::DISPLAY_ORIENTATION_HORIZONTAL === $this->displayOrientation;
        $vertical = self::DISPLAY_ORIENTATION_VERTICAL === $this->displayOrientation;

        $rows = [];
        if ($horizontal) {
            foreach ($this->headers[0] ?? [] as $i => $header) {
                $rows[$i] = [$header];
                foreach ($this->rows as $row) {
                    if ($row instanceof TableSeparator) {
                        continue;
                    }
                    if (isset($row[$i])) {
                        $rows[$i][] = $row[$i];
                    } elseif ($isCellWithColspan($rows[$i][0])) {
                        // Noop, there is a "title"
                    } else {
                        $rows[$i][] = null;
                    }
                }
            }
        } elseif ($vertical) {
            $formatter = $this->output->getFormatter();
            $maxHeaderLength = array_reduce($this->headers[0] ?? [], static fn ($max, $header) => max($max, Helper::width(Helper::removeDecoration($formatter, $header))), 0);

            foreach ($this->rows as $row) {
                if ($row instanceof TableSeparator) {
                    continue;
                }

                if ($rows) {
                    $rows[] = [$divider];
                }

                $containsColspan = false;
                foreach ($row as $cell) {
                    if ($containsColspan = $isCellWithColspan($cell)) {
                        break;
                    }
                }

                $headers = $this->headers[0] ?? [];
                $maxRows = max(\count($headers), \count($row));
                for ($i = 0; $i < $maxRows; ++$i) {
                    $cell = (string) ($row[$i] ?? '');

                    $eol = str_contains($cell, "\r\n") ? "\r\n" : "\n";
                    $parts = explode($eol, $cell);
                    foreach ($parts as $idx => $part) {
                        if ($headers && !$containsColspan) {
                            if (0 === $idx) {
                                $rows[] = [\sprintf(
                                    '<comment>%s%s</>: %s',
                                    str_repeat(' ', $maxHeaderLength - Helper::width(Helper::removeDecoration($formatter, $headers[$i] ?? ''))),
                                    $headers[$i] ?? '',
                                    $part
                                )];
                            } else {
                                $rows[] = [\sprintf(
                                    '%s  %s',
                                    str_pad('', $maxHeaderLength, ' ', \STR_PAD_LEFT),
                                    $part
                                )];
                            }
                        } elseif ('' !== $cell) {
                            $rows[] = [$part];
                        }
                    }
                }
            }
        } else {
            $rows = array_merge($this->headers, [$divider], $this->rows);
        }

        $this->calculateNumberOfColumns($rows);

        $rowGroups = $this->buildTableRows($rows);
        $this->calculateColumnsWidth($rowGroups);

        $isHeader = !$horizontal;
        $isFirstRow = $horizontal;
        $hasTitle = (bool) $this->headerTitle;

        foreach ($rowGroups as $rowGroup) {
            $isHeaderSeparatorRendered = false;

            foreach ($rowGroup as $row) {
                if ($divider === $row) {
                    $isHeader = false;
                    $isFirstRow = true;

                    continue;
                }

                if ($row instanceof TableSeparator) {
                    $this->renderRowSeparator();

                    continue;
                }

                if (!$row) {
                    continue;
                }

                if ($isHeader && !$isHeaderSeparatorRendered && $this->style->displayOutsideBorder()) {
                    $this->renderRowSeparator(
                        self::SEPARATOR_TOP,
                        $hasTitle ? $this->headerTitle : null,
                        $hasTitle ? $this->style->getHeaderTitleFormat() : null
                    );
                    $hasTitle = false;
                    $isHeaderSeparatorRendered = true;
                }

                if ($isFirstRow) {
                    $this->renderRowSeparator(
                        $horizontal ? self::SEPARATOR_TOP : self::SEPARATOR_TOP_BOTTOM,
                        $hasTitle ? $this->headerTitle : null,
                        $hasTitle ? $this->style->getHeaderTitleFormat() : null
                    );
                    $isFirstRow = false;
                    $hasTitle = false;
                }

                if ($vertical) {
                    $isHeader = false;
                    $isFirstRow = false;
                }

                if ($horizontal) {
                    $this->renderRow($row, $this->style->getCellRowFormat(), $this->style->getCellHeaderFormat());
                } else {
                    $this->renderRow($row, $isHeader ? $this->style->getCellHeaderFormat() : $this->style->getCellRowFormat());
                }
            }
        }

        if ($this->getStyle()->displayOutsideBorder()) {
            $this->renderRowSeparator(self::SEPARATOR_BOTTOM, $this->footerTitle, $this->style->getFooterTitleFormat());
        }

        $this->cleanup();
        $this->rendered = true;
    }

    /**
     * Renders horizontal header separator.
     *
     * Example:
     *
     *     +-----+-----------+-------+
     */
    private function renderRowSeparator(int $type = self::SEPARATOR_MID, ?string $title = null, ?string $titleFormat = null): void
    {
        if (!$count = $this->numberOfColumns) {
            return;
        }

        $borders = $this->style->getBorderChars();
        if (!$borders[0] && !$borders[2] && !$this->style->getCrossingChar()) {
            return;
        }

        $crossings = $this->style->getCrossingChars();
        if (self::SEPARATOR_MID === $type) {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[2], $crossings[8], $crossings[0], $crossings[4]];
        } elseif (self::SEPARATOR_TOP === $type) {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[0], $crossings[1], $crossings[2], $crossings[3]];
        } elseif (self::SEPARATOR_TOP_BOTTOM === $type) {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[0], $crossings[9], $crossings[10], $crossings[11]];
        } else {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[0], $crossings[7], $crossings[6], $crossings[5]];
        }

        $markup = $leftChar;
        for ($column = 0; $column < $count; ++$column) {
            $markup .= str_repeat($horizontal, $this->effectiveColumnWidths[$column]);
            $markup .= $column === $count - 1 ? $rightChar : $midChar;
        }

        if (null !== $title) {
            $titleLength = Helper::width(Helper::removeDecoration($formatter = $this->output->getFormatter(), $formattedTitle = \sprintf($titleFormat, $title)));
            $markupLength = Helper::width($markup);
            if ($titleLength > $limit = $markupLength - 4) {
                $titleLength = $limit;
                $formatLength = Helper::width(Helper::removeDecoration($formatter, \sprintf($titleFormat, '')));
                $formattedTitle = \sprintf($titleFormat, Helper::substr($title, 0, $limit - $formatLength - 3).'...');
            }

            $titleStart = intdiv($markupLength - $titleLength, 2);
            if (false === mb_detect_encoding($markup, null, true)) {
                $markup = substr_replace($markup, $formattedTitle, $titleStart, $titleLength);
            } else {
                $markup = mb_substr($markup, 0, $titleStart).$formattedTitle.mb_substr($markup, $titleStart + $titleLength);
            }
        }

        $this->output->writeln(\sprintf($this->style->getBorderFormat(), $markup));
    }

    /**
     * Renders vertical column separator.
     */
    private function renderColumnSeparator(int $type = self::BORDER_OUTSIDE): string
    {
        $borders = $this->style->getBorderChars();

        return \sprintf($this->style->getBorderFormat(), self::BORDER_OUTSIDE === $type ? $borders[1] : $borders[3]);
    }

    /**
     * Renders table row.
     *
     * Example:
     *
     *     | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
     */
    private function renderRow(array $row, string $cellFormat, ?string $firstCellFormat = null): void
    {
        $rowContent = $this->renderColumnSeparator(self::BORDER_OUTSIDE);
        $columns = $this->getRowColumns($row);
        $last = \count($columns) - 1;
        foreach ($columns as $i => $column) {
            if ($firstCellFormat && 0 === $i) {
                $rowContent .= $this->renderCell($row, $column, $firstCellFormat);
            } else {
                $rowContent .= $this->renderCell($row, $column, $cellFormat);
            }
            $rowContent .= $this->renderColumnSeparator($last === $i ? self::BORDER_OUTSIDE : self::BORDER_INSIDE);
        }
        $this->output->writeln($rowContent);
    }

    /**
     * Renders table cell with padding.
     */
    private function renderCell(array $row, int $column, string $cellFormat): string
    {
        $cell = $row[$column] ?? '';
        $width = $this->effectiveColumnWidths[$column];
        if ($cell instanceof TableCell && $cell->getColspan() > 1) {
            // add the width of the following columns(numbers of colspan).
            foreach (range($column + 1, $column + $cell->getColspan() - 1) as $nextColumn) {
                $width += $this->getColumnSeparatorWidth() + $this->effectiveColumnWidths[$nextColumn];
            }
        }

        // str_pad won't work properly with multi-byte strings, we need to fix the padding
        $width += \strlen($cell) - Helper::width($cell) - substr_count($cell, "\0");
        $style = $this->getColumnStyle($column);

        if ($cell instanceof TableSeparator) {
            return \sprintf($style->getBorderFormat(), str_repeat($style->getBorderChars()[2], $width));
        }

        $width += Helper::length($cell) - Helper::length(Helper::removeDecoration($this->output->getFormatter(), $cell));
        $content = \sprintf($style->getCellRowContentFormat(), $cell);

        $padType = $style->getPadType();
        if ($cell instanceof TableCell && $cell->getStyle() instanceof TableCellStyle) {
            $isNotStyledByTag = !preg_match('/^<(\w+|(\w+=[\w,]+;?)*)>.+<\/(\w+|(\w+=\w+;?)*)?>$/', $cell);
            if ($isNotStyledByTag) {
                $cellFormat = $cell->getStyle()->getCellFormat();
                if (!\is_string($cellFormat)) {
                    $tag = http_build_query($cell->getStyle()->getTagOptions(), '', ';');
                    $cellFormat = '<'.$tag.'>%s</>';
                }

                if (str_contains($content, '</>')) {
                    $content = str_replace('</>', '', $content);
                    $width -= 3;
                }
                if (str_contains($content, '<fg=default;bg=default>')) {
                    $content = str_replace('<fg=default;bg=default>', '', $content);
                    $width -= \strlen('<fg=default;bg=default>');
                }
            }

            $padType = $cell->getStyle()->getPadByAlign();
        }

        return \sprintf($cellFormat, str_pad($content, $width, $style->getPaddingChar(), $padType));
    }

    /**
     * Calculate number of columns for this table.
     */
    private function calculateNumberOfColumns(array $rows): void
    {
        $columns = [0];
        foreach ($rows as $row) {
            if ($row instanceof TableSeparator) {
                continue;
            }

            $columns[] = $this->getNumberOfColumns($row);
        }

        $this->numberOfColumns = max($columns);
    }

    private function buildTableRows(array $rows): TableRows
    {
        /** @var WrappableOutputFormatterInterface $formatter */
        $formatter = $this->output->getFormatter();
        $unmergedRows = [];
        for ($rowKey = 0; $rowKey < \count($rows); ++$rowKey) {
            $rows = $this->fillNextRows($rows, $rowKey);

            // Remove any new line breaks and replace it with a new line
            foreach ($rows[$rowKey] as $column => $cell) {
                $colspan = $cell instanceof TableCell ? $cell->getColspan() : 1;

                $minWrappedWidth = 0;
                $widthApplied = [];
                $lengthColumnBorder = $this->getColumnSeparatorWidth() + Helper::width($this->style->getCellRowContentFormat()) - 2;
                for ($i = $column; $i < ($column + $colspan); ++$i) {
                    if (isset($this->columnMaxWidths[$i])) {
                        $minWrappedWidth += $this->columnMaxWidths[$i];
                        $widthApplied[] = ['type' => 'max', 'column' => $i];
                    } elseif (($this->columnWidths[$i] ?? 0) > 0 && $colspan > 1) {
                        $minWrappedWidth += $this->columnWidths[$i];
                        $widthApplied[] = ['type' => 'min', 'column' => $i];
                    }
                }
                if (1 === \count($widthApplied)) {
                    if ($colspan > 1) {
                        $minWrappedWidth *= $colspan;  // previous logic
                    }
                } elseif (\count($widthApplied) > 1) {
                    $minWrappedWidth += (\count($widthApplied) - 1) * $lengthColumnBorder;
                }

                $cellWidth = Helper::width(Helper::removeDecoration($formatter, $cell));
                if ($minWrappedWidth && $cellWidth > $minWrappedWidth) {
                    $cell = $formatter->formatAndWrap($cell, $minWrappedWidth);
                }
                // update minimal columnWidths for spanned columns
                if ($colspan > 1 && $minWrappedWidth > 0) {
                    $columnsMinWidthProcessed = [];
                    $cellWidth = min($cellWidth, $minWrappedWidth);
                    foreach ($widthApplied as $item) {
                        if ('max' === $item['type'] && $cellWidth >= $this->columnMaxWidths[$item['column']]) {
                            $minWidthColumn = $this->columnMaxWidths[$item['column']];
                            $this->columnWidths[$item['column']] = $minWidthColumn;
                            $columnsMinWidthProcessed[$item['column']] = true;
                            $cellWidth -= $minWidthColumn + $lengthColumnBorder;
                        }
                    }
                    for ($i = $column; $i < ($column + $colspan); ++$i) {
                        if (isset($columnsMinWidthProcessed[$i])) {
                            continue;
                        }
                        $this->columnWidths[$i] = $cellWidth + $lengthColumnBorder;
                    }
                }
                if (!str_contains($cell ?? '', "\n")) {
                    continue;
                }
                $eol = str_contains($cell ?? '', "\r\n") ? "\r\n" : "\n";
                $escaped = implode($eol, array_map(OutputFormatter::escapeTrailingBackslash(...), explode($eol, $cell)));
                $cell = $cell instanceof TableCell ? new TableCell($escaped, ['colspan' => $cell->getColspan()]) : $escaped;
                $lines = explode($eol, str_replace($eol, '<fg=default;bg=default></>'.$eol, $cell));
                foreach ($lines as $lineKey => $line) {
                    if ($colspan > 1) {
                        $line = new TableCell($line, ['colspan' => $colspan]);
                    }
                    if (0 === $lineKey) {
                        $rows[$rowKey][$column] = $line;
                    } else {
                        if (!\array_key_exists($rowKey, $unmergedRows) || !\array_key_exists($lineKey, $unmergedRows[$rowKey])) {
                            $unmergedRows[$rowKey][$lineKey] = $this->copyRow($rows, $rowKey);
                        }
                        $unmergedRows[$rowKey][$lineKey][$column] = $line;
                    }
                }
            }
        }

        return new TableRows(function () use ($rows, $unmergedRows): \Traversable {
            foreach ($rows as $rowKey => $row) {
                $rowGroup = [$row instanceof TableSeparator ? $row : $this->fillCells($row)];

                if (isset($unmergedRows[$rowKey])) {
                    foreach ($unmergedRows[$rowKey] as $row) {
                        $rowGroup[] = $row instanceof TableSeparator ? $row : $this->fillCells($row);
                    }
                }
                yield $rowGroup;
            }
        });
    }

    private function calculateRowCount(): int
    {
        $numberOfRows = \count(iterator_to_array($this->buildTableRows(array_merge($this->headers, [new TableSeparator()], $this->rows))));

        if ($this->headers) {
            ++$numberOfRows; // Add row for header separator
        }

        if ($this->rows) {
            ++$numberOfRows; // Add row for footer separator
        }

        return $numberOfRows;
    }

    /**
     * fill rows that contains rowspan > 1.
     *
     * @throws InvalidArgumentException
     */
    private function fillNextRows(array $rows, int $line): array
    {
        $unmergedRows = [];
        foreach ($rows[$line] as $column => $cell) {
            if (null !== $cell && !$cell instanceof TableCell && !\is_scalar($cell) && !$cell instanceof \Stringable) {
                throw new InvalidArgumentException(\sprintf('A cell must be a TableCell, a scalar or an object implementing "__toString()", "%s" given.', get_debug_type($cell)));
            }
            if ($cell instanceof TableCell && $cell->getRowspan() > 1) {
                $nbLines = $cell->getRowspan() - 1;
                $lines = [$cell];
                if (str_contains($cell, "\n")) {
                    $eol = str_contains($cell, "\r\n") ? "\r\n" : "\n";
                    $lines = explode($eol, str_replace($eol, '<fg=default;bg=default>'.$eol.'</>', $cell));
                    $nbLines = \count($lines) > $nbLines ? substr_count($cell, $eol) : $nbLines;

                    $rows[$line][$column] = new TableCell($lines[0], ['colspan' => $cell->getColspan(), 'style' => $cell->getStyle()]);
                    unset($lines[0]);
                }

                // create a two dimensional array (rowspan x colspan)
                $unmergedRows = array_replace_recursive(array_fill($line + 1, $nbLines, []), $unmergedRows);
                foreach ($unmergedRows as $unmergedRowKey => $unmergedRow) {
                    $value = $lines[$unmergedRowKey - $line] ?? '';
                    $unmergedRows[$unmergedRowKey][$column] = new TableCell($value, ['colspan' => $cell->getColspan(), 'style' => $cell->getStyle()]);
                    if ($nbLines === $unmergedRowKey - $line) {
                        break;
                    }
                }
            }
        }

        foreach ($unmergedRows as $unmergedRowKey => $unmergedRow) {
            // we need to know if $unmergedRow will be merged or inserted into $rows
            if (isset($rows[$unmergedRowKey]) && \is_array($rows[$unmergedRowKey]) && ($this->getNumberOfColumns($rows[$unmergedRowKey]) + $this->getNumberOfColumns($unmergedRow) <= $this->numberOfColumns)) {
                foreach ($unmergedRow as $cellKey => $cell) {
                    // insert cell into row at cellKey position
                    array_splice($rows[$unmergedRowKey], $cellKey, 0, [$cell]);
                }
            } else {
                $row = $this->copyRow($rows, $unmergedRowKey - 1);
                foreach ($unmergedRow as $column => $cell) {
                    if ($cell) {
                        $row[$column] = $cell;
                    }
                }
                array_splice($rows, $unmergedRowKey, 0, [$row]);
            }
        }

        return $rows;
    }

    /**
     * fill cells for a row that contains colspan > 1.
     */
    private function fillCells(iterable $row): iterable
    {
        $newRow = [];

        foreach ($row as $column => $cell) {
            $newRow[] = $cell;
            if ($cell instanceof TableCell && $cell->getColspan() > 1) {
                foreach (range($column + 1, $column + $cell->getColspan() - 1) as $position) {
                    // insert empty value at column position
                    $newRow[] = '';
                }
            }
        }

        return $newRow ?: $row;
    }

    private function copyRow(array $rows, int $line): array
    {
        $row = $rows[$line];
        foreach ($row as $cellKey => $cellValue) {
            $row[$cellKey] = '';
            if ($cellValue instanceof TableCell) {
                $row[$cellKey] = new TableCell('', ['colspan' => $cellValue->getColspan()]);
            }
        }

        return $row;
    }

    /**
     * Gets number of columns by row.
     */
    private function getNumberOfColumns(array $row): int
    {
        $columns = \count($row);
        foreach ($row as $column) {
            $columns += $column instanceof TableCell ? ($column->getColspan() - 1) : 0;
        }

        return $columns;
    }

    /**
     * Gets list of columns for the given row.
     */
    private function getRowColumns(array $row): array
    {
        $columns = range(0, $this->numberOfColumns - 1);
        foreach ($row as $cellKey => $cell) {
            if ($cell instanceof TableCell && $cell->getColspan() > 1) {
                // exclude grouped columns.
                $columns = array_diff($columns, range($cellKey + 1, $cellKey + $cell->getColspan() - 1));
            }
        }

        return $columns;
    }

    /**
     * Calculates columns widths.
     */
    private function calculateColumnsWidth(iterable $groups): void
    {
        for ($column = 0; $column < $this->numberOfColumns; ++$column) {
            $lengths = [];
            foreach ($groups as $group) {
                foreach ($group as $row) {
                    if ($row instanceof TableSeparator) {
                        continue;
                    }

                    foreach ($row as $i => $cell) {
                        if ($cell instanceof TableCell) {
                            $textContent = Helper::removeDecoration($this->output->getFormatter(), $cell);
                            $textLength = Helper::width($textContent);
                            if ($textLength > 0) {
                                $contentColumns = mb_str_split($textContent, ceil($textLength / $cell->getColspan()));
                                foreach ($contentColumns as $position => $content) {
                                    $row[$i + $position] = $content;
                                }
                            }
                        }
                    }

                    $lengths[] = $this->getCellWidth($row, $column);
                }
            }

            $this->effectiveColumnWidths[$column] = max($lengths) + Helper::width($this->style->getCellRowContentFormat()) - 2;
        }
    }

    private function getColumnSeparatorWidth(): int
    {
        return Helper::width(\sprintf($this->style->getBorderFormat(), $this->style->getBorderChars()[3]));
    }

    private function getCellWidth(array $row, int $column): int
    {
        $cellWidth = 0;

        if (isset($row[$column])) {
            $cell = $row[$column];
            $cellWidth = Helper::width(Helper::removeDecoration($this->output->getFormatter(), $cell));
        }

        $columnWidth = $this->columnWidths[$column] ?? 0;
        $cellWidth = max($cellWidth, $columnWidth);

        return isset($this->columnMaxWidths[$column]) ? min($this->columnMaxWidths[$column], $cellWidth) : $cellWidth;
    }

    /**
     * Called after rendering to cleanup cache data.
     */
    private function cleanup(): void
    {
        $this->effectiveColumnWidths = [];
        unset($this->numberOfColumns);
    }

    /**
     * @return array<string, TableStyle>
     */
    private static function initStyles(): array
    {
        $markdown = new TableStyle();
        $markdown
            ->setDefaultCrossingChar('|')
            ->setDisplayOutsideBorder(false)
        ;

        $borderless = new TableStyle();
        $borderless
            ->setHorizontalBorderChars('=')
            ->setVerticalBorderChars(' ')
            ->setDefaultCrossingChar(' ')
        ;

        $compact = new TableStyle();
        $compact
            ->setHorizontalBorderChars('')
            ->setVerticalBorderChars('')
            ->setDefaultCrossingChar('')
            ->setCellRowContentFormat('%s ')
        ;

        $styleGuide = new TableStyle();
        $styleGuide
            ->setHorizontalBorderChars('-')
            ->setVerticalBorderChars(' ')
            ->setDefaultCrossingChar(' ')
            ->setCellHeaderFormat('%s')
        ;

        $box = (new TableStyle())
            ->setHorizontalBorderChars('─')
            ->setVerticalBorderChars('│')
            ->setCrossingChars('┼', '┌', '┬', '┐', '┤', '┘', '┴', '└', '├')
        ;

        $boxDouble = (new TableStyle())
            ->setHorizontalBorderChars('═', '─')
            ->setVerticalBorderChars('║', '│')
            ->setCrossingChars('┼', '╔', '╤', '╗', '╢', '╝', '╧', '╚', '╟', '╠', '╪', '╣')
        ;

        return [
            'default' => new TableStyle(),
            'markdown' => $markdown,
            'borderless' => $borderless,
            'compact' => $compact,
            'symfony-style-guide' => $styleGuide,
            'box' => $box,
            'box-double' => $boxDouble,
        ];
    }

    private function resolveStyle(TableStyle|string $name): TableStyle
    {
        if ($name instanceof TableStyle) {
            return $name;
        }

        return self::$styles[$name] ?? throw new InvalidArgumentException(\sprintf('Style "%s" is not defined.', $name));
    }
}
