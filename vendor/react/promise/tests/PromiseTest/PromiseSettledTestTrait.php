<?php

namespace React\Promise\PromiseTest;

trait PromiseSettledTestTrait
{
    /**
     * @return \React\Promise\PromiseAdapter\PromiseAdapterInterface
     */
    abstract public function getPromiseTestAdapter(callable $canceller = null);

    /** @test */
    public function thenShouldReturnAPromiseForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $this->assertInstanceOf('React\\Promise\\PromiseInterface', $adapter->promise()->then());
    }

    /** @test */
    public function thenShouldReturnAllowNullForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $this->assertInstanceOf('React\\Promise\\PromiseInterface', $adapter->promise()->then(null, null, null));
    }

    /** @test */
    public function cancelShouldReturnNullForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();

        $this->assertNull($adapter->promise()->cancel());
    }

    /** @test */
    public function cancelShouldHaveNoEffectForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter($this->expectCallableNever());

        $adapter->settle();

        $adapter->promise()->cancel();
    }

    /** @test */
    public function doneShouldReturnNullForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $this->assertNull($adapter->promise()->done(null, function () {}));
    }

    /** @test */
    public function doneShouldReturnAllowNullForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $this->assertNull($adapter->promise()->done(null, function () {}, null));
    }

    /** @test */
    public function progressShouldNotInvokeProgressHandlerForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $adapter->promise()->progress($this->expectCallableNever());
        $adapter->notify();
    }

    /** @test */
    public function alwaysShouldReturnAPromiseForSettledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->settle();
        $this->assertInstanceOf('React\\Promise\\PromiseInterface', $adapter->promise()->always(function () {}));
    }
}
