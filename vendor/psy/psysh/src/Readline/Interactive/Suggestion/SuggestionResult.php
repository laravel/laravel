<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Suggestion;

/**
 * Represents a suggestion result from a suggestion source.
 */
class SuggestionResult
{
    public const SOURCE_HISTORY = 'history';
    public const SOURCE_CALL_SIGNATURE = 'call-signature';
    public const SOURCE_CONTEXT_AWARE = 'context-aware';

    private string $displayText;
    private string $source;
    private string $acceptText;
    private int $replaceStart;
    private int $replaceEnd;

    /**
     * @param string $displayText  Text shown in ghost-text preview
     * @param string $source       Source type: 'history', 'call-signature', 'context-aware'
     * @param string $acceptText   Text inserted when accepting the suggestion
     * @param int    $replaceStart Start offset of the replacement range (inclusive)
     * @param int    $replaceEnd   End offset of the replacement range (exclusive)
     */
    public function __construct(string $displayText, string $source, string $acceptText, int $replaceStart, int $replaceEnd)
    {
        if ($replaceStart < 0 || $replaceEnd < $replaceStart) {
            throw new \InvalidArgumentException('Invalid suggestion replacement range.');
        }

        $this->displayText = $displayText;
        $this->source = $source;
        $this->acceptText = $acceptText;
        $this->replaceStart = $replaceStart;
        $this->replaceEnd = $replaceEnd;
    }

    /**
     * Build a suggestion that appends text at the current cursor.
     *
     * @param string      $displayText    Text shown in ghost-text preview
     * @param string      $source         Source type
     * @param int         $cursorPosition Current cursor position
     * @param string|null $acceptText     Optional accept text (defaults to display text)
     */
    public static function forAppend(
        string $displayText,
        string $source,
        int $cursorPosition,
        ?string $acceptText = null
    ): self {
        $insertText = $acceptText ?? $displayText;

        return new self($displayText, $source, $insertText, $cursorPosition, $cursorPosition);
    }

    /**
     * Get the suggestion text shown in the ghost-text preview.
     */
    public function getDisplayText(): string
    {
        return $this->displayText;
    }

    /**
     * Get the source that provided this suggestion.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Get the text inserted when accepting this suggestion.
     */
    public function getAcceptText(): string
    {
        return $this->acceptText;
    }

    /**
     * Replacement range start (inclusive).
     */
    public function getReplaceStart(): int
    {
        return $this->replaceStart;
    }

    /**
     * Replacement range end (exclusive).
     */
    public function getReplaceEnd(): int
    {
        return $this->replaceEnd;
    }

    /**
     * Whether this suggestion appends at the given cursor without replacing.
     */
    public function isAppendOnly(int $cursor): bool
    {
        return $this->replaceStart === $cursor && $this->replaceEnd === $cursor;
    }

    /**
     * Apply the suggestion edit to a buffer string.
     */
    public function applyToBuffer(string $buffer): string
    {
        $length = \mb_strlen($buffer);
        $start = \max(0, \min($this->replaceStart, $length));
        $end = \max($start, \min($this->replaceEnd, $length));

        return \mb_substr($buffer, 0, $start).$this->acceptText.\mb_substr($buffer, $end);
    }
}
