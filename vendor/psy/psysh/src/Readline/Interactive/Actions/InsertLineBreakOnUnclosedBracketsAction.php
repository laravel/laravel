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
 * Insert a line break when smart brackets detect unclosed pairs.
 */
class InsertLineBreakOnUnclosedBracketsAction implements ActionInterface
{
    private bool $smartBrackets;
    private InsertLineBreakAction $lineBreakAction;

    public function __construct(bool $smartBrackets = false)
    {
        $this->smartBrackets = $smartBrackets;
        $this->lineBreakAction = new InsertLineBreakAction();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if (!$this->smartBrackets || !$buffer->hasUnclosedBracketsBeforeCursor()) {
            return false;
        }

        // If the full buffer is semantically complete, let it submit
        // even if the cursor is between brackets.
        if ($buffer->isCompleteStatement()) {
            return false;
        }

        return $this->lineBreakAction->execute($buffer, $terminal, $readline);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'insert-line-break-on-unclosed-brackets';
    }
}
