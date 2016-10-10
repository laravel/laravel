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
class TransactionDiscardTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\TransactionDiscard';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'DISCARD';
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
    public function testAbortsTransactionAndRestoresNormalFlow()
    {
        $redis = $this->getClient();

        $redis->multi();

        $this->assertInstanceOf('Predis\ResponseQueued', $redis->set('foo', 'bar'));
        $this->assertTrue($redis->discard());
        $this->assertFalse($redis->exists('foo'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR DISCARD without MULTI
     */
    public function testThrowsExceptionWhenCallingOutsideTransaction()
    {
        $redis = $this->getClient();

        $redis->discard();
    }
}
