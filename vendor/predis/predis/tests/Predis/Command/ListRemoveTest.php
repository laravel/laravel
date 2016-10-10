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
class ListRemoveTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListRemove';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'LREM';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 1, 'value');
        $expected = array('key', 1, 'value');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 1, 'value');
        $expected = array('prefix:key', 1, 'value');

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
    public function testRemovesMatchingElementsFromHeadToTail()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', '_', 'b', '_', 'c', '_', 'd', '_');

        $this->assertSame(2, $redis->lrem('letters', 2, '_'));
        $this->assertSame(array('a', 'b', 'c', '_', 'd', '_'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testRemovesMatchingElementsFromTailToHead()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', '_', 'b', '_', 'c', '_', 'd', '_');

        $this->assertSame(2, $redis->lrem('letters', -2, '_'));
        $this->assertSame(array('a', '_', 'b', '_', 'c', 'd'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testRemovesAllMatchingElements()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', '_', 'b', '_', 'c', '_', 'd', '_');

        $this->assertSame(4, $redis->lrem('letters', 0, '_'));
        $this->assertSame(array('a', 'b', 'c', 'd'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testReturnsZeroOnNonMatchingElementsOrEmptyList()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'b', 'c', 'd');

        $this->assertSame(0, $redis->lrem('letters', 0, 'z'));
        $this->assertSame(0, $redis->lrem('digits', 0, 100));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('metavars', 'foo');
        $redis->lrem('metavars', 0, 0);
    }
}
