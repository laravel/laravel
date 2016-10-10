<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Application;

class HelpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteForCommandAlias()
    {
        $command = new HelpCommand();
        $command->setApplication(new Application());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command_name' => 'li'));
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command alias');
    }

    public function testExecuteForCommand()
    {
        $command = new HelpCommand();
        $commandTester = new CommandTester($command);
        $command->setCommand(new ListCommand());
        $commandTester->execute(array());
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
    }

    public function testExecuteForCommandWithXmlOption()
    {
        $command = new HelpCommand();
        $commandTester = new CommandTester($command);
        $command->setCommand(new ListCommand());
        $commandTester->execute(array('--format' => 'xml'));
        $this->assertRegExp('/<command/', $commandTester->getDisplay(), '->execute() returns an XML help text if --xml is passed');
    }

    public function testExecuteForApplicationCommand()
    {
        $application = new Application();
        $commandTester = new CommandTester($application->get('help'));
        $commandTester->execute(array('command_name' => 'list'));
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
    }

    public function testExecuteForApplicationCommandWithXmlOption()
    {
        $application = new Application();
        $commandTester = new CommandTester($application->get('help'));
        $commandTester->execute(array('command_name' => 'list', '--format' => 'xml'));
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertRegExp('/<command/', $commandTester->getDisplay(), '->execute() returns an XML help text if --format=xml is passed');
    }
}
