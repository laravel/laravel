<?php

namespace Laravel\Prompts\Concerns;

use Closure;

trait Events
{
    /**
     * The registered event listeners.
     *
     * @var array<string, array<int, Closure>>
     */
    protected array $listeners = [];

    /**
     * Register an event listener.
     */
    public function on(string $event, Closure $callback): void
    {
        $this->listeners[$event][] = $callback;
    }

    /**
     * Emit an event.
     */
    public function emit(string $event, mixed ...$data): void
    {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener(...$data);
        }
    }

    /**
     * Clean the event listeners.
     */
    public function clearListeners(): void
    {
        $this->listeners = [];
    }
}
