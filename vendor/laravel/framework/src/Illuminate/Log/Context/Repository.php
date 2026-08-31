<?php

namespace Illuminate\Log\Context;

use __PHP_Incomplete_Class;
use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Log\Context\Events\ContextDehydrating as Dehydrating;
use Illuminate\Log\Context\Events\ContextHydrated as Hydrated;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use Throwable;

class Repository
{
    use Conditionable, Macroable, SerializesModels;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The contextual data.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * The hidden contextual data.
     *
     * @var array<string, mixed>
     */
    protected $hidden = [];

    /**
     * The callback that should handle unserialize exceptions.
     *
     * @var callable|null
     */
    protected static $handleUnserializeExceptionsUsing;

    /**
     * Create a new Context instance.
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Determine if the given key exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Determine if the given key is missing.
     *
     * @param  string  $key
     * @return bool
     */
    public function missing($key)
    {
        return ! $this->has($key);
    }

    /**
     * Determine if the given key exists within the hidden context data.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasHidden($key)
    {
        return array_key_exists($key, $this->hidden);
    }

    /**
     * Determine if the given key is missing within the hidden context data.
     *
     * @param  string  $key
     * @return bool
     */
    public function missingHidden($key)
    {
        return ! $this->hasHidden($key);
    }

    /**
     * Retrieve all the context data.
     *
     * @return array<string, mixed>
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Retrieve all the hidden context data.
     *
     * @return array<string, mixed>
     */
    public function allHidden()
    {
        return $this->hidden;
    }

    /**
     * Retrieve the given key's value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->data[$key] ?? value($default);
    }

    /**
     * Retrieve the given key's hidden value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getHidden($key, $default = null)
    {
        return $this->hidden[$key] ?? value($default);
    }

    /**
     * Retrieve the given key's value and then forget it.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return tap($this->get($key, $default), function () use ($key) {
            $this->forget($key);
        });
    }

    /**
     * Retrieve the given key's hidden value and then forget it.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pullHidden($key, $default = null)
    {
        return tap($this->getHidden($key, $default), function () use ($key) {
            $this->forgetHidden($key);
        });
    }

    /**
     * Retrieve only the values of the given keys.
     *
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public function only($keys)
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    /**
     * Retrieve only the hidden values of the given keys.
     *
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public function onlyHidden($keys)
    {
        return array_intersect_key($this->hidden, array_flip($keys));
    }

    /**
     * Retrieve all values except those with the given keys.
     *
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public function except($keys)
    {
        return array_diff_key($this->data, array_flip($keys));
    }

    /**
     * Retrieve all hidden values except those with the given keys.
     *
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public function exceptHidden($keys)
    {
        return array_diff_key($this->hidden, array_flip($keys));
    }

    /**
     * Add a context value.
     *
     * @param  string|array<string, mixed>  $key
     * @param  mixed  $value
     * @return $this
     */
    public function add($key, $value = null)
    {
        $this->data = array_merge(
            $this->data,
            is_array($key) ? $key : [$key => $value]
        );

        return $this;
    }

    /**
     * Add a hidden context value.
     *
     * @param  string|array<string, mixed>  $key
     * @param  mixed  $value
     * @return $this
     */
    public function addHidden($key, #[\SensitiveParameter] $value = null)
    {
        $this->hidden = array_merge(
            $this->hidden,
            is_array($key) ? $key : [$key => $value]
        );

        return $this;
    }

    /**
     * Add a context value if it does not exist yet, and return the value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function remember($key, $value)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        return tap(value($value), function ($value) use ($key) {
            $this->add($key, $value);
        });
    }

    /**
     * Add a hidden context value if it does not exist yet, and return the value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function rememberHidden($key, #[\SensitiveParameter] $value)
    {
        if ($this->hasHidden($key)) {
            return $this->getHidden($key);
        }

        return tap(value($value), function ($value) use ($key) {
            $this->addHidden($key, $value);
        });
    }

    /**
     * Forget the given context key.
     *
     * @param  string|array<int, string>  $key
     * @return $this
     */
    public function forget($key)
    {
        foreach ((array) $key as $k) {
            unset($this->data[$k]);
        }

        return $this;
    }

    /**
     * Forget the given hidden context key.
     *
     * @param  string|array<int, string>  $key
     * @return $this
     */
    public function forgetHidden($key)
    {
        foreach ((array) $key as $k) {
            unset($this->hidden[$k]);
        }

        return $this;
    }

    /**
     * Add a context value if it does not exist yet.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function addIf($key, $value)
    {
        if (! $this->has($key)) {
            $this->add($key, $value);
        }

        return $this;
    }

    /**
     * Add a hidden context value if it does not exist yet.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function addHiddenIf($key, #[\SensitiveParameter] $value)
    {
        if (! $this->hasHidden($key)) {
            $this->addHidden($key, $value);
        }

        return $this;
    }

    /**
     * Push the given values onto the key's stack.
     *
     * @param  string  $key
     * @param  mixed  ...$values
     * @return $this
     *
     * @throws \RuntimeException
     */
    public function push($key, ...$values)
    {
        if (! $this->isStackable($key)) {
            throw new RuntimeException("Unable to push value onto context stack for key [{$key}].");
        }

        $this->data[$key] = [
            ...$this->data[$key] ?? [],
            ...$values,
        ];

        return $this;
    }

    /**
     * Pop the latest value from the key's stack.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function pop($key)
    {
        if (! $this->isStackable($key) || ! count($this->data[$key])) {
            throw new RuntimeException("Unable to pop value from context stack for key [{$key}].");
        }

        return array_pop($this->data[$key]);
    }

    /**
     * Push the given hidden values onto the key's stack.
     *
     * @param  string  $key
     * @param  mixed  ...$values
     * @return $this
     *
     * @throws \RuntimeException
     */
    public function pushHidden($key, ...$values)
    {
        if (! $this->isHiddenStackable($key)) {
            throw new RuntimeException("Unable to push value onto hidden context stack for key [{$key}].");
        }

        $this->hidden[$key] = [
            ...$this->hidden[$key] ?? [],
            ...$values,
        ];

        return $this;
    }

    /**
     * Pop the latest hidden value from the key's stack.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function popHidden($key)
    {
        if (! $this->isHiddenStackable($key) || ! count($this->hidden[$key])) {
            throw new RuntimeException("Unable to pop value from hidden context stack for key [{$key}].");
        }

        return array_pop($this->hidden[$key]);
    }

    /**
     * Increment a context counter.
     *
     * @param  string  $key
     * @param  int  $amount
     * @return $this
     */
    public function increment(string $key, int $amount = 1)
    {
        $this->add(
            $key,
            (int) $this->get($key, 0) + $amount,
        );

        return $this;
    }

    /**
     * Decrement a context counter.
     *
     * @param  string  $key
     * @param  int  $amount
     * @return $this
     */
    public function decrement(string $key, int $amount = 1)
    {
        return $this->increment($key, $amount * -1);
    }

    /**
     * Determine if the given value is in the given stack.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $strict
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function stackContains(string $key, mixed $value, bool $strict = false): bool
    {
        if (! $this->isStackable($key)) {
            throw new RuntimeException("Given key [{$key}] is not a stack.");
        }

        if (! array_key_exists($key, $this->data)) {
            return false;
        }

        if ($value instanceof Closure) {
            return (new Collection($this->data[$key]))->contains($value);
        }

        return in_array($value, $this->data[$key], $strict);
    }

    /**
     * Determine if the given value is in the given hidden stack.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $strict
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function hiddenStackContains(string $key, mixed $value, bool $strict = false): bool
    {
        if (! $this->isHiddenStackable($key)) {
            throw new RuntimeException("Given key [{$key}] is not a stack.");
        }

        if (! array_key_exists($key, $this->hidden)) {
            return false;
        }

        if ($value instanceof Closure) {
            return (new Collection($this->hidden[$key]))->contains($value);
        }

        return in_array($value, $this->hidden[$key], $strict);
    }

    /**
     * Determine if a given key can be used as a stack.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isStackable($key)
    {
        return ! $this->has($key) ||
            (is_array($this->data[$key]) && array_is_list($this->data[$key]));
    }

    /**
     * Determine if a given key can be used as a hidden stack.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isHiddenStackable($key)
    {
        return ! $this->hasHidden($key) ||
            (is_array($this->hidden[$key]) && array_is_list($this->hidden[$key]));
    }

    /**
     * @template TReturn of mixed
     *
     * Run the callback function with the given context values and restore the original context state when complete.
     *
     * @param  (callable(): TReturn)  $callback
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $hidden
     * @return TReturn
     *
     * @throws \Throwable
     */
    public function scope(callable $callback, array $data = [], array $hidden = [])
    {
        $dataBefore = $this->data;
        $hiddenBefore = $this->hidden;

        if ($data !== []) {
            $this->add($data);
        }

        if ($hidden !== []) {
            $this->addHidden($hidden);
        }

        try {
            return $callback();
        } finally {
            $this->data = $dataBefore;
            $this->hidden = $hiddenBefore;
        }
    }

    /**
     * Determine if the repository is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->all() === [] && $this->allHidden() === [];
    }

    /**
     * Execute the given callback when context is about to be dehydrated.
     *
     * @param  (callable(static): void)  $callback
     * @return $this
     */
    public function dehydrating($callback)
    {
        $this->events->listen(fn (Dehydrating $event) => $callback($event->context));

        return $this;
    }

    /**
     * Execute the given callback when context has been hydrated.
     *
     * @param  (callable(static): void)  $callback
     * @return $this
     */
    public function hydrated($callback)
    {
        $this->events->listen(fn (Hydrated $event) => $callback($event->context));

        return $this;
    }

    /**
     * Handle unserialize exceptions using the given callback.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function handleUnserializeExceptionsUsing($callback)
    {
        static::$handleUnserializeExceptionsUsing = $callback;

        return $this;
    }

    /**
     * Flush all context data.
     *
     * @return $this
     */
    public function flush()
    {
        $this->data = [];
        $this->hidden = [];

        return $this;
    }

    /**
     * Dehydrate the context data.
     *
     * @internal
     *
     * @return ?array
     */
    public function dehydrate()
    {
        $instance = (new static($this->events))
            ->add($this->all())
            ->addHidden($this->allHidden());

        $instance->events->dispatch(new Dehydrating($instance));

        $serialize = fn ($value) => serialize($instance->getSerializedPropertyValue($value, withRelations: false));

        return $instance->isEmpty() ? null : [
            'data' => array_map($serialize, $instance->all()),
            'hidden' => array_map($serialize, $instance->allHidden()),
        ];
    }

    /**
     * Hydrate the context instance.
     *
     * @internal
     *
     * @param  ?array  $context
     * @return $this
     *
     * @throws \RuntimeException
     */
    public function hydrate($context)
    {
        $unserialize = function ($value, $key, $hidden) {
            try {
                return tap($this->getRestoredPropertyValue(unserialize($value)), function ($value) {
                    if ($value instanceof __PHP_Incomplete_Class) {
                        throw new RuntimeException('Value is incomplete class: '.json_encode($value));
                    }
                });
            } catch (Throwable $e) {
                if (static::$handleUnserializeExceptionsUsing !== null) {
                    return (static::$handleUnserializeExceptionsUsing)($e, $key, $value, $hidden);
                }

                if ($e instanceof ModelNotFoundException) {
                    if (function_exists('report')) {
                        report($e);
                    }

                    return null;
                }

                throw $e;
            }
        };

        [$data, $hidden] = [
            (new Collection($context['data'] ?? []))->map(fn ($value, $key) => $unserialize($value, $key, false))->all(),
            (new Collection($context['hidden'] ?? []))->map(fn ($value, $key) => $unserialize($value, $key, true))->all(),
        ];

        $this->events->dispatch(new Hydrated(
            $this->flush()->add($data)->addHidden($hidden)
        ));

        return $this;
    }
}
