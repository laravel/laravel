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
class ZSetCountTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetCount';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZCOUNT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 0, 10);
        $expected = array('key', 0, 10);

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
        $arguments = array('key', 0, 10);
        $expected = array('prefix:key', 0, 10);

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
    public function testReturnsNumberOfElementsInGivenScoreRange()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(5, $redis->zcount('letters', 0, 100));
        $this->assertSame(5, $redis->zcount('letters', -100, 100));
        $this->assertSame(2, $redis->zcount('letters', 25, 45));
        $this->assertSame(1, $redis->zcount('letters', 20, 20));
        $this->assertSame(0, $redis->zcount('letters', 0, 0));

        $this->assertSame(0, $redis->zcount('unknown', 0, 100));
    }

    /**
     * @group connected
     */
    public function testInfinityScoreIntervals()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(3, $redis->zcount('letters', '-inf', 30));
        $this->assertSame(3, $redis->zcount('letters', 30, '+inf'));
        $this->assertSame(5, $redis->zcount('letters', '-inf', '+inf'));
    }

    /**
     * @group connected
     */
    public function testExclusiveScoreIntervals()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(2, $redis->zcount('letters', 10, '(30'));
        $this->assertSame(2, $redis->zcount('letters', '(10', 30));
        $this->assertSame(1, $redis->zcount('letters', '(10', '(30'));
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
        $redis->zcount('foo', 0, 10);
    }
}
