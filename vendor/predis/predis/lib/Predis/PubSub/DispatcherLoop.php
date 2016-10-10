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

use Predis\ClientInterface;

/**
 * Method-dispatcher loop built around the client-side abstraction of a Redis
 * Publish / Subscribe context.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class DispatcherLoop
{
    private $pubSubContext;

    protected $callbacks;
    protected $defaultCallback;
    protected $subscriptionCallback;

    /**
     * @param ClientInterface $client Client instance used by the context.
     */
    public function __construct(ClientInterface $client)
    {
        $this->callbacks = array();
        $this->pubSubContext = $client->pubSubLoop();
    }

    /**
     * Checks if the passed argument is a valid callback.
     *
     * @param mixed $callable A callback.
     */
    protected function validateCallback($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("A valid callable object must be provided");
        }
    }

    /**
     * Returns the underlying Publish / Subscribe context.
     *
     * @return PubSubContext
     */
    public function getPubSubContext()
    {
        return $this->pubSubContext;
    }

    /**
     * Sets a callback that gets invoked upon new subscriptions.
     *
     * @param mixed $callable A callback.
     */
    public function subscriptionCallback($callable = null)
    {
        if (isset($callable)) {
            $this->validateCallback($callable);
        }

        $this->subscriptionCallback = $callable;
    }

    /**
     * Sets a callback that gets invoked when a message is received on a
     * channel that does not have an associated callback.
     *
     * @param mixed $callable A callback.
     */
    public function defaultCallback($callable = null)
    {
        if (isset($callable)) {
            $this->validateCallback($callable);
        }

        $this->subscriptionCallback = $callable;
    }

    /**
     * Binds a callback to a channel.
     *
     * @param string   $channel  Channel name.
     * @param Callable $callback A callback.
     */
    public function attachCallback($channel, $callback)
    {
        $callbackName = $this->getPrefixKeys() . $channel;

        $this->validateCallback($callback);
        $this->callbacks[$callbackName] = $callback;
        $this->pubSubContext->subscribe($channel);
    }

    /**
     * Stops listening to a channel and removes the associated callback.
     *
     * @param string $channel Redis channel.
     */
    public function detachCallback($channel)
    {
        $callbackName = $this->getPrefixKeys() . $channel;

        if (isset($this->callbacks[$callbackName])) {
            unset($this->callbacks[$callbackName]);
            $this->pubSubContext->unsubscribe($channel);
        }
    }

    /**
     * Starts the dispatcher loop.
     */
    public function run()
    {
        foreach ($this->pubSubContext as $message) {
            $kind = $message->kind;

            if ($kind !== PubSubContext::MESSAGE && $kind !== PubSubContext::PMESSAGE) {
                if (isset($this->subscriptionCallback)) {
                    $callback = $this->subscriptionCallback;
                    call_user_func($callback, $message);
                }

                continue;
            }

            if (isset($this->callbacks[$message->channel])) {
                $callback = $this->callbacks[$message->channel];
                call_user_func($callback, $message->payload);
            } elseif (isset($this->defaultCallback)) {
                $callback = $this->defaultCallback;
                call_user_func($callback, $message);
            }
        }
    }

    /**
     * Terminates the dispatcher loop.
     */
    public function stop()
    {
        $this->pubSubContext->closeContext();
    }

    /**
     * Return the prefix of the keys
     *
     * @return string
     */
    protected function getPrefixKeys()
    {
        $options = $this->pubSubContext->getClient()->getOptions();

        if (isset($options->prefix)) {
            return $options->prefix->getPrefix();
        }

        return '';
    }
}
