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
class ZSetRangeByLexTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetRangeByLex';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZRANGEBYLEX';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $modifiers = array(
            'limit' => array(0, 100),
        );

        $arguments = array('zset', '[a', '[z', $modifiers);
        $expected = array('zset', '[a', '[z', 'LIMIT', 0, 100);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithNamedLimit()
    {
        $arguments = array('zset', '[a', '[z', array('limit' => array('offset' => 1, 'count' => 2)));
        $expected = array('zset', '[a', '[z', 'LIMIT', 1, 2);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('a', 'b', 'c');
        $expected = array('a', 'b', 'c');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $modifiers = array(
            'limit' => array(0, 100),
        );

        $arguments = array('zset', '[a', '[z', $modifiers);
        $expected = array('prefix:zset', '[a', '[z', 'LIMIT', 0, 100);

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
    public function testReturnsElementsInWholeRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(array('a', 'b', 'c', 'd', 'e', 'f', 'g'), $redis->zrangebylex('letters', '-', '+'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '+', '-'));
        $this->assertSame(array(), $redis->zrangebylex('unknown', '-', '+'));
        $this->assertSame(array(), $redis->zrangebylex('unknown', '+', '-'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testReturnsElementsInInclusiveRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(array('a'), $redis->zrangebylex('letters', '[a', '[a'));
        $this->assertSame(array('c', 'd', 'e', 'f'), $redis->zrangebylex('letters', '[c', '[f'));
        $this->assertSame(array('a', 'b', 'c'), $redis->zrangebylex('letters', '-', '[c'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '+', '[c'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '[x', '[z'));
        $this->assertSame(array(), $redis->zrangebylex('unknown', '[0', '[1'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testReturnsElementsInExclusiveRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(array(), $redis->zrangebylex('letters', '(a', '(a'));
        $this->assertSame(array('d', 'e'), $redis->zrangebylex('letters', '(c', '(f'));
        $this->assertSame(array('a', 'b'), $redis->zrangebylex('letters', '-', '(c'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '+', '(c'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '(x', '(z'));
        $this->assertSame(array(), $redis->zrangebylex('unknown', '(0', '(1'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testReturnsElementsInMixedRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(array(), $redis->zrangebylex('letters', '[a', '(a'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '(a', '[a'));
        $this->assertSame(array('c', 'd', 'e'), $redis->zrangebylex('letters', '[c', '(f'));
        $this->assertSame(array('d', 'e', 'f'), $redis->zrangebylex('letters', '(c', '[f'));
        $this->assertSame(array(), $redis->zrangebylex('unknown', '[0', '(5'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     */
    public function testRangeWithLimitModifier()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0, 'a', 0, 'b', 0, 'c', 0, 'd', 0, 'e', 0, 'f', 0, 'g');

        $this->assertSame(array('c', 'd', 'e'), $redis->zrangebylex('letters', '-', '+', 'LIMIT', '2', '3'));
        $this->assertSame(array('c', 'd', 'e'), $redis->zrangebylex('letters', '-', '+', array('limit' => array(2, 3))));
        $this->assertSame(array('c', 'd', 'e'), $redis->zrangebylex('letters', '-', '+', array('limit' => array('offset' => 2, 'count' => 3))));
        $this->assertSame(array(), $redis->zrangebylex('letters', '[a', '[f', 'LIMIT', '2', '0'));
        $this->assertSame(array(), $redis->zrangebylex('letters', '[a', '[f', 'LIMIT', '-4', '2'));
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
        $redis->zrangebylex('letters', 'b', 'f');
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
        $redis->zrangebylex('foo', '-', '+');
    }
}
