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
class ZSetRangeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetRange';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZRANGE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('zset', 0, 100, array('withscores' => true));
        $expected = array('zset', 0, 100, 'WITHSCORES');

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
        $arguments = array('zset', 0, 100, array('withscores' => true));
        $expected = array('prefix:zset', 0, 100, 'WITHSCORES');

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
    public function testReturnsElementsInRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');

        $this->assertSame(array(), $redis->zrange('letters', 1, 0));
        $this->assertSame(array('a'), $redis->zrange('letters', 0, 0));
        $this->assertSame(array('a', 'b', 'c', 'd'), $redis->zrange('letters', 0, 3));

        $this->assertSame(array('a', 'b', 'c', 'd', 'e', 'f'), $redis->zrange('letters', 0, -1));
        $this->assertSame(array('a', 'b', 'c'), $redis->zrange('letters', 0, -4));
        $this->assertSame(array('c'), $redis->zrange('letters', 2, -4));
        $this->assertSame(array('a', 'b', 'c', 'd', 'e', 'f'), $redis->zrange('letters', -100, 100));

        $this->assertSame(array(), $redis->zrange('unknown', 0, 30));
    }

    /**
     * @group connected
     */
    public function testRangeWithWithscoresModifier()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', -10, 'a', 0, 'b', 10, 'c', 20, 'd', 20, 'e', 30, 'f');
        $expected = array(array('c', '10'), array('d', '20'), array('e', '20'));

        $this->assertSame($expected, $redis->zrange('letters', 2, 4, 'withscores'));
        $this->assertSame($expected, $redis->zrange('letters', 2, 4, array('withscores' => true)));
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
        $redis->zrange('foo', 0, 10);
    }
}
