<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion;

/**
 * Normalized completion request.
 *
 * Uses the full buffer and an absolute cursor position so callers can request
 * completions from any position, including multiline input.
 */
class CompletionRequest
{
    public const MODE_TAB = 'tab';
    public const MODE_SUGGESTION = 'suggestion';

    private string $buffer;
    private int $cursor;
    private string $mode;
    /** @var array Raw callback metadata from the caller, if any */
    private array $readlineInfo;

    public function __construct(string $buffer, int $cursor, string $mode = self::MODE_TAB, array $readlineInfo = [])
    {
        $this->buffer = $buffer;
        $this->cursor = $this->normalizeCursor($buffer, $cursor);
        $this->mode = $mode;
        $this->readlineInfo = $readlineInfo;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function getCursor(): int
    {
        return $this->cursor;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get raw readline callback metadata associated with this request.
     */
    public function getReadlineInfo(): array
    {
        return $this->readlineInfo;
    }

    private function normalizeCursor(string $buffer, int $cursor): int
    {
        $length = \mb_strlen($buffer);

        return \max(0, \min($cursor, $length));
    }
}
