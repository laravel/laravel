<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Profile;

use PredisTestCase;
use Predis\Command\Processor\ProcessorChain;

/**
 *
 */
class ServerProfileTest extends PredisTestCase
{
    const DEFAULT_PROFILE_VERSION = '2.8';
    const DEVELOPMENT_PROFILE_VERSION = '3.0';

    /**
     * @group disconnected
     */
    public function testGetVersion()
    {
        $profile = ServerProfile::get('2.0');

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertEquals('2.0', $profile->getVersion());
    }

    /**
     * @group disconnected
     */
    public function testGetDefault()
    {
        $profile1 = ServerProfile::get(self::DEFAULT_PROFILE_VERSION);
        $profile2 = ServerProfile::get('default');
        $profile3 = ServerProfile::getDefault();

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile1);
        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile2);
        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile3);
        $this->assertEquals($profile1->getVersion(), $profile2->getVersion());
        $this->assertEquals($profile2->getVersion(), $profile3->getVersion());
    }

    /**
     * @group disconnected
     */
    public function testGetDevelopment()
    {
        $profile1 = ServerProfile::get('dev');
        $profile2 = ServerProfile::getDevelopment();

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile1);
        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile2);
        $this->assertEquals(self::DEVELOPMENT_PROFILE_VERSION, $profile2->getVersion());
    }

    /**
     * @group disconnected
     * @expectedException Predis\ClientException
     * @expectedExceptionMessage Unknown server profile: 1.0
     */
    public function testGetUndefinedProfile()
    {
        ServerProfile::get('1.0');
    }

    /**
     * @group disconnected
     */
    public function testDefineProfile()
    {
        $profileClass = get_class($this->getMock('Predis\Profile\ServerProfileInterface'));

        ServerProfile::define('mock', $profileClass);

        $this->assertInstanceOf($profileClass, ServerProfile::get('mock'));
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot register 'stdClass' as it is not a valid profile class
     */
    public function testDefineInvalidProfile()
    {
        ServerProfile::define('bogus', 'stdClass');
    }

    /**
     * @group disconnected
     */
    public function testToString()
    {
        $this->assertEquals('2.0', (string) ServerProfile::get('2.0'));
    }

    /**
     * @group disconnected
     */
    public function testSupportCommand()
    {
        $profile = ServerProfile::getDefault();

        $this->assertTrue($profile->supportsCommand('info'));
        $this->assertTrue($profile->supportsCommand('INFO'));

        $this->assertFalse($profile->supportsCommand('unknown'));
        $this->assertFalse($profile->supportsCommand('UNKNOWN'));
    }

    /**
     * @group disconnected
     */
    public function testSupportCommands()
    {
        $profile = ServerProfile::getDefault();

        $this->assertTrue($profile->supportsCommands(array('get', 'set')));
        $this->assertTrue($profile->supportsCommands(array('GET', 'SET')));

        $this->assertFalse($profile->supportsCommands(array('get', 'unknown')));

        $this->assertFalse($profile->supportsCommands(array('unknown1', 'unknown2')));
    }

    /**
     * @group disconnected
     */
    public function testGetCommandClass()
    {
        $profile = ServerProfile::getDefault();

        $this->assertSame('Predis\Command\ConnectionPing', $profile->getCommandClass('ping'));
        $this->assertSame('Predis\Command\ConnectionPing', $profile->getCommandClass('PING'));

        $this->assertNull($profile->getCommandClass('unknown'));
        $this->assertNull($profile->getCommandClass('UNKNOWN'));
    }

    /**
     * @group disconnected
     */
    public function testDefineCommand()
    {
        $profile = ServerProfile::getDefault();
        $command = $this->getMock('Predis\Command\CommandInterface');

        $profile->defineCommand('mock', get_class($command));

        $this->assertTrue($profile->supportsCommand('mock'));
        $this->assertTrue($profile->supportsCommand('MOCK'));

        $this->assertSame(get_class($command), $profile->getCommandClass('mock'));
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot register 'stdClass' as it is not a valid Redis command
     */
    public function testDefineInvalidCommand()
    {
        $profile = ServerProfile::getDefault();

        $profile->defineCommand('mock', 'stdClass');
    }

    /**
     * @group disconnected
     */
    public function testCreateCommandWithoutArguments()
    {
        $profile = ServerProfile::getDefault();

        $command = $profile->createCommand('info');
        $this->assertInstanceOf('Predis\Command\CommandInterface', $command);
        $this->assertEquals('INFO', $command->getId());
        $this->assertEquals(array(), $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testCreateCommandWithArguments()
    {
        $profile = ServerProfile::getDefault();
        $arguments = array('foo', 'bar');

        $command = $profile->createCommand('set', $arguments);
        $this->assertInstanceOf('Predis\Command\CommandInterface', $command);
        $this->assertEquals('SET', $command->getId());
        $this->assertEquals($arguments, $command->getArguments());
    }

    /**
     * @group disconnected
     * @expectedException Predis\ClientException
     * @expectedExceptionMessage 'unknown' is not a registered Redis command
     */
    public function testCreateUndefinedCommand()
    {
        $profile = ServerProfile::getDefault();
        $profile->createCommand('unknown');
    }

    /**
     * @group disconnected
     */
    public function testGetDefaultProcessor()
    {
        $profile = ServerProfile::getDefault();

        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     */
    public function testSetProcessor()
    {
        $processor = $this->getMock('Predis\Command\Processor\CommandProcessorInterface');

        $profile = ServerProfile::getDefault();
        $profile->setProcessor($processor);

        $this->assertSame($processor, $profile->getProcessor());
    }

    /**
     * @group disconnected
     */
    public function testSetAndUnsetProcessor()
    {
        $processor = $this->getMock('Predis\Command\Processor\CommandProcessorInterface');
        $profile = ServerProfile::getDefault();

        $profile->setProcessor($processor);
        $this->assertSame($processor, $profile->getProcessor());

        $profile->setProcessor(null);
        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     * @todo Could it be that objects passed to the return callback of a mocked
     *       method are cloned instead of being passed by reference?
     */
    public function testSingleProcessor()
    {
        $argsRef = null;

        $processor = $this->getMock('Predis\Command\Processor\CommandProcessorInterface');
        $processor->expects($this->once())
                  ->method('process')
                  ->with($this->isInstanceOf('Predis\Command\CommandInterface'))
                  ->will($this->returnCallback(function ($cmd) use (&$argsRef) {
                        $cmd->setRawArguments($argsRef = array_map('strtoupper', $cmd->getArguments()));
                    }));

        $profile = ServerProfile::getDefault();
        $profile->setProcessor($processor);
        $command = $profile->createCommand('set', array('foo', 'bar'));

        $this->assertSame(array('FOO', 'BAR'), $argsRef);
    }

    /**
     * @group disconnected
     */
    public function testChainOfProcessors()
    {
        $processor = $this->getMock('Predis\Command\Processor\CommandProcessorInterface');
        $processor->expects($this->exactly(2))
                  ->method('process');

        $chain = new ProcessorChain();
        $chain->add($processor);
        $chain->add($processor);

        $profile = ServerProfile::getDefault();
        $profile->setProcessor($chain);
        $profile->createCommand('info');
    }
}
