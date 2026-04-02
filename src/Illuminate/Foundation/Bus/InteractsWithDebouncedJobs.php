<?php

namespace Illuminate\Foundation\Bus;

use Illuminate\Bus\DebounceLock;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\ShouldBeDebounced;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Attributes\DebounceFor;
use LogicException;
use ReflectionClass;

/**
 * Adds debounce lock acquisition to PendingDispatch.
 *
 * Integration: Use this trait in PendingDispatch and call
 * $this->acquireDebounceLock() in __destruct() AFTER the
 * shouldDispatch() check but BEFORE the actual dispatch.
 *
 * @see \Illuminate\Foundation\Bus\PendingDispatch::__destruct()
 */
trait InteractsWithDebouncedJobs
{
    /**
     * Acquire a debounce lock for the job and set its delay.
     *
     * This method should be called in PendingDispatch::__destruct()
     * AFTER the shouldDispatch() check but BEFORE the actual dispatch.
     * It replaces any existing debounce lock for this job identity,
     * stores the owner token on the job, and sets the delay.
     *
     * @return void
     *
     * @throws \LogicException
     */
    protected function acquireDebounceLock(): void
    {
        if (! $this->job instanceof ShouldBeDebounced) {
            return;
        }

        if ($this->job instanceof ShouldBeUnique) {
            throw new LogicException(
                'A job cannot implement both ShouldBeDebounced and ShouldBeUnique. '.
                'Debounce (last wins) and unique (first wins) are mutually exclusive.'
            );
        }

        $lock = new DebounceLock(Container::getInstance()->make(Cache::class));
        $this->job->debounceOwner = $lock->acquire($this->job);

        if (is_null($this->job->delay)) {
            $debounceFor = method_exists($this->job, 'debounceFor')
                ? $this->job->debounceFor()
                : $this->getDebounceForFromAttribute();

            if ($debounceFor > 0) {
                $this->job->delay = $debounceFor;
            }
        }
    }

    /**
     * Read the debounceFor value from a DebounceFor PHP attribute.
     *
     * @return int
     */
    private function getDebounceForFromAttribute(): int
    {
        $attributes = (new ReflectionClass($this->job))
            ->getAttributes(DebounceFor::class);

        if (! empty($attributes)) {
            return $attributes[0]->newInstance()->debounceFor;
        }

        return 0;
    }
}
