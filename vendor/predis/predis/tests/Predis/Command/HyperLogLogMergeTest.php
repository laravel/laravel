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
class HyperLogLogMergeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HyperLogLogMerge';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PFMERGE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key:1', 'key:2', 'key:3');
        $expected = array('key:1', 'key:2', 'key:3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsFieldsAsSingleArray()
    {
        $arguments = array(array('key:1', 'key:2', 'key:3'));
        $expected = array('key:1', 'key:2', 'key:3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
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

        $redis->pfadd('metavars:1', 'foo', 'hoge');
        $redis->lpush('metavars:2', 'foofoo');
        $redis->pfmerge('metavars:1', 'metavars:2');
    }
}
