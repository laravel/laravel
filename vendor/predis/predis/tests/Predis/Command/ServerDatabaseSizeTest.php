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
class ServerDatabaseSizeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerDatabaseSize';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'DBSIZE';
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
        $this->assertSame(100, $this->getCommand()->parseResponse(100));
    }

    /**
     * @group connected
     */
    public function testReturnsCurrentSizeOfDatabase()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $this->assertGreaterThan(0, $redis->dbsize());
    }
}
