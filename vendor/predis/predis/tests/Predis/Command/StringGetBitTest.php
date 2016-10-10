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

/**
 * @group commands
 * @group realm-string
 */
class StringGetBitTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringGetBit';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'GETBIT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 100);
        $expected = array('key', 100);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();
        $this->assertSame(0, $command->parseResponse(0));
        $this->assertSame(1, $command->parseResponse(1));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 100);
        $expected = array('prefix:key', 100);

        $command = $this->getCommandWithArgumentsArray($arguments);
        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysIgnoredOnEmptyArguments()
    {
        $command = $this->getCommand();
        $command->prefixKeys('prefix:');

        $this->assertSame(array(), $command->getArguments());
    }

    /**
     * @group connected
     */
    public function testCanGetBitsFromString()
    {
        $redis = $this->getClient();

        $redis->set('key:binary', "\x80\x00\00\x01");

        $this->assertSame(1, $redis->getbit('key:binary', 0));
        $this->assertSame(0, $redis->getbit('key:binary', 15));
        $this->assertSame(1, $redis->getbit('key:binary', 31));
        $this->assertSame(0, $redis->getbit('key:binary', 63));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR bit offset is not an integer or out of range
     */
    public function testThrowsExceptionOnNegativeOffset()
    {
        $redis = $this->getClient();

        $redis->set('key:binary', "\x80\x00\00\x01");
        $redis->getbit('key:binary', -1);
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR bit offset is not an integer or out of range
     */
    public function testThrowsExceptionOnInvalidOffset()
    {
        $redis = $this->getClient();

        $redis->set('key:binary', "\x80\x00\00\x01");
        $redis->getbit('key:binary', 'invalid');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo');
        $redis->getbit('metavars', '1');
    }
}
