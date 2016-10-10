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
 * @group realm-hyperloglog
 * @todo Add integration tests depending on the minor redis version
 */
class HyperLogLogCountTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HyperLogLogCount';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PFCOUNT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key:1', 'key:2');
        $expected = array('key:1', 'key:2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsFieldsAsSingleArray()
    {
        $arguments = array(array('key:1', 'key:2'));
        $expected = array('key:1', 'key:2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $this->assertSame(0, $command->parseResponse(0));
        $this->assertSame(1, $command->parseResponse(1));
        $this->assertSame(10, $command->parseResponse(10));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo', 'hoge');
        $redis->pfcount('metavars');
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.9
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongTypeOfAtLeastOneKey()
    {
        $redis = $this->getClient();

        $redis->pfadd('metavars:1', 'foo', 'hoge');
        $redis->lpush('metavars:2', 'foofoo');
        $redis->pfcount('metavars:1', 'metavars:2');
    }
}
