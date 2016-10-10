<?php

namespace React\Promise\PromiseTest;

use React\Promise\Deferred;

trait PromiseRejectedTestTrait
{
    /**
     * @return \React\Promise\PromiseAdapter\PromiseAdapterInterface
     */
    abstract public function getPromiseTestAdapter(callable $canceller = null);

    /** @test */
    public function rejectedPromiseShouldBeImmutable()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->reject(1);
        $adapter->reject(2);

        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $mock
            );
    }

    /** @test */
    public function rejectedPromiseShouldInvokeNewlyAddedCallback()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->reject(1);

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->promise()
            ->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldForwardUndefinedRejectionValue()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with(null);

        $adapter->reject(1);
        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                function () {
                    // Presence of rejection handler is enough to switch back
                    // to resolve mode, even though it returns undefined.
                    // The ONLY way to propagate a rejection is to re-throw or
                    // return a rejected promise;
                }
            )
            ->then(
                $mock,
                $this->expectCallableNever()
            );
    }

    /** @test */
    public function shouldSwitchFromErrbacksToCallbacksWhenErrbackDoesNotExplicitlyPropagate()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $adapter->reject(1);
        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                function ($val) {
                    return $val + 1;
                }
            )
            ->then(
                $mock,
                $this->expectCallableNever()
            );
    }

    /** @test */
    public function shouldSwitchFromErrbacksToCallbacksWhenErrbackReturnsAResolution()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $adapter->reject(1);
        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                function ($val) {
                    return \React\Promise\resolve($val + 1);
                }
            )
            ->then(
                $mock,
                $this->expectCallableNever()
            );
    }

    /** @test */
    public function shouldPropagateRejectionsWhenErrbackThrows()
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

        $adapter->reject(1);
        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                $mock
            )
            ->then(
                $this->expectCallableNever(),
                $mock2
            );
    }

    /** @test */
    public function shouldPropagateRejectionsWhenErrbackReturnsARejection()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $adapter->reject(1);
        $adapter->promise()
            ->then(
                $this->expectCallableNever(),
                function ($val) {
                    return \React\Promise\reject($val + 1);
                }
            )
            ->then(
                $this->expectCallableNever(),
                $mock
            );
    }

    /** @test */
    public function doneShouldInvokeRejectionHandlerForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->reject(1);
        $this->assertNull($adapter->promise()->done(null, $mock));
    }

    /** @test */
    public function doneShouldThrowExceptionThrownByRejectionHandlerForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $adapter->reject(1);
        $this->assertNull($adapter->promise()->done(null, function () {
            throw new \Exception('UnhandledRejectionException');
        }));
    }

    /** @test */
    public function doneShouldThrowUnhandledRejectionExceptionWhenRejectedWithNonExceptionForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('React\\Promise\\UnhandledRejectionException');

        $adapter->reject(1);
        $this->assertNull($adapter->promise()->done());
    }

    /** @test */
    public function doneShouldThrowUnhandledRejectionExceptionWhenRejectionHandlerRejectsForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('React\\Promise\\UnhandledRejectionException');

        $adapter->reject(1);
        $this->assertNull($adapter->promise()->done(null, function () {
            return \React\Promise\reject();
        }));
    }

    /** @test */
    public function doneShouldThrowRejectionExceptionWhenRejectionHandlerRejectsWithExceptionForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $adapter->reject(1);
        $this->assertNull($adapter->promise()->done(null, function () {
            return \React\Promise\reject(new \Exception('UnhandledRejectionException'));
        }));
    }

    /** @test */
    public function doneShouldThrowExceptionProvidedAsRejectionValueForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $adapter->reject(new \Exception('UnhandledRejectionException'));
        $this->assertNull($adapter->promise()->done());
    }

    /** @test */
    public function doneShouldThrowWithDeepNestingPromiseChainsForRejectedPromise()
    {
        $this->setExpectedException('\Exception', 'UnhandledRejectionException');

        $exception = new \Exception('UnhandledRejectionException');

        $d = new Deferred();
        $d->resolve();

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
    }

    /** @test */
    public function doneShouldRecoverWhenRejectionHandlerCatchesExceptionForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->reject(new \Exception('UnhandledRejectionException'));
        $this->assertNull($adapter->promise()->done(null, function (\Exception $e) {

        }));
    }

    /** @test */
    public function otherwiseShouldInvokeRejectionHandlerForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $adapter->reject(1);
        $adapter->promise()->otherwise($mock);
    }

    /** @test */
    public function otherwiseShouldInvokeNonTypeHintedRejectionHandlerIfReasonIsAnExceptionForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->reject($exception);
        $adapter->promise()
            ->otherwise(function ($reason) use ($mock) {
                $mock($reason);
            });
    }

    /** @test */
    public function otherwiseShouldInvokeRejectionHandlerIfReasonMatchesTypehintForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \InvalidArgumentException();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->reject($exception);
        $adapter->promise()
            ->otherwise(function (\InvalidArgumentException $reason) use ($mock) {
                $mock($reason);
            });
    }

    /** @test */
    public function otherwiseShouldNotInvokeRejectionHandlerIfReaonsDoesNotMatchTypehintForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->expectCallableNever();

        $adapter->reject($exception);
        $adapter->promise()
            ->otherwise(function (\InvalidArgumentException $reason) use ($mock) {
                $mock($reason);
            });
    }

    /** @test */
    public function alwaysShouldNotSuppressRejectionForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->reject($exception);
        $adapter->promise()
            ->always(function () {})
            ->then(null, $mock);
    }

    /** @test */
    public function alwaysShouldNotSuppressRejectionWhenHandlerReturnsANonPromiseForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->reject($exception);
        $adapter->promise()
            ->always(function () {
                return 1;
            })
            ->then(null, $mock);
    }

    /** @test */
    public function alwaysShouldNotSuppressRejectionWhenHandlerReturnsAPromiseForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception));

        $adapter->reject($exception);
        $adapter->promise()
            ->always(function () {
                return \React\Promise\resolve(1);
            })
            ->then(null, $mock);
    }

    /** @test */
    public function alwaysShouldRejectWhenHandlerThrowsForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception1 = new \Exception();
        $exception2 = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception2));

        $adapter->reject($exception1);
        $adapter->promise()
            ->always(function () use ($exception2) {
                throw $exception2;
            })
            ->then(null, $mock);
    }

    /** @test */
    public function alwaysShouldRejectWhenHandlerRejectsForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $exception1 = new \Exception();
        $exception2 = new \Exception();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($exception2));

        $adapter->reject($exception1);
        $adapter->promise()
            ->always(function () use ($exception2) {
                return \React\Promise\reject($exception2);
            })
            ->then(null, $mock);
    }

    /** @test */
    public function cancelShouldReturnNullForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter();

        $adapter->reject();

        $this->assertNull($adapter->promise()->cancel());
    }

    /** @test */
    public function cancelShouldHaveNoEffectForRejectedPromise()
    {
        $adapter = $this->getPromiseTestAdapter($this->expectCallableNever());

        $adapter->reject();

        $adapter->promise()->cancel();
    }
}
