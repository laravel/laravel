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
 * Expand history shorthand (e.g. !!, !$) on Tab when present.
 */
class ExpandHistoryOnTabAction implements ActionInterface
{
    private HistoryExpansionAction $historyExpansion;

    public function __construct(HistoryExpansionAction $historyExpansion)
    {
        $this->historyExpansion = $historyExpansion;
    }

    /**
     * Replace the HistoryExpansionAction (e.g. after Shell is set).
     */
    public function setHistoryExpansion(HistoryExpansionAction $historyExpansion): void
    {
        $this->historyExpansion = $historyExpansion;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($this->historyExpansion->detectExpansion($buffer->getText(), $buffer->getCursor()) === null) {
            return false;
        }

        return $this->historyExpansion->execute($buffer, $terminal, $readline);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'expand-history-on-tab';
    }
}
