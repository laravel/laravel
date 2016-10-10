<?php

namespace React\Promise\PromiseTest;

trait NotifyTestTrait
{
    /**
     * @return \React\Promise\PromiseAdapter\PromiseAdapterInterface
     */
    abstract public function getPromiseTestAdapter(callable $canceller = null);

    /** @test */
    public function notifyShouldProgress()
    {
        $adapter = $this->getPromiseTestAdapter();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        $adapter->promise()
            ->then($this->expectCallableNever(), $this->expectCallableNever(), $mock);

        $adapter->notify($sentinel);
    }

    /** @test */
    public function notifyShouldPropagateProgressToDownstreamPromises()
    {
        $adapter = $this->getPromiseTestAdapter();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->returnArgument(0));

        $mock2 = $this->createCallableMock();
        $mock2
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock
            )
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock2
            );

        $adapter->notify($sentinel);
    }

    /** @test */
    public function notifyShouldPropagateTransformedProgressToDownstreamPromises()
    {
        $adapter = $this->getPromiseTestAdapter();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($sentinel));

        $mock2 = $this->createCallableMock();
        $mock2
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock
            )
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock2
            );

        $adapter->notify(1);
    }

    /** @test */
    public function notifyShouldPropagateCaughtExceptionValueAsProgress()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->throwException($exception));

        $mock2 = $this->createCallableMock();
        $mock2
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock
            )
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock2
            );

        $adapter->notify(1);
    }

    /** @test */
    public function notifyShouldForwardProgressEventsWhenIntermediaryCallbackTiedToAResolvedPromiseReturnsAPromise()
    {
        $adapter = $this->getPromiseTestAdapter();
        $adapter2 = $this->getPromiseTestAdapter();

        $promise2 = $adapter2->promise();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        // resolve BEFORE attaching progress handler
        $adapter->resolve();

        $adapter->promise()
            ->then(function () use ($promise2) {
                return $promise2;
            })
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock
            );

        $adapter2->notify($sentinel);
    }

    /** @test */
    public function notifyShouldForwardProgressEventsWhenIntermediaryCallbackTiedToAnUnresolvedPromiseReturnsAPromise()
    {
        $adapter = $this->getPromiseTestAdapter();
        $adapter2 = $this->getPromiseTestAdapter();

        $promise2 = $adapter2->promise();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        $adapter->promise()
            ->then(function () use ($promise2) {
                return $promise2;
            })
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock
            );

        // resolve AFTER attaching progress handler
        $adapter->resolve();
        $adapter2->notify($sentinel);
    }

    /** @test */
    public function notifyShouldForwardProgressWhenResolvedWithAnotherPromise()
    {
        $adapter = $this->getPromiseTestAdapter();
        $adapter2 = $this->getPromiseTestAdapter();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($sentinel));

        $mock2 = $this->createCallableMock();
        $mock2
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock
            )
            ->then(
                $this->expectCallableNever(),
                $this->expectCallableNever(),
                $mock2
            );

        $adapter->resolve($adapter2->promise());
        $adapter2->notify($sentinel);
    }

    /** @test */
    public function notifyShouldAllowResolveAfterProgress()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->at(0))
            ->method('__invoke')
            ->with($this->identicalTo(1));
        $mock
            ->expects($this->at(1))
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $adapter->promise()
            ->then(
                $mock,
                $this->expectCallableNever(),
                $mock
            );

        $adapter->notify(1);
        $adapter->resolve(2);
    }

    /** @test */
    public function notifyShouldAllowRejectAfterProgress()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->at(0))
            ->method('__invoke')
            ->with($this->identicalTo(1));
        $mock
            ->expects($this->at(1))
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $mock,
                $mock
            );

        $adapter->notify(1);
        $adapter->reject(2);
    }

    /** @test */
    public function notifyShouldReturnSilentlyOnProgressWhenAlreadyRejected()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->reject(1);

        $this->assertNull($adapter->notify());
    }

    /** @test */
    public function notifyShouldInvokeProgressHandler()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()->progress($mock);
        $adapter->notify(1);
    }

    /** @test */
    public function notifyShouldInvokeProgressHandlerFromDone()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $this->assertNull($adapter->promise()->done(null, null, $mock));
        $adapter->notify(1);
    }

    /** @test */
    public function notifyShouldThrowExceptionThrownProgressHandlerFromDone()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done(null, null, function () {
            throw new \Exception('UnhandledRejectionException');
        }));
        $adapter->notify(1);
    }
}
