<?php

declare(strict_types=1);

namespace GuzzleHttp\Promise;

/**
 * A promise represents the eventual result of an asynchronous operation.
 *
 * The primary way of interacting with a promise is through its then method,
 * which registers callbacks to receive either a promise’s eventual value or
 * the reason why the promise cannot be fulfilled.
 *
 * @see https://promisesaplus.com/
 */
interface PromiseInterface
{
    public const PENDING = 'pending';
    public const FULFILLED = 'fulfilled';
    public const REJECTED = 'rejected';

    /**
     * Appends fulfillment and rejection handlers to the promise, and returns
     * a new promise resolving to the return value of the called handler.
     *
     * @param callable $onFulfilled Invoked when the promise fulfills.
     * @param callable $onRejected  Invoked when the promise is rejected.
     */
    public function then(
        ?callable $onFulfilled = null,
        ?callable $onRejected = null
    ): PromiseInterface;

    /**
     * Appends a rejection handler callback to the promise, and returns a new
     * promise resolving to the return value of the callback if it is called,
     * or to its original fulfillment value if the promise is instead
     * fulfilled.
     *
     * @param callable $onRejected Invoked when the promise is rejected.
     */
    public function otherwise(callable $onRejected): PromiseInterface;

    /**
     * Get the state of the promise ("pending", "rejected", or "fulfilled").
     *
     * The three states can be checked against the constants defined on
     * PromiseInterface: PENDING, FULFILLED, and REJECTED.
     */
    public function getState(): string;

    /**
     * Resolve the promise with the given value.
     *
     * @param mixed $value
     *
     * @throws \RuntimeException if the promise is already resolved.
     */
    public function resolve($value): void;

    /**
     * Reject the promise with the given reason.
     *
     * @param mixed $reason
     *
     * @throws \RuntimeException if the promise is already resolved.
     */
    public function reject($reason): void;

    /**
     * Cancels the promise if possible.
     *
     * @see https://github.com/promises-aplus/cancellation-spec/issues/7
     */
    public function cancel(): void;

    /**
     * Waits until the promise completes if possible.
     *
     * Pass $unwrap as true to unwrap the result of the promise, either
     * returning the resolved value or throwing the rejected exception.
     *
     * If the promise cannot be waited on, then the promise will be rejected.
     *
     * @return mixed
     *
     * @throws \LogicException if the promise has no wait function or if the
     *                         promise does not settle after waiting.
     */
    public function wait(bool $unwrap = true);
}
