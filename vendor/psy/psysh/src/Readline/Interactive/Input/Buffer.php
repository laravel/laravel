<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Input;

use Psy\CodeAnalysis\BufferAnalysis;
use Psy\CodeAnalysis\BufferAnalyzer;
use Psy\Readline\Interactive\Helper\BracketPair;

/**
 * Manages the text buffer (the text being edited).
 *
 * Includes PHP-awareness through PHP-Parser integration for understanding
 * syntax, detecting complete statements, and providing context-aware features.
 */
class Buffer
{
    private string $text = '';
    private int $cursor = 0;

    private BufferAnalyzer $bufferAnalyzer;
    private StatementCompletenessPolicy $statementCompletenessPolicy;
    private IndentationPolicy $indentationPolicy;
    private TokenNavigationPolicy $tokenNavigationPolicy;
    private WordNavigationPolicy $wordNavigationPolicy;
    private VisualNavigationPolicy $visualNavigationPolicy;

    private bool $requireSemicolons = false;
    private bool $graphemeCacheInitialized = false;
    /** @var int[]|null */
    private ?array $graphemeBoundaries = null;
    /** @var array<int,int>|null */
    private ?array $graphemeBoundaryMap = null;

    public function __construct(bool $requireSemicolons = false)
    {
        $this->requireSemicolons = $requireSemicolons;
        $this->bufferAnalyzer = new BufferAnalyzer();
        $this->statementCompletenessPolicy = new StatementCompletenessPolicy(
            $this->bufferAnalyzer,
            $this->requireSemicolons
        );
        $this->indentationPolicy = new IndentationPolicy();
        $this->tokenNavigationPolicy = new TokenNavigationPolicy();
        $this->wordNavigationPolicy = new WordNavigationPolicy();
        $this->visualNavigationPolicy = new VisualNavigationPolicy();
    }

    /**
     * Get the buffer text.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set the buffer text and move cursor to end.
     */
    public function setText(string $text): void
    {
        $this->text = $text;
        $this->cursor = \mb_strlen($text);
        $this->invalidateCaches();
    }

    /**
     * Clear the buffer.
     */
    public function clear(): void
    {
        $this->text = '';
        $this->cursor = 0;
        $this->invalidateCaches();
    }

    /**
     * Get the cursor position.
     */
    public function getCursor(): int
    {
        return $this->cursor;
    }

    /**
     * Set the cursor position.
     */
    public function setCursor(int $position): void
    {
        $this->cursor = \max(0, \min($position, \mb_strlen($this->text)));
    }

    /**
     * Get the length of the text in characters.
     */
    public function getLength(): int
    {
        return \mb_strlen($this->text);
    }

    /**
     * Insert text at the cursor position.
     */
    public function insert(string $text): void
    {
        $before = \mb_substr($this->text, 0, $this->cursor);
        $after = \mb_substr($this->text, $this->cursor);
        $this->text = $before.$text.$after;
        $this->cursor += \mb_strlen($text);
        $this->invalidateCaches();
    }

    /**
     * Auto-dedent if typing a closing bracket at start of line.
     *
     * If the line currently starts with auto-inserted whitespace and
     * we're about to insert a closing bracket, remove one indent level.
     *
     * @param string $char    The character being typed
     * @param string $context Optional accumulated code context for bracket matching
     */
    public function autoDedentIfClosingBracket(string $char, string $context = ''): void
    {
        $spacesToRemove = $this->indentationPolicy->calculateClosingBracketDedent(
            $char, $this->text, $this->cursor, $context
        );

        if ($spacesToRemove > 0) {
            $lines = \explode("\n", $this->text);
            $currentLineNum = $this->getCurrentLineNumber();
            $lines[$currentLineNum] = \substr($lines[$currentLineNum], $spacesToRemove);
            $this->text = \implode("\n", $lines);
            $this->cursor -= $spacesToRemove;
            $this->invalidateCaches();
        }
    }

    /**
     * Delete grapheme clusters backward from cursor.
     *
     * @return bool True if characters were deleted
     */
    public function deleteBackward(int $count = 1): bool
    {
        if ($this->cursor === 0 || $count <= 0) {
            return false;
        }

        $span = $this->graphemeClusterSpanReverse($count);

        $before = \mb_substr($this->text, 0, $this->cursor - $span);
        $after = \mb_substr($this->text, $this->cursor);

        $this->text = $before.$after;
        $this->cursor -= $span;
        $this->invalidateCaches();

        return true;
    }

    /**
     * Delete grapheme clusters forward from cursor.
     *
     * @return bool True if characters were deleted
     */
    public function deleteForward(int $count = 1): bool
    {
        if ($this->cursor >= \mb_strlen($this->text) || $count <= 0) {
            return false;
        }

        $span = $this->graphemeClusterSpanForward($count);

        $before = \mb_substr($this->text, 0, $this->cursor);
        $after = \mb_substr($this->text, $this->cursor + $span);

        $this->text = $before.$after;
        $this->invalidateCaches();

        return true;
    }

    /**
     * Delete from cursor to end of line.
     *
     * @return string The deleted text
     */
    public function deleteToEnd(): string
    {
        $nextNewline = \mb_strpos($this->text, "\n", $this->cursor);
        $end = $nextNewline === false ? \mb_strlen($this->text) : $nextNewline;

        $killed = \mb_substr($this->text, $this->cursor, $end - $this->cursor);
        $this->text = \mb_substr($this->text, 0, $this->cursor).\mb_substr($this->text, $end);
        $this->invalidateCaches();

        return $killed;
    }

    /**
     * Delete from start of line to cursor.
     *
     * @return string The deleted text
     */
    public function deleteToStart(): string
    {
        $beforeCursor = \mb_substr($this->text, 0, $this->cursor);
        $lastNewline = \mb_strrpos($beforeCursor, "\n");
        $start = $lastNewline === false ? 0 : $lastNewline + 1;

        $killed = \mb_substr($this->text, $start, $this->cursor - $start);
        $this->text = \mb_substr($this->text, 0, $start).\mb_substr($this->text, $this->cursor);
        $this->cursor = $start;
        $this->invalidateCaches();

        return $killed;
    }

    /**
     * Move cursor left by specified number of grapheme clusters.
     *
     * @return int Number of code points actually moved
     */
    public function moveCursorLeft(int $count = 1): int
    {
        $oldCursor = $this->cursor;
        $span = $this->graphemeClusterSpanReverse($count);
        $this->cursor = \max(0, $this->cursor - $span);

        return $oldCursor - $this->cursor;
    }

    /**
     * Move cursor right by specified number of grapheme clusters.
     *
     * @return int Number of code points actually moved
     */
    public function moveCursorRight(int $count = 1): int
    {
        $oldCursor = $this->cursor;
        $span = $this->graphemeClusterSpanForward($count);
        $this->cursor = \min(\mb_strlen($this->text), $this->cursor + $span);

        return $this->cursor - $oldCursor;
    }

    /**
     * Get the grapheme cluster before the cursor.
     *
     * @return string|null The grapheme cluster, or null if at start
     */
    public function getCharBeforeCursor(): ?string
    {
        if ($this->cursor === 0) {
            return null;
        }

        $span = $this->graphemeClusterSpanReverse(1);

        return \mb_substr($this->text, $this->cursor - $span, $span);
    }

    /**
     * Get the grapheme cluster after the cursor.
     *
     * @return string|null The grapheme cluster, or null if at end
     */
    public function getCharAfterCursor(): ?string
    {
        if ($this->cursor >= \mb_strlen($this->text)) {
            return null;
        }

        $span = $this->graphemeClusterSpanForward(1);

        return \mb_substr($this->text, $this->cursor, $span);
    }

    /**
     * Move cursor to start of line.
     */
    public function moveCursorToStart(): void
    {
        $this->cursor = 0;
    }

    /**
     * Move cursor to end of line.
     */
    public function moveCursorToEnd(): void
    {
        $this->cursor = \mb_strlen($this->text);
    }

    /**
     * Move cursor to start of current line (for multi-line buffers).
     */
    public function moveCursorToStartOfCurrentLine(): void
    {
        $beforeCursor = \mb_substr($this->text, 0, $this->cursor);
        $lastNewline = \mb_strrpos($beforeCursor, "\n");
        $this->cursor = $lastNewline === false ? 0 : $lastNewline + 1;
    }

    /**
     * Move cursor to end of current line (for multi-line buffers).
     */
    public function moveCursorToEndOfCurrentLine(): void
    {
        $nextNewline = \mb_strpos($this->text, "\n", $this->cursor);
        $this->cursor = $nextNewline === false ? \mb_strlen($this->text) : $nextNewline;
    }

    /**
     * Get the current line number (0-indexed) based on cursor position.
     */
    public function getCurrentLineNumber(): int
    {
        return \substr_count(\mb_substr($this->text, 0, $this->cursor), "\n");
    }

    /**
     * Get the text of the current line (where cursor is).
     */
    public function getCurrentLineText(): string
    {
        $lines = \explode("\n", $this->text);
        $lineNumber = $this->getCurrentLineNumber();

        return $lines[$lineNumber] ?? '';
    }

    /**
     * Get the cursor position within the current line (0-indexed).
     */
    public function getCursorPositionInLine(): int
    {
        $lineNumber = $this->getCurrentLineNumber();

        $lines = \explode("\n", $this->text);
        $charsBeforeLine = 0;
        for ($i = 0; $i < $lineNumber; $i++) {
            $charsBeforeLine += \mb_strlen($lines[$i]) + 1;
        }

        return $this->cursor - $charsBeforeLine;
    }

    /**
     * Get the total number of lines in the buffer.
     */
    public function getLineCount(): int
    {
        return \substr_count($this->text, "\n") + 1;
    }

    /**
     * Check if the cursor is on the first line.
     */
    public function isOnFirstLine(): bool
    {
        return $this->getCurrentLineNumber() === 0;
    }

    /**
     * Check if the cursor is on the last line.
     */
    public function isOnLastLine(): bool
    {
        return $this->getCurrentLineNumber() === $this->getLineCount() - 1;
    }

    /**
     * Move cursor to the previous line, maintaining column position if possible.
     *
     * @return bool True if moved, false if already on first line
     */
    public function moveToPreviousLine(): bool
    {
        if ($this->isOnFirstLine()) {
            return false;
        }

        $beforeCursor = \mb_substr($this->text, 0, $this->cursor);
        $lastNewline = \mb_strrpos($beforeCursor, "\n");

        if ($lastNewline === false) {
            return false;
        }

        $currentColumn = $this->cursor - $lastNewline - 1;

        $beforeCurrentLine = \mb_substr($this->text, 0, $lastNewline);
        $prevLineStart = \mb_strrpos($beforeCurrentLine, "\n");
        $prevLineStart = $prevLineStart === false ? 0 : $prevLineStart + 1;

        $prevLineLength = $lastNewline - $prevLineStart;

        $this->cursor = $prevLineStart + \min($currentColumn, $prevLineLength);

        return true;
    }

    /**
     * Move cursor to the previous soft-wrapped visual row on the current line.
     *
     * @param int $terminalWidth Number of terminal columns
     * @param int $promptWidth   Display width of the active prompt
     *
     * @return bool True if moved, false if already on first visual row
     */
    public function moveToPreviousVisualRow(int $terminalWidth, int $promptWidth): bool
    {
        return $this->moveVisualRows(-1, $terminalWidth, $promptWidth);
    }

    /**
     * Move cursor to the next soft-wrapped visual row on the current line.
     *
     * @param int $terminalWidth Number of terminal columns
     * @param int $promptWidth   Display width of the active prompt
     *
     * @return bool True if moved, false if already on last visual row
     */
    public function moveToNextVisualRow(int $terminalWidth, int $promptWidth): bool
    {
        return $this->moveVisualRows(1, $terminalWidth, $promptWidth);
    }

    /**
     * Move cursor by soft-wrapped visual rows.
     */
    private function moveVisualRows(int $deltaRows, int $terminalWidth, int $promptWidth): bool
    {
        $target = $this->visualNavigationPolicy->moveByRows(
            $this->text,
            $this->cursor,
            $deltaRows,
            $terminalWidth,
            $promptWidth
        );

        if ($target === null) {
            return false;
        }

        $this->cursor = $target;

        return true;
    }

    /**
     * Move cursor to the next line, maintaining column position if possible.
     *
     * @return bool True if moved, false if already on last line
     */
    public function moveToNextLine(): bool
    {
        if ($this->isOnLastLine()) {
            return false;
        }

        $beforeCursor = \mb_substr($this->text, 0, $this->cursor);
        $lastNewline = \mb_strrpos($beforeCursor, "\n");
        $currentLineStart = $lastNewline === false ? 0 : $lastNewline + 1;

        $currentColumn = $this->cursor - $currentLineStart;

        $nextNewline = \mb_strpos($this->text, "\n", $this->cursor);

        if ($nextNewline === false) {
            return false;
        }

        $nextLineStart = $nextNewline + 1;

        $lineAfterNext = \mb_strpos($this->text, "\n", $nextLineStart);
        $nextLineEnd = $lineAfterNext === false ? \mb_strlen($this->text) : $lineAfterNext;

        $nextLineLength = $nextLineEnd - $nextLineStart;

        $this->cursor = $nextLineStart + \min($currentColumn, $nextLineLength);

        return true;
    }

    /**
     * Get text before cursor.
     */
    public function getBeforeCursor(): string
    {
        return \mb_substr($this->text, 0, $this->cursor);
    }

    /**
     * Get text after cursor.
     */
    public function getAfterCursor(): string
    {
        return \mb_substr($this->text, $this->cursor);
    }

    /**
     * Find the position of the start of the previous word.
     */
    public function findPreviousWord(): int
    {
        return $this->wordNavigationPolicy->findPreviousWord($this->text, $this->cursor);
    }

    /**
     * Find the position of the start of the next word.
     */
    public function findNextWord(): int
    {
        return $this->wordNavigationPolicy->findNextWord($this->text, $this->cursor);
    }

    /**
     * Delete previous word (from cursor backwards).
     *
     * @return string The deleted text
     */
    public function deletePreviousWord(): string
    {
        return $this->deleteBackwardTo(fn () => $this->findPreviousWord());
    }

    /**
     * Delete next word (from cursor forwards).
     *
     * @return string The deleted text
     */
    public function deleteNextWord(): string
    {
        return $this->deleteForwardTo($this->findNextWord());
    }

    /**
     * Check if the buffer is empty.
     */
    public function isEmpty(): bool
    {
        return $this->text === '';
    }

    /**
     * Get the code-point span of N grapheme clusters forward from cursor.
     *
     * Uses PCRE \X to match extended grapheme clusters (Unicode TR#29).
     */
    private function graphemeClusterSpanForward(int $count): int
    {
        if ($count <= 0) {
            return 0;
        }

        if ($this->initializeGraphemeCache() && $this->graphemeBoundaries !== null && isset($this->graphemeBoundaryMap[$this->cursor])) {
            $startIndex = $this->graphemeBoundaryMap[$this->cursor];
            $targetIndex = \min($startIndex + $count, \count($this->graphemeBoundaries) - 1);

            return $this->graphemeBoundaries[$targetIndex] - $this->cursor;
        }

        return $this->graphemeClusterSpan(\mb_substr($this->text, $this->cursor), $count, false);
    }

    /**
     * Get the code-point span of N grapheme clusters backward from cursor.
     */
    private function graphemeClusterSpanReverse(int $count): int
    {
        if ($count <= 0) {
            return 0;
        }

        if ($this->initializeGraphemeCache() && $this->graphemeBoundaries !== null && isset($this->graphemeBoundaryMap[$this->cursor])) {
            $startIndex = $this->graphemeBoundaryMap[$this->cursor];
            $targetIndex = \max(0, $startIndex - $count);

            return $this->cursor - $this->graphemeBoundaries[$targetIndex];
        }

        return $this->graphemeClusterSpan(\mb_substr($this->text, 0, $this->cursor), $count, true);
    }

    /**
     * Build grapheme boundaries for current text.
     */
    private function initializeGraphemeCache(): bool
    {
        if ($this->graphemeCacheInitialized) {
            return $this->graphemeBoundaries !== null;
        }

        $this->graphemeCacheInitialized = true;

        if ($this->text === '') {
            $this->graphemeBoundaries = [0];
            $this->graphemeBoundaryMap = [0 => 0];

            return true;
        }

        if (\preg_match_all('/\X/u', $this->text, $matches) === false || empty($matches[0])) {
            $this->graphemeBoundaries = null;
            $this->graphemeBoundaryMap = null;

            return false;
        }

        $boundaries = [0];
        $boundaryMap = [0 => 0];
        $offset = 0;
        $index = 0;

        foreach ($matches[0] as $cluster) {
            $offset += \mb_strlen($cluster);
            $boundaries[] = $offset;
            $boundaryMap[$offset] = ++$index;
        }

        $this->graphemeBoundaries = $boundaries;
        $this->graphemeBoundaryMap = $boundaryMap;

        return true;
    }

    /**
     * Get the code-point span of N grapheme clusters from one end of a string.
     *
     * @param string $text    The text to measure
     * @param int    $count   Number of grapheme clusters
     * @param bool   $fromEnd Take clusters from the end rather than the start
     */
    private function graphemeClusterSpan(string $text, int $count, bool $fromEnd): int
    {
        if ($count <= 0 || $text === '') {
            return 0;
        }

        // Fall back to code-point count if regex fails (e.g. invalid UTF-8)
        if (\preg_match_all('/\X/u', $text, $matches) === false || empty($matches[0])) {
            return \min($count, \mb_strlen($text));
        }

        $offset = $fromEnd ? -$count : 0;
        $clusters = \array_slice($matches[0], $offset, $count);

        return \array_sum(\array_map('mb_strlen', $clusters));
    }

    /**
     * Delete backward from cursor to a target position, including bracket pairs when applicable.
     *
     * @param callable $findTarget Returns the target position when called with current cursor state
     *
     * @return string The deleted text
     */
    private function deleteBackwardTo(callable $findTarget): string
    {
        if (BracketPair::isInsideEmptyBrackets($this)) {
            // Include the bracket pair in the deletion
            $savedCursor = $this->cursor;
            $this->cursor--;
            $targetStart = $findTarget();
            $this->cursor = $savedCursor;

            $killed = \mb_substr($this->text, $targetStart, $this->cursor - $targetStart + 1);
            $before = \mb_substr($this->text, 0, $targetStart);
            $after = \mb_substr($this->text, $this->cursor + 1);

            $this->text = $before.$after;
            $this->cursor = $targetStart;
            $this->invalidateCaches();

            return $killed;
        }

        $targetStart = $findTarget();
        $killed = \mb_substr($this->text, $targetStart, $this->cursor - $targetStart);

        $before = \mb_substr($this->text, 0, $targetStart);
        $after = \mb_substr($this->text, $this->cursor);

        $this->text = $before.$after;
        $this->cursor = $targetStart;
        $this->invalidateCaches();

        return $killed;
    }

    /**
     * Delete forward from cursor to a target position.
     *
     * @return string The deleted text
     */
    private function deleteForwardTo(int $targetEnd): string
    {
        $killed = \mb_substr($this->text, $this->cursor, $targetEnd - $this->cursor);

        $before = \mb_substr($this->text, 0, $this->cursor);
        $after = \mb_substr($this->text, $targetEnd);

        $this->text = $before.$after;
        $this->invalidateCaches();

        return $killed;
    }

    private function invalidateCaches(): void
    {
        $this->graphemeCacheInitialized = false;
        $this->graphemeBoundaries = null;
        $this->graphemeBoundaryMap = null;
    }

    private function getBufferAnalysis(): BufferAnalysis
    {
        return $this->bufferAnalyzer->analyze($this->getText());
    }

    /**
     * @return array Raw token_get_all() tokens
     */
    private function getParsedTokens(): array
    {
        return $this->getBufferAnalysis()->getTokens();
    }

    /**
     * @return array<int, array{start: int, end: int}>
     */
    private function getParsedTokenPositions(): array
    {
        return $this->getBufferAnalysis()->getTokenPositions();
    }

    /**
     * Check if the buffer contains a complete PHP statement.
     */
    public function isCompleteStatement(): bool
    {
        return $this->statementCompletenessPolicy->isCompleteStatement($this->text);
    }

    /**
     * Check if the buffer has an unrecoverable syntax error.
     */
    public function hasUnrecoverableSyntaxError(): bool
    {
        return $this->statementCompletenessPolicy->hasUnrecoverableSyntaxError($this->text);
    }

    /**
     * Check whether text before the cursor has unclosed brackets.
     */
    public function hasUnclosedBracketsBeforeCursor(): bool
    {
        $textBeforeCursor = $this->getBeforeCursor();
        if (\trim($textBeforeCursor) === '') {
            return false;
        }

        return !$this->statementCompletenessPolicy->hasBalancedBrackets($textBeforeCursor);
    }

    /**
     * Calculate the appropriate indentation for the next line.
     *
     * Analyzes the current line to determine how much whitespace should
     * be automatically inserted when continuing to a new line.
     *
     * @return string The whitespace to insert (spaces or tabs)
     */
    public function calculateNextLineIndent(): string
    {
        return $this->indentationPolicy->calculateNextLineIndent(
            $this->text,
            $this->getParsedTokens()
        );
    }

    /**
     * Calculate indentation using only text before the cursor.
     */
    public function calculateIndentBeforeCursor(): string
    {
        $textBeforeCursor = $this->getBeforeCursor();
        $tokens = $this->bufferAnalyzer->analyze($textBeforeCursor)->getTokens();

        return $this->indentationPolicy->calculateNextLineIndent($textBeforeCursor, $tokens);
    }

    /**
     * Remove one level of indentation from an indent string.
     */
    public function dedent(string $indent): string
    {
        return $this->indentationPolicy->dedent($indent);
    }

    /**
     * Get the number of spaces needed to reach the next tab stop.
     */
    public function spacesToNextTabStop(int $column): int
    {
        return $this->indentationPolicy->spacesToNextTabStop($column);
    }

    /**
     * Get the number of spaces to remove to reach the previous tab stop.
     */
    public function spacesToPreviousTabStop(int $spaces): int
    {
        return $this->indentationPolicy->spacesToPreviousTabStop($spaces);
    }

    /**
     * Find the start position of the previous token.
     *
     * Navigates to the start of the token before the cursor position.
     * If cursor is inside a token (not at start), goes to start of that token.
     * If cursor is at the start of a token, goes to previous token.
     *
     * @return int Position of previous token start, or 0 if at beginning
     */
    public function findPreviousToken(): int
    {
        return $this->tokenNavigationPolicy->findPreviousToken(
            $this->getParsedTokens(),
            $this->getParsedTokenPositions(),
            $this->cursor
        );
    }

    /**
     * Find the start position of the next token.
     *
     * Navigates to the start of the token after the cursor position.
     * If cursor is inside a token, goes to next token after that.
     * If cursor is between tokens, goes to next token.
     *
     * @return int Position of next token start, or end of line if at end
     */
    public function findNextToken(): int
    {
        return $this->tokenNavigationPolicy->findNextToken(
            $this->getParsedTokens(),
            $this->getParsedTokenPositions(),
            $this->cursor,
            $this->getLength()
        );
    }

    /**
     * Delete from cursor backwards to start of previous token.
     *
     * @return string The deleted text
     */
    public function deletePreviousToken(): string
    {
        return $this->deleteBackwardTo(fn () => $this->findPreviousToken());
    }

    /**
     * Delete from cursor forwards to start of next token.
     *
     * @return string The deleted text
     */
    public function deleteNextToken(): string
    {
        return $this->deleteForwardTo($this->findNextToken());
    }
}
