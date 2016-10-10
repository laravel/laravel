<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

use PredisTestCase;

/**
 *
 */
class PrefixableCommandTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testImplementsCorrectInterface()
    {
        $command = $this->getMockForAbstractClass('Predis\Command\PrefixableCommand');

        $this->assertInstanceOf('Predis\Command\PrefixableCommandInterface', $command);
        $this->assertInstanceOf('Predis\Command\CommandInterface', $command);
    }

    /**
     * @group disconnected
     */
    public function testAddPrefixToFirstArgument()
    {
        $command = $this->getMockForAbstractClass('Predis\Command\PrefixableCommand');
        $command->setRawArguments(array('key', 'value'));
        $command->prefixKeys('prefix:');

        $this->assertSame(array('prefix:key', 'value'), $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testDoesNotBreakOnEmptyArguments()
    {
        $command = $this->getMockForAbstractClass('Predis\Command\PrefixableCommand');
        $command->prefixKeys('prefix:');

        $this->assertEmpty($command->getArguments());
    }
}
