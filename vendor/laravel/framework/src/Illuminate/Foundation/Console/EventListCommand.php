<?php

namespace Illuminate\Foundation\Console;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use ReflectionFunction;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'event:list')]
class EventListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:list
                            {--event= : Filter the events by name}
                            {--json : Output the events and listeners as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List the application's events and listeners";

    /**
     * The events dispatcher resolver callback.
     *
     * @var \Closure|null
     */
    protected static $eventsResolver;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $events = $this->getEvents()->sortKeys();

        if ($events->isEmpty()) {
            if ($this->option('json')) {
                $this->output->writeln('[]');
            } else {
                $this->components->info("Your application doesn't have any events matching the given criteria.");
            }

            return;
        }

        if ($this->option('json')) {
            $this->displayJson($events);
        } else {
            $this->displayForCli($events);
        }
    }

    /**
     * Display events and their listeners in JSON.
     *
     * @param  \Illuminate\Support\Collection  $events
     * @return void
     */
    protected function displayJson(Collection $events)
    {
        $data = $events->map(function ($listeners, $event) {
            return [
                'event' => strip_tags($this->appendEventInterfaces($event)),
                'listeners' => (new Collection($listeners))->map(fn ($listener) => strip_tags($listener))->values()->all(),
            ];
        })->values();

        $this->output->writeln($data->toJson());
    }

    /**
     * Display the events and their listeners for the CLI.
     *
     * @param  \Illuminate\Support\Collection  $events
     * @return void
     */
    protected function displayForCli(Collection $events)
    {
        $this->newLine();

        $events->each(function ($listeners, $event) {
            $this->components->twoColumnDetail($this->appendEventInterfaces($event));
            $this->components->bulletList($listeners);
        });

        $this->newLine();
    }

    /**
     * Get all of the events and listeners configured for the application.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getEvents()
    {
        $events = new Collection($this->getListenersOnDispatcher());

        if ($this->filteringByEvent()) {
            $events = $this->filterEvents($events);
        }

        return $events;
    }

    /**
     * Get the event / listeners from the dispatcher object.
     *
     * @return array
     */
    protected function getListenersOnDispatcher()
    {
        $events = [];

        foreach ($this->getRawListeners() as $event => $rawListeners) {
            foreach ($rawListeners as $rawListener) {
                if (is_string($rawListener)) {
                    $events[$event][] = $this->appendListenerInterfaces($rawListener);
                } elseif ($rawListener instanceof Closure) {
                    $events[$event][] = $this->stringifyClosure($rawListener);
                } elseif (is_array($rawListener) && count($rawListener) === 2) {
                    if (is_object($rawListener[0])) {
                        $rawListener[0] = get_class($rawListener[0]);
                    }

                    $events[$event][] = $this->appendListenerInterfaces(implode('@', $rawListener));
                }
            }
        }

        return $events;
    }

    /**
     * Add the event implemented interfaces to the output.
     *
     * @param  string  $event
     * @return string
     */
    protected function appendEventInterfaces($event)
    {
        if (! class_exists($event)) {
            return $event;
        }

        $interfaces = class_implements($event);

        if (in_array(ShouldBroadcast::class, $interfaces)) {
            $event .= ' <fg=bright-blue>(ShouldBroadcast)</>';
        }

        return $event;
    }

    /**
     * Add the listener implemented interfaces to the output.
     *
     * @param  string  $listener
     * @return string
     */
    protected function appendListenerInterfaces($listener)
    {
        $listener = explode('@', $listener);

        $interfaces = class_implements($listener[0]);

        $listener = implode('@', $listener);

        if (in_array(ShouldQueue::class, $interfaces)) {
            $listener .= ' <fg=bright-blue>(ShouldQueue)</>';
        }

        return $listener;
    }

    /**
     * Get a displayable string representation of a Closure listener.
     *
     * @param  \Closure  $rawListener
     * @return string
     */
    protected function stringifyClosure(Closure $rawListener)
    {
        $reflection = new ReflectionFunction($rawListener);

        $path = str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], $reflection->getFileName() ?: '');

        return 'Closure at: '.$path.':'.$reflection->getStartLine();
    }

    /**
     * Filter the given events using the provided event name filter.
     *
     * @param  \Illuminate\Support\Collection  $events
     * @return \Illuminate\Support\Collection
     */
    protected function filterEvents($events)
    {
        if (! $eventName = $this->option('event')) {
            return $events;
        }

        return $events->filter(
            fn ($listeners, $event) => str_contains($event, $eventName)
        );
    }

    /**
     * Determine whether the user is filtering by an event name.
     *
     * @return bool
     */
    protected function filteringByEvent()
    {
        return ! empty($this->option('event'));
    }

    /**
     * Gets the raw version of event listeners from the event dispatcher.
     *
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getRawListeners()
    {
        return $this->getEventsDispatcher()->getRawListeners();
    }

    /**
     * Get the event dispatcher.
     *
     * @return \Illuminate\Events\Dispatcher
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getEventsDispatcher()
    {
        return is_null(self::$eventsResolver)
            ? $this->getLaravel()->make('events')
            : call_user_func(self::$eventsResolver);
    }

    /**
     * Set a callback that should be used when resolving the events dispatcher.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public static function resolveEventsUsing($resolver)
    {
        static::$eventsResolver = $resolver;
    }
}
