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
class ListPushTailTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListPushTail';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'RPUSH';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'value1', 'value2', 'value3');
        $expected = array('key', 'value1', 'value2', 'value3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsValuesAsSingleArray()
    {
        $arguments = array('key', array('value1', 'value2', 'value3'));
        $expected = array('key', 'value1', 'value2', 'value3');

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
        $arguments = array('key', 'value1', 'value2', 'value3');
        $expected = array('prefix:key', 'value1', 'value2', 'value3');

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
    public function testPushesElementsToHeadOfList()
    {
        $redis = $this->getClient();

        // NOTE: List push operations return the list length since Redis commit 520b5a3
        $this->assertSame(1, $redis->rpush('metavars', 'foo'));
        $this->assertSame(2, $redis->rpush('metavars', 'hoge'));
        $this->assertSame(array('foo', 'hoge'), $redis->lrange('metavars', 0, -1));
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
        $redis->rpush('metavars', 'hoge');
    }
}
