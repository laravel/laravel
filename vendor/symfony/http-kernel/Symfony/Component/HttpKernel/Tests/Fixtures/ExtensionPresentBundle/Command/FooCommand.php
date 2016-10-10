<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Fixtures\ExtensionPresentBundle\Command;

use Symfony\Component\Console\Command\Command;

class FooCommand extends Command
{
    protected function configure()
    {
        $this->setName('foo');
    }
}
