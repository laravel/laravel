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
class CommandTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testImplementsCorrectInterface()
    {
        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');

        $this->assertInstanceOf('Predis\Command\CommandInterface', $command);
    }

    /**
     * @group disconnected
     */
    public function testGetEmptyArguments()
    {
        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');

        $this->assertEmpty($command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testSetRawArguments()
    {
        $arguments = array('1st', '2nd', '3rd');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setRawArguments($arguments);

        $this->assertEquals($arguments, $command->getArguments());
    }

    /**
     * @group disconnected
     *
     * @todo Since AbstractCommand::filterArguments is protected we cannot set an expectation
     *       for it when AbstractCommand::setArguments() is invoked. I wonder how we can do that.
     */
    public function testSetArguments()
    {
        $arguments = array('1st', '2nd', '3rd');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setArguments($arguments);

        $this->assertEquals($arguments, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testGetArgumentAtIndex()
    {
        $arguments = array('1st', '2nd', '3rd');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setArguments($arguments);

        $this->assertEquals($arguments[0], $command->getArgument(0));
        $this->assertEquals($arguments[2], $command->getArgument(2));
        $this->assertNull($command->getArgument(10));
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $response = 'response-buffer';
        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');

        $this->assertEquals($response, $command->parseResponse($response));
    }

    /**
     * @group disconnected
     */
    public function testSetAndGetHash()
    {
        $hash = "key-hash";

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setRawArguments(array('key'));

        $this->assertNull($command->getHash());

        $command->setHash($hash);
        $this->assertSame($hash, $command->getHash());

        $command->setArguments(array('key'));
        $this->assertNull($command->getHash());

        $command->setHash($hash);
        $command->setRawArguments(array('key'));
        $this->assertNull($command->getHash());
    }
    /**
     * @group disconnected
     */
    public function testToString()
    {
        $expected = 'SET key value';
        $arguments = array('key', 'value');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->expects($this->once())->method('getId')->will($this->returnValue('SET'));

        $command->setRawArguments($arguments);

        $this->assertEquals($expected, (string) $command);
    }

    /**
     * @group disconnected
     */
    public function testToStringWithLongArguments()
    {
        $expected = 'SET key abcdefghijklmnopqrstuvwxyz012345[...]';
        $arguments = array('key', 'abcdefghijklmnopqrstuvwxyz0123456789');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->expects($this->once())->method('getId')->will($this->returnValue('SET'));

        $command->setRawArguments($arguments);

        $this->assertEquals($expected, (string) $command);
    }

    /**
     * @group disconnected
     */
    public function testNormalizeArguments()
    {
        $arguments = array('arg1', 'arg2', 'arg3', 'arg4');

        $this->assertSame($arguments, AbstractCommand::normalizeArguments($arguments));
        $this->assertSame($arguments, AbstractCommand::normalizeArguments(array($arguments)));

        $arguments = array(array(), array());
        $this->assertSame($arguments, AbstractCommand::normalizeArguments($arguments));

        $arguments = array(new \stdClass());
        $this->assertSame($arguments, AbstractCommand::normalizeArguments($arguments));
    }

    /**
     * @group disconnected
     */
    public function testNormalizeVariadic()
    {
        $arguments = array('key', 'value1', 'value2', 'value3');

        $this->assertSame($arguments, AbstractCommand::normalizeVariadic($arguments));
        $this->assertSame($arguments, AbstractCommand::normalizeVariadic(array('key', array('value1', 'value2', 'value3'))));

        $arguments = array(new \stdClass());
        $this->assertSame($arguments, AbstractCommand::normalizeVariadic($arguments));
    }
}
