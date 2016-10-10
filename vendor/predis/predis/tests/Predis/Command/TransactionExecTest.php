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
class TransactionExecTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\TransactionExec';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'EXEC';
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
        $raw = array('tx1', 'tx2');
        $expected = array('tx1', 'tx2');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group connected
     */
    public function testExecutesTransactionAndReturnsArrayOfReplies()
    {
        $redis = $this->getClient();

        $redis->multi();
        $redis->echo('tx1');
        $redis->echo('tx2');

        $this->assertSame(array('tx1', 'tx2'), $redis->exec());
    }

    /**
     * @group connected
     */
    public function testReturnsEmptyArrayOnEmptyTransactions()
    {
        $redis = $this->getClient();

        $redis->multi();

        $this->assertSame(array(), $redis->exec());
    }

    /**
     * @group connected
     */
    public function testRepliesOfTransactionsAreNotParsed()
    {
        $redis = $this->getClient();

        $redis->multi();
        $redis->ping();
        $redis->set('foo', 'bar');
        $redis->exists('foo');

        $this->assertSame(array('PONG', true, 1), $redis->exec());
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR EXEC without MULTI
     */
    public function testThrowsExceptionWhenCallingOutsideTransaction()
    {
        $redis = $this->getClient();

        $redis->exec();
    }
}
