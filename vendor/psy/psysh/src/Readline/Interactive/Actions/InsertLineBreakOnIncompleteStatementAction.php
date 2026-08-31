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

use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\Terminal;

/**
 * Insert a line break when input should continue on another line.
 */
class InsertLineBreakOnIncompleteStatementAction implements ActionInterface
{
    private InsertLineBreakAction $lineBreakAction;

    public function __construct()
    {
        $this->lineBreakAction = new InsertLineBreakAction();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($readline->isMultilineMode()) {
            if ($buffer->isCompleteStatement()) {
                return false;
            }

            return $this->lineBreakAction->execute($buffer, $terminal, $readline);
        }

        if ($buffer->isEmpty() || $buffer->isCompleteStatement()) {
            return false;
        }

        $line = $buffer->getText();
        if ($readline->isCommand($line) && !$readline->isInOpenStringOrComment($line)) {
            return false;
        }

        return $this->lineBreakAction->execute($buffer, $terminal, $readline);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'insert-line-break-on-incomplete-statement';
    }
}
