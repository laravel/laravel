<?php

namespace Illuminate\Console\Scheduling;

use BadMethodCallException;
use Closure;
use DateTimeInterface;
use Illuminate\Bus\UniqueLock;
use Illuminate\Console\Application;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\ProcessUtils;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use function Illuminate\Support\enum_value;

/**
 * @mixin \Illuminate\Console\Scheduling\PendingEventAttributes
 */
class Schedule
{
    use Macroable {
        __call as macroCall;
    }

    const SUNDAY = 0;

    const MONDAY = 1;

    const TUESDAY = 2;

    const WEDNESDAY = 3;

    const THURSDAY = 4;

    const FRIDAY = 5;

    const SATURDAY = 6;

    /**
     * All of the events on the schedule.
     *
     * @var \Illuminate\Console\Scheduling\Event[]
     */
    protected $events = [];

    /**
     * The event mutex implementation.
     *
     * @var \Illuminate\Console\Scheduling\EventMutex
     */
    protected $eventMutex;

    /**
     * The scheduling mutex implementation.
     *
     * @var \Illuminate\Console\Scheduling\SchedulingMutex
     */
    protected $schedulingMutex;

    /**
     * The timezone the date should be evaluated on.
     *
     * @var \DateTimeZone|string
     */
    protected $timezone;

    /**
     * The job dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $dispatcher;

    /**
     * The cache of mutex results.
     *
     * @var array<string, bool>
     */
    protected $mutexCache = [];

    /**
     * The attributes to pass to the event.
     *
     * @var \Illuminate\Console\Scheduling\PendingEventAttributes|null
     */
    protected $attributes;

    /**
     * The schedule group attributes stack.
     *
     * @var array<int, PendingEventAttributes>
     */
    protected array $groupStack = [];

    /**
     * Create a new schedule instance.
     *
     * @param  \DateTimeZone|string|null  $timezone
     *
     * @throws \RuntimeException
     */
    public function __construct($timezone = null)
    {
        $this->timezone = $timezone;

        if (! class_exists(Container::class)) {
            throw new RuntimeException(
                'A container implementation is required to use the scheduler. Please install the illuminate/container package.'
            );
        }

        $container = Container::getInstance();

        $this->eventMutex = $container->bound(EventMutex::class)
            ? $container->make(EventMutex::class)
            : $container->make(CacheEventMutex::class);

        $this->schedulingMutex = $container->bound(SchedulingMutex::class)
            ? $container->make(SchedulingMutex::class)
            : $container->make(CacheSchedulingMutex::class);
    }

    /**
     * Add a new callback event to the schedule.
     *
     * @param  string|callable  $callback
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    public function call($callback, array $parameters = [])
    {
        $this->events[] = $event = new CallbackEvent(
            $this->eventMutex, $callback, $parameters, $this->timezone
        );

        $this->mergePendingAttributes($event);

        return $event;
    }

    /**
     * Add a new Artisan command event to the schedule.
     *
     * @param  \Symfony\Component\Console\Command\Command|string  $command
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function command($command, array $parameters = [])
    {
        if ($command instanceof SymfonyCommand) {
            $command = get_class($command);

            $command = Container::getInstance()->make($command);

            return $this->exec(
                Application::formatCommandString($command->getName()), $parameters,
            )->description($command->getDescription());
        }

        if (class_exists($command)) {
            $command = Container::getInstance()->make($command);

            return $this->exec(
                Application::formatCommandString($command->getName()), $parameters,
            )->description($command->getDescription());
        }

        return $this->exec(
            Application::formatCommandString($command), $parameters
        );
    }

    /**
     * Add a new job callback event to the schedule.
     *
     * @param  object|string  $job
     * @param  \UnitEnum|string|null  $queue
     * @param  \UnitEnum|string|null  $connection
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    public function job($job, $queue = null, $connection = null)
    {
        $jobName = $job;

        $queue = enum_value($queue);
        $connection = enum_value($connection);

        if (! is_string($job)) {
            $jobName = method_exists($job, 'displayName')
                ? $job->displayName()
                : $job::class;
        }

        $this->events[] = $event = new CallbackEvent(
            $this->eventMutex, function () use ($job, $queue, $connection) {
                $job = is_string($job) ? Container::getInstance()->make($job) : $job;

                if ($job instanceof ShouldQueue) {
                    $this->dispatchToQueue($job, $queue ?? $job->queue, $connection ?? $job->connection);
                } else {
                    $this->dispatchNow($job);
                }
            }, [], $this->timezone
        );

        $event->name($jobName);

        $this->mergePendingAttributes($event);

        return $event;
    }

    /**
     * Dispatch the given job to the queue.
     *
     * @param  object  $job
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function dispatchToQueue($job, $queue, $connection)
    {
        if ($job instanceof Closure) {
            if (! class_exists(CallQueuedClosure::class)) {
                throw new RuntimeException(
                    'To enable support for closure jobs, please install the illuminate/queue package.'
                );
            }

            $job = CallQueuedClosure::create($job);
        }

        if ($job instanceof ShouldBeUnique) {
            return $this->dispatchUniqueJobToQueue($job, $queue, $connection);
        }

        $this->getDispatcher()->dispatch(
            $job->onConnection($connection)->onQueue($queue)
        );
    }

    /**
     * Dispatch the given unique job to the queue.
     *
     * @param  object  $job
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function dispatchUniqueJobToQueue($job, $queue, $connection)
    {
        if (! Container::getInstance()->bound(Cache::class)) {
            throw new RuntimeException('Cache driver not available. Scheduling unique jobs not supported.');
        }

        if (! (new UniqueLock(Container::getInstance()->make(Cache::class)))->acquire($job)) {
            return;
        }

        $this->getDispatcher()->dispatch(
            $job->onConnection($connection)->onQueue($queue)
        );
    }

    /**
     * Dispatch the given job right now.
     *
     * @param  object  $job
     * @return void
     */
    protected function dispatchNow($job)
    {
        $this->getDispatcher()->dispatchNow($job);
    }

    /**
     * Add a new command event to the schedule.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function exec($command, array $parameters = [])
    {
        if (count($parameters)) {
            $command .= ' '.$this->compileParameters($parameters);
        }

        $this->events[] = $event = new Event($this->eventMutex, $command, $this->timezone);

        $this->mergePendingAttributes($event);

        return $event;
    }

    /**
     * Create new schedule group.
     *
     * @param  \Closure  $events
     * @return void
     *
     * @throws \RuntimeException
     */
    public function group(Closure $events)
    {
        if ($this->attributes === null) {
            throw new RuntimeException('Invoke an attribute method such as Schedule::daily() before defining a schedule group.');
        }

        $this->groupStack[] = $this->attributes;
        $this->attributes = null;

        $events($this);

        array_pop($this->groupStack);
    }

    /**
     * Merge the current group attributes with the given event.
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return void
     */
    protected function mergePendingAttributes(Event $event)
    {
        if (! empty($this->groupStack)) {
            $group = array_last($this->groupStack);

            $group->mergeAttributes($event);
        }

        if (isset($this->attributes)) {
            $this->attributes->mergeAttributes($event);

            $this->attributes = null;
        }
    }

    /**
     * Compile parameters for a command.
     *
     * @param  array  $parameters
     * @return string
     */
    protected function compileParameters(array $parameters)
    {
        return (new Collection($parameters))->map(function ($value, $key) {
            if (is_array($value)) {
                return $this->compileArrayInput($key, $value);
            }

            if (! is_numeric($value) && ! preg_match('/^(-.$|--.*)/i', $value)) {
                $value = ProcessUtils::escapeArgument($value);
            }

            return is_numeric($key) ? $value : "{$key}={$value}";
        })->implode(' ');
    }

    /**
     * Compile array input for a command.
     *
     * @param  string|int  $key
     * @param  array  $value
     * @return string
     */
    public function compileArrayInput($key, $value)
    {
        $value = (new Collection($value))->map(function ($value) {
            return ProcessUtils::escapeArgument($value);
        });

        if (str_starts_with($key, '--')) {
            $value = $value->map(function ($value) use ($key) {
                return "{$key}={$value}";
            });
        } elseif (str_starts_with($key, '-')) {
            $value = $value->map(function ($value) use ($key) {
                return "{$key} {$value}";
            });
        }

        return $value->implode(' ');
    }

    /**
     * Determine if the server is allowed to run this event.
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @param  \DateTimeInterface  $time
     * @return bool
     */
    public function serverShouldRun(Event $event, DateTimeInterface $time)
    {
        return $this->mutexCache[$event->mutexName()] ??= $this->schedulingMutex->create($event, $time);
    }

    /**
     * Get all of the events on the schedule that are due.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Illuminate\Support\Collection
     */
    public function dueEvents($app)
    {
        return (new Collection($this->events))->filter->isDue($app);
    }

    /**
     * Get all of the events on the schedule.
     *
     * @return \Illuminate\Console\Scheduling\Event[]
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * Specify the cache store that should be used to store mutexes.
     *
     * @param  \UnitEnum|string  $store
     * @return $this
     */
    public function useCache($store)
    {
        $store = enum_value($store);

        if ($this->eventMutex instanceof CacheAware) {
            $this->eventMutex->useStore($store);
        }

        if ($this->schedulingMutex instanceof CacheAware) {
            $this->schedulingMutex->useStore($store);
        }

        return $this;
    }

    /**
     * Get the job dispatcher, if available.
     *
     * @return \Illuminate\Contracts\Bus\Dispatcher
     *
     * @throws \RuntimeException
     */
    protected function getDispatcher()
    {
        if ($this->dispatcher === null) {
            try {
                $this->dispatcher = Container::getInstance()->make(Dispatcher::class);
            } catch (BindingResolutionException $e) {
                throw new RuntimeException(
                    'Unable to resolve the dispatcher from the service container. Please bind it or install the illuminate/bus package.',
                    is_int($e->getCode()) ? $e->getCode() : 0, $e
                );
            }
        }

        return $this->dispatcher;
    }

    /**
     * Dynamically handle calls into the schedule instance.
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

        if (method_exists(PendingEventAttributes::class, $method) || Event::hasMacro($method)) {
            $this->attributes ??= $this->groupStack ? clone array_last($this->groupStack) : new PendingEventAttributes($this);

            return $this->attributes->$method(...$parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
