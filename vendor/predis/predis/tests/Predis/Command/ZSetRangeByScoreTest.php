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
class ZSetRangeByScoreTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetRangeByScore';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZRANGEBYSCORE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $modifiers = array(
            'withscores' => true,
            'limit' => array(0, 100),
        );

        $arguments = array('zset', 0, 100, $modifiers);
        $expected = array('zset', 0, 100, 'LIMIT', 0, 100, 'WITHSCORES');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithStringWithscores()
    {
        $arguments = array('zset', 0, 100, 'withscores');
        $expected = array('zset', 0, 100, 'WITHSCORES');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithNamedLimit()
    {
        $arguments = array('zset', 0, 100, array('limit' => array('offset' => 1, 'count' => 2)));
        $expected = array('zset', 0, 100, 'LIMIT', 1, 2);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('element1', 'element2', 'element3');
        $expected = array('element1', 'element2', 'element3');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testParseResponseWithScores()
    {
        $raw = array('element1', '1', 'element2', '2', 'element3', '3');
        $expected = array(array('element1', '1'), array('element2', '2'), array('element3', '3'));

        $command = $this->getCommandWithArgumentsArray(array('zset', 0, 1, 'withscores'));

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $modifiers = array(
            'withscores' => true,
            'limit' => array(0, 100),
        );

        $arguments = array('zset', 0, 100, $modifiers);
        $expected = array('prefix:zset', 0, 100, 'LIMIT', 0, 100, 'WITHSCORES');

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
     * @group disconnected
     */
    public function testAddsWithscoresModifiersOnlyWhenOptionIsTrue()
    {
        $command = $this->getCommandWithArguments('zset', 0, 100, array('withscores' => true));
        $this->assertSame(array('zset', 0, 100, 'WITHSCORES'), $command->getArguments());

        $command = $this->getCommandWithArguments('zset', 0, 100, array('withscores' => 1));
        $this->assertSame(array('zset', 0, 100, 'WITHSCORES'), $command->getArguments());

        $command = $this->getCommandWithArguments('zset', 0, 100, array('withscores' => false));
        $this->assertSame(array('zset', 0, 100), $command->getArguments());

        $command = $this->getCommandWithArguments('zset', 0, 100, array('withscores' => 0));
        $this->assertSame(array('zset', 0, 100), $command->getArguments());
    }

    /**
     * @group connected
     */
    public function testReturnsElementsInScoreRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');

        $this->assertSame(array('a'), $redis->zrangebyscore('letters', -10, -10));
        $this->assertSame(array('c', 'd', 'e', 'f'), $redis->zrangebyscore('letters', 10, 30));
        $this->assertSame(array('d', 'e'), $redis->zrangebyscore('letters', 20, 20));
        $this->assertSame(array(), $redis->zrangebyscore('letters', 30, 0));

        $this->assertSame(array(), $redis->zrangebyscore('unknown', 0, 30));
    }

    /**
     * @group connected
     */
    public function testInfinityScoreIntervals()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');

        $this->assertSame(array('a', 'b', 'c'), $redis->zrangebyscore('letters', '-inf', 15));
        $this->assertSame(array('d', 'e', 'f'), $redis->zrangebyscore('letters', 15, '+inf'));
        $this->assertSame(array('a', 'b', 'c', 'd', 'e', 'f'), $redis->zrangebyscore('letters', '-inf', '+inf'));
    }

    /**
     * @group connected
     */
    public function testExclusiveScoreIntervals()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');

        $this->assertSame(array('c', 'd', 'e'), $redis->zrangebyscore('letters', 10, '(30'));
        $this->assertSame(array('d', 'e', 'f'), $redis->zrangebyscore('letters', '(10', 30));
        $this->assertSame(array('d', 'e'), $redis->zrangebyscore('letters', '(10', '(30'));
    }

    /**
     * @group connected
     */
    public function testRangeWithWithscoresModifier()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');
        $expected = array(array('c', '10'), array('d', '20'), array('e', '20'));

        $this->assertSame($expected, $redis->zrangebyscore('letters', 10, 20, 'withscores'));
        $this->assertSame($expected, $redis->zrangebyscore('letters', 10, 20, array('withscores' => true)));
    }

    /**
     * @group connected
     */
    public function testRangeWithLimitModifier()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');
        $expected = array('d', 'e');

        $this->assertSame($expected, $redis->zrangebyscore('letters', 10, 20, array('limit' => array(1, 2))));
        $this->assertSame($expected, $redis->zrangebyscore('letters', 10, 20, array('limit' => array('offset' => 1, 'count' => 2))));
    }

    /**
     * @group connected
     */
    public function testRangeWithCombinedModifiers()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');

        $options = array('limit' => array(1, 2), 'withscores' => true);
        $expected = array(array('d', '20'), array('e', '20'));

        $this->assertSame($expected, $redis->zrangebyscore('letters', 10, 20, $options));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->zrangebyscore('foo', 0, 10);
    }
}
