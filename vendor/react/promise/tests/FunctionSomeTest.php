<?php

namespace React\Promise;

use React\Promise\Exception\LengthException;

class FunctionSomeTest extends TestCase
{
    /** @test */
    public function shouldRejectWithLengthExceptionWithEmptyInputArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->callback(function($exception){
                    return $exception instanceof LengthException &&
                           'Input array must contain at least 1 item but contains only 0 items.' === $exception->getMessage();
                })
            );

        some(
            [],
            1
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldRejectWithLengthExceptionWithInputArrayContainingNotEnoughItems()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->callback(function($exception){
                    return $exception instanceof LengthException &&
                           'Input array must contain at least 4 items but contains only 3 items.' === $exception->getMessage();
                })
            );

        some(
            [1, 2, 3],
            4
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldResolveToEmptyArrayWithNonArrayInput()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([]));

        some(
            null,
            1
        )->then($mock);
    }

    /** @test */
    public function shouldResolveValuesArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([1, 2]));

        some(
            [1, 2, 3],
            2
        )->then($mock);
    }

    /** @test */
    public function shouldResolvePromisesArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([1, 2]));

        some(
            [resolve(1), resolve(2), resolve(3)],
            2
        )->then($mock);
    }

    /** @test */
    public function shouldResolveSparseArrayInput()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([null, 1]));

        some(
            [null, 1, null, 2, 3],
            2
        )->then($mock);
    }

    /** @test */
    public function shouldRejectIfAnyInputPromiseRejectsBeforeDesiredNumberOfInputsAreResolved()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([1 => 2, 2 => 3]));

        some(
            [resolve(1), reject(2), reject(3)],
            2
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldAcceptAPromiseForAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([1, 2]));

        some(
            resolve([1, 2, 3]),
            2
        )->then($mock);
    }

    /** @test */
    public function shouldResolveWithEmptyArrayIfHowManyIsLessThanOne()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([]));

        some(
            [1],
            0
        )->then($mock);
    }

    /** @test */
    public function shouldResolveToEmptyArrayWhenInputPromiseDoesNotResolveToArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([]));

        some(
            resolve(1),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldRejectWhenInputPromiseRejects()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        some(
            reject(),
            1
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldCancelInputPromise()
    {
        $mock = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock
            ->expects($this->once())
            ->method('cancel');

        some($mock, 1)->cancel();
    }

    /** @test */
    public function shouldCancelInputArrayPromises()
    {
        $mock1 = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock1
            ->expects($this->once())
            ->method('cancel');

        $mock2 = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock2
            ->expects($this->once())
            ->method('cancel');

        some([$mock1, $mock2], 1)->cancel();
    }

    /** @test */
    public function shouldCancelOtherPendingInputArrayPromisesIfEnoughPromisesFulfill()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->never())
            ->method('__invoke');


        $deferred = New Deferred($mock);
        $deferred->resolve();

        $mock2 = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock2
            ->expects($this->once())
            ->method('cancel');

        some([$deferred->promise(), $mock2], 1)->cancel();
    }
}
