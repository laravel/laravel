<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command\Processor;

use PredisTestCase;

/**
 *
 */
class KeyPrefixProcessorTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testConstructorWithPrefix()
    {
        $prefix = 'prefix:';
        $processor = new KeyPrefixProcessor($prefix);

        $this->assertInstanceOf('Predis\Command\Processor\CommandProcessorInterface', $processor);
        $this->assertEquals($prefix, $processor->getPrefix());
    }

    /**
     * @group disconnected
     */
    public function testChangePrefix()
    {
        $prefix1 = 'prefix:';
        $prefix2 = 'prefix:new:';

        $processor = new KeyPrefixProcessor($prefix1);
        $this->assertEquals($prefix1, $processor->getPrefix());

        $processor->setPrefix($prefix2);
        $this->assertEquals($prefix2, $processor->getPrefix());
    }

    /**
     * @group disconnected
     */
    public function testProcessPrefixableCommands()
    {
        $prefix = 'prefix:';

        $command = $this->getMock('Predis\Command\PrefixableCommand');
        $command->expects($this->once())
                ->method('prefixKeys')
                ->with($prefix);
        $command->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue('key'));

        $processor = new KeyPrefixProcessor($prefix);

        $processor->process($command);
    }

    /**
     * @group disconnected
     */
    public function testSkipPrefixableCommandsWithNoArguments()
    {
        $prefix = 'prefix:';

        $command = $this->getMock('Predis\Command\PrefixableCommand');
        $command->expects($this->never())
                ->method('prefixKeys');

        $processor = new KeyPrefixProcessor($prefix);

        $processor->process($command);
    }

    /**
     * @group disconnected
     */
    public function testSkipNotPrefixableCommands()
    {
        $prefix = 'prefix:';
        $unprefixed = 'key';
        $expected = "$prefix$unprefixed";

        $command = $this->getMock('Predis\Command\CommandInterface');
        $command->expects($this->never())->method('prefixKeys');

        $processor = new KeyPrefixProcessor($prefix);

        $processor->process($command);
    }

    /**
     * @group disconnected
     */
    public function testInstanceCanBeCastedToString()
    {
        $prefix = 'prefix:';
        $processor = new KeyPrefixProcessor($prefix);

        $this->assertEquals($prefix, (string) $processor);
    }
}
