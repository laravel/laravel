<?php

namespace Illuminate\Support\Defer;

use Illuminate\Support\Str;

class DeferredCallback
{
    /**
     * Create a new deferred callback instance.
     *
     * @param  callable  $callback
     */
    public function __construct(public $callback, public ?string $name = null, public bool $always = false)
    {
        $this->name = $name ?? (string) Str::uuid();
    }

    /**
     * Specify the name of the deferred callback so it can be cancelled later.
     *
     * @param  string  $name
     * @return $this
     */
    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Indicate that the deferred callback should run even on unsuccessful requests and jobs.
     *
     * @param  bool  $always
     * @return $this
     */
    public function always(bool $always = true): static
    {
        $this->always = $always;

        return $this;
    }

    /**
     * Invoke the deferred callback.
     *
     * @return void
     */
    public function __invoke(): void
    {
        call_user_func($this->callback);
    }
}
