<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DescriptorCommand2 extends Command
{
    protected function configure()
    {
        $this
            ->setName('descriptor:command2')
            ->setDescription('command 2 description')
            ->setHelp('command 2 help')
            ->addArgument('argument_name', InputArgument::REQUIRED)
            ->addOption('option_name', 'o', InputOption::VALUE_NONE)
        ;
    }
}
