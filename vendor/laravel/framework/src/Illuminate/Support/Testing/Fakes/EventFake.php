<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;
use ReflectionFunction;

class EventFake implements Dispatcher, Fake
{
    use ForwardsCalls, ReflectsClosures;

    /**
     * The original event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    public $dispatcher;

    /**
     * The event types that should be intercepted instead of dispatched.
     *
     * @var array
     */
    protected $eventsToFake = [];

    /**
     * The event types that should be dispatched instead of intercepted.
     *
     * @var array
     */
    protected $eventsToDispatch = [];

    /**
     * All of the events that have been intercepted keyed by type.
     *
     * @var array
     */
    protected $events = [];

    /**
     * Create a new event fake instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  array|string  $eventsToFake
     */
    public function __construct(Dispatcher $dispatcher, $eventsToFake = [])
    {
        $this->dispatcher = $dispatcher;

        $this->eventsToFake = Arr::wrap($eventsToFake);
    }

    /**
     * Specify the events that should be dispatched instead of faked.
     *
     * @param  array|string  $eventsToDispatch
     * @return $this
     */
    public function except($eventsToDispatch)
    {
        $this->eventsToDispatch = array_merge(
            $this->eventsToDispatch,
            Arr::wrap($eventsToDispatch)
        );

        return $this;
    }

    /**
     * Assert if an event has a listener attached to it.
     *
     * @param  string  $expectedEvent
     * @param  string|array  $expectedListener
     * @return void
     */
    public function assertListening($expectedEvent, $expectedListener)
    {
        foreach ($this->dispatcher->getListeners($expectedEvent) as $listenerClosure) {
            $actualListener = (new ReflectionFunction($listenerClosure))
                ->getStaticVariables()['listener'];

            $normalizedListener = $expectedListener;

            if (is_string($actualListener) && Str::contains($actualListener, '@')) {
                $actualListener = Str::parseCallback($actualListener);

                if (is_string($expectedListener)) {
                    if (Str::contains($expectedListener, '@')) {
                        $normalizedListener = Str::parseCallback($expectedListener);
                    } else {
                        $normalizedListener = [
                            $expectedListener,
                            method_exists($expectedListener, 'handle') ? 'handle' : '__invoke',
                        ];
                    }
                }
            }

            if ($actualListener === $normalizedListener ||
                ($actualListener instanceof Closure &&
                $normalizedListener === Closure::class)) {
                PHPUnit::assertTrue(true);

                return;
            }
        }

        PHPUnit::assertTrue(
            false,
            sprintf(
                'Event [%s] does not have the [%s] listener attached to it',
                $expectedEvent,
                print_r($expectedListener, true)
            )
        );
    }

    /**
     * Assert if an event was dispatched based on a truth-test callback.
     *
     * @param  string|\Closure  $event
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertDispatched($event, $callback = null)
    {
        if ($event instanceof Closure) {
            [$event, $callback] = [$this->firstClosureParameterType($event), $event];
        }

        if (is_int($callback)) {
            return $this->assertDispatchedTimes($event, $callback);
        }

        PHPUnit::assertTrue(
            $this->dispatched($event, $callback)->count() > 0,
            "The expected [{$event}] event was not dispatched."
        );
    }

    /**
     * Assert if an event was dispatched exactly once.
     *
     * @param  string  $event
     * @return void
     */
    public function assertDispatchedOnce($event)
    {
        $this->assertDispatchedTimes($event, 1);
    }

    /**
     * Assert if an event was dispatched a number of times.
     *
     * @param  string  $event
     * @param  int  $times
     * @return void
     */
    public function assertDispatchedTimes($event, $times = 1)
    {
        $count = $this->dispatched($event)->count();

        PHPUnit::assertSame(
            $times, $count,
            sprintf(
                "The expected [{$event}] event was dispatched {$count} %s instead of {$times} %s.",
                Str::plural('time', $count),
                Str::plural('time', $times)
            )
        );
    }

    /**
     * Determine if an event was dispatched based on a truth-test callback.
     *
     * @param  string|\Closure  $event
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotDispatched($event, $callback = null)
    {
        if ($event instanceof Closure) {
            [$event, $callback] = [$this->firstClosureParameterType($event), $event];
        }

        PHPUnit::assertCount(
            0, $this->dispatched($event, $callback),
            "The unexpected [{$event}] event was dispatched."
        );
    }

    /**
     * Assert that no events were dispatched.
     *
     * @return void
     */
    public function assertNothingDispatched()
    {
        $count = count(Arr::flatten($this->events));

        $eventNames = (new Collection($this->events))
            ->map(fn ($events, $eventName) => sprintf(
                '%s dispatched %s %s',
                $eventName,
                count($events),
                Str::plural('time', count($events)),
            ))
            ->join("\n- ");

        PHPUnit::assertSame(
            0, $count,
            "{$count} unexpected events were dispatched:\n\n- $eventNames\n"
        );
    }

    /**
     * Get all of the events matching a truth-test callback.
     *
     * @param  string  $event
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function dispatched($event, $callback = null)
    {
        if (! $this->hasDispatched($event)) {
            return new Collection;
        }

        $callback = $callback ?: fn () => true;

        return (new Collection($this->events[$event]))->filter(
            fn ($arguments) => $callback(...$arguments)
        );
    }

    /**
     * Determine if the given event has been dispatched.
     *
     * @param  string  $event
     * @return bool
     */
    public function hasDispatched($event)
    {
        return isset($this->events[$event]) && ! empty($this->events[$event]);
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  \Closure|string|array  $events
     * @param  mixed  $listener
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
     * Register an event and payload to be dispatched later.
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
        //
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        $name = is_object($event) ? get_class($event) : (string) $event;

        if ($this->shouldFakeEvent($name, $payload)) {
            $this->fakeEvent($event, $name, func_get_args());
        } else {
            return $this->dispatcher->dispatch($event, $payload, $halt);
        }
    }

    /**
     * Determine if an event should be faked or actually dispatched.
     *
     * @param  string  $eventName
     * @param  mixed  $payload
     * @return bool
     */
    protected function shouldFakeEvent($eventName, $payload)
    {
        if ($this->shouldDispatchEvent($eventName, $payload)) {
            return false;
        }

        if (empty($this->eventsToFake)) {
            return true;
        }

        return (new Collection($this->eventsToFake))
            ->filter(function ($event) use ($eventName, $payload) {
                return $event instanceof Closure
                    ? $event($eventName, $payload)
                    : $event === $eventName;
            })
            ->isNotEmpty();
    }

    /**
     * Push the event onto the fake events array immediately or after the next database transaction.
     *
     * @param  string|object  $event
     * @param  string  $name
     * @param  array  $arguments
     * @return void
     */
    protected function fakeEvent($event, $name, $arguments)
    {
        if ($event instanceof ShouldDispatchAfterCommit && Container::getInstance()->bound('db.transactions')) {
            return Container::getInstance()->make('db.transactions')
                ->addCallback(fn () => $this->events[$name][] = $arguments);
        }

        $this->events[$name][] = $arguments;
    }

    /**
     * Determine whether an event should be dispatched or not.
     *
     * @param  string  $eventName
     * @param  mixed  $payload
     * @return bool
     */
    protected function shouldDispatchEvent($eventName, $payload)
    {
        if (empty($this->eventsToDispatch)) {
            return false;
        }

        return (new Collection($this->eventsToDispatch))
            ->filter(function ($event) use ($eventName, $payload) {
                return $event instanceof Closure
                    ? $event($eventName, $payload)
                    : $event === $eventName;
            })
            ->isNotEmpty();
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param  string  $event
     * @return void
     */
    public function forget($event)
    {
        //
    }

    /**
     * Forget all of the queued listeners.
     *
     * @return void
     */
    public function forgetPushed()
    {
        //
    }

    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @return mixed
     */
    public function until($event, $payload = [])
    {
        return $this->dispatch($event, $payload, true);
    }

    /**
     * Get the events that have been dispatched.
     *
     * @return array
     */
    public function dispatchedEvents()
    {
        return $this->events;
    }

    /**
     * Handle dynamic method calls to the dispatcher.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->dispatcher, $method, $parameters);
    }
}
