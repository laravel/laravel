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

/**
 * Client-side abstraction of a Publish / Subscribe context.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class AbstractPubSubContext implements \Iterator
{
    const SUBSCRIBE    = 'subscribe';
    const UNSUBSCRIBE  = 'unsubscribe';
    const PSUBSCRIBE   = 'psubscribe';
    const PUNSUBSCRIBE = 'punsubscribe';
    const MESSAGE      = 'message';
    const PMESSAGE     = 'pmessage';
    const PONG         = 'pong';

    const STATUS_VALID       = 1;	// 0b0001
    const STATUS_SUBSCRIBED  = 2;	// 0b0010
    const STATUS_PSUBSCRIBED = 4;	// 0b0100

    private $position = null;
    private $statusFlags = self::STATUS_VALID;

    /**
     * Automatically closes the context when PHP's garbage collector kicks in.
     */
    public function __destruct()
    {
        $this->closeContext(true);
    }

    /**
     * Checks if the specified flag is valid in the state of the context.
     *
     * @param  int  $value Flag.
     * @return bool
     */
    protected function isFlagSet($value)
    {
        return ($this->statusFlags & $value) === $value;
    }

    /**
     * PING the server with an optional payload that will be echoed as a
     * PONG message in the pub/sub loop.
     *
     * @param string $payload Optional PING payload.
     */
    public function ping($payload = null)
    {
        $this->writeCommand('PING', array($payload));
    }

    /**
     * Subscribes to the specified channels.
     *
     * @param mixed $channel,... One or more channel names.
     */
    public function subscribe($channel /*, ... */)
    {
        $this->writeCommand(self::SUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_SUBSCRIBED;
    }

    /**
     * Unsubscribes from the specified channels.
     *
     * @param string ... One or more channel names.
     */
    public function unsubscribe(/* ... */)
    {
        $this->writeCommand(self::UNSUBSCRIBE, func_get_args());
    }

    /**
     * Subscribes to the specified channels using a pattern.
     *
     * @param mixed $pattern,... One or more channel name patterns.
     */
    public function psubscribe($pattern /* ... */)
    {
        $this->writeCommand(self::PSUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_PSUBSCRIBED;
    }

    /**
     * Unsubscribes from the specified channels using a pattern.
     *
     * @param string ... One or more channel name patterns.
     */
    public function punsubscribe(/* ... */)
    {
        $this->writeCommand(self::PUNSUBSCRIBE, func_get_args());
    }

    /**
     * Closes the context by unsubscribing from all the subscribed channels.
     * Optionally, the context can be forcefully closed by dropping the
     * underlying connection.
     *
     * @param  bool $force Forcefully close the context by closing the connection.
     * @return bool Returns false if there are no pending messages.
     */
    public function closeContext($force = false)
    {
        if (!$this->valid()) {
            return false;
        }

        if ($force) {
            $this->invalidate();
            $this->disconnect();
        } else {
            if ($this->isFlagSet(self::STATUS_SUBSCRIBED)) {
                $this->unsubscribe();
            }
            if ($this->isFlagSet(self::STATUS_PSUBSCRIBED)) {
                $this->punsubscribe();
            }
        }

        return !$force;
    }

    /**
     * Closes the underlying connection on forced disconnection.
     */
    abstract protected function disconnect();

    /**
     * Writes a Redis command on the underlying connection.
     *
     * @param string $method    ID of the command.
     * @param array  $arguments List of arguments.
     */
    abstract protected function writeCommand($method, $arguments);

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        // NOOP
    }

    /**
     * Returns the last message payload retrieved from the server and generated
     * by one of the active subscriptions.
     *
     * @return array
     */
    public function current()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if ($this->valid()) {
            $this->position++;
        }

        return $this->position;
    }

    /**
     * Checks if the the context is still in a valid state to continue.
     *
     * @return bool
     */
    public function valid()
    {
        $isValid = $this->isFlagSet(self::STATUS_VALID);
        $subscriptionFlags = self::STATUS_SUBSCRIBED | self::STATUS_PSUBSCRIBED;
        $hasSubscriptions = ($this->statusFlags & $subscriptionFlags) > 0;

        return $isValid && $hasSubscriptions;
    }

    /**
     * Resets the state of the context.
     */
    protected function invalidate()
    {
        $this->statusFlags = 0;	// 0b0000;
    }

    /**
     * Waits for a new message from the server generated by one of the active
     * subscriptions and returns it when available.
     *
     * @return array
     */
    abstract protected function getValue();
}
