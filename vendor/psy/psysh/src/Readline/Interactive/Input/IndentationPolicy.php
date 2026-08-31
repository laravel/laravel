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

use Psy\Readline\Interactive\Helper\BracketPair;

/**
 * Calculates indentation for multiline editing.
 */
class IndentationPolicy
{
    private const INDENT_WIDTH = 4;

    /**
     * @param array $tokens Snapshot tokens for the current full buffer
     */
    public function calculateNextLineIndent(string $bufferText, array $tokens): string
    {
        $lines = \explode("\n", $bufferText);
        $lastLine = (string) \end($lines);
        $trimmedLastLine = \trim($lastLine);

        if ($trimmedLastLine === '') {
            return '';
        }

        if ($this->isEndOfLineInStringOrComment($bufferText, $tokens)) {
            return '';
        }

        $currentIndent = $this->getLineIndentation($lastLine);
        $lastChar = \substr(\rtrim($trimmedLastLine), -1);

        if (\in_array($lastChar, BracketPair::OPENING_BRACKETS)) {
            return $this->indent($currentIndent);
        }

        if ($this->endsWithControlStructure($trimmedLastLine)) {
            return $this->indent($currentIndent);
        }

        return $currentIndent;
    }

    /**
     * Add one level of indentation.
     */
    public function indent(string $currentIndent): string
    {
        return $currentIndent.\str_repeat(' ', self::INDENT_WIDTH);
    }

    /**
     * Remove one level of indentation.
     */
    public function dedent(string $indent): string
    {
        if ($indent === '') {
            return '';
        }

        // Calculate the visual column width, accounting for tabs.
        $column = 0;
        $length = \strlen($indent);

        for ($i = 0; $i < $length; $i++) {
            $column += $indent[$i] === "\t" ? $this->spacesToNextTabStop($column) : 1;
        }

        $target = $column - $this->spacesToPreviousTabStop($column);

        // Find the byte position where truncating leaves us at the target column.
        $column = 0;
        for ($i = 0; $i < $length; $i++) {
            $column += $indent[$i] === "\t" ? $this->spacesToNextTabStop($column) : 1;
            if ($column > $target) {
                return \substr($indent, 0, $i);
            }
        }

        return $indent;
    }

    /**
     * Calculate how many spaces to remove when typing a closing bracket.
     *
     * Returns the number of leading spaces to strip from the current line
     * when a closing bracket is typed at the end of a whitespace-only line.
     *
     * @param string $char    The character being typed
     * @param string $text    The full buffer text
     * @param int    $cursor  The current cursor position
     * @param string $context Accumulated code context for bracket matching
     */
    public function calculateClosingBracketDedent(string $char, string $text, int $cursor, string $context = ''): int
    {
        if (!\in_array($char, BracketPair::CLOSING_BRACKETS)) {
            return 0;
        }

        $lines = \explode("\n", $text);
        $currentLineNum = \substr_count(\mb_substr($text, 0, $cursor), "\n");
        $currentLine = $lines[$currentLineNum];

        $charsBeforeLine = 0;
        for ($i = 0; $i < $currentLineNum; $i++) {
            $charsBeforeLine += \mb_strlen($lines[$i]) + 1;
        }
        $cursorInLine = $cursor - $charsBeforeLine;

        if (\trim($currentLine) !== '') {
            return 0;
        }

        if ($cursorInLine !== \mb_strlen($currentLine)) {
            return 0;
        }

        $leadingSpaces = \mb_strlen($currentLine) - \mb_strlen(\ltrim($currentLine));
        if ($leadingSpaces === 0) {
            return 0;
        }

        if ($context !== '' && !BracketPair::doesClosingBracketMatch($char, $context)) {
            return 0;
        }

        return $this->spacesToPreviousTabStop($leadingSpaces);
    }

    /**
     * @param array $tokens
     */
    private function isEndOfLineInStringOrComment(string $bufferText, array $tokens): bool
    {
        if (empty($tokens)) {
            return false;
        }

        $lastToken = null;
        for ($i = \count($tokens) - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            if (\is_array($token) && $token[0] === \T_WHITESPACE) {
                continue;
            }

            $lastToken = $token;
            break;
        }

        if ($lastToken === null) {
            return false;
        }

        if (\is_array($lastToken)) {
            $tokenType = $lastToken[0];
            if ($tokenType === \T_CONSTANT_ENCAPSED_STRING ||
                $tokenType === \T_ENCAPSED_AND_WHITESPACE ||
                $tokenType === \T_START_HEREDOC ||
                $tokenType === \T_END_HEREDOC) {
                return true;
            }

            if ($tokenType === \T_COMMENT) {
                $text = \ltrim($lastToken[1]);

                // Only suppress indentation for block comments, not single-line
                return \strpos($text, '/*') === 0;
            }

            if ($tokenType === \T_DOC_COMMENT) {
                return true;
            }

            if (\defined('T_BACKTICK') && $tokenType === T_BACKTICK) {
                return true;
            }
        }

        $trimmed = \rtrim($bufferText);
        $lastChar = \substr($trimmed, -1);
        if ($lastChar === '"' || $lastChar === "'" || $lastChar === '`') {
            $doubleQuoteCount = \substr_count($bufferText, '"');
            $singleQuoteCount = \substr_count($bufferText, "'");
            $backtickCount = \substr_count($bufferText, '`');

            if ($lastChar === '"' && $doubleQuoteCount % 2 === 1) {
                return true;
            }
            if ($lastChar === "'" && $singleQuoteCount % 2 === 1) {
                return true;
            }
            if ($lastChar === '`' && $backtickCount % 2 === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the number of spaces needed to reach the next tab stop.
     */
    public function spacesToNextTabStop(int $column): int
    {
        return self::INDENT_WIDTH - ($column % self::INDENT_WIDTH);
    }

    /**
     * Get the number of spaces to remove to reach the previous tab stop.
     */
    public function spacesToPreviousTabStop(int $spaces): int
    {
        $remainder = $spaces % self::INDENT_WIDTH;

        return $remainder === 0 ? self::INDENT_WIDTH : $remainder;
    }

    /**
     * Extract leading whitespace from a line.
     */
    private function getLineIndentation(string $line): string
    {
        if (\preg_match('/^(\s+)/', $line, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Check whether a line ends with a control structure (if, for, while, etc.).
     */
    private function endsWithControlStructure(string $line): bool
    {
        return (bool) \preg_match('/\b(if|for|foreach|while|switch|else|elseif|do)\s*\([^)]*\)\s*$/', $line);
    }
}
