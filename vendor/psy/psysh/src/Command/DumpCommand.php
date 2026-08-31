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
use Psy\VarDumper\Presenter;
use Psy\VarDumper\PresenterAware;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump an object or primitive.
 *
 * This is like var_dump but *way* awesomer.
 */
class DumpCommand extends ReflectingCommand implements PresenterAware
{
    private Presenter $presenter;

    /**
     * PresenterAware interface.
     *
     * @param Presenter $presenter
     */
    public function setPresenter(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('dump')
            ->setDefinition([
                new CodeArgument('target', CodeArgument::REQUIRED, 'A target object or primitive to dump.'),
                new InputOption('depth', '', InputOption::VALUE_REQUIRED, 'Depth to parse.', 10),
                new InputOption('all', 'a', InputOption::VALUE_NONE, 'Include private and protected methods and properties.'),
            ])
            ->setDescription('Dump an object or primitive.')
            ->setHelp(
                <<<'HELP'
Dump an object or primitive.

This is like var_dump but <strong>way</strong> awesomer.

e.g.
<return>>>> dump $_</return>
<return>>>> dump $someVar</return>
<return>>>> dump $stuff->getAll()</return>
HELP
            );
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $depth = $input->getOption('depth');
        $target = $this->resolveCode($input->getArgument('target'));
        $this->shellOutput($output)->page($this->presenter->present($target, $depth, ($input->getOption('all') ? Presenter::VERBOSE : 0) | Presenter::RAW), OutputInterface::OUTPUT_RAW);

        if (\is_object($target)) {
            $this->setCommandScopeVariables(new \ReflectionObject($target));
        }

        return 0;
    }
}
