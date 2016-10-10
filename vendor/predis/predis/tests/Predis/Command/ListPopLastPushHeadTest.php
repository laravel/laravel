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
 * @group realm-list
 */
class ListPopLastPushHeadTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListPopLastPushHead';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'RPOPLPUSH';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key:source', 'key:destination');
        $expected = array('key:source', 'key:destination');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('element', $this->getCommand()->parseResponse('element'));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key:source', 'key:destination');
        $expected = array('prefix:key:source', 'prefix:key:destination');

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
    public function testReturnsElementPoppedFromSourceAndPushesToDestination()
    {
        $redis = $this->getClient();

        $redis->rpush('letters:source', 'a', 'b', 'c');

        $this->assertSame('c', $redis->rpoplpush('letters:source', 'letters:destination'));
        $this->assertSame('b', $redis->rpoplpush('letters:source', 'letters:destination'));
        $this->assertSame('a', $redis->rpoplpush('letters:source', 'letters:destination'));

        $this->assertSame(array(), $redis->lrange('letters:source', 0, -1));
        $this->assertSame(array('a', 'b', 'c'), $redis->lrange('letters:destination', 0, -1));
    }

    /**
     * @group connected
     */
    public function testReturnsElementPoppedFromSourceAndPushesToSelf()
    {
        $redis = $this->getClient();

        $redis->rpush('letters:source', 'a', 'b', 'c');

        $this->assertSame('c', $redis->rpoplpush('letters:source', 'letters:source'));
        $this->assertSame('b', $redis->rpoplpush('letters:source', 'letters:source'));
        $this->assertSame('a', $redis->rpoplpush('letters:source', 'letters:source'));

        $this->assertSame(array('a', 'b', 'c'), $redis->lrange('letters:source', 0, -1));
    }

    /**
     * @group connected
     */
    public function testReturnsNullOnEmptySource()
    {
        $redis = $this->getClient();

        $this->assertNull($redis->rpoplpush('key:source', 'key:destination'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongTypeOfSourceKey()
    {
        $redis = $this->getClient();

        $redis->set('key:source', 'foo');
        $redis->rpoplpush('key:source', 'key:destination');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongTypeOfDestinationKey()
    {
        $redis = $this->getClient();

        $redis->rpush('key:source', 'foo');
        $redis->set('key:destination', 'bar');

        $redis->rpoplpush('key:source', 'key:destination');
    }
}
