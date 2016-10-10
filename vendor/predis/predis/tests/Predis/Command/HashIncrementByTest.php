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
 * @group realm-hash
 */
class HashIncrementByTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HashIncrementBy';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'HINCRBY';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'field', 10);
        $expected = array('key', 'field', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(10, $this->getCommand()->parseResponse(10));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 'field', 10);
        $expected = array('prefix:key', 'field', 10);

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
    public function testIncrementsValueOfFieldByInteger()
    {
        $redis = $this->getClient();

        $this->assertSame(10, $redis->hincrby('metavars', 'foo', 10));
        $this->assertSame(5, $redis->hincrby('metavars', 'hoge', 5));
        $this->assertSame(15, $redis->hincrby('metavars', 'hoge', 10));
        $this->assertSame(array('foo' => '10', 'hoge' => '15'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     */
    public function testDecrementsValueOfFieldByInteger()
    {
        $redis = $this->getClient();

        $this->assertSame(-10, $redis->hincrby('metavars', 'foo', -10));
        $this->assertSame(-5, $redis->hincrby('metavars', 'hoge', -5));
        $this->assertSame(-15, $redis->hincrby('metavars', 'hoge', -10));
        $this->assertSame(array('foo' => '-10', 'hoge' => '-15'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR hash value is not an integer
     */
    public function testThrowsExceptionOnStringField()
    {
        $redis = $this->getClient();

        $redis->hset('metavars', 'foo', 'bar');
        $redis->hincrby('metavars', 'foo', 10);
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
        $redis->hincrby('foo', 'bar', 10);
    }
}
