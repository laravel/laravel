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

use Psy\Readline\Interactive\Input\ClosureKeyBindings;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Input\InputQueue;
use Psy\Readline\Interactive\Input\Key;
use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Readline\Interactive\Renderer\FrameRenderer;
use Psy\Readline\Interactive\Renderer\PagerWidget;

/**
 * Userland pager, peer to Readline.
 *
 * Presents pre-styled output lines in a scrollable viewport with
 * incremental search. Runs in the terminal's alternate screen buffer
 * for a clean takeover; on exit, the alt-screen is dismissed and a
 * subset of the buffer is emitted to the normal terminal so the user
 * ends up with the relevant content in scrollback:
 *
 *   - Graceful quit (`q`, scroll-past-bottom): emit the **whole** buffer.
 *   - Abort (Ctrl-C, Esc): emit only `0..bottom-of-current-viewport`.
 *
 * If the content fits on screen, page() prints inline without entering
 * interactive mode.
 */
class Pager
{
    private const EXIT_GRACEFUL = 'graceful';
    private const EXIT_ABORTED = 'aborted';

    private Terminal $terminal;
    private InteractiveSession $session;
    private InputQueue $inputQueue;
    private FrameRenderer $frameRenderer;

    private ClosureKeyBindings $browseBindings;
    private ClosureKeyBindings $searchInputBindings;

    /** @var string[] */
    private array $lines = [];
    /** @var string[]|null Plain-text haystacks for searching; ANSI stripped, lazily built. */
    private ?array $searchHaystacks = null;
    private int $scrollOffset = 0;

    private string $searchQuery = '';
    private bool $searchInputActive = false;
    /** @var int[] Line indices that match the current query. */
    private array $matches = [];
    private int $currentMatchIndex = -1;

    private bool $quitting = false;
    private string $exitMode = self::EXIT_GRACEFUL;

    public function __construct(
        Terminal $terminal,
        InteractiveSession $session,
        InputQueue $inputQueue,
        FrameRenderer $frameRenderer
    ) {
        $this->terminal = $terminal;
        $this->session = $session;
        $this->inputQueue = $inputQueue;
        $this->frameRenderer = $frameRenderer;

        $this->browseBindings = $this->buildBrowseBindings();
        $this->searchInputBindings = $this->buildSearchInputBindings();
    }

    /**
     * @param string[] $lines
     */
    public function page(array $lines): void
    {
        if ($lines === []) {
            return;
        }

        // Inline path: small content prints without entering interactive mode.
        if ($this->fitsInline($lines)) {
            foreach ($lines as $line) {
                $this->terminal->write($line."\n");
            }
            $this->terminal->flush();

            return;
        }

        $this->resetState($lines);

        // Track ownership so we can unwind cleanly if anything throws and
        // so we don't tear down terminal state that someone else set up.
        $startedSession = !$this->session->isActive();
        $altScreenEnabled = false;
        $mouseReportingEnabled = false;
        $completed = false;

        try {
            if ($startedSession) {
                $this->session->start();
            }
            $this->terminal->enableAltScreen();
            $altScreenEnabled = true;
            $this->terminal->enableMouseReporting();
            $mouseReportingEnabled = true;

            $this->render();

            while (!$this->quitting) {
                $key = $this->inputQueue->read();
                if ($key->isEof()) {
                    $this->exitMode = self::EXIT_ABORTED;
                    break;
                }

                $this->handleKey($key);
                if (!$this->quitting) {
                    $this->render();
                }
            }

            $completed = true;
        } finally {
            if ($mouseReportingEnabled) {
                $this->terminal->disableMouseReporting();
            }
            if ($altScreenEnabled) {
                $this->terminal->disableAltScreen();
            }
            if ($startedSession) {
                $this->session->stop();
            }

            if ($completed) {
                $this->emitToScrollback();
            }
        }
    }

    /**
     * Dispatch a single key. Public for testability; production goes
     * through page().
     *
     * @internal
     */
    public function handleKey(Key $key): void
    {
        if ($this->searchInputActive) {
            $action = $this->searchInputBindings->get($key);
            if ($action !== null) {
                $action($key);

                return;
            }
            if ($key->isChar() && !$key->isControl()) {
                $this->appendToSearch($key->getValue());
            }

            return;
        }

        $action = $this->browseBindings->get($key);
        if ($action !== null) {
            $action($key);
        }
    }

    /**
     * Total number of content lines.
     *
     * @internal exposed for tests
     */
    public function getLineCount(): int
    {
        return \count($this->lines);
    }

    /**
     * @internal exposed for tests
     */
    public function getScrollOffset(): int
    {
        return $this->scrollOffset;
    }

    /**
     * @internal exposed for tests
     */
    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    /**
     * @internal exposed for tests
     */
    public function isSearchInputActive(): bool
    {
        return $this->searchInputActive;
    }

    /**
     * @internal exposed for tests
     */
    public function isQuitting(): bool
    {
        return $this->quitting;
    }

    /**
     * @internal exposed for tests
     */
    public function getMatchCount(): int
    {
        return \count($this->matches);
    }

    /**
     * @internal exposed for tests
     */
    public function getExitMode(): string
    {
        return $this->exitMode;
    }

    /**
     * @internal exposed for tests
     *
     * @param string[] $lines
     */
    public function resetState(array $lines): void
    {
        $this->lines = \array_values($lines);
        $this->searchHaystacks = null;
        $this->scrollOffset = 0;
        $this->searchQuery = '';
        $this->searchInputActive = false;
        $this->matches = [];
        $this->currentMatchIndex = -1;
        $this->quitting = false;
        $this->exitMode = self::EXIT_GRACEFUL;
    }

    /**
     * Emit the scrollback slice appropriate to how the pager exited.
     *
     *   - Graceful: full buffer.
     *   - Aborted: lines 0..bottom-of-current-viewport, where the
     *     viewport bottom is computed wrap-aware so what gets emitted
     *     matches what was actually visible (including any wrapped
     *     content).
     */
    private function emitToScrollback(): void
    {
        $stop = $this->exitMode === self::EXIT_ABORTED
            ? $this->viewportEndLine()
            : \count($this->lines);

        for ($i = 0; $i < $stop; $i++) {
            $this->terminal->write($this->lines[$i]."\n");
        }
        $this->terminal->flush();
    }

    private function render(): void
    {
        $widget = new PagerWidget(
            $this->terminal,
            $this->frameRenderer->getLineMetrics(),
            $this->lines,
            $this->scrollOffset,
            $this->searchQuery,
            $this->searchInputActive,
            null,
            \count($this->matches),
            $this->currentMatchIndex,
        );
        $this->frameRenderer->renderFullScreenWidget($widget);
    }

    private function viewportHeight(): int
    {
        // Reserve one row for the status line.
        return \max(1, $this->terminal->getHeight() - 1);
    }

    /**
     * Check whether content fits in the non-interactive inline path,
     * accounting for soft-wrapped rows rather than logical line count.
     *
     * @param string[] $lines
     */
    private function fitsInline(array $lines): bool
    {
        $maxRows = \max(1, $this->terminal->getHeight() - 2);
        $lineMetrics = $this->frameRenderer->getLineMetrics();
        $rows = 0;

        foreach ($lines as $line) {
            $rows += $lineMetrics->lineRowCount($line);
            if ($rows > $maxRows) {
                return false;
            }
        }

        return true;
    }

    /**
     * Exclusive end index of lines currently visible in the viewport,
     * accounting for soft-wrap (one logical line may consume multiple
     * visual rows).
     *
     * Mirrors PagerWidget's render loop: if the first line at scrollOffset
     * is taller than the entire viewport, the widget shows a truncated
     * preview and advances past it, so we count it as visible too.
     */
    private function viewportEndLine(): int
    {
        $viewportRows = $this->viewportHeight();
        $totalLines = \count($this->lines);
        $lineMetrics = $this->frameRenderer->getLineMetrics();

        $rowsUsed = 0;
        $i = $this->scrollOffset;
        while ($i < $totalLines) {
            $rows = $lineMetrics->lineRowCount($this->lines[$i]);
            if ($rowsUsed + $rows > $viewportRows) {
                // Oversized first visible line: PagerWidget renders a
                // truncated preview of it, so include it in the slice.
                if ($rowsUsed === 0) {
                    $i++;
                }
                break;
            }
            $rowsUsed += $rows;
            $i++;
        }

        return $i;
    }

    /**
     * Largest scroll offset that still produces a useful viewport: i.e.
     * the smallest offset such that lines from there to the end fill at
     * most the viewport budget. Capped at totalLines-1 so the last logical
     * line is always reachable, even when it alone exceeds the viewport.
     */
    private function maxScrollOffset(): int
    {
        $viewportRows = $this->viewportHeight();
        $totalLines = \count($this->lines);
        if ($totalLines === 0) {
            return 0;
        }
        $lineMetrics = $this->frameRenderer->getLineMetrics();

        $rowsAccum = 0;
        for ($i = $totalLines - 1; $i >= 0; $i--) {
            $rowsAccum += $lineMetrics->lineRowCount($this->lines[$i]);
            if ($rowsAccum > $viewportRows) {
                // Keep the last logical line addressable rather than
                // scrolling one past the end into a blank viewport.
                return \min($i + 1, $totalLines - 1);
            }
        }

        return 0;
    }

    private function scrollDownLines(int $count): void
    {
        $max = $this->maxScrollOffset();
        if ($this->scrollOffset >= $max) {
            // Already at the bottom; treat further downward scrolling as
            // "done reading" and quit gracefully.
            $this->quitGracefully();

            return;
        }
        $this->scrollOffset = \min($max, $this->scrollOffset + $count);
    }

    private function scrollUpLines(int $count): void
    {
        $this->scrollOffset = \max(0, $this->scrollOffset - $count);
    }

    private function jumpToTop(): void
    {
        $this->scrollOffset = 0;
    }

    private function jumpToBottom(): void
    {
        $this->scrollOffset = $this->maxScrollOffset();
    }

    private function quitGracefully(): void
    {
        $this->exitMode = self::EXIT_GRACEFUL;
        $this->quitting = true;
    }

    private function abort(): void
    {
        $this->exitMode = self::EXIT_ABORTED;
        $this->quitting = true;
    }

    private function beginSearch(): void
    {
        $this->searchInputActive = true;
        $this->searchQuery = '';
        $this->matches = [];
        $this->currentMatchIndex = -1;
    }

    private function commitSearch(): void
    {
        $this->searchInputActive = false;
        $this->scrollToCurrentMatch();
    }

    private function cancelSearch(): void
    {
        $this->searchInputActive = false;
        $this->searchQuery = '';
        $this->matches = [];
        $this->currentMatchIndex = -1;
    }

    private function appendToSearch(string $char): void
    {
        $this->searchQuery .= $char;
        $this->recomputeMatches();
    }

    private function trimSearch(): void
    {
        if ($this->searchQuery === '') {
            return;
        }
        $this->searchQuery = \mb_substr($this->searchQuery, 0, -1, 'UTF-8');
        if ($this->searchQuery === '') {
            $this->matches = [];
            $this->currentMatchIndex = -1;

            return;
        }
        $this->recomputeMatches();
    }

    private function recomputeMatches(): void
    {
        $this->matches = [];
        if ($this->searchQuery === '') {
            $this->currentMatchIndex = -1;

            return;
        }

        // Mid-multibyte input arrives one byte at a time from fgetc(); skip
        // the search until the query is a complete UTF-8 sequence so we don't
        // call mb_* on invalid bytes (which warns and clears matches).
        if (!\mb_check_encoding($this->searchQuery, 'UTF-8')) {
            return;
        }

        $caseSensitive = History::isSearchCaseSensitive($this->searchQuery);
        $needle = $caseSensitive ? $this->searchQuery : \mb_strtolower($this->searchQuery, 'UTF-8');

        foreach ($this->getSearchHaystacks() as $i => $haystack) {
            if (!$caseSensitive) {
                $haystack = \mb_strtolower($haystack, 'UTF-8');
            }
            if (\mb_strpos($haystack, $needle, 0, 'UTF-8') !== false) {
                $this->matches[] = $i;
            }
        }

        $this->currentMatchIndex = empty($this->matches) ? -1 : 0;
        $this->scrollToCurrentMatch();
    }

    /**
     * @return string[]
     */
    private function getSearchHaystacks(): array
    {
        if ($this->searchHaystacks === null) {
            $this->searchHaystacks = \array_map(
                fn (string $line) => DisplayString::stripAnsi($line),
                $this->lines,
            );
        }

        return $this->searchHaystacks;
    }

    private function nextMatch(): void
    {
        if (empty($this->matches)) {
            return;
        }
        $this->currentMatchIndex = ($this->currentMatchIndex + 1) % \count($this->matches);
        $this->scrollToCurrentMatch();
    }

    private function previousMatch(): void
    {
        if (empty($this->matches)) {
            return;
        }
        $count = \count($this->matches);
        $this->currentMatchIndex = ($this->currentMatchIndex - 1 + $count) % $count;
        $this->scrollToCurrentMatch();
    }

    private function scrollToCurrentMatch(): void
    {
        if ($this->currentMatchIndex < 0 || empty($this->matches)) {
            return;
        }
        $line = $this->matches[$this->currentMatchIndex];

        // Above the viewport: scroll up so the match becomes the top line.
        if ($line < $this->scrollOffset) {
            $this->scrollOffset = $line;

            return;
        }

        // Already visible? Done.
        if ($line < $this->viewportEndLine()) {
            return;
        }

        // Below the viewport: walk backwards from the match summing wrapped
        // row counts until adding the previous line would overflow the row
        // budget. That offset puts the match line as close to the bottom of
        // the viewport as it fits.
        $viewportRows = $this->viewportHeight();
        $lineMetrics = $this->frameRenderer->getLineMetrics();

        $rowsBack = $lineMetrics->lineRowCount($this->lines[$line]);
        $newScroll = $line;
        while ($newScroll > 0) {
            $prevRows = $lineMetrics->lineRowCount($this->lines[$newScroll - 1]);
            if ($rowsBack + $prevRows > $viewportRows) {
                break;
            }
            $rowsBack += $prevRows;
            $newScroll--;
        }

        $this->scrollOffset = \min($this->maxScrollOffset(), $newScroll);
    }

    private function buildBrowseBindings(): ClosureKeyBindings
    {
        $bindings = new ClosureKeyBindings();

        $bindings->bind(fn () => $this->scrollDownLines(1), 'char:j', 'escape:[B', 'control:n', 'char:'."\n", 'char:'."\r");
        $bindings->bind(fn () => $this->scrollUpLines(1), 'char:k', 'escape:[A', 'control:p');

        $pageRows = fn () => \max(1, $this->viewportHeight() - 1);
        $bindings->bind(fn () => $this->scrollDownLines($pageRows()), 'char: ', 'escape:[6~', 'control:v', 'control:f');
        $bindings->bind(fn () => $this->scrollUpLines($pageRows()), 'char:b', 'escape:[5~', 'control:b');

        $bindings->bind(fn () => $this->jumpToTop(), 'char:g', 'escape:[H', 'escape:[1~');
        $bindings->bind(fn () => $this->jumpToBottom(), 'char:G', 'escape:[F', 'escape:[4~');

        $bindings->bind(fn () => $this->scrollDownLines(3), 'mouse:wheel-down');
        $bindings->bind(fn () => $this->scrollUpLines(3), 'mouse:wheel-up');

        $bindings->bind(fn () => $this->beginSearch(), 'char:/', 'control:r', 'control:s');
        $bindings->bind(fn () => $this->nextMatch(), 'char:n');
        $bindings->bind(fn () => $this->previousMatch(), 'char:N');

        $bindings->bind(fn () => $this->quitGracefully(), 'char:q');
        $bindings->bind(fn () => $this->abort(), 'control:c', 'escape:');

        return $bindings;
    }

    private function buildSearchInputBindings(): ClosureKeyBindings
    {
        $bindings = new ClosureKeyBindings();

        $bindings->bind(fn () => $this->commitSearch(), 'char:'."\r", 'char:'."\n");
        $bindings->bind(fn () => $this->cancelSearch(), 'control:c', 'escape:');
        $bindings->bind(fn () => $this->trimSearch(), 'control:h', 'control:?');

        return $bindings;
    }
}
