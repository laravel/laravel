<?php

namespace Illuminate\Concurrency;

use Closure;
use Illuminate\Contracts\Concurrency\Driver;
use Illuminate\Support\Arr;
use Illuminate\Support\Defer\DeferredCallback;
use Spatie\Fork\Fork;

use function Illuminate\Support\defer;

class ForkDriver implements Driver
{
    /**
     * Run the given tasks concurrently and return an array containing the results.
     */
    public function run(Closure|array $tasks): array
    {
        $tasks = Arr::wrap($tasks);

        $keys = array_keys($tasks);
        $values = array_values($tasks);

        /** @phpstan-ignore class.notFound */
        $results = Fork::new()->run(...$values);

        ksort($results);

        return array_combine($keys, $results);
    }

    /**
     * Start the given tasks in the background after the current task has finished.
     */
    public function defer(Closure|array $tasks): DeferredCallback
    {
        return defer(fn () => $this->run($tasks));
    }
}
