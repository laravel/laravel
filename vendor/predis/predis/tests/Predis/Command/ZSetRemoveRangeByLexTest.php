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
 * @group realm-zset
 */
class ZSetRemoveRangeByLexTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetRemoveRangeByLex';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZREMRANGEBYLEX';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', '[a', '[b');
        $expected = array('key', '[a', '[b');

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
        $arguments = array('key', '[a', '[b');
        $expected = array('prefix:key', '[a', '[b');

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
     * @requiresRedisVersion >= 2.8.9
     */
    public function testRemovesRangeByLexWithWholeRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(0, $redis->zremrangebylex('letters', '+', '-'));
        $this->assertSame(7, $redis->zremrangebylex('letters', '-', '+'));

        $this->assertSame(array(), $redis->zrange('letters', 0, -1));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testRemovesRangeByLexWithInclusiveRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(3, $redis->zremrangebylex('letters', '[b', '[d'));
        $this->assertSame(array('a', 'e', 'f', 'g'), $redis->zrange('letters', 0, -1));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testRemovesRangeByLexWithExclusiveRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(3, $redis->zremrangebylex('letters', '(a', '(e'));
        $this->assertSame(array('a', 'e', 'f', 'g'), $redis->zrange('letters', 0, -1));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testRemovesRangeByLexWithMixedRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(3, $redis->zremrangebylex('letters', '[b', '(e'));
        $this->assertSame(array('a', 'e', 'f', 'g'), $redis->zrange('letters', 0, -1));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage min or max not valid string range item
     */
    public function testThrowsExceptionOnInvalidRangeFormat()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');
        $redis->zremrangebylex('letters', 'b', 'f');
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->zremrangebylex('foo', '[a', '[b');
    }
}
