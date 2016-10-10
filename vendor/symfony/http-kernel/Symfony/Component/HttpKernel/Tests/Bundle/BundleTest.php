<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Bundle;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Tests\Fixtures\ExtensionAbsentBundle\ExtensionAbsentBundle;
use Symfony\Component\HttpKernel\Tests\Fixtures\ExtensionPresentBundle\Command\FooCommand;
use Symfony\Component\HttpKernel\Tests\Fixtures\ExtensionPresentBundle\ExtensionPresentBundle;

class BundleTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterCommands()
    {
        $cmd = new FooCommand();
        $app = $this->getMock('Symfony\Component\Console\Application');
        $app->expects($this->once())->method('add')->with($this->equalTo($cmd));

        $bundle = new ExtensionPresentBundle();
        $bundle->registerCommands($app);

        $bundle2 = new ExtensionAbsentBundle();

        $this->assertNull($bundle2->registerCommands($app));
    }
}
