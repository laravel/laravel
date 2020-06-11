<?php

namespace ProjectName\Util;

use Illuminate\Contracts\Events\Dispatcher;

interface RaisesEvents
{
    public function dispatch(Dispatcher $dispatcher): void;

    public function release(): array;
}
