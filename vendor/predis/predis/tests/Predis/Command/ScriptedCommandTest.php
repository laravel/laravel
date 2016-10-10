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
 * @group realm-scripting
 */
class ScriptedCommandTest extends PredisTestCase
{
    const LUA_SCRIPT = 'return { KEYS[1], KEYS[2], ARGV[1], ARGV[2] }';
    const LUA_SCRIPT_SHA1 = '6e07f61f502e36d123fe28523076af588f5c315e';

    /**
     * @group disconnected
     */
    public function testGetArguments()
    {
        $arguments = array('key1', 'key2', 'value1', 'value2');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->once())
                ->method('getKeysCount')
                ->will($this->returnValue(2));
        $command->setArguments($arguments);

        $this->assertSame(array_merge(array(self::LUA_SCRIPT_SHA1, 2), $arguments), $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testGetArgumentsWithNegativeKeysCount()
    {
        $arguments = array('key1', 'key2', 'value1', 'value2');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->once())
                ->method('getKeysCount')
                ->will($this->returnValue(-2));
        $command->setArguments($arguments);

        $this->assertSame(array_merge(array(self::LUA_SCRIPT_SHA1, 2), $arguments), $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testGetArgumentsWithZeroKeysCount()
    {
        $arguments = array('value1', 'value2', 'value3');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->once())
                ->method('getKeysCount')
                ->will($this->returnValue(0));
        $command->setArguments($arguments);

        $this->assertSame(array_merge(array(self::LUA_SCRIPT_SHA1, 0), $arguments), $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testGetKeys()
    {
        $arguments = array('key1', 'key2', 'value1', 'value2');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->exactly(2))
                ->method('getKeysCount')
                ->will($this->returnValue(2));
        $command->setArguments($arguments);

        $this->assertSame(array('key1', 'key2'), $command->getKeys());
    }

    /**
     * @group disconnected
     */
    public function testGetKeysWithZeroKeysCount()
    {
        $arguments = array('value1', 'value2', 'value3');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->exactly(2))
                ->method('getKeysCount')
                ->will($this->returnValue(0));
        $command->setArguments($arguments);

        $this->assertSame(array(), $command->getKeys());
    }

    /**
     * @group disconnected
     */
    public function testGetKeysWithNegativeKeysCount()
    {
        $arguments = array('key1', 'key2', 'value1', 'value2');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->exactly(2))
                ->method('getKeysCount')
                ->will($this->returnValue(-2));
        $command->setArguments($arguments);

        $this->assertSame(array('key1', 'key2'), $command->getKeys());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('foo', 'hoge', 'bar', 'piyo');
        $expected = array('prefix:foo', 'prefix:hoge');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->exactly(2))
                ->method('getKeysCount')
                ->will($this->returnValue(2));
        $command->setArguments($arguments);

        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getKeys());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysWithNegativeKeysCount()
    {
        $arguments = array('foo', 'hoge', 'bar', 'piyo');
        $expected = array('prefix:foo', 'prefix:hoge');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->exactly(2))
                ->method('getKeysCount')
                ->will($this->returnValue(-2));
        $command->setArguments($arguments);

        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getKeys());
    }

    /**
     * @group disconnected
     */
    public function testGetScriptHash()
    {
        $arguments = array('key1', 'key2', 'value1', 'value2');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue(self::LUA_SCRIPT));
        $command->expects($this->once())
                ->method('getKeysCount')
                ->will($this->returnValue(2));
        $command->setArguments($arguments);

        $this->assertSame(self::LUA_SCRIPT_SHA1, $command->getScriptHash());
    }
}
