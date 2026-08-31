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
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\Terminal;

/**
 * Navigate to previous history entry (Up arrow).
 *
 * Fish/ZSH-style behavior: first moves within soft-wrapped visual rows, then
 * in multi-line mode moves to the previous logical line. Only navigates to
 * previous history entry when already at the top of the buffer.
 */
class PreviousHistoryAction implements ActionInterface
{
    private History $history;

    public function __construct(History $history)
    {
        $this->history = $history;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($buffer->moveToPreviousVisualRow(
            $terminal->getWidth(),
            $readline->getPromptWidthForCurrentLine($buffer)
        )) {
            return true;
        }

        if ($readline->isMultilineMode() && !$buffer->isOnFirstLine()) {
            $buffer->moveToPreviousLine();

            return true;
        }

        $enteringHistory = !$this->history->isInHistory();
        $text = $buffer->getText();

        // Set the search term when first entering history navigation
        if ($enteringHistory) {
            $this->history->setSearchTerm($text !== '' ? $text : null);
        }

        $entry = $this->history->getPrevious();

        if ($entry === null) {
            $terminal->bell();

            return true;
        }

        // Save current input before navigating away so we can restore it
        if ($enteringHistory && $text !== '') {
            $this->history->saveTemporaryEntry($text);
        }

        $buffer->setText($entry);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'previous-history';
    }
}
