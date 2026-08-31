<?php

namespace Illuminate\Events;

use Closure;
use Exception;
use Illuminate\Bus\UniqueLock;
use Illuminate\Container\Container;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use ReflectionClass;

use function Illuminate\Support\enum_value;

class Dispatcher implements DispatcherContract
{
    use Macroable, ReflectsClosures;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The registered event listeners.
     *
     * @var array<string, callable|array|class-string|null>
     */
    protected $listeners = [];

    /**
     * The wildcard listeners.
     *
     * @var array<string, \Closure|string>
     */
    protected $wildcards = [];

    /**
     * The cached wildcard listeners.
     *
     * @var array<string, \Closure|string>
     */
    protected $wildcardsCache = [];

    /**
     * The queue resolver instance.
     *
     * @var callable(): \Illuminate\Contracts\Queue\Queue
     */
    protected $queueResolver;

    /**
     * The database transaction manager resolver instance.
     *
     * @var callable
     */
    protected $transactionManagerResolver;

    /**
     * The currently deferred events.
     *
     * @var array
     */
    protected $deferredEvents = [];

    /**
     * Indicates if events should be deferred.
     *
     * @var bool
     */
    protected $deferringEvents = false;

    /**
     * The specific events to defer (null means defer all events).
     *
     * @var string[]|null
     */
    protected $eventsToDefer = null;

    /**
     * Create a new event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     */
    public function __construct(?ContainerContract $container = null)
    {
        $this->container = $container ?: new Container;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|callable|array|class-string|string  $events
     * @param  \Illuminate\Events\QueuedClosure|callable|array|class-string|null  $listener
     * @return void
     */
    public function listen($events, $listener = null)
    {
        if ($events instanceof Closure) {
            return (new Collection($this->firstClosureParameterTypes($events)))
                ->each(function ($event) use ($events) {
                    $this->listen($event, $events);
                });
        } elseif ($events instanceof QueuedClosure) {
            return (new Collection($this->firstClosureParameterTypes($events->closure)))
                ->each(function ($event) use ($events) {
                    $this->listen($event, $events->resolve());
                });
        } elseif ($listener instanceof QueuedClosure) {
            $listener = $listener->resolve();
        }

        foreach ((array) $events as $event) {
            if (str_contains($event, '*')) {
                $this->setupWildcardListen($event, $listener);
            } else {
                $this->listeners[$event][] = $listener;
            }
        }
    }

    /**
     * Setup a wildcard listener callback.
     *
     * @param  string  $event
     * @param  \Closure|string  $listener
     * @return void
     */
    protected function setupWildcardListen($event, $listener)
    {
        $this->wildcards[$event][] = $listener;

        $this->wildcardsCache = [];
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        return isset($this->listeners[$eventName]) ||
               isset($this->wildcards[$eventName]) ||
               $this->hasWildcardListeners($eventName);
    }

    /**
     * Determine if the given event has any wildcard listeners.
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasWildcardListeners($eventName)
    {
        foreach ($this->wildcards as $key => $listeners) {
            if (Str::is($key, $eventName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register an event and payload to be fired later.
     *
     * @param  string  $event
     * @param  object|array  $payload
     * @return void
     */
    public function push($event, $payload = [])
    {
        $this->listen($event.'_pushed', function () use ($event, $payload) {
            $this->dispatch($event, $payload);
        });
    }

    /**
     * Flush a set of pushed events.
     *
     * @param  string  $event
     * @return void
     */
    public function flush($event)
    {
        $this->dispatch($event.'_pushed');
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string  $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        $subscriber = $this->resolveSubscriber($subscriber);

        $events = $subscriber->subscribe($this);

        if (is_array($events)) {
            foreach ($events as $event => $listeners) {
                foreach (Arr::wrap($listeners) as $listener) {
                    if (is_string($listener) && method_exists($subscriber, $listener)) {
                        $this->listen($event, [get_class($subscriber), $listener]);

                        continue;
                    }

                    $this->listen($event, $listener);
                }
            }
        }
    }

    /**
     * Resolve the subscriber instance.
     *
     * @param  object|class-string  $subscriber
     * @return ($subscriber is object ? object : mixed)
     */
    protected function resolveSubscriber($subscriber)
    {
        if (is_string($subscriber)) {
            return $this->container->make($subscriber);
        }

        return $subscriber;
    }

    /**
     * Fire an event until the first non-null response is returned.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @return array|null
     */
    public function until($event, $payload = [])
    {
        return $this->dispatch($event, $payload, true);
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
        // When the given "event" is actually an object, we will assume it is an event
        // object, and use the class as the event name and this event itself as the
        // payload to the handler, which makes object-based events quite simple.
        [$isEventObject, $parsedEvent, $parsedPayload] = [
            is_object($event),
            ...$this->parseEventAndPayload($event, $payload),
        ];

        if ($this->shouldDeferEvent($parsedEvent)) {
            $this->deferredEvents[] = func_get_args();

            return null;
        }

        // If the event is not intended to be dispatched unless the current database
        // transaction is successful, we'll register a callback which will handle
        // dispatching this event on the next successful DB transaction commit.
        if ($isEventObject &&
            $parsedPayload[0] instanceof ShouldDispatchAfterCommit &&
            ! is_null($transactions = $this->resolveTransactionManager())) {
            $transactions->addCallback(
                fn () => $this->invokeListeners($parsedEvent, $parsedPayload, $halt)
            );

            return null;
        }

        return $this->invokeListeners($parsedEvent, $parsedPayload, $halt);
    }

    /**
     * Broadcast an event and call its listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    protected function invokeListeners($event, $payload, $halt = false)
    {
        if ($this->shouldBroadcast($payload)) {
            $this->broadcastEvent($payload[0]);
        }

        $responses = [];

        foreach ($this->getListeners($event) as $listener) {
            $response = $listener($event, $payload);

            // If a response is returned from the listener and event halting is enabled
            // we will just return this response, and not call the rest of the event
            // listeners. Otherwise we will add the response on the response list.
            if ($halt && ! is_null($response)) {
                return $response;
            }

            // If a boolean false is returned from a listener, we will stop propagating
            // the event to any further listeners down in the chain, else we keep on
            // looping through the listeners and firing every one in our sequence.
            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }

    /**
     * Parse the given event and payload and prepare them for dispatching.
     *
     * @param  mixed  $event
     * @param  mixed  $payload
     * @return array{string, array}
     */
    protected function parseEventAndPayload($event, $payload)
    {
        if (is_object($event)) {
            [$payload, $event] = [[$event], get_class($event)];
        }

        return [$event, Arr::wrap($payload)];
    }

    /**
     * Determine if the payload has a broadcastable event.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function shouldBroadcast(array $payload)
    {
        return isset($payload[0]) &&
               $payload[0] instanceof ShouldBroadcast &&
               $this->broadcastWhen($payload[0]);
    }

    /**
     * Check if the event should be broadcasted by the condition.
     *
     * @param  mixed  $event
     * @return bool
     */
    protected function broadcastWhen($event)
    {
        return method_exists($event, 'broadcastWhen')
            ? $event->broadcastWhen()
            : true;
    }

    /**
     * Broadcast the given event class.
     *
     * @param  \Illuminate\Contracts\Broadcasting\ShouldBroadcast  $event
     * @return void
     */
    protected function broadcastEvent($event)
    {
        $this->container->make(BroadcastFactory::class)->queue($event);
    }

    /**
     * Get all of the listeners for a given event name.
     *
     * @param  string  $eventName
     * @return array
     */
    public function getListeners($eventName)
    {
        $listeners = array_merge(
            $this->prepareListeners($eventName),
            $this->wildcardsCache[$eventName] ?? $this->getWildcardListeners($eventName)
        );

        return class_exists($eventName, false)
            ? $this->addInterfaceListeners($eventName, $listeners)
            : $listeners;
    }

    /**
     * Get the wildcard listeners for the event.
     *
     * @param  string  $eventName
     * @return array
     */
    protected function getWildcardListeners($eventName)
    {
        $wildcards = [];

        foreach ($this->wildcards as $key => $listeners) {
            if (Str::is($key, $eventName)) {
                foreach ($listeners as $listener) {
                    $wildcards[] = $this->makeListener($listener, true);
                }
            }
        }

        return $this->wildcardsCache[$eventName] = $wildcards;
    }

    /**
     * Add the listeners for the event's interfaces to the given array.
     *
     * @param  string  $eventName
     * @param  array  $listeners
     * @return array
     */
    protected function addInterfaceListeners($eventName, array $listeners = [])
    {
        foreach (class_implements($eventName) as $interface) {
            if (isset($this->listeners[$interface])) {
                foreach ($this->prepareListeners($interface) as $names) {
                    $listeners = array_merge($listeners, (array) $names);
                }
            }
        }

        return $listeners;
    }

    /**
     * Prepare the listeners for a given event.
     *
     * @param  string  $eventName
     * @return \Closure[]
     */
    protected function prepareListeners(string $eventName)
    {
        $listeners = [];

        foreach ($this->listeners[$eventName] ?? [] as $listener) {
            $listeners[] = $this->makeListener($listener);
        }

        return $listeners;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  \Closure|string|array{class-string, string}  $listener
     * @param  bool  $wildcard
     * @return \Closure
     */
    public function makeListener($listener, $wildcard = false)
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener, $wildcard);
        }

        if (is_array($listener) && isset($listener[0]) && is_string($listener[0])) {
            return $this->createClassListener($listener, $wildcard);
        }

        return function ($event, $payload) use ($listener, $wildcard) {
            if ($wildcard) {
                return $listener($event, $payload);
            }

            return $listener(...array_values($payload));
        };
    }

    /**
     * Create a class based listener using the IoC container.
     *
     * @param  string  $listener
     * @param  bool  $wildcard
     * @return \Closure
     */
    public function createClassListener($listener, $wildcard = false)
    {
        return function ($event, $payload) use ($listener, $wildcard) {
            if ($wildcard) {
                return call_user_func($this->createClassCallable($listener), $event, $payload);
            }

            $callable = $this->createClassCallable($listener);

            return $callable(...array_values($payload));
        };
    }

    /**
     * Create the class based event callable.
     *
     * @param  array{class-string, string}|string  $listener
     * @return callable
     */
    protected function createClassCallable($listener)
    {
        [$class, $method] = is_array($listener)
            ? $listener
            : $this->parseClassCallable($listener);

        if (! method_exists($class, $method)) {
            $method = '__invoke';
        }

        if ($this->handlerShouldBeQueued($class)) {
            return $this->createQueuedHandlerCallable($class, $method);
        }

        $listener = $this->container->make($class);

        return $this->handlerShouldBeDispatchedAfterDatabaseTransactions($listener)
                && ! in_array($method, ['creating', 'updating', 'saving', 'deleting', 'restoring', 'forceDeleting'])
            ? $this->createCallbackForListenerRunningAfterCommits($listener, $method)
            : [$listener, $method];
    }

    /**
     * Parse the class listener into class and method.
     *
     * @param  string  $listener
     * @return array{class-string, string}
     */
    protected function parseClassCallable($listener)
    {
        return Str::parseCallback($listener, 'handle');
    }

    /**
     * Determine if the event handler class should be queued.
     *
     * @param  class-string  $class
     * @return bool
     *
     * @phpstan-assert-if-true class-string<\Illuminate\Contracts\Queue\ShouldQueue> $class
     */
    protected function handlerShouldBeQueued($class)
    {
        try {
            return (new ReflectionClass($class))->implementsInterface(
                ShouldQueue::class
            );
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Create a callable for putting an event handler on the queue.
     *
     * @param  class-string  $class
     * @param  string  $method
     * @return \Closure(): void
     */
    protected function createQueuedHandlerCallable($class, $method)
    {
        return function () use ($class, $method) {
            $arguments = array_map(function ($a) {
                return is_object($a) ? clone $a : $a;
            }, func_get_args());

            if ($this->handlerWantsToBeQueued($class, $arguments)) {
                $this->queueHandler($class, $method, $arguments);
            }
        };
    }

    /**
     * Determine if the given event handler should be dispatched after all database transactions have committed.
     *
     * @param  mixed  $listener
     * @return bool
     */
    protected function handlerShouldBeDispatchedAfterDatabaseTransactions($listener)
    {
        return (($listener->afterCommit ?? null) ||
                $listener instanceof ShouldHandleEventsAfterCommit) &&
                $this->resolveTransactionManager();
    }

    /**
     * Create a callable for dispatching a listener after database transactions.
     *
     * @param  mixed  $listener
     * @param  string  $method
     * @return \Closure
     */
    protected function createCallbackForListenerRunningAfterCommits($listener, $method)
    {
        return function () use ($method, $listener) {
            $payload = func_get_args();

            $this->resolveTransactionManager()->addCallback(
                function () use ($listener, $method, $payload) {
                    $listener->$method(...$payload);
                }
            );
        };
    }

    /**
     * Determine if the event handler wants to be queued.
     *
     * @param  class-string  $class
     * @param  array  $arguments
     * @return bool
     */
    protected function handlerWantsToBeQueued($class, $arguments)
    {
        $instance = $this->container->make($class);

        if (method_exists($instance, 'shouldQueue')) {
            return $instance->shouldQueue($arguments[0]);
        }

        return true;
    }

    /**
     * Queue the handler class.
     *
     * @param  string  $class
     * @param  string  $method
     * @param  array  $arguments
     * @return void
     */
    protected function queueHandler($class, $method, $arguments)
    {
        [$listener, $job] = $this->createListenerAndJob($class, $method, $arguments);

        if ($job->shouldBeUnique &&
            ! (new UniqueLock($this->container->make(Cache::class)))->acquire($job)) {
            return;
        }

        $connection = $this->resolveQueue()->connection(method_exists($listener, 'viaConnection')
            ? (isset($arguments[0]) ? $listener->viaConnection($arguments[0]) : $listener->viaConnection())
            : $listener->connection ?? null);

        $queue = method_exists($listener, 'viaQueue')
            ? (isset($arguments[0]) ? $listener->viaQueue($arguments[0]) : $listener->viaQueue())
            : $listener->queue ?? null;

        $delay = method_exists($listener, 'withDelay')
            ? (isset($arguments[0]) ? $listener->withDelay($arguments[0]) : $listener->withDelay())
            : $listener->delay ?? null;

        is_null($delay)
            ? $connection->pushOn(enum_value($queue), $job)
            : $connection->laterOn(enum_value($queue), $delay, $job);
    }

    /**
     * Create the listener and job for a queued listener.
     *
     * @template TListener
     *
     * @param  class-string<TListener>  $class
     * @param  string  $method
     * @param  array  $arguments
     * @return array{TListener, mixed}
     */
    protected function createListenerAndJob($class, $method, $arguments)
    {
        $listener = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        return [$listener, $this->propagateListenerOptions(
            $listener, new CallQueuedListener($class, $method, $arguments)
        )];
    }

    /**
     * Propagate listener options to the job.
     *
     * @param  mixed  $listener
     * @param  \Illuminate\Events\CallQueuedListener  $job
     * @return \Illuminate\Events\CallQueuedListener
     */
    protected function propagateListenerOptions($listener, $job)
    {
        return tap($job, function ($job) use ($listener) {
            $data = array_values($job->data);

            if ($listener instanceof ShouldQueueAfterCommit) {
                $job->afterCommit = true;
            } else {
                $job->afterCommit = property_exists($listener, 'afterCommit') ? $listener->afterCommit : null;
            }

            $job->backoff = method_exists($listener, 'backoff') ? $listener->backoff(...$data) : ($listener->backoff ?? null);
            $job->maxExceptions = $listener->maxExceptions ?? null;
            $job->retryUntil = method_exists($listener, 'retryUntil') ? $listener->retryUntil(...$data) : null;
            $job->shouldBeEncrypted = $listener instanceof ShouldBeEncrypted;
            $job->timeout = $listener->timeout ?? null;
            $job->failOnTimeout = $listener->failOnTimeout ?? false;
            $job->tries = method_exists($listener, 'tries') ? $listener->tries(...$data) : ($listener->tries ?? null);
            $job->messageGroup = method_exists($listener, 'messageGroup') ? $listener->messageGroup(...$data) : ($listener->messageGroup ?? null);
            $job->withDeduplicator(method_exists($listener, 'deduplicator')
                ? $listener->deduplicator(...$data)
                : (method_exists($listener, 'deduplicationId') ? $listener->deduplicationId(...) : null)
            );

            $job->through(array_merge(
                method_exists($listener, 'middleware') ? $listener->middleware(...$data) : [],
                $listener->middleware ?? []
            ));

            $job->shouldBeUnique = $listener instanceof ShouldBeUnique;
            $job->shouldBeUniqueUntilProcessing = $listener instanceof ShouldBeUniqueUntilProcessing;

            if ($job->shouldBeUnique) {
                $job->uniqueId = method_exists($listener, 'uniqueId')
                    ? $listener->uniqueId(...$data)
                    : ($listener->uniqueId ?? null);

                $job->uniqueFor = method_exists($listener, 'uniqueFor')
                    ? $listener->uniqueFor(...$data)
                    : ($listener->uniqueFor ?? 0);
            }
        });
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param  string  $event
     * @return void
     */
    public function forget($event)
    {
        if (str_contains($event, '*')) {
            unset($this->wildcards[$event]);
        } else {
            unset($this->listeners[$event]);
        }

        foreach ($this->wildcardsCache as $key => $listeners) {
            if (Str::is($event, $key)) {
                unset($this->wildcardsCache[$key]);
            }
        }
    }

    /**
     * Forget all of the pushed listeners.
     *
     * @return void
     */
    public function forgetPushed()
    {
        foreach ($this->listeners as $key => $value) {
            if (str_ends_with($key, '_pushed')) {
                $this->forget($key);
            }
        }
    }

    /**
     * Get the queue implementation from the resolver.
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    protected function resolveQueue()
    {
        return call_user_func($this->queueResolver);
    }

    /**
     * Set the queue resolver implementation.
     *
     * @param  callable(): \Illuminate\Contracts\Queue\Queue  $resolver
     * @return $this
     */
    public function setQueueResolver(callable $resolver)
    {
        $this->queueResolver = $resolver;

        return $this;
    }

    /**
     * Get the database transaction manager implementation from the resolver.
     *
     * @return \Illuminate\Database\DatabaseTransactionsManager|null
     */
    protected function resolveTransactionManager()
    {
        return call_user_func($this->transactionManagerResolver);
    }

    /**
     * Set the database transaction manager resolver implementation.
     *
     * @param  (callable(): (\Illuminate\Database\DatabaseTransactionsManager|null))  $resolver
     * @return $this
     */
    public function setTransactionManagerResolver(callable $resolver)
    {
        $this->transactionManagerResolver = $resolver;

        return $this;
    }

    /**
     * Execute the given callback while deferring events, then dispatch all deferred events.
     *
     * @template TResult
     *
     * @param  callable(): TResult  $callback
     * @param  string[]|null  $events
     * @return TResult
     */
    public function defer(callable $callback, ?array $events = null)
    {
        $wasDeferring = $this->deferringEvents;
        $previousDeferredEvents = $this->deferredEvents;
        $previousEventsToDefer = $this->eventsToDefer;

        $this->deferringEvents = true;
        $this->deferredEvents = [];
        $this->eventsToDefer = $events;

        try {
            $result = $callback();

            $this->deferringEvents = false;

            foreach ($this->deferredEvents as $args) {
                $this->dispatch(...$args);
            }

            return $result;
        } finally {
            $this->deferringEvents = $wasDeferring;
            $this->deferredEvents = $previousDeferredEvents;
            $this->eventsToDefer = $previousEventsToDefer;
        }
    }

    /**
     * Determine if the given event should be deferred.
     *
     * @param  string  $event
     * @return bool
     */
    protected function shouldDeferEvent(string $event)
    {
        return $this->deferringEvents && ($this->eventsToDefer === null || in_array($event, $this->eventsToDefer));
    }

    /**
     * Gets the raw, unprepared listeners.
     *
     * @return array
     */
    public function getRawListeners()
    {
        return $this->listeners;
    }
}
