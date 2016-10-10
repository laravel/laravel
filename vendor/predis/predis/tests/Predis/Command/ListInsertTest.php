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
class ListInsertTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListInsert';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'LINSERT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'before', 'value1', 'value2');
        $expected = array('key', 'before', 'value1', 'value2');

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
        $arguments = array('key', 'before', 'value1', 'value2');
        $expected = array('prefix:key', 'before', 'value1', 'value2');

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
    public function testReturnsLengthOfListAfterInser()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'c', 'e');

        $this->assertSame(4, $redis->linsert('letters', 'before', 'c', 'b'));
        $this->assertSame(5, $redis->linsert('letters', 'after', 'c', 'd'));
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testReturnsNegativeLengthOnFailedInsert()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'c', 'e');

        $this->assertSame(-1, $redis->linsert('letters', 'before', 'n', 'm'));
        $this->assertSame(-1, $redis->linsert('letters', 'after', 'o', 'p'));
    }

    /**
     * @group connected
     */
    public function testReturnsZeroLengthOnNonExistingList()
    {
        $redis = $this->getClient();

        $this->assertSame(0, $redis->linsert('letters', 'after', 'a', 'b'));
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
        $redis->linsert('foo', 'BEFORE', 'bar', 'baz');
    }
}
