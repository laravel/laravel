<?php
namespace GuzzleHttp\Ring\Future;

use React\Promise\FulfilledPromise;
use React\Promise\RejectedPromise;

/**
 * Represents a future value that has been resolved or rejected.
 */
class CompletedFutureValue implements FutureInterface
{
    protected $result;
    protected $error;

    private $cachedPromise;

    /**
     * @param mixed      $result Resolved result
     * @param \Exception $e      Error. Pass a GuzzleHttp\Ring\Exception\CancelledFutureAccessException
     *                           to mark the future as cancelled.
     */
    public function __construct($result, \Exception $e = null)
    {
        $this->result = $result;
        $this->error = $e;
    }

    public function wait()
    {
        if ($this->error) {
            throw $this->error;
        }

        return $this->result;
    }

    public function cancel() {}

    public function promise()
    {
        if (!$this->cachedPromise) {
            $this->cachedPromise = $this->error
                ? new RejectedPromise($this->error)
                : new FulfilledPromise($this->result);
        }

        return $this->cachedPromise;
    }

    public function then(
        callable $onFulfilled = null,
        callable $onRejected = null,
        callable $onProgress = null
    ) {
        return $this->promise()->then($onFulfilled, $onRejected, $onProgress);
    }
}
