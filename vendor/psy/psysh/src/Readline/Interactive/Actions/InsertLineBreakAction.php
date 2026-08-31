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
 * Insert a line break without executing the current buffer.
 */
class InsertLineBreakAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        $indent = $buffer->calculateIndentBeforeCursor();
        $expandBracket = $this->isCursorBeforeClosingBracket($buffer);

        $buffer->insert("\n".$indent);

        // Push the closing bracket to its own line, dedented one level.
        if ($expandBracket) {
            $cursorPos = $buffer->getCursor();
            $buffer->insert("\n".$buffer->dedent($indent));
            $buffer->setCursor($cursorPos);
        }

        return true;
    }

    /**
     * Check whether the cursor is immediately before a closing bracket.
     */
    private function isCursorBeforeClosingBracket(Buffer $buffer): bool
    {
        $afterCursor = $buffer->getAfterCursor();

        return $afterCursor !== '' && \in_array($afterCursor[0], BracketPair::CLOSING_BRACKETS);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'insert-line-break';
    }
}
