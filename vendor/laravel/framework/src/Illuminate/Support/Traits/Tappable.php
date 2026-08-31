<?php

namespace Illuminate\Support\Traits;

trait Tappable
{
    /**
     * Call the given Closure with this instance then return the instance.
     *
     * @param  (callable($this): mixed)|null  $callback
     * @return ($callback is null ? \Illuminate\Support\HigherOrderTapProxy : $this)
     */
    public function tap($callback = null)
    {
        return tap($this, $callback);
    }
}
