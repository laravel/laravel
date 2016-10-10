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
 * @group realm-string
 */
class StringDecrementByTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringDecrementBy';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'DECRBY';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 5);
        $expected = array('key', 5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(5, $this->getCommand()->parseResponse(5));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 5);
        $expected = array('prefix:key', 5);

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
    public function testCreatesNewKeyOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertSame(-10, $redis->decrby('foo', 10));
        $this->assertEquals(-10, $redis->get('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsTheValueOfTheKeyAfterDecrement()
    {
        $redis = $this->getClient();

        $redis->set('foo', 10);

        $this->assertSame(6, $redis->decrby('foo', 4));
        $this->assertSame(0, $redis->decrby('foo', 6));
        $this->assertSame(-25, $redis->decrby('foo', 25));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR value is not an integer or out of range
     */
    public function testThrowsExceptionOnDecrementValueNotInteger()
    {
        $redis = $this->getClient();

        $redis->decrby('foo', 'bar');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR value is not an integer or out of range
     */
    public function testThrowsExceptionOnKeyValueNotInteger()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->decrby('foo', 5);
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo');
        $redis->decrby('metavars', 10);
    }
}
