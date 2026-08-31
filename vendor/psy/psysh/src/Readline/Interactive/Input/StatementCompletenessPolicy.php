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

use PhpParser\Error;
use Psy\CodeAnalysis\BufferAnalyzer;

/**
 * Determines whether a buffer is ready to execute as a statement.
 */
class StatementCompletenessPolicy
{
    private BufferAnalyzer $bufferAnalyzer;
    private bool $requireSemicolons;

    public function __construct(BufferAnalyzer $bufferAnalyzer, bool $requireSemicolons = false)
    {
        $this->bufferAnalyzer = $bufferAnalyzer;
        $this->requireSemicolons = $requireSemicolons;
    }

    public function isCompleteStatement(string $line): bool
    {
        if ($line === '') {
            return true;
        }

        $analysis = $this->bufferAnalyzer->analyze($line);
        $lastError = $analysis->getLastError();

        // Control structures like `if (...)` are incomplete in REPL context.
        if ($analysis->hasControlStructureWithoutBody()) {
            return false;
        }

        if ($lastError === null) {
            if (!$analysis->hasBalancedBrackets()) {
                return false;
            }

            if ($analysis->hasTrailingOperator()) {
                return false;
            }

            return true;
        }

        if (!$this->requireSemicolons && $analysis->hasEOFError()) {
            if ($this->bufferAnalyzer->canBeFixedWithSemicolon($line)) {
                return true;
            }
        }

        if ($this->isUnterminatedComment($lastError) || $this->isUnclosedString($lastError)) {
            return false;
        }

        if (!$analysis->hasEOFError()) {
            // Real syntax errors should execute immediately so the user sees them.
            return true;
        }

        return false;
    }

    /**
     * Check whether the buffer has a non-EOF parse error that isn't recoverable
     * by adding more input (e.g. extra closing brackets, invalid syntax).
     */
    public function hasUnrecoverableSyntaxError(string $line): bool
    {
        if ($line === '') {
            return false;
        }

        $analysis = $this->bufferAnalyzer->analyze($line);
        $lastError = $analysis->getLastError();

        if ($lastError === null) {
            return false;
        }

        if ($analysis->hasEOFError()) {
            return false;
        }

        if ($this->isUnterminatedComment($lastError) || $this->isUnclosedString($lastError)) {
            return false;
        }

        // Control structures without body aren't syntax errors, they're incomplete.
        if ($analysis->hasControlStructureWithoutBody()) {
            return false;
        }

        return true;
    }

    public function hasBalancedBrackets(string $line): bool
    {
        return $this->bufferAnalyzer->analyze($line)->hasBalancedBrackets();
    }

    private function isUnterminatedComment(Error $error): bool
    {
        return $error->getRawMessage() === 'Unterminated comment';
    }

    private function isUnclosedString(Error $error): bool
    {
        $msg = $error->getRawMessage();

        return $msg === 'Syntax error, unexpected T_ENCAPSED_AND_WHITESPACE' ||
               \strpos($msg, 'Unterminated string') !== false;
    }
}
