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
class StringDecrementTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringDecrement';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'DECR';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key');
        $expected = array('key');

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
        $arguments = array('key');
        $expected = array('prefix:key');

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

        $this->assertSame(-1, $redis->decr('foo'));
        $this->assertEquals(-1, $redis->get('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsTheValueOfTheKeyAfterDecrement()
    {
        $redis = $this->getClient();

        $redis->set('foo', 1);

        $this->assertSame(0, $redis->decr('foo'));
        $this->assertSame(-1, $redis->decr('foo'));
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
        $redis->decr('foo');
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
        $redis->decr('metavars');
    }
}
