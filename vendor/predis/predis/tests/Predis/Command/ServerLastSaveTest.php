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
class ServerLastSaveTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerLastSave';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'LASTSAVE';
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
    public function testReturnsIntegerValue()
    {
        $redis = $this->getClient();

        $this->assertInternalType('integer', $redis->lastsave());
    }
}
