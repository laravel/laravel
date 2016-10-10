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
 * @group realm-pubsub
 */
class PubSubPublishTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\PubSubPublish';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PUBLISH';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('channel', 'message');
        $expected = array('channel', 'message');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('channel', 'message');
        $expected = array('prefix:channel', 'message');

        $command = $this->getCommandWithArgumentsArray($arguments);
        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysIgnoredOnEmptyArguments()
    {
        $command = $this->getCommand();
        $command->prefixKeys('prefix:');

        $this->assertSame(array(), $command->getArguments());
    }

    /**
     * @group connected
     */
    public function testPublishesMessagesToChannel()
    {
        $redis1 = $this->getClient();
        $redis2 = $this->getClient();

        $redis1->subscribe('channel:foo');

        $this->assertSame(1, $redis2->publish('channel:foo', 'bar'));
        $this->assertSame(0, $redis2->publish('channel:hoge', 'piyo'));
    }
}
