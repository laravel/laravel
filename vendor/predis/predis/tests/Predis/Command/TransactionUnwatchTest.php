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
class TransactionUnwatchTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\TransactionUnwatch';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'UNWATCH';
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
    public function testUnwatchWatchedKeys()
    {
        $redis1 = $this->getClient();
        $redis2 = $this->getClient();

        $redis1->set('foo', 'bar');
        $redis1->watch('foo');
        $this->assertTrue($redis1->unwatch());
        $redis1->multi();
        $redis1->get('foo');

        $redis2->set('foo', 'hijacked');

        $this->assertSame(array('hijacked'), $redis1->exec());
    }

    /**
     * @group connected
     */
    public function testCanBeCalledInsideTransaction()
    {
        $redis = $this->getClient();

        $redis->multi();
        $this->assertInstanceOf('Predis\ResponseQueued', $redis->unwatch());
    }
}
