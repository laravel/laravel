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
class StringSetBitTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringSetBit';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SETBIT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 7, 1);
        $expected = array('key', 7, 1);

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
        $arguments = array('key', 7, 1);
        $expected = array('prefix:key', 7, 1);

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
    public function testCanSetBitsOfStrings()
    {
        $redis = $this->getClient();

        $redis->set('key:binary', "\x80\x00\00\x01");

        $this->assertEquals(1, $redis->setbit('key:binary', 0, 0));
        $this->assertEquals(0, $redis->setbit('key:binary', 0, 0));
        $this->assertEquals("\x00\x00\00\x01", $redis->get('key:binary'));
    }

    /**
     * @group connected
     */
    public function testCreatesNewKeyOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertSame(0, $redis->setbit('key:binary', 31, 1));
        $this->assertSame(0, $redis->setbit('key:binary', 0, 1));
        $this->assertSame(4, $redis->strlen('key:binary'));
        $this->assertSame("\x80\x00\00\x01", $redis->get('key:binary'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR bit is not an integer or out of range
     */
    public function testThrowsExceptionOnInvalidBitValue()
    {
        $redis = $this->getClient()->setbit('key:binary', 10, 255);
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR bit offset is not an integer or out of range
     */
    public function testThrowsExceptionOnNegativeOffset()
    {
        $redis = $this->getClient()->setbit('key:binary', -1, 1);
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR bit offset is not an integer or out of range
     */
    public function testThrowsExceptionOnInvalidOffset()
    {
        $redis = $this->getClient()->setbit('key:binary', 'invalid', 1);
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
        $redis->setbit('metavars', 0, 1);
    }
}
