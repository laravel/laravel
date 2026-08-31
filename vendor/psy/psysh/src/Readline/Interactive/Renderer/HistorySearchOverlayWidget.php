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
 * History search results overlay, rendered as a single-column list.
 *
 * Each item is prefixed and the selected item is highlighted. Matches
 * of the query string are emphasized inline.
 */
class HistorySearchOverlayWidget implements WidgetInterface
{
    private Terminal $terminal;
    private string $prefix;
    /** @var string[] */
    private array $items;
    private int $selectedIndex;
    private int $scrollOffset;
    private string $query;
    private bool $expanded;

    /**
     * @param string[] $items
     */
    public function __construct(
        Terminal $terminal,
        string $prefix,
        array $items,
        int $selectedIndex = 0,
        int $scrollOffset = 0,
        string $query = '',
        bool $expanded = false
    ) {
        $this->terminal = $terminal;
        $this->prefix = $prefix;
        $this->items = $items;
        $this->selectedIndex = $selectedIndex;
        $this->scrollOffset = $scrollOffset;
        $this->query = $query;
        $this->expanded = $expanded;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Frame $frame, Area $area): int
    {
        if (empty($this->items)) {
            $frame->appendLine($this->terminal->format('   <whisper>(no matches)</whisper>'));

            return 1;
        }

        $items = \array_map([History::class, 'collapseToSingleLine'], \array_values($this->items));
        $prefixWidth = DisplayString::width($this->prefix);
        $indent = \str_repeat(' ', $prefixWidth);
        $maxWidth = \max(1, $this->terminal->getWidth() - $prefixWidth);
        $totalRows = \count($items);

        $maxRows = $this->resolveMaxRows($area);
        $needsTruncation = $totalRows > $maxRows;
        $visibleRows = $needsTruncation ? \max(1, $maxRows - 1) : $totalRows;

        $startRow = $needsTruncation ? $this->scrollOffset : 0;
        $endRow = \min($totalRows, $startRow + $visibleRows);

        $consumed = 0;

        for ($row = $startRow; $row < $endRow; $row++) {
            $item = DisplayString::truncate($items[$row], $maxWidth, true);

            if ($row === $this->selectedIndex) {
                $escaped = OutputFormatter::escape($item);
                $itemWidth = DisplayString::width($item);
                $padding = \max(0, $maxWidth - $itemWidth);
                $frame->appendLine($this->terminal->format(
                    '<input_highlight>'.$this->prefix.$escaped.\str_repeat(' ', $padding).'</input_highlight>',
                ));
            } else {
                $frame->appendLine($indent.$this->highlightQuery($item));
            }
            $consumed++;
        }

        if ($needsTruncation) {
            $text = \sprintf('Items %d to %d of %d', $startRow + 1, $endRow, $totalRows);
            $frame->appendLine($this->terminal->format($indent.'<whisper>'.$text.'</whisper>'));
            $consumed++;
        }

        return $consumed;
    }

    /**
     * Resolve the visible-row budget within the given Area.
     *
     * Caps at half the terminal height in compact (non-expanded) mode.
     */
    private function resolveMaxRows(Area $area): int
    {
        $maxRows = $area->getHeight();

        if (!$this->expanded) {
            $halfTerminal = (int) \floor($this->terminal->getHeight() / 2);
            $maxRows = \min($maxRows, $halfTerminal);
        }

        return \max(1, $maxRows);
    }

    /**
     * Highlight the first occurrence of the search query in an item.
     */
    private function highlightQuery(string $item): string
    {
        if ($this->query === '') {
            return $item;
        }

        $caseSensitive = History::isSearchCaseSensitive($this->query);
        $pos = $caseSensitive
            ? \mb_strpos($item, $this->query)
            : \mb_stripos($item, $this->query);

        if ($pos === false) {
            return $item;
        }

        $before = \mb_substr($item, 0, $pos);
        $match = \mb_substr($item, $pos, \mb_strlen($this->query));
        $after = \mb_substr($item, $pos + \mb_strlen($this->query));

        return $before.$this->terminal->format('<info>'.OutputFormatter::escape($match).'</info>').$after;
    }
}
