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

use Predis\ClientException;
use Predis\ClientInterface;
use Predis\Command\AbstractCommand as Command;
use Predis\NotSupportedException;
use Predis\Connection\AggregatedConnectionInterface;

/**
 * Client-side abstraction of a Publish / Subscribe context.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubContext extends AbstractPubSubContext
{
    private $client;
    private $options;

    /**
     * @param ClientInterface $client  Client instance used by the context.
     * @param array           $options Options for the context initialization.
     */
    public function __construct(ClientInterface $client, Array $options = null)
    {
        $this->checkCapabilities($client);
        $this->options = $options ?: array();
        $this->client = $client;

        $this->genericSubscribeInit('subscribe');
        $this->genericSubscribeInit('psubscribe');
    }

    /**
     * Returns the underlying client instance used by the pub/sub iterator.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Checks if the passed client instance satisfies the required conditions
     * needed to initialize a Publish / Subscribe context.
     *
     * @param ClientInterface $client Client instance used by the context.
     */
    private function checkCapabilities(ClientInterface $client)
    {
        if ($client->getConnection() instanceof AggregatedConnectionInterface) {
            throw new NotSupportedException('Cannot initialize a PUB/SUB context when using aggregated connections');
        }

        $commands = array('publish', 'subscribe', 'unsubscribe', 'psubscribe', 'punsubscribe');

        if ($client->getProfile()->supportsCommands($commands) === false) {
            throw new NotSupportedException('The current profile does not support PUB/SUB related commands');
        }
    }

    /**
     * This method shares the logic to handle both SUBSCRIBE and PSUBSCRIBE.
     *
     * @param string $subscribeAction Type of subscription.
     */
    private function genericSubscribeInit($subscribeAction)
    {
        if (isset($this->options[$subscribeAction])) {
            $this->$subscribeAction($this->options[$subscribeAction]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function writeCommand($method, $arguments)
    {
        $arguments = Command::normalizeArguments($arguments);
        $command = $this->client->createCommand($method, $arguments);
        $this->client->getConnection()->writeCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    protected function disconnect()
    {
        $this->client->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue()
    {
        $response = $this->client->getConnection()->read();

        switch ($response[0]) {
            case self::SUBSCRIBE:
            case self::UNSUBSCRIBE:
            case self::PSUBSCRIBE:
            case self::PUNSUBSCRIBE:
                if ($response[2] === 0) {
                    $this->invalidate();
                }
                // The missing break here is intentional as we must process
                // subscriptions and unsubscriptions as standard messages.

            case self::MESSAGE:
                return (object) array(
                    'kind'    => $response[0],
                    'channel' => $response[1],
                    'payload' => $response[2],
                );

            case self::PMESSAGE:
                return (object) array(
                    'kind'    => $response[0],
                    'pattern' => $response[1],
                    'channel' => $response[2],
                    'payload' => $response[3],
                );

            case self::PONG:
                return (object) array(
                    'kind'    => $response[0],
                    'payload' => $response[1],
                );

            default:
                $message = "Received an unknown message type {$response[0]} inside of a pubsub context";
                throw new ClientException($message);
        }
    }
}
