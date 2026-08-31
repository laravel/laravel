<?php

namespace Illuminate\Testing\Fluent\Concerns;

use Illuminate\Support\Traits\Dumpable;

trait Debugging
{
    use Dumpable;

    /**
     * Dumps the given props.
     *
     * @param  string|null  $prop
     * @return $this
     */
    public function dump(?string $prop = null): static
    {
        dump($this->prop($prop));

        return $this;
    }

    /**
     * Retrieve a prop within the current scope using "dot" notation.
     *
     * @param  string|null  $key
     * @return mixed
     */
    abstract protected function prop(?string $key = null);
}
