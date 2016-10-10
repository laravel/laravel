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
 * @group realm-connection
 */
class ConnectionAuthTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ConnectionAuth';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'AUTH';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('password');
        $expected = array('password');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = null;
        $expected = null;

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }
}
