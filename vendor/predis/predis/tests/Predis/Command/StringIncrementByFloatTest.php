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
class StringIncrementByFloatTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringIncrementByFloat';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'INCRBYFLOAT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 5.0);
        $expected = array('key', 5.0);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(5.0, $this->getCommand()->parseResponse(5.0));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 5.0);
        $expected = array('prefix:key', 5.0);

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

        $this->assertEquals(10.5, $redis->incrbyfloat('foo', 10.5));
        $this->assertEquals(10.5, $redis->get('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsTheValueOfTheKeyAfterIncrement()
    {
        $redis = $this->getClient();

        $redis->set('foo', 2);

        $this->assertEquals(22.123, $redis->incrbyfloat('foo', 20.123));
        $this->assertEquals(10, $redis->incrbyfloat('foo', -12.123));
        $this->assertEquals(-100.01, $redis->incrbyfloat('foo', -110.01));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR value is not a valid float
     */
    public function testThrowsExceptionOnDecrementValueNotFloat()
    {
        $redis = $this->getClient();

        $redis->incrbyfloat('foo', 'bar');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR value is not a valid float
     */
    public function testThrowsExceptionOnKeyValueNotFloat()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->incrbyfloat('foo', 10.0);
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
        $redis->incrbyfloat('metavars', 10.0);
    }
}
