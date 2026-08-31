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
 * Insert a quote with auto-closing and skip-over logic.
 */
class InsertQuoteAction implements ActionInterface
{
    private string $quote;

    public function __construct(string $quote)
    {
        $this->quote = $quote;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if (BracketPair::shouldSkipOver($this->quote, $buffer)) {
            $buffer->moveCursorRight(1);

            return true;
        }

        if (BracketPair::shouldAutoClose($this->quote, $buffer)) {
            $buffer->insert($this->quote.$this->quote);
            $buffer->moveCursorLeft(1);
        } else {
            $buffer->insert($this->quote);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'insert-quote';
    }
}
