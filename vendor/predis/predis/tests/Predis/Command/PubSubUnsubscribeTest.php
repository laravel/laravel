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
class PubSubUnsubscribeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\PubSubUnsubscribe';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'UNSUBSCRIBE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('channel1', 'channel2', 'channel3');
        $expected = array('channel1', 'channel2', 'channel3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('channel1', 'channel2', 'channel3'));
        $expected = array('channel1', 'channel2', 'channel3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('unsubscribe', 'channel', 1);
        $expected = array('unsubscribe', 'channel', 1);

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array(array('channel1', 'channel2', 'channel3'));
        $expected = array('prefix:channel1', 'prefix:channel2', 'prefix:channel3');

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
    public function testDoesNotSwitchToSubscribeMode()
    {
        $redis = $this->getClient();

        $this->assertSame(array('unsubscribe', 'channel', 0), $redis->unsubscribe('channel'));
        $this->assertSame('echoed', $redis->echo('echoed'));
    }

    /**
     * @group connected
     */
    public function testUnsubscribesFromNotSubscribedChannels()
    {
        $redis = $this->getClient();

        $this->assertSame(array('unsubscribe', 'channel', 0), $redis->unsubscribe('channel'));
    }

    /**
     * @group connected
     */
    public function testUnsubscribesFromSubscribedChannels()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel', 1), $redis->subscribe('channel'));
        $this->assertSame(array('unsubscribe', 'channel', 0), $redis->unsubscribe('channel'));
    }

    /**
     * @group connected
     */
    public function testUnsubscribesFromAllSubscribedChannels()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('subscribe', 'channel:bar', 2), $redis->subscribe('channel:bar'));

        list($_, $unsubscribed1, $_) = $redis->unsubscribe();
        list($_, $unsubscribed2, $_) = $redis->getConnection()->read();
        $this->assertSameValues(array('channel:foo', 'channel:bar'), array($unsubscribed1, $unsubscribed2));

        $this->assertSame('echoed', $redis->echo('echoed'));
    }
}
