<?php

namespace Illuminate\Contracts\Support;

interface HasOnceHash
{
    /**
     * Compute the hash that should be used to represent the object when given to a function using "once".
     *
     * @return string
     */
    public function onceHash();
}
