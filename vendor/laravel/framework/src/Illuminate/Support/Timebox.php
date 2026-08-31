<?php

namespace Illuminate\Support;

use Throwable;

class Timebox
{
    /**
     * Indicates if the timebox is allowed to return early.
     *
     * @var bool
     */
    public $earlyReturn = false;

    /**
     * Invoke the given callback within the specified timebox minimum.
     *
     * @template TCallReturnType
     *
     * @param  (callable($this): TCallReturnType)  $callback
     * @param  int  $microseconds
     * @return TCallReturnType
     *
     * @throws \Throwable
     */
    public function call(callable $callback, int $microseconds)
    {
        $exception = null;

        $start = microtime(true);

        try {
            $result = $callback($this);
        } catch (Throwable $caught) {
            $exception = $caught;
        }

        $remainder = (int) ($microseconds - ((microtime(true) - $start) * 1_000_000));

        if (! $this->earlyReturn && $remainder > 0) {
            $this->usleep($remainder);
        }

        if ($exception) {
            throw $exception;
        }

        return $result;
    }

    /**
     * Indicate that the timebox can return early.
     *
     * @return $this
     */
    public function returnEarly()
    {
        $this->earlyReturn = true;

        return $this;
    }

    /**
     * Indicate that the timebox cannot return early.
     *
     * @return $this
     */
    public function dontReturnEarly()
    {
        $this->earlyReturn = false;

        return $this;
    }

    /**
     * Sleep for the specified number of microseconds.
     *
     * @param  int  $microseconds
     * @return void
     */
    protected function usleep(int $microseconds)
    {
        Sleep::usleep($microseconds);
    }
}
