<?php

namespace React\Promise\PromiseTest;

use React\Promise;
use React\Promise\Deferred;

trait RejectTestTrait
{
    /**
     * @return \React\Promise\PromiseAdapter\PromiseAdapterInterface
     */
    abstract public function getPromiseTestAdapter(callable $canceller = null);

    /** @test */
    public function rejectShouldRejectWithAnImmediateValue()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($this->expectCallableNever(), $mock);

        $adapter->reject(1);
    }

    /** @test */
    public function rejectShouldRejectWithFulfilledPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($this->expectCallableNever(), $mock);

        $adapter->reject(Promise\resolve(1));
    }

    /** @test */
    public function rejectShouldRejectWithRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($this->expectCallableNever(), $mock);

        $adapter->reject(Promise\reject(1));
    }

    /** @test */
    public function rejectShouldForwardReasonWhenCallbackIsNull()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then(
                $this->expectCallableNever()
            )
            ->then(
                $this->expectCallableNever(),
                $mock
            );

        $adapter->reject(1);
    }

    /** @test */
    public function rejectShouldMakePromiseImmutable()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then(null, function ($value) use ($adapter) {
                $adapter->reject(3);

                return Promise\reject($value);
            })
            ->then(
                $this->expectCallableNever(),
                $mock
            );

        $adapter->reject(1);
        $adapter->reject(2);
    }

    /** @test */
    public function notifyShouldInvokeOtherwiseHandler()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->otherwise($mock);

        $adapter->reject(1);
    }

    /** @test */
    public function doneShouldInvokeRejectionHandler()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $this->assertNull($adapter->promise()->done(null, $mock));
        $adapter->reject(1);
    }

    /** @test */
    public function doneShouldThrowExceptionThrownByRejectionHandler()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done(null, function () {
            throw new \Exception('UnhandledRejectionException');
        }));
        $adapter->reject(1);
    }

    /** @test */
    public function doneShouldThrowUnhandledRejectionExceptionWhenRejectedWithNonException()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('React\\Promise\\UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done());
        $adapter->reject(1);
    }

    /** @test */
    public function doneShouldThrowUnhandledRejectionExceptionWhenRejectionHandlerRejects()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('React\\Promise\\UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done(null, function () {
            return \React\Promise\reject();
        }));
        $adapter->reject(1);
    }

    /** @test */
    public function doneShouldThrowRejectionExceptionWhenRejectionHandlerRejectsWithException()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done(null, function () {
            return \React\Promise\reject(new \Exception('UnhandledRejectionException'));
        }));
        $adapter->reject(1);
    }

    /** @test */
    public function doneShouldThrowUnhandledRejectionExceptionWhenRejectionHandlerRetunsPendingPromiseWhichRejectsLater()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('React\\Promise\\UnhandledRejectionException');

        $d = new Deferred();
        $promise = $d->promise();

        $this->assertNull($adapter->promise()->done(null, function () use ($promise) {
            return $promise;
        }));
        $adapter->reject(1);
        $d->reject(1);
    }

    /** @test */
    public function doneShouldThrowExceptionProvidedAsRejectionValue()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $this->assertNull($adapter->promise()->done());
        $adapter->reject(new \Exception('UnhandledRejectionException'));
    }

    /** @test */
    public function doneShouldThrowWithDeepNestingPromiseChains()
    {
        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $exception = new \Exception('UnhandledRejectionException');

        $d = new Deferred();

        $result = \React\Promise\resolve(\React\Promise\resolve($d->promise()->then(function () use ($exception) {
            $d = new Deferred();
            $d->resolve();

            return \React\Promise\resolve($d->promise()->then(function () {}))->then(
                function () use ($exception) {
                    throw $exception;
                }
            );
        })));

        $result->done();

        $d->resolve();
    }

    /** @test */
    public function doneShouldRecoverWhenRejectionHandlerCatchesException()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->assertNull($adapter->promise()->done(null, function (\Exception $e) {

        }));
        $adapter->reject(new \Exception('UnhandledRejectionException'));
    }

    /** @test */
    public function alwaysShouldNotSuppressRejection()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->promise()
            ->always(function () {})
            ->then(null, $mock);

        $adapter->reject($exception);
    }

    /** @test */
    public function alwaysShouldNotSuppressRejectionWhenHandlerReturnsANonPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->promise()
            ->always(function () {
                return 1;
            })
            ->then(null, $mock);

        $adapter->reject($exception);
    }

    /** @test */
    public function alwaysShouldNotSuppressRejectionWhenHandlerReturnsAPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->promise()
            ->always(function () {
                return \React\Promise\resolve(1);
            })
            ->then(null, $mock);

        $adapter->reject($exception);
    }

    /** @test */
    public function alwaysShouldRejectWhenHandlerThrowsForRejection()
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

        $adapter->reject($exception);
    }

    /** @test */
    public function alwaysShouldRejectWhenHandlerRejectsForRejection()
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

        $adapter->reject($exception);
    }
}
