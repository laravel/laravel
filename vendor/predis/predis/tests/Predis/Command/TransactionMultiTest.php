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
 * @group realm-transaction
 */
class TransactionMultiTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\TransactionMulti';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'MULTI';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $command = $this->getCommand();
        $command->setArguments(array());

        $this->assertSame(array(), $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertTrue($this->getCommand()->parseResponse(true));
    }

    /**
     * @group connected
     */
    public function testInitializesNewTransaction()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->multi());
        $this->assertSame('QUEUED', (string) $redis->echo('tx1'));
        $this->assertSame('QUEUED', (string) $redis->echo('tx2'));
    }

    /**
     * @group connected
     */
    public function testActuallyReturnsReplyObjectAbstraction()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->multi());
        $this->assertInstanceOf('Predis\ResponseObjectInterface', $redis->echo('tx1'));
        $this->assertInstanceOf('Predis\ResponseQueued', $redis->echo('tx2'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR MULTI calls can not be nested
     */
    public function testThrowsExceptionWhenCallingMultiInsideTransaction()
    {
        $redis = $this->getClient();

        $redis->multi();
        $redis->multi();
    }
}
