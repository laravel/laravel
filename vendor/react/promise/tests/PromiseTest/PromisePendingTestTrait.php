<?php

namespace React\Promise\PromiseTest;

trait PromisePendingTestTrait
{
    /**
     * @return \React\Promise\PromiseAdapter\PromiseAdapterInterface
     */
    abstract public function getPromiseTestAdapter(callable $canceller = null);

    /** @test */
    public function thenShouldReturnAPromiseForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertInstanceOf('React\\Promise\\PromiseInterface', $adapter->promise()->then());
    }

    /** @test */
    public function thenShouldReturnAllowNullForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertInstanceOf('React\\Promise\\PromiseInterface', $adapter->promise()->then(null, null, null));
    }

    /** @test */
    public function cancelShouldReturnNullForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertNull($adapter->promise()->cancel());
    }

    /** @test */
    public function doneShouldReturnNullForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertNull($adapter->promise()->done());
    }

    /** @test */
    public function doneShouldReturnAllowNullForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertNull($adapter->promise()->done(null, null, null));
    }

    /** @test */
    public function otherwiseShouldNotInvokeRejectionHandlerForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $adapter->promise()->otherwise($this->expectCallableNever());
    }

    /** @test */
    public function alwaysShouldReturnAPromiseForPendingPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertInstanceOf('React\\Promise\\PromiseInterface', $adapter->promise()->always(function () {}));
    }
}
