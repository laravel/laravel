<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive;

use Psy\Output\Theme;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Input\Key;
use Psy\Readline\Interactive\Input\WordNavigationPolicy;
use Psy\Readline\Interactive\Renderer\FrameRenderer;
use Psy\Readline\Interactive\Renderer\HistorySearchOverlayWidget;
use Psy\Readline\Interactive\Renderer\OverlayViewport;

/**
 * History search state machine for interactive readline.
 *
 * Manages the search query, match list, selection, viewport scrolling,
 * and rendering for reverse history search (Ctrl-R).
 */
class HistorySearch implements ReadlineMode
{
    private Terminal $terminal;
    private History $history;
    private FrameRenderer $frameRenderer;
    private OverlayViewport $overlayViewport;
    private Theme $theme;

    private bool $active = false;
    private string $searchQuery = '';
    /** @var string[] */
    private array $searchMatches = [];
    private int $currentMatchIndex = -1;
    private int $searchScrollOffset = 0;
    private bool $searchExpanded = false;
    private ?string $savedBufferText = null;
    private int $savedCursorPosition = 0;

    public function __construct(
        Terminal $terminal,
        History $history,
        FrameRenderer $frameRenderer,
        OverlayViewport $overlayViewport,
        Theme $theme
    ) {
        $this->terminal = $terminal;
        $this->history = $history;
        $this->frameRenderer = $frameRenderer;
        $this->overlayViewport = $overlayViewport;
        $this->theme = $theme;
    }

    /**
     * Set the theme.
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Check if search mode is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Enter search mode with an optional initial query.
     */
    public function enter(string $query = ''): void
    {
        $this->active = true;
        $this->updateQuery($query);
    }

    /**
     * Exit search mode and clear state.
     */
    public function exit(): void
    {
        $this->active = false;
        $this->resetState();
        $this->savedBufferText = null;
        $this->savedCursorPosition = 0;
        $this->frameRenderer->setOverlay(null);
    }

    /**
     * {@inheritdoc}
     */
    public function onExit(): void
    {
        $this->exit();
    }

    /**
     * Save the current buffer before starting search.
     */
    public function saveBuffer(Buffer $buffer): void
    {
        $this->savedBufferText = $buffer->getText();
        $this->savedCursorPosition = $buffer->getCursor();
    }

    /**
     * {@inheritdoc}
     */
    public function handleKey(Key $key, Buffer $buffer): ?bool
    {
        $value = $key->getValue();
        $keyStr = (string) $key;

        if ($value === "\x07" || $value === "\x1b") { // Ctrl-G or Escape
            $this->cancelSearch($buffer);

            return false;
        } elseif ($value === "\r" || $value === "\n") {
            $this->acceptMatch($buffer);

            return false;
        } elseif ($keyStr === 'escape:[C' || $value === "\x06") { // Right or Ctrl-F: accept, cursor at end
            $this->acceptMatch($buffer);

            return false;
        } elseif ($keyStr === 'escape:[D' || $value === "\x02") { // Left or Ctrl-B: accept, cursor at start
            $this->acceptMatch($buffer);
            $buffer->setCursor(0);

            return false;
        } elseif ($keyStr === 'escape:[A' || $value === "\x10" || $value === "\x13") { // Up, Ctrl-P, or Ctrl-S
            $this->moveSelection(-1);

            return true;
        } elseif ($keyStr === 'escape:[B' || $value === "\x0e" || $value === "\x12") { // Down, Ctrl-N, or Ctrl-R
            $this->moveSelection(1);

            return true;
        } elseif ($value === "\x17") { // Ctrl-W: delete last word
            $this->removeWord();

            return true;
        } elseif ($value === "\x08" || $value === "\x7f") { // Backspace
            $this->removeChar();

            return true;
        } elseif ($key->isChar() && !$key->isControl()) {
            $this->addChar($value);

            return true;
        } else {
            $this->acceptMatch($buffer);

            return null;
        }
    }

    /**
     * Render the search UI with overlay.
     */
    public function display(Buffer $buffer): void
    {
        $preview = $this->getSelectedMatch() ?? '';
        $searchPrompt = $this->terminal->format('<whisper>search:</whisper>').' '.$this->searchQuery;

        $this->updateOverlay();
        $this->frameRenderer->renderSearchFrame($preview, $searchPrompt);
    }

    /**
     * Get the search query.
     */
    public function getQuery(): string
    {
        return $this->searchQuery;
    }

    /**
     * Get the number of search matches.
     */
    public function getMatchCount(): int
    {
        return \count($this->searchMatches);
    }

    /**
     * Get the search selection index.
     */
    public function getSelectedIndex(): int
    {
        return $this->currentMatchIndex;
    }

    /**
     * Get the current search match.
     *
     * @return string|null The current match, or null if no match
     */
    public function getSelectedMatch(): ?string
    {
        if (empty($this->searchMatches) || $this->currentMatchIndex < 0) {
            return null;
        }

        return $this->searchMatches[$this->currentMatchIndex] ?? null;
    }

    /**
     * Find next match in search results (older entries).
     */
    public function findNext(): void
    {
        $this->moveSelection(1);
    }

    /**
     * Find previous match in search results (newer entries).
     */
    public function findPrevious(): void
    {
        $this->moveSelection(-1);
    }

    /**
     * Update search query and find matches.
     */
    public function updateQuery(string $query): void
    {
        $this->searchQuery = $query;

        $this->searchMatches = $this->history->search($query);
        $this->currentMatchIndex = empty($this->searchMatches) ? -1 : 0;
        $this->searchScrollOffset = 0;
        $this->searchExpanded = false;
    }

    /**
     * Add a character to the search query.
     */
    private function addChar(string $char): void
    {
        $this->updateQuery($this->searchQuery.$char);
    }

    /**
     * Remove last character from search query.
     */
    private function removeChar(): void
    {
        if ($this->searchQuery !== '') {
            $this->updateQuery(\mb_substr($this->searchQuery, 0, -1));
        }
    }

    /**
     * Remove last word from search query.
     */
    private function removeWord(): void
    {
        if ($this->searchQuery === '') {
            return;
        }

        $policy = new WordNavigationPolicy();
        $boundary = $policy->findPreviousWord($this->searchQuery, \mb_strlen($this->searchQuery));
        $this->updateQuery(\mb_substr($this->searchQuery, 0, $boundary));
    }

    /**
     * Reset search query and match state.
     */
    private function resetState(): void
    {
        $this->searchQuery = '';
        $this->searchMatches = [];
        $this->currentMatchIndex = -1;
        $this->searchScrollOffset = 0;
        $this->searchExpanded = false;
    }

    /**
     * Move search selection up or down, adjusting viewport as needed.
     */
    private function moveSelection(int $delta): void
    {
        $count = \count($this->searchMatches);
        if ($count === 0) {
            $this->terminal->bell();

            return;
        }

        $newIndex = $this->currentMatchIndex + $delta;

        if ($newIndex < 0) {
            $newIndex = $count - 1;
            $this->terminal->bell();
        } elseif ($newIndex >= $count) {
            $newIndex = 0;
            $this->terminal->bell();
        }

        $this->currentMatchIndex = $newIndex;
        $this->adjustViewport();
    }

    /**
     * Accept current search match and exit search mode.
     */
    private function acceptMatch(Buffer $buffer): void
    {
        $match = $this->getSelectedMatch();
        if ($match !== null) {
            $buffer->clear();
            $buffer->insert($match);
        }

        $this->exit();
    }

    /**
     * Cancel search and restore original buffer.
     */
    private function cancelSearch(Buffer $buffer): void
    {
        if ($this->savedBufferText !== null) {
            $buffer->clear();
            $buffer->insert($this->savedBufferText);
            $buffer->setCursor($this->savedCursorPosition);
        }

        $this->exit();
    }

    /**
     * Build and set overlay lines for the current search results.
     */
    private function updateOverlay(): void
    {
        if (empty($this->searchMatches) && $this->searchQuery === '') {
            $this->frameRenderer->setOverlay(null);

            return;
        }

        $this->frameRenderer->setOverlay(new HistorySearchOverlayWidget(
            $this->terminal,
            $this->theme->replayPrompt(),
            $this->searchMatches,
            $this->currentMatchIndex,
            $this->searchScrollOffset,
            $this->searchQuery,
            $this->searchExpanded,
        ));
    }

    /**
     * Get the maximum visible rows for search results.
     *
     * Returns null if all rows fit without truncation.
     */
    private function getMaxRows(): ?int
    {
        $available = $this->overlayViewport->getAvailableRows(!$this->searchExpanded);

        // OverlayViewport already accounts for input frame + search prompt rows.
        $menuBudget = \max(1, $available);
        $totalRows = \count($this->searchMatches);

        if ($totalRows <= $menuBudget) {
            return null;
        }

        // Reserve one row for the status line
        return \max(1, $menuBudget - 1);
    }

    /**
     * Adjust search scroll offset so the selected item is visible.
     */
    private function adjustViewport(): void
    {
        $maxRows = $this->getMaxRows();

        if ($maxRows === null) {
            $this->searchScrollOffset = 0;

            return;
        }

        $selected = $this->currentMatchIndex;

        // Scrolled past the top: snap to selection
        if ($selected < $this->searchScrollOffset) {
            $this->searchScrollOffset = $selected;
        }

        // Scrolled past the bottom: try expanding the viewport first
        if ($selected >= $this->searchScrollOffset + $maxRows && !$this->searchExpanded) {
            $this->searchExpanded = true;
            $maxRows = $this->getMaxRows();

            if ($maxRows === null) {
                $this->searchScrollOffset = 0;

                return;
            }
        }

        // Still past the bottom after expanding: scroll down
        if ($selected >= $this->searchScrollOffset + $maxRows) {
            $this->searchScrollOffset = $selected - $maxRows + 1;
        }

        $count = \count($this->searchMatches);
        $this->searchScrollOffset = \max(0, \min($this->searchScrollOffset, $count - $maxRows));
    }
}
