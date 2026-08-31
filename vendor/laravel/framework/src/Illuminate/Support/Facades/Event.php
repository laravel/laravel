<?php

namespace Illuminate\Support\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Testing\Fakes\EventFake;

/**
 * @method static void listen(\Illuminate\Events\QueuedClosure|callable|array|string $events, \Illuminate\Events\QueuedClosure|callable|array|string|null $listener = null)
 * @method static bool hasListeners(string $eventName)
 * @method static bool hasWildcardListeners(string $eventName)
 * @method static void push(string $event, object|array $payload = [])
 * @method static void flush(string $event)
 * @method static void subscribe(object|string $subscriber)
 * @method static array|null until(string|object $event, mixed $payload = [])
 * @method static array|null dispatch(string|object $event, mixed $payload = [], bool $halt = false)
 * @method static array getListeners(string $eventName)
 * @method static \Closure makeListener(\Closure|string|array $listener, bool $wildcard = false)
 * @method static \Closure createClassListener(string $listener, bool $wildcard = false)
 * @method static void forget(string $event)
 * @method static void forgetPushed()
 * @method static \Illuminate\Events\Dispatcher setQueueResolver(callable $resolver)
 * @method static \Illuminate\Events\Dispatcher setTransactionManagerResolver(callable $resolver)
 * @method static mixed defer(callable $callback, string[]|null $events = null)
 * @method static array getRawListeners()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static \Illuminate\Support\Testing\Fakes\EventFake except(array|string $eventsToDispatch)
 * @method static void assertListening(string $expectedEvent, string|array $expectedListener)
 * @method static void assertDispatched(string|\Closure $event, callable|int|null $callback = null)
 * @method static void assertDispatchedOnce(string $event)
 * @method static void assertDispatchedTimes(string $event, int $times = 1)
 * @method static void assertNotDispatched(string|\Closure $event, callable|null $callback = null)
 * @method static void assertNothingDispatched()
 * @method static \Illuminate\Support\Collection dispatched(string $event, callable|null $callback = null)
 * @method static bool hasDispatched(string $event)
 * @method static array dispatchedEvents()
 *
 * @see \Illuminate\Events\Dispatcher
 * @see \Illuminate\Support\Testing\Fakes\EventFake
 */
class Event extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $eventsToFake
     * @return \Illuminate\Support\Testing\Fakes\EventFake
     */
    public static function fake($eventsToFake = [])
    {
        $actualDispatcher = static::isFake()
            ? static::getFacadeRoot()->dispatcher
            : static::getFacadeRoot();

        return tap(new EventFake($actualDispatcher, $eventsToFake), function ($fake) {
            static::swap($fake);

            Model::setEventDispatcher($fake);
            Cache::refreshEventDispatcher();
        });
    }

    /**
     * Replace the bound instance with a fake that fakes all events except the given events.
     *
     * @param  string[]|string  $eventsToAllow
     * @return \Illuminate\Support\Testing\Fakes\EventFake
     */
    public static function fakeExcept($eventsToAllow)
    {
        return static::fake([
            function ($eventName) use ($eventsToAllow) {
                return ! in_array($eventName, (array) $eventsToAllow);
            },
        ]);
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
     *
     * @param  callable  $callable
     * @param  array  $eventsToFake
     * @return mixed
     */
    public static function fakeFor(callable $callable, array $eventsToFake = [])
    {
        $originalDispatcher = static::getFacadeRoot();

        static::fake($eventsToFake);

        try {
            return $callable();
        } finally {
            static::swap($originalDispatcher);

            Model::setEventDispatcher($originalDispatcher);
            Cache::refreshEventDispatcher();
        }
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
     *
     * @param  callable  $callable
     * @param  array  $eventsToAllow
     * @return mixed
     */
    public static function fakeExceptFor(callable $callable, array $eventsToAllow = [])
    {
        $originalDispatcher = static::getFacadeRoot();

        static::fakeExcept($eventsToAllow);

        try {
            return $callable();
        } finally {
            static::swap($originalDispatcher);

            Model::setEventDispatcher($originalDispatcher);
            Cache::refreshEventDispatcher();
        }
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'events';
    }
}
