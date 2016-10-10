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
class HyperLogLogAddTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HyperLogLogAdd';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PFADD';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'a', 'b', 'c');
        $expected = array('key', 'a', 'b', 'c');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsFieldsAsSingleArray()
    {
        $arguments = array('key', array('a', 'b', 'c'));
        $expected = array('key', 'a', 'b', 'c');

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

        $this->assertSame(false, $command->parseResponse(0));
        $this->assertSame(true, $command->parseResponse(1));
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
        $redis->pfadd('metavars', 'foofoo');
    }
}
