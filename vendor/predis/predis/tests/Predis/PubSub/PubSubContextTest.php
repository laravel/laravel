<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\PubSub;

use PredisTestCase;
use Predis\Client;
use Predis\Profile\ServerProfile;

/**
 * @group realm-pubsub
 */
class PubSubContextTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The current profile does not support PUB/SUB related commands
     */
    public function testPubSubContextRequirePubSubRelatedCommand()
    {
        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->any())
                ->method('supportsCommands')
                ->will($this->returnValue(false));

        $client = new Client(null, array('profile' => $profile));
        $pubsub = new PubSubContext($client);
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage Cannot initialize a PUB/SUB context when using aggregated connections
     */
    public function testPubSubContextDoesNotWorkOnClusters()
    {
        $cluster = $this->getMock('Predis\Connection\ClusterConnectionInterface');

        $client = new Client($cluster);
        $pubsub = new PubSubContext($client);
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithoutSubscriptionsDoesNotOpenContext()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        $client = $this->getMock('Predis\Client', array('executeCommand'), array($connection));
        $client->expects($this->never())->method('executeCommand');

        $pubsub = new PubSubContext($client);
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithSubscriptionsOpensContext()
    {
        $profile = ServerProfile::get(REDIS_SERVER_VERSION);

        $cmdSubscribe = $profile->createCommand('subscribe', array('channel:foo'));
        $cmdPsubscribe = $profile->createCommand('psubscribe', array('channels:*'));

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->exactly(2))->method('writeCommand');

        $client = $this->getMock('Predis\Client', array('createCommand', 'writeCommand'), array($connection));
        $client->expects($this->exactly(2))
               ->method('createCommand')
               ->with($this->logicalOr($this->equalTo('subscribe'), $this->equalTo('psubscribe')))
               ->will($this->returnCallback(function ($id, $args) use ($profile) {
                   return $profile->createCommand($id, $args);
               }));

        $options = array('subscribe' => 'channel:foo', 'psubscribe' => 'channels:*');
        $pubsub = new PubSubContext($client, $options);
    }

    /**
     * @group disconnected
     */
    public function testClosingContextWithTrueClosesConnection()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        $client = $this->getMock('Predis\Client', array('disconnect'), array($connection));
        $client->expects($this->exactly(1))->method('disconnect');

        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $connection->expects($this->never())->method('writeCommand');

        $pubsub->closeContext(true);
    }

    /**
     * @group disconnected
     */
    public function testClosingContextWithFalseSendsUnsubscriptions()
    {
        $profile = ServerProfile::get(REDIS_SERVER_VERSION);
        $classUnsubscribe = $profile->getCommandClass('unsubscribe');
        $classPunsubscribe = $profile->getCommandClass('punsubscribe');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        $client = $this->getMock('Predis\Client', array('disconnect'), array($connection));

        $options = array('subscribe' => 'channel:foo', 'psubscribe' => 'channels:*');
        $pubsub = new PubSubContext($client, $options);

        $connection->expects($this->exactly(2))
                   ->method('writeCommand')
                   ->with($this->logicalOr(
                       $this->isInstanceOf($classUnsubscribe),
                       $this->isInstanceOf($classPunsubscribe)
                   ));

        $pubsub->closeContext(false);
    }

    /**
     * @group disconnected
     */
    public function testIsNotValidWhenNotSubscribed()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $client = $this->getMock('Predis\Client', array('disconnect'), array($connection));

        $pubsub = new PubSubContext($client);

        $this->assertFalse($pubsub->valid());
        $this->assertNull($pubsub->next());
    }

    public function testHandlesPongMessages()
    {
        $rawmessage = array('pong', '');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $message = $pubsub->current();
        $this->assertSame('pong', $message->kind);
        $this->assertSame('', $message->payload);
    }

    /**
     * @group disconnected
     */
    public function testHandlesPongMessagesWithPayload()
    {
        $rawmessage = array('pong', 'foobar');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $message = $pubsub->current();
        $this->assertSame('pong', $message->kind);
        $this->assertSame('foobar', $message->payload);
    }

    /**
     * @group disconnected
     */
    public function testReadsMessageFromConnection()
    {
        $rawmessage = array('message', 'channel:foo', 'message from channel');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $message = $pubsub->current();
        $this->assertSame('message', $message->kind);
        $this->assertSame('channel:foo', $message->channel);
        $this->assertSame('message from channel', $message->payload);
    }

    /**
     * @group disconnected
     */
    public function testReadsPmessageFromConnection()
    {
        $rawmessage = array('pmessage', 'channel:*', 'channel:foo', 'message from channel');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('psubscribe' => 'channel:*'));

        $message = $pubsub->current();
        $this->assertSame('pmessage', $message->kind);
        $this->assertSame('channel:*', $message->pattern);
        $this->assertSame('channel:foo', $message->channel);
        $this->assertSame('message from channel', $message->payload);
    }

    /**
     * @group disconnected
     */
    public function testReadsSubscriptionMessageFromConnection()
    {
        $rawmessage = array('subscribe', 'channel:foo', 1);

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $message = $pubsub->current();
        $this->assertSame('subscribe', $message->kind);
        $this->assertSame('channel:foo', $message->channel);
        $this->assertSame(1, $message->payload);
    }

    /**
     * @group disconnected
     */
    public function testReadsUnsubscriptionMessageFromConnection()
    {
        $rawmessage = array('unsubscribe', 'channel:foo', 1);

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $message = $pubsub->current();
        $this->assertSame('unsubscribe', $message->kind);
        $this->assertSame('channel:foo', $message->channel);
        $this->assertSame(1, $message->payload);
    }

    /**
     * @group disconnected
     */
    public function testUnsubscriptionMessageWithZeroChannelCountInvalidatesContext()
    {
        $rawmessage = array('unsubscribe', 'channel:foo', 0);

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('read')->will($this->returnValue($rawmessage));

        $client = new Client($connection);
        $pubsub = new PubSubContext($client, array('subscribe' => 'channel:foo'));

        $this->assertTrue($pubsub->valid());

        $message = $pubsub->current();
        $this->assertSame('unsubscribe', $message->kind);
        $this->assertSame('channel:foo', $message->channel);
        $this->assertSame(0, $message->payload);

        $this->assertFalse($pubsub->valid());
    }

    /**
     * @group disconnected
     */
    public function testGetUnderlyingClientInstance()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        $client = new Client($connection);
        $pubsub = new PubSubContext($client);

        $this->assertSame($client, $pubsub->getClient());
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testPubSubAgainstRedisServer()
    {
        $parameters = array(
            'host' => REDIS_SERVER_HOST,
            'port' => REDIS_SERVER_PORT,
            'database' => REDIS_SERVER_DBNUM,
            // Prevents suite from handing on broken test
            'read_write_timeout' => 2,
        );

        $options = array('profile' => REDIS_SERVER_VERSION);
        $messages = array();

        $producer = new Client($parameters, $options);
        $producer->connect();

        $consumer = new Client($parameters, $options);
        $consumer->connect();

        $pubsub = new PubSubContext($consumer);
        $pubsub->subscribe('channel:foo');

        $producer->publish('channel:foo', 'message1');
        $producer->publish('channel:foo', 'message2');
        $producer->publish('channel:foo', 'QUIT');

        foreach ($pubsub as $message) {
            if ($message->kind !== 'message') {
                continue;
            }
            $messages[] = ($payload = $message->payload);
            if ($payload === 'QUIT') {
                $pubsub->closeContext();
            }
        }

        $this->assertSame(array('message1', 'message2', 'QUIT'), $messages);
        $this->assertFalse($pubsub->valid());
        $this->assertEquals('ECHO', $consumer->echo('ECHO'));
    }
}
