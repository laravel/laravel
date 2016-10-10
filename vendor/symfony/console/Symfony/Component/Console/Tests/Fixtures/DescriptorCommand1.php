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

class DescriptorCommand1 extends Command
{
    protected function configure()
    {
        $this
            ->setName('descriptor:command1')
            ->setAliases(array('alias1', 'alias2'))
            ->setDescription('command 1 description')
            ->setHelp('command 1 help')
        ;
    }
}
