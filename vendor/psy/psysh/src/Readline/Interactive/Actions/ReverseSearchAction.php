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

use Psy\Readline\Interactive\HistorySearch;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\Terminal;

/**
 * Action for interactive reverse history search (Ctrl-R).
 */
class ReverseSearchAction implements ActionInterface
{
    private HistorySearch $search;

    public function __construct(HistorySearch $search)
    {
        $this->search = $search;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($this->search->isActive()) {
            $this->search->findNext();
        } else {
            $readline->clearSuggestion();
            $this->search->saveBuffer($buffer);
            $this->search->enter($buffer->getCurrentLineText());
            $readline->pushMode($this->search);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'reverse-search-history';
    }
}
