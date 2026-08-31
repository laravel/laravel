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
 * Action chain with fallback behavior.
 *
 * Executes actions in order until one handles the keypress.
 *
 * In a fallback chain, an action returning false means "not handled, try next
 * fallback action". If all actions return false, the chain returns the
 * configured default result (continue by default).
 */
class FallbackAction implements ActionInterface
{
    /** @var ActionInterface[] */
    private array $actions;
    private bool $defaultToContinue;

    /**
     * @param ActionInterface[] $actions
     * @param bool              $defaultToContinue Result when all actions return false
     */
    public function __construct(array $actions, bool $defaultToContinue = true)
    {
        if (empty($actions)) {
            throw new \InvalidArgumentException('FallbackAction requires at least one action.');
        }

        $this->actions = \array_values($actions);
        $this->defaultToContinue = $defaultToContinue;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        foreach ($this->actions as $action) {
            if ($action->execute($buffer, $terminal, $readline)) {
                return true;
            }
        }

        return $this->defaultToContinue;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'fallback('.\implode(',', \array_map(fn (ActionInterface $action) => $action->getName(), $this->actions)).')';
    }
}
