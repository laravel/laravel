<?php

declare(strict_types=1);

namespace GuzzleHttp\Promise;

/**
 * Promises/A+ implementation that avoids recursion when possible.
 *
 * @see https://promisesaplus.com/
 *
 * @final
 */
class Promise implements PromiseInterface
{
    private $state = self::PENDING;
    private $result;
    private $cancelFn;
    private $waitFn;
    private $waitList;
    private $handlers = [];

    /**
     * @param callable $waitFn   Fn that when invoked resolves the promise.
     * @param callable $cancelFn Fn that when invoked cancels the promise.
     */
    public function __construct(
        ?callable $waitFn = null,
        ?callable $cancelFn = null
    ) {
        $this->waitFn = $waitFn;
        $this->cancelFn = $cancelFn;
    }

    public function then(
        ?callable $onFulfilled = null,
        ?callable $onRejected = null
    ): PromiseInterface {
        if ($this->state === self::PENDING) {
            $p = new Promise(null, [$this, 'cancel']);
            $this->handlers[] = [$p, $onFulfilled, $onRejected];
            $p->waitList = $this->waitList;
            $p->waitList[] = $this;

            return $p;
        }

        // Return a fulfilled promise and immediately invoke any callbacks.
        if ($this->state === self::FULFILLED) {
            $promise = Create::promiseFor($this->result);

            return $onFulfilled ? $promise->then($onFulfilled) : $promise;
        }

        // It's either cancelled or rejected, so return a rejected promise
        // and immediately invoke any callbacks.
        $rejection = Create::rejectionFor($this->result);

        return $onRejected ? $rejection->then(null, $onRejected) : $rejection;
    }

    public function otherwise(callable $onRejected): PromiseInterface
    {
        return $this->then(null, $onRejected);
    }

    public function wait(bool $unwrap = true)
    {
        $this->waitIfPending();

        if ($this->result instanceof PromiseInterface) {
            return $this->result->wait($unwrap);
        }
        if ($unwrap) {
            if ($this->state === self::FULFILLED) {
                return $this->result;
            }
            // It's rejected so "unwrap" and throw an exception.
            throw Create::exceptionFor($this->result);
        }
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function cancel(): void
    {
        if ($this->state !== self::PENDING) {
            return;
        }

        $this->waitFn = $this->waitList = null;

        if ($this->cancelFn) {
            $fn = $this->cancelFn;
            $this->cancelFn = null;
            try {
                $fn();
            } catch (\Throwable $e) {
                $this->reject($e);
            }
        }

        // Reject the promise only if it wasn't rejected in a then callback.
        /** @psalm-suppress RedundantCondition */
        if ($this->state === self::PENDING) {
            $this->reject(new CancellationException('Promise has been cancelled'));
        }
    }

    public function resolve($value): void
    {
        $this->settle(self::FULFILLED, $value);
    }

    public function reject($reason): void
    {
        $this->settle(self::REJECTED, $reason);
    }

    private function settle(string $state, $value): void
    {
        if ($this->state !== self::PENDING) {
            // Ignore calls with the same resolution.
            if ($state === $this->state && $value === $this->result) {
                return;
            }
            throw $this->state === $state
                ? new \LogicException("The promise is already {$state}.")
                : new \LogicException("Cannot change a {$this->state} promise to {$state}");
        }

        if ($value === $this) {
            throw new \LogicException('Cannot fulfill or reject a promise with itself');
        }

        // Clear out the state of the promise but stash the handlers.
        $this->state = $state;
        $this->result = $value;
        $handlers = $this->handlers;
        $this->handlers = null;
        $this->waitList = $this->waitFn = null;
        $this->cancelFn = null;

        if (!$handlers) {
            return;
        }

        // If the value was not a settled promise or a thenable, then resolve
        // it in the task queue using the correct ID.
        if (!is_object($value) || !method_exists($value, 'then')) {
            $id = $state === self::FULFILLED ? 1 : 2;
            // It's a success, so resolve the handlers in the queue.
            Utils::queue()->add(static function () use ($id, $value, $handlers): void {
                foreach ($handlers as $handler) {
                    self::callHandler($id, $value, $handler);
                }
            });
        } elseif ($value instanceof Promise && Is::pending($value)) {
            // We can just merge our handlers onto the next promise.
            $value->handlers = array_merge($value->handlers, $handlers);
        } else {
            // Resolve the handlers when the forwarded promise is resolved.
            $value->then(
                static function ($value) use ($handlers): void {
                    foreach ($handlers as $handler) {
                        self::callHandler(1, $value, $handler);
                    }
                },
                static function ($reason) use ($handlers): void {
                    foreach ($handlers as $handler) {
                        self::callHandler(2, $reason, $handler);
                    }
                }
            );
        }
    }

    /**
     * Call a stack of handlers using a specific callback index and value.
     *
     * @param int   $index   1 (resolve) or 2 (reject).
     * @param mixed $value   Value to pass to the callback.
     * @param array $handler Array of handler data (promise and callbacks).
     */
    private static function callHandler(int $index, $value, array $handler): void
    {
        /** @var PromiseInterface $promise */
        $promise = $handler[0];

        // The promise may have been cancelled or resolved before placing
        // this thunk in the queue.
        if (Is::settled($promise)) {
            return;
        }

        try {
            if (isset($handler[$index])) {
                /*
                 * If $f throws an exception, then $handler will be in the exception
                 * stack trace. Since $handler contains a reference to the callable
                 * itself we get a circular reference. We clear the $handler
                 * here to avoid that memory leak.
                 */
                $f = $handler[$index];
                unset($handler);
                $promise->resolve($f($value));
            } elseif ($index === 1) {
                // Forward resolution values as-is.
                $promise->resolve($value);
            } else {
                // Forward rejections down the chain.
                $promise->reject($value);
            }
        } catch (\Throwable $reason) {
            $promise->reject($reason);
        }
    }

    private function waitIfPending(): void
    {
        if ($this->state !== self::PENDING) {
            return;
        } elseif ($this->waitFn) {
            $this->invokeWaitFn();
        } elseif ($this->waitList) {
            $this->invokeWaitList();
        } else {
            // If there's no wait function, then reject the promise.
            $this->reject('Cannot wait on a promise that has '
                .'no internal wait function. You must provide a wait '
                .'function when constructing the promise to be able to '
                .'wait on a promise.');
        }

        Utils::queue()->run();

        /** @psalm-suppress RedundantCondition */
        if ($this->state === self::PENDING) {
            $this->reject('Invoking the wait callback did not resolve the promise');
        }
    }

    private function invokeWaitFn(): void
    {
        try {
            $wfn = $this->waitFn;
            $this->waitFn = null;
            $wfn(true);
        } catch (\Throwable $reason) {
            if ($this->state === self::PENDING) {
                // The promise has not been resolved yet, so reject the promise
                // with the exception.
                $this->reject($reason);
            } else {
                // The promise was already resolved, so there's a problem in
                // the application.
                throw $reason;
            }
        }
    }

    private function invokeWaitList(): void
    {
        $waitList = $this->waitList;
        $this->waitList = null;

        foreach ($waitList as $result) {
            do {
                $result->waitIfPending();
                $result = $result->result;
            } while ($result instanceof Promise);

            if ($result instanceof PromiseInterface) {
                $result->wait(false);
            }
        }
    }
}
