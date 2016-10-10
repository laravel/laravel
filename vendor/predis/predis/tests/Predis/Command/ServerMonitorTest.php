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
 * @group realm-monitor
 */
class ServerMonitorTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerMonitor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'MONITOR';
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
    public function testReturnsTrueAndReadsEventsFromTheConnection()
    {
        $connection = $this->getClient()->getConnection();
        $command = $this->getCommand();

        $this->assertTrue($connection->executeCommand($command));

        // NOTE: Starting with 2.6 Redis does not return the "MONITOR" message after
        // +OK to the client that issued the MONITOR command.
        if (version_compare($this->getProfile()->getVersion(), '2.4', '<=')) {
            $this->assertRegExp('/\d+.\d+(\s?\(db \d+\))? "MONITOR"/', $connection->read());
        }
    }
}
