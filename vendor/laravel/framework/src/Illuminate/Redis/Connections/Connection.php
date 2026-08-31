<?php

namespace Illuminate\Redis\Connections;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Redis\Events\CommandFailed;
use Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder;
use Illuminate\Redis\Limiters\DurationLimiterBuilder;
use Illuminate\Support\Traits\Macroable;
use Throwable;

/**
 * @mixin \Redis
 */
abstract class Connection
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The Redis client.
     *
     * @var \Redis
     */
    protected $client;

    /**
     * The Redis connection name.
     *
     * @var string|null
     */
    protected $name;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * Subscribe to a set of given channels for messages.
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $method
     * @return void
     */
    abstract public function createSubscription($channels, Closure $callback, $method = 'subscribe');

    /**
     * Funnel a callback for a maximum number of simultaneous executions.
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder
     */
    public function funnel($name)
    {
        return new ConcurrencyLimiterBuilder($this, $name);
    }

    /**
     * Throttle a callback for a maximum number of executions over a given duration.
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Limiters\DurationLimiterBuilder
     */
    public function throttle($name)
    {
        return new DurationLimiterBuilder($this, $name);
    }

    /**
     * Get the underlying Redis client.
     *
     * @return mixed
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Subscribe to a set of given channels for messages.
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @return void
     */
    public function subscribe($channels, Closure $callback)
    {
        $this->createSubscription($channels, $callback, __FUNCTION__);
    }

    /**
     * Subscribe to a set of given channels with wildcards.
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @return void
     */
    public function psubscribe($channels, Closure $callback)
    {
        $this->createSubscription($channels, $callback, __FUNCTION__);
    }

    /**
     * Run a command against the Redis database.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function command($method, array $parameters = [])
    {
        $start = microtime(true);

        try {
            $result = $this->client->{$method}(...$parameters);
        } catch (Throwable $e) {
            $this->events?->dispatch(new CommandFailed(
                $method, $this->parseParametersForEvent($parameters), $e, $this
            ));

            throw $e;
        }

        $time = round((microtime(true) - $start) * 1000, 2);

        $this->events?->dispatch(new CommandExecuted(
            $method, $this->parseParametersForEvent($parameters), $time, $this
        ));

        return $result;
    }

    /**
     * Parse the command's parameters for event dispatching.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function parseParametersForEvent(array $parameters)
    {
        return $parameters;
    }

    /**
     * Fire the given event if possible.
     *
     * @param  mixed  $event
     * @return void
     *
     * @deprecated since Laravel 11.x
     */
    protected function event($event)
    {
        $this->events?->dispatch($event);
    }

    /**
     * Register a Redis command listener with the connection.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function listen(Closure $callback)
    {
        $this->events?->listen(CommandExecuted::class, $callback);
    }

    /**
     * Register a Redis command failure listener with the connection.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function listenForFailures(Closure $callback)
    {
        $this->events?->listen(CommandFailed::class, $callback);
    }

    /**
     * Get the connection name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the connection's name.
     *
     * @param  string  $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the event dispatcher used by the connection.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getEventDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance on the connection.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function setEventDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Unset the event dispatcher instance on the connection.
     *
     * @return void
     */
    public function unsetEventDispatcher()
    {
        $this->events = null;
    }

    /**
     * Pass other method calls down to the underlying client.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->command($method, $parameters);
    }
}
