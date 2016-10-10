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
 * @group realm-server
 */
class ServerObjectTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerObject';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'OBJECT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('REFCOUNT', 'key');
        $expected = array('REFCOUNT', 'key');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('ziplist', $this->getCommand()->parseResponse('ziplist'));
    }

    /**
     * @group connected
     */
    public function testObjectRefcount()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $this->assertInternalType('integer', $redis->object('REFCOUNT', 'foo'));
    }

    /**
     * @group connected
     */
    public function testObjectIdletime()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $this->assertInternalType('integer', $redis->object('IDLETIME', 'foo'));
    }

    /**
     * @group connected
     */
    public function testObjectEncoding()
    {
        $redis = $this->getClient();

        $redis->lpush('list:metavars', 'foo', 'bar');
        $this->assertSame('ziplist', $redis->object('ENCODING', 'list:metavars'));
    }

    /**
     * @group connected
     */
    public function testReturnsNullOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertNull($redis->object('REFCOUNT', 'foo'));
        $this->assertNull($redis->object('IDLETIME', 'foo'));
        $this->assertNull($redis->object('ENCODING', 'foo'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnInvalidSubcommand()
    {
        $redis = $this->getClient();

        $redis->object('INVALID', 'foo');
    }
}
