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
class ServerTimeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerTime';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'TIME';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array();
        $expected = array();

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $expected = array(1331114908, 453990);
        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($expected));
    }

    /**
     * @group connected
     */
    public function testReturnsServerTime()
    {
        $redis = $this->getClient();

        $this->assertInternalType('array', $time = $redis->time());
        $this->assertInternalType('string', $time[0]);
        $this->assertInternalType('string', $time[1]);
    }
}
