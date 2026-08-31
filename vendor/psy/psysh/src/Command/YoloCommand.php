<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command;

use Psy\Input\CodeArgument;
use Psy\Readline\Readline;
use Psy\Readline\ReadlineAware;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Execute code while bypassing reloader safety checks.
 */
class YoloCommand extends Command implements ReadlineAware
{
    private Readline $readline;

    /**
     * Set the Shell's Readline service.
     */
    public function setReadline(Readline $readline)
    {
        $this->readline = $readline;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('yolo')
            ->setDefinition([
                new CodeArgument('code', CodeArgument::REQUIRED, 'Code to execute, or !! to repeat last.'),
            ])
            ->setDescription('Execute code while bypassing reloader safety checks.')
            ->setHelp(
                <<<'HELP'
Execute code with all reloader safety checks bypassed.

When the reloader shows warnings about skipped conditionals or other
risky operations, use yolo to force reload and execute anyway:

e.g.
<return>>>> my_helper()</return>
<return>Warning: Skipped conditional: if (...) { function my_helper() ... }</return>

<return>>>> yolo !!</return>
<return>=> "result"</return>
HELP
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $code = $input->getArgument('code');

        // Handle !! for last command
        if ($code === '!!') {
            $history = $this->readline->listHistory();
            \array_pop($history); // Remove the current `yolo !!` invocation
            $code = \end($history) ?: '';
            if (empty($code)) {
                throw new \RuntimeException('No previous command to repeat');
            }
        }

        $shell = $this->getShell();

        $shell->setForceReload(true);

        try {
            $shell->addCode($code);

            return 0;
        } finally {
            $shell->setForceReload(false);
        }
    }
}
