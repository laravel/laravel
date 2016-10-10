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
class ZSetIntersectionStoreTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetIntersectionStore';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZINTERSTORE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $modifiers = array(
            'aggregate' => 'sum',
            'weights' => array(10, 100),
        );
        $arguments = array('zset:destination', 2, 'zset1', 'zset2', $modifiers);

        $expected = array(
            'zset:destination', 2, 'zset1', 'zset2', 'WEIGHTS', 10, 100, 'AGGREGATE', 'sum'
        );

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsSourceKeysAsSingleArray()
    {
        $modifiers = array(
            'aggregate' => 'sum',
            'weights' => array(10, 100),
        );
        $arguments = array('zset:destination', array('zset1', 'zset2'), $modifiers);

        $expected = array(
            'zset:destination', 2, 'zset1', 'zset2', 'WEIGHTS', 10, 100, 'AGGREGATE', 'sum'
        );

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
        $modifiers = array(
            'aggregate' => 'sum',
            'weights' => array(10, 100),
        );
        $arguments = array('zset:destination', 2, 'zset1', 'zset2', $modifiers);

        $expected = array(
            'prefix:zset:destination', 2, 'prefix:zset1', 'prefix:zset2', 'WEIGHTS', 10, 100, 'AGGREGATE', 'sum'
        );

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
    public function testStoresIntersectionInNewSortedSet()
    {
        $redis = $this->getClient();

        $redis->zadd('letters:1st', 1, 'a', 2, 'b', 3, 'c');
        $redis->zadd('letters:2nd', 1, 'b', 2, 'c', 3, 'd');

        $this->assertSame(2, $redis->zinterstore('letters:out', 2, 'letters:1st', 'letters:2nd'));
        $this->assertSame(array(array('b', '3'), array('c', '5')), $redis->zrange('letters:out', 0, -1, 'withscores'));

        $this->assertSame(0, $redis->zinterstore('letters:out', 2, 'letters:1st', 'letters:void'));
        $this->assertSame(0, $redis->zinterstore('letters:out', 2, 'letters:void', 'letters:2nd'));
        $this->assertSame(0, $redis->zinterstore('letters:out', 2, 'letters:void', 'letters:void'));
    }

    /**
     * @group connected
     */
    public function testStoresIntersectionWithAggregateModifier()
    {
        $redis = $this->getClient();

        $redis->zadd('letters:1st', 1, 'a', 2, 'b', 3, 'c');
        $redis->zadd('letters:2nd', 1, 'b', 2, 'c', 3, 'd');

        $options = array('aggregate' => 'min');
        $this->assertSame(2, $redis->zinterstore('letters:min', 2, 'letters:1st', 'letters:2nd', $options));
        $this->assertSame(array(array('b', '1'), array('c', '2')), $redis->zrange('letters:min', 0, -1, 'withscores'));

        $options = array('aggregate' => 'max');
        $this->assertSame(2, $redis->zinterstore('letters:max', 2, 'letters:1st', 'letters:2nd', $options));
        $this->assertSame(array(array('b', '2'), array('c', '3')), $redis->zrange('letters:max', 0, -1, 'withscores'));

        $options = array('aggregate' => 'sum');
        $this->assertSame(2, $redis->zinterstore('letters:sum', 2, 'letters:1st', 'letters:2nd', $options));
        $this->assertSame(array(array('b', '3'), array('c', '5')), $redis->zrange('letters:sum', 0, -1, 'withscores'));
    }

    /**
     * @group connected
     */
    public function testStoresIntersectionWithWeightsModifier()
    {
        $redis = $this->getClient();

        $redis->zadd('letters:1st', 1, 'a', 2, 'b', 3, 'c');
        $redis->zadd('letters:2nd', 1, 'b', 2, 'c', 3, 'd');

        $options = array('weights' => array(2, 3));
        $this->assertSame(2, $redis->zinterstore('letters:out', 2, 'letters:1st', 'letters:2nd', $options));
        $this->assertSame(array(array('b', '7'), array('c', '12')), $redis->zrange('letters:out', 0, -1, 'withscores'));
    }

    /**
     * @group connected
     */
    public function testStoresIntersectionWithCombinedModifiers()
    {
        $redis = $this->getClient();

        $redis->zadd('letters:1st', 1, 'a', 2, 'b', 3, 'c');
        $redis->zadd('letters:2nd', 1, 'b', 2, 'c', 3, 'd');

        $options = array('aggregate' => 'max', 'weights' => array(10, 15));
        $this->assertSame(2, $redis->zinterstore('letters:out', 2, 'letters:1st', 'letters:2nd', $options));
        $this->assertSame(array(array('b', '20'), array('c', '30')), $redis->zrange('letters:out', 0, -1, 'withscores'));
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
        $redis->zinterstore('zset:destination', 1, 'foo');
    }
}
