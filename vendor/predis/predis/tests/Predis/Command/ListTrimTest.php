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
class ListTrimTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListTrim';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'LTRIM';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 0, 1);
        $expected = array('key', 0, 1);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertTrue($this->getCommand()->parseResponse(true));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 0, 1);
        $expected = array('prefix:key', 0, 1);

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
    public function testTrimsListWithPositiveStartAndStop()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'l');

        $this->assertTrue($redis->ltrim('letters', 0, 2));
        $this->assertSame(array('a', 'b', 'c'), $redis->lrange('letters', 0, -1));

        $redis->flushdb();
        $redis->rpush('letters', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'l');

        $this->assertTrue($redis->ltrim('letters', 5, 9));
        $this->assertSame(array('f', 'g', 'h', 'i', 'l'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testTrimsListWithPositiveStartAndNegativeStop()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'l');

        $this->assertTrue($redis->ltrim('letters', 0, -6));
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testTrimsListWithNegativeStartAndStop()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'l');

        $this->assertTrue($redis->ltrim('letters', -5, -5));
        $this->assertSame(array('f'), $redis->lrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testHandlesStartAndStopOverflow()
    {
        $redis = $this->getClient();

        $redis->rpush('letters', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'l');

        $this->assertTrue($redis->ltrim('letters', -100, 100));
        $this->assertSame(array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'l'), $redis->lrange('letters', -100, 100));
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
        $redis->ltrim('metavars', 0, 1);
    }
}
