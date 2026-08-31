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
 * Insert a closing bracket with skip-over logic.
 */
class InsertCloseBracketAction implements ActionInterface
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
        if (BracketPair::shouldSkipOver($this->bracket, $buffer)) {
            if ($this->shouldDedent($buffer)) {
                $this->dedentAndSkip($buffer);
            } else {
                $buffer->moveCursorRight(1);
            }
        } else {
            $buffer->autoDedentIfClosingBracket($this->bracket, $buffer->getBeforeCursor());
            $buffer->insert($this->bracket);
        }

        return true;
    }

    /**
     * Check if we should dedent when typing a closing bracket.
     *
     * Dedent when:
     * - Cursor is before the closing bracket
     * - Current line only has whitespace before the cursor
     * - We're in multi-line mode
     */
    private function shouldDedent(Buffer $buffer): bool
    {
        $currentLine = $buffer->getCurrentLineText();
        $cursorInLine = $buffer->getCursorPositionInLine();

        $textBeforeCursor = \mb_substr($currentLine, 0, $cursorInLine);

        return \trim($textBeforeCursor) === '' && \strpos($buffer->getText(), "\n") !== false;
    }

    /**
     * Dedent by removing indentation before the closing bracket.
     *
     * Keeps the closing bracket on its own line but removes the indentation.
     */
    private function dedentAndSkip(Buffer $buffer): void
    {
        $buffer->deleteBackward($buffer->getCursorPositionInLine());
        $buffer->moveCursorRight(1);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'insert-close-bracket';
    }
}
