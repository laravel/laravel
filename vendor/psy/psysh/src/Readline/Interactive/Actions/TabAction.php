<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Actions;

use Psy\Completion\CompletionEngine;
use Psy\Completion\CompletionRequest;
use Psy\Completion\FuzzyMatcher;
use Psy\Readline\Interactive\Helper\CurrentWord;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\Key;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\ReadlineMode;
use Psy\Readline\Interactive\Renderer\CompletionMenuWidget;
use Psy\Readline\Interactive\Terminal;
use Psy\Util\Tty;

/**
 * Tab completion action.
 *
 * Triggers context-aware tab completion when the Tab key is pressed.
 */
class TabAction implements ActionInterface, ReadlineMode
{
    private ?CompletionEngine $completer = null;
    private bool $smartBrackets = false;

    /** @var string[] */
    private array $currentMatches = [];
    /** @var string[] */
    private array $filteredMatches = [];
    private int $selectedIndex = 0;
    private string $filterText = '';
    private ?bool $interactiveSelectionEnabled = null;

    /** @var int Viewport: first visible row when scrolling. */
    private int $scrollOffset = 0;
    /** @var bool Viewport: whether the menu has expanded to full height. */
    private bool $expanded = false;
    private int $totalRows = 0;
    private int $totalColumns = 0;

    // Per-activation refs, set in execute() and used while this is the active mode.
    private ?Terminal $terminal = null;
    private ?Readline $readline = null;

    public function __construct(?CompletionEngine $completer = null, bool $smartBrackets = false)
    {
        $this->completer = $completer;
        $this->smartBrackets = $smartBrackets;
    }

    /**
     * Set the CompletionEngine instance.
     */
    public function setCompleter(CompletionEngine $completer): void
    {
        $this->completer = $completer;
    }

    /**
     * Force-enable or disable interactive menu mode (for tests).
     *
     * @internal
     */
    public function setInteractiveSelectionEnabled(?bool $enabled): void
    {
        $this->interactiveSelectionEnabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($this->completer === null) {
            $terminal->bell();

            return true;
        }

        $text = $buffer->getText();
        $cursor = $buffer->getCursor();

        $matches = $this->completer->getCompletions(
            new CompletionRequest($text, $cursor, CompletionRequest::MODE_TAB)
        );

        if (empty($matches)) {
            return true;
        }

        if (\count($matches) === 1) {
            $this->insertMatch($buffer, $matches[0]);

            return true;
        }

        $commonPrefix = $this->getCommonPrefix($matches);
        $currentWord = CurrentWord::extract($text, $cursor);

        if (!empty($commonPrefix) && $commonPrefix !== $currentWord) {
            $this->insertMatch($buffer, $commonPrefix);
            $currentWord = $commonPrefix;
        }

        if ($this->interactiveSelectionEnabled !== null) {
            $isTTY = $this->interactiveSelectionEnabled;
        } else {
            $isTTY = Tty::isatty(\STDIN);
        }

        if (!$isTTY) {
            // No interactive menu without a TTY; the common-prefix insertion
            // above is the best we can do.
            return true;
        }

        $this->currentMatches = \array_values($matches);
        $this->filterText = $currentWord;
        $this->filteredMatches = $this->currentMatches;
        $this->selectedIndex = 0;
        $this->scrollOffset = 0;
        $this->expanded = false;

        $this->terminal = $terminal;
        $this->readline = $readline;
        $this->updateLayout($terminal);

        $readline->pushMode($this);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'tab-completion';
    }

    /**
     * Insert a match at the cursor position.
     */
    private function insertMatch(Buffer $buffer, string $match): void
    {
        $text = $buffer->getText();
        $cursor = $buffer->getCursor();
        $textLen = \mb_strlen($text);

        $start = $cursor;
        $hasVarPrefix = false;
        while ($start > 0) {
            $char = \mb_substr($text, $start - 1, 1);
            if (\ctype_space($char)) {
                break;
            }
            if ($char === '>' && $start >= 2 && \mb_substr($text, $start - 2, 1) === '-') {
                break;
            }
            if ($char === ':' && $start >= 2 && \mb_substr($text, $start - 2, 1) === ':') {
                break;
            }
            if ($char === '$') {
                $hasVarPrefix = true;
                $start--;
                break;
            }
            $start--;
        }

        $end = $cursor;
        while ($end < $textLen) {
            $char = \mb_substr($text, $end, 1);
            if (\ctype_space($char) || (!\ctype_alnum($char) && $char !== '_')) {
                break;
            }
            $end++;
        }

        if ($hasVarPrefix && $match[0] !== '$') {
            $match = '$'.$match;
        }

        $addParens = $this->smartBrackets && $this->shouldAddParentheses($match);

        $before = \mb_substr($text, 0, $start);
        $after = \mb_substr($text, $end);

        if ($addParens) {
            $buffer->setText($before.$match.'()'.$after);
            // Place cursor after parens for zero-arg functions, inside for others
            $offset = $this->functionHasParameters($match) ? 1 : 2;
            $buffer->setCursor($start + \mb_strlen($match) + $offset);
        } else {
            $buffer->setText($before.$match.$after);
            $buffer->setCursor($start + \mb_strlen($match));
        }
    }

    /**
     * Determine if parentheses should be added for this completion.
     */
    private function shouldAddParentheses(string $match): bool
    {
        if (\substr($match, 0, 1) === '$') {
            return false;
        }

        if (\strtoupper($match) === $match && \ctype_upper($match[0])) {
            return false;
        }

        return \function_exists($match);
    }

    /**
     * Check whether a function accepts any parameters (required or optional).
     */
    private function functionHasParameters(string $name): bool
    {
        try {
            return (new \ReflectionFunction($name))->getNumberOfParameters() > 0;
        } catch (\ReflectionException $e) {
            // If we can't reflect it, assume it has parameters
            return true;
        }
    }

    /**
     * {@inheritdoc}
     *
     * One iteration of the menu loop: update selection / filter / scroll
     * for the key, or return false/null to leave the menu.
     */
    public function handleKey(Key $key, Buffer $buffer): ?bool
    {
        $keyStr = (string) $key;
        $keyValue = $key->getValue();

        if ($keyStr === 'escape:[A' || $keyValue === "\x10") { // Up or Ctrl-P
            $this->moveSelection(-1, $this->readline);

            return true;
        }

        if ($keyStr === 'escape:[B' || $keyValue === "\x0e") { // Down or Ctrl-N
            $this->moveSelection(1, $this->readline);

            return true;
        }

        if ($keyStr === 'escape:[C' || $keyValue === "\x06") { // Right or Ctrl-F
            $this->moveSelectionHorizontal(1, $this->readline);

            return true;
        }

        if ($keyStr === 'escape:[D' || $keyValue === "\x02") { // Left or Ctrl-B
            $this->moveSelectionHorizontal(-1, $this->readline);

            return true;
        }

        if ($keyValue === "\t") {
            // Tab cycles to the next match
            $this->moveSelection(1, $this->readline);

            return true;
        }

        if ($keyValue === "\r" || $keyValue === "\n") {
            if (!empty($this->filteredMatches)) {
                $selected = $this->filteredMatches[$this->selectedIndex];
                $this->insertMatch($buffer, $selected);
            }

            return false;
        }

        if ($keyValue === "\e" || $keyValue === "\x03") { // Escape or Ctrl-C
            return false;
        }

        if ($keyValue === "\x7f" || $keyValue === "\x08") { // Backspace
            if ($buffer->getCursor() === 0) {
                return null;
            }

            $buffer->deleteBackward();
            $this->filterText = CurrentWord::extract($buffer->getText(), $buffer->getCursor());

            if ($this->filterText === '') {
                return false;
            }

            $this->updateFilter($this->terminal);

            return true;
        }

        if ($key->isChar() && $keyValue !== '') {
            $buffer->insert($keyValue);

            if ($keyValue === ' ') {
                return false;
            }

            $this->filterText = CurrentWord::extract($buffer->getText(), $buffer->getCursor());
            $this->updateFilter($this->terminal);

            return true;
        }

        // Unknown key: leave the menu and let the outer loop reprocess it.
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function display(Buffer $buffer): void
    {
        $this->updateOverlay($buffer, $this->terminal, $this->readline);
    }

    /**
     * {@inheritdoc}
     */
    public function onExit(): void
    {
        $this->currentMatches = [];
        $this->filteredMatches = [];
        $this->filterText = '';
        $this->selectedIndex = 0;
        $this->scrollOffset = 0;
        $this->expanded = false;
        $this->totalRows = 0;
        $this->totalColumns = 0;
        $this->terminal = null;
        $this->readline = null;
    }

    /**
     * Generate overlay lines from matches and render the frame.
     */
    private function updateOverlay(Buffer $buffer, Terminal $terminal, Readline $readline): void
    {
        // Terminal width may change while the menu is open.
        $this->updateLayout($terminal);

        $maxRows = $this->getMaxRows($readline);
        if ($maxRows === null) {
            $this->scrollOffset = 0;
        } else {
            $maxOffset = \max(0, $this->totalRows - $maxRows);
            $this->scrollOffset = \max(0, \min($this->scrollOffset, $maxOffset));
        }

        $readline->setOverlay($buffer, new CompletionMenuWidget(
            $terminal,
            $this->filteredMatches,
            $this->selectedIndex,
            $this->scrollOffset,
            $this->expanded,
        ));
    }

    /**
     * Update filtered matches based on current filter text.
     */
    private function updateFilter(?Terminal $terminal = null): void
    {
        $previousSelection = $this->filteredMatches[$this->selectedIndex] ?? null;

        if ($this->filterText === '') {
            $this->filteredMatches = $this->currentMatches;
        } else {
            $this->filteredMatches = FuzzyMatcher::filter($this->filterText, $this->currentMatches);
        }

        // Keep the same item selected if it survived filtering, otherwise
        // reset to the top where the best matches are.
        $newIndex = \array_search($previousSelection, $this->filteredMatches, true);
        $this->selectedIndex = $newIndex !== false ? $newIndex : 0;

        // Reset viewport when filter changes
        $this->scrollOffset = 0;
        $this->expanded = false;

        if ($terminal !== null) {
            $this->updateLayout($terminal);
        }
    }

    /**
     * Recalculate total row count for the current matches.
     */
    private function updateLayout(Terminal $terminal): void
    {
        if (empty($this->filteredMatches)) {
            $this->totalRows = 0;
            $this->totalColumns = 0;

            return;
        }

        $layout = CompletionMenuWidget::calculateLayout($terminal, $this->filteredMatches);
        $this->totalRows = $layout['rows'];
        $this->totalColumns = $layout['columns'];
    }

    /**
     * Get the maximum visible rows for the current viewport state.
     *
     * Returns null if all rows fit without truncation.
     */
    private function getMaxRows(Readline $readline): ?int
    {
        $available = $readline->getOverlayAvailableRows(!$this->expanded);

        // Account for the blank separator line above the menu
        $menuBudget = \max(1, $available - 1);

        if ($this->totalRows <= $menuBudget) {
            // No truncation needed if everything fits (including the +1 off-by-one:
            // don't show a status line when the last row would fit in its place)
            return null;
        }

        // When truncated, reserve one row for the status line
        return \max(1, $menuBudget - 1);
    }

    /**
     * Move selection up or down, adjusting viewport as needed.
     */
    private function moveSelection(int $delta, Readline $readline): void
    {
        $count = \count($this->filteredMatches);
        if ($count === 0) {
            return;
        }

        $newIndex = $this->selectedIndex + $delta;

        // Wrap around
        if ($newIndex < 0) {
            $newIndex = $count - 1;
        } elseif ($newIndex >= $count) {
            $newIndex = 0;
        }

        $this->selectedIndex = $newIndex;
        $this->adjustViewport($readline);
    }

    /**
     * Adjust scroll offset so the selected item is visible.
     */
    private function adjustViewport(Readline $readline): void
    {
        $maxRows = $this->getMaxRows($readline);

        // Everything fits, no scrolling needed
        if ($maxRows === null) {
            $this->scrollOffset = 0;

            return;
        }

        $selectedRow = $this->selectedIndex % $this->totalRows;

        // Selection is above the visible window
        if ($selectedRow < $this->scrollOffset) {
            $this->scrollOffset = $selectedRow;

            return;
        }

        // Selection is below the visible window
        if ($selectedRow >= $this->scrollOffset + $maxRows) {
            if (!$this->expanded) {
                $this->expanded = true;
                $maxRows = $this->getMaxRows($readline);

                // After expanding, everything might fit
                if ($maxRows === null) {
                    $this->scrollOffset = 0;

                    return;
                }
            }

            // Scroll to keep selection visible at the bottom
            if ($selectedRow >= $this->scrollOffset + $maxRows) {
                $this->scrollOffset = $selectedRow - $maxRows + 1;
            }
        }

        // Clamp scroll offset
        $this->scrollOffset = \max(0, \min($this->scrollOffset, $this->totalRows - $maxRows));
    }

    /**
     * Move selection left or right (horizontal between columns).
     */
    private function moveSelectionHorizontal(int $delta, Readline $readline): void
    {
        $count = \count($this->filteredMatches);
        if ($count === 0) {
            return;
        }

        $rows = $this->totalRows;
        $columns = $this->totalColumns;

        $currentRow = $this->selectedIndex % $rows;
        $currentCol = \intdiv($this->selectedIndex, $rows);

        $newCol = $currentCol + $delta;

        // Wrap to next/previous row at column boundaries
        if ($newCol >= $columns || ($currentRow + ($newCol * $rows)) >= $count) {
            $newCol = 0;
            $currentRow++;
            if ($currentRow >= $rows) {
                $currentRow = 0;
            }
        } elseif ($newCol < 0) {
            $newCol = $columns - 1;
            $currentRow--;
            if ($currentRow < 0) {
                $currentRow = $rows - 1;
            }
        }

        $newIndex = $currentRow + ($newCol * $rows);

        // If we landed past the end (sparse last row), fall back to last column on this row
        if ($newIndex >= $count) {
            while ($newCol > 0 && $newIndex >= $count) {
                $newCol--;
                $newIndex = $currentRow + ($newCol * $rows);
            }
        }

        $this->selectedIndex = $newIndex;
        $this->adjustViewport($readline);
    }

    /**
     * Get the common prefix of all matches.
     *
     * @param string[] $matches
     */
    private function getCommonPrefix(array $matches): string
    {
        if (empty($matches)) {
            return '';
        }

        $first = \array_shift($matches);
        $prefix = '';

        for ($i = 0; $i < \mb_strlen($first, 'UTF-8'); $i++) {
            $char = \mb_substr($first, $i, 1, 'UTF-8');
            foreach ($matches as $match) {
                if (\mb_substr($match, $i, 1, 'UTF-8') !== $char) {
                    return $prefix;
                }
            }
            $prefix .= $char;
        }

        return $prefix;
    }
}
