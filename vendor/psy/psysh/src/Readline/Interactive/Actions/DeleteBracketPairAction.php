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
 * Delete matching bracket pairs when the cursor is between them.
 */
class DeleteBracketPairAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if (!BracketPair::shouldDeletePair($buffer)) {
            return false;
        }

        $buffer->deleteBackward(1);
        $buffer->deleteForward(1);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'delete-bracket-pair';
    }
}
