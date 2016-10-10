<?php

namespace React\Promise;

use React\Promise\Exception\LengthException;

class FunctionAnyTest extends TestCase
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

        any([])
            ->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldResolveToNullWithNonArrayInput()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        any(null)
            ->then($mock);
    }

    /** @test */
    public function shouldResolveWithAnInputValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        any([1, 2, 3])
            ->then($mock);
    }

    /** @test */
    public function shouldResolveWithAPromisedInputValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        any([resolve(1), resolve(2), resolve(3)])
            ->then($mock);
    }

    /** @test */
    public function shouldRejectWithAllRejectedInputValuesIfAllInputsAreRejected()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([0 => 1, 1 => 2, 2 => 3]));

        any([reject(1), reject(2), reject(3)])
            ->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldResolveWhenFirstInputPromiseResolves()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        any([resolve(1), reject(2), reject(3)])
            ->then($mock);
    }

    /** @test */
    public function shouldAcceptAPromiseForAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        any(resolve([1, 2, 3]))
            ->then($mock);
    }

    /** @test */
    public function shouldResolveToNullArrayWhenInputPromiseDoesNotResolveToArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        any(resolve(1))
            ->then($mock);
    }

    /** @test */
    public function shouldNotRelyOnArryIndexesWhenUnwrappingToASingleResolutionValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $d1 = new Deferred();
        $d2 = new Deferred();

        any(['abc' => $d1->promise(), 1 => $d2->promise()])
            ->then($mock);

        $d2->resolve(2);
        $d1->resolve(1);
    }

    /** @test */
    public function shouldRejectWhenInputPromiseRejects()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        any(reject())
            ->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldCancelInputPromise()
    {
        $mock = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock
            ->expects($this->once())
            ->method('cancel');

        any($mock)->cancel();
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

        any([$mock1, $mock2])->cancel();
    }

    /** @test */
    public function shouldCancelOtherPendingInputArrayPromisesIfOnePromiseFulfills()
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
