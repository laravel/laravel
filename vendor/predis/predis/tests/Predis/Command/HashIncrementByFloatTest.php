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
class HashIncrementByFloatTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HashIncrementByFloat';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'HINCRBYFLOAT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'field', 10.5);
        $expected = array('key', 'field', 10.5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(10.5, $this->getCommand()->parseResponse(10.5));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 'field', 10.5);
        $expected = array('prefix:key', 'field', 10.5);

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
    public function testIncrementsValueOfFieldByFloat()
    {
        $redis = $this->getClient();

        $this->assertSame('10.5', $redis->hincrbyfloat('metavars', 'foo', 10.5));
        $this->assertSame('10.001', $redis->hincrbyfloat('metavars', 'hoge', 10.001));
        $this->assertSame('11', $redis->hincrbyfloat('metavars', 'hoge', 0.999));
        $this->assertSame(array('foo' => '10.5', 'hoge' => '11'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     */
    public function testDecrementsValueOfFieldByFloat()
    {
        $redis = $this->getClient();

        $this->assertSame('-10.5', $redis->hincrbyfloat('metavars', 'foo', -10.5));
        $this->assertSame('-10.001', $redis->hincrbyfloat('metavars', 'hoge', -10.001));
        $this->assertSame('-11', $redis->hincrbyfloat('metavars', 'hoge', -0.999));
        $this->assertSame(array('foo' => '-10.5', 'hoge' => '-11'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR hash value is not a valid float
     */
    public function testThrowsExceptionOnStringField()
    {
        $redis = $this->getClient();

        $redis->hset('metavars', 'foo', 'bar');
        $redis->hincrbyfloat('metavars', 'foo', 10.0);
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
        $redis->hincrbyfloat('foo', 'bar', 10.5);
    }
}
