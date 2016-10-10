<?php

namespace React\Promise\PromiseTest;

use React\Promise;

trait ResolveTestTrait
{
    /**
     * @return \React\Promise\PromiseAdapter\PromiseAdapterInterface
     */
    abstract public function getPromiseTestAdapter(callable $canceller = null);

    /** @test */
    public function resolveShouldResolve()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($mock);

        $adapter->resolve(1);
    }

    /** @test */
    public function resolveShouldResolveWithPromisedValue()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($mock);

        $adapter->resolve(Promise\resolve(1));
    }

    /** @test */
    public function resolveShouldRejectWhenResolvedWithRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($this->expectCallableNever(), $mock);

        $adapter->resolve(Promise\reject(1));
    }

    /** @test */
    public function resolveShouldForwardValueWhenCallbackIsNull()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then(
                null,
                $this->expectCallableNever()
            )
            ->then(
                $mock,
                $this->expectCallableNever()
            );

        $adapter->resolve(1);
    }

    /** @test */
    public function resolveShouldMakePromiseImmutable()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then(function ($value) use ($adapter) {
                $adapter->resolve(3);

                return $value;
            })
            ->then(
                $mock,
                $this->expectCallableNever()
            );

        $adapter->resolve(1);
        $adapter->resolve(2);
    }

    /** @test */
    public function doneShouldInvokeFulfillmentHandler()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $this->assertNull($adapter->promise()->done($mock));
        $adapter->resolve(1);
    }

    /** @test */
    public function doneShouldThrowExceptionThrownFulfillmentHandler()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done(function () {
            throw new \Exception('UnhandledRejectionException');
        }));
        $adapter->resolve(1);
    }

    /** @test */
    public function doneShouldThrowUnhandledRejectionExceptionWhenFulfillmentHandlerRejects()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('React\\Promise\\UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done(function () {
            return \React\Promise\reject();
        }));
        $adapter->resolve(1);
    }

    /** @test */
    public function alwaysShouldNotSuppressValue()
    {
        $adapter = $this->getPromiseTestAdapter();

        $value = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($value));

        $adapter->promise()
            ->always(function () {})
            ->then($mock);

        $adapter->resolve($value);
    }

    /** @test */
    public function alwaysShouldNotSuppressValueWhenHandlerReturnsANonPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $value = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($value));

        $adapter->promise()
            ->always(function () {
                return 1;
            })
            ->then($mock);

        $adapter->resolve($value);
    }

    /** @test */
    public function alwaysShouldNotSuppressValueWhenHandlerReturnsAPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $value = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($value));

        $adapter->promise()
            ->always(function () {
                return \React\Promise\resolve(1);
            })
            ->then($mock);

        $adapter->resolve($value);
    }

    /** @test */
    public function alwaysShouldRejectWhenHandlerThrowsForFulfillment()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->promise()
            ->always(function () use ($exception) {
                throw $exception;
            })
            ->then(null, $mock);

        $adapter->resolve(1);
    }

    /** @test */
    public function alwaysShouldRejectWhenHandlerRejectsForFulfillment()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->promise()
            ->always(function () use ($exception) {
                return \React\Promise\reject($exception);
            })
            ->then(null, $mock);

        $adapter->resolve(1);
    }
}
