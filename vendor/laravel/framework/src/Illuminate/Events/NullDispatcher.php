<?php

namespace Illuminate\Events;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Traits\ForwardsCalls;

class NullDispatcher implements DispatcherContract
{
    use ForwardsCalls;

    /**
     * The underlying event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new event dispatcher instance that does not fire.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     */
    public function __construct(DispatcherContract $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Don't fire an event.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return void
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        //
    }

    /**
     * Don't register an event and payload to be fired later.
     *
     * @param  string  $event
     * @param  array  $payload
     * @return void
     */
    public function push($event, $payload = [])
    {
        //
    }

    /**
     * Don't dispatch an event.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @return mixed
     */
    public function until($event, $payload = [])
    {
        //
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  \Closure|string|array  $events
     * @param  \Closure|string|array|null  $listener
     * @return void
     */
    public function listen($events, $listener = null)
    {
        $this->dispatcher->listen($events, $listener);
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        return $this->dispatcher->hasListeners($eventName);
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string  $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        $this->dispatcher->subscribe($subscriber);
    }

    /**
     * Flush a set of pushed events.
     *
     * @param  string  $event
     * @return void
     */
    public function flush($event)
    {
        $this->dispatcher->flush($event);
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param  string  $event
     * @return void
     */
    public function forget($event)
    {
        $this->dispatcher->forget($event);
    }

    /**
     * Forget all of the queued listeners.
     *
     * @return void
     */
    public function forgetPushed()
    {
        $this->dispatcher->forgetPushed();
    }

    /**
     * Dynamically pass method calls to the underlying dispatcher.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->dispatcher, $method, $parameters);
    }
}
