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

use Psy\Readline\Interactive\Helper\BracketPair;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\Terminal;

/**
 * Insert an opening bracket with auto-closing.
 */
class InsertOpenBracketAction implements ActionInterface
{
    private string $bracket;

    public function __construct(string $bracket)
    {
        $this->bracket = $bracket;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($this->shouldDedentForControlStructureBrace($buffer)) {
            $this->dedentForControlStructureBrace($buffer);
        }

        if (BracketPair::shouldAutoClose($this->bracket, $buffer)) {
            $closingBracket = BracketPair::getClosingBracket($this->bracket);
            $buffer->insert($this->bracket.$closingBracket);
            $buffer->moveCursorLeft(1);
        } else {
            $buffer->insert($this->bracket);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'insert-open-bracket';
    }

    /**
     * Detect Allman-style opening braces after a control structure line.
     */
    private function shouldDedentForControlStructureBrace(Buffer $buffer): bool
    {
        if ($this->bracket !== '{') {
            return false;
        }

        $currentLine = $buffer->getCurrentLineText();
        $cursorInLine = $buffer->getCursorPositionInLine();
        $textBeforeCursor = \mb_substr($currentLine, 0, $cursorInLine);

        if (\trim($currentLine) !== '' || \trim($textBeforeCursor) !== '' || $cursorInLine !== \mb_strlen($currentLine)) {
            return false;
        }

        $linesBeforeCursor = \explode("\n", $buffer->getBeforeCursor());
        \array_pop($linesBeforeCursor);
        $previousLine = \end($linesBeforeCursor);

        if ($previousLine === false) {
            return false;
        }

        return (bool) \preg_match('/^\s*(?:(?:if|for|foreach|while|switch|elseif)\s*\([^)]*\)|else|do)\s*$/', $previousLine);
    }

    /**
     * Align the opening brace with the preceding control structure line.
     */
    private function dedentForControlStructureBrace(Buffer $buffer): void
    {
        $cursorInLine = $buffer->getCursorPositionInLine();
        $indent = \mb_substr($buffer->getCurrentLineText(), 0, $cursorInLine);
        $dedentedIndent = $buffer->dedent($indent);

        $buffer->deleteBackward($cursorInLine);
        $buffer->insert($dedentedIndent);
    }
}
