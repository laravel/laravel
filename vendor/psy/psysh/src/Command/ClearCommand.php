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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear the Psy Shell.
 *
 * Just what it says on the tin.
 */
class ClearCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('clear')
            ->setDefinition([])
            ->setDescription('Clear the Psy Shell screen.')
            ->setHelp(
                <<<'HELP'
Clear the Psy Shell screen.

Pro Tip: If your PHP has readline support, you should be able to use ctrl+l too!
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
        $output->write(\sprintf('%c[2J%c[0;0f', 27, 27));

        return 0;
    }
}
